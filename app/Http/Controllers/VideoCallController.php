<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\Models\Solicitation;
use App\Models\Message;

class VideoCallController extends Controller
{
    /**
     * Inicia videochamada com Jitsi como principal e Google Meet como fallback.
     */
    public function redirectToGoogle(Request $request)
    {
        $request->validate(["solicitation_id" => "required|exists:solicitations,id"]);
        
        $solicitationId = $request->input("solicitation_id");
        $solicitation = Solicitation::find($solicitationId);
        
        if (!$solicitation) {
            return redirect()->route("chat.index")->with("error", "Chamado não encontrado.");
        }

        $route = auth()->user()->role === "atendente"
            ? route("atendente.chat.index", $solicitationId)
            : route("chat.index", $solicitationId);

        if ($solicitation->status === "na_fila") {
            return redirect($route)->with("error", "Não é possível iniciar chamada de vídeo antes do atendimento ser iniciado pelo atendente.");
        }

        // Verifica se já existe uma chamada de vídeo ativa para este chamado
        $activeCallExists = Message::where('solicitation_id', $solicitationId)
            ->where('type', 'videocall')
            ->where('metadata->status', 'active')
            ->exists();

        if ($activeCallExists) {
            return redirect($route)->with("error", "Não é possível iniciar uma nova chamada enquanto a chamada atual estiver ativa.");
        }

        // Jitsi é o principal.
        $roomId = "prisma-" . $solicitationId . "-" . substr(md5(time()), 0, 8);
        $jitsiDomain = config('services.jitsi.domain', 'meet.jit.si');
        $meetUrl = "https://" . $jitsiDomain . "/" . $roomId;

        // Google Meet é fallback quando disponível.
        $fallbackMeetUrl = null;
        $spaceData = $this->createGoogleMeetSpace();
        if ($spaceData) {
            $fallbackMeetUrl = $spaceData['meetingUri'] ?? null;
        }

        // Cria mensagem especial de videochamada no chat
        $message = Message::create([
            "solicitation_id" => $solicitationId,
            "user_id" => auth()->id(),
            "text" => null,
            "type" => "videocall",
            "metadata" => [
                "room_id" => $roomId,
                "meet_url" => $meetUrl,
                "fallback_meet_url" => $fallbackMeetUrl,
                "initiated_by" => auth()->user()->name,
                "initiated_by_role" => auth()->user()->role,
                "status" => "active", // active ou ended
                "started_at" => now()->toISOString(),
            ],
            "reactions" => [],
        ]);

        return redirect($route);
    }

    /**
     * Callback do Google OAuth (mantido para compatibilidade de rotas).
     */
    public function handleGoogleCallback(Request $request)
    {
        return redirect()->route("chat.index");
    }

    /**
     * Ingressa na chamada: Jitsi por padrão, com fallback opcional para Google Meet.
     */
    public function joinCall(Request $request, Message $message)
    {
        if ($message->type !== 'videocall') {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Mensagem inválida.'], 422);
            }
            return redirect()->back()->with('error', 'Mensagem inválida.');
        }

        $metadata = $message->metadata;
        $meetUrl = $metadata['meet_url'] ?? null;
        $fallbackMeetUrl = $metadata['fallback_meet_url'] ?? null;
        $useFallback = $request->boolean('fallback');
        $targetUrl = $useFallback && $fallbackMeetUrl ? $fallbackMeetUrl : $meetUrl;

        if (!$targetUrl) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'URL da reunião não encontrada.'], 422);
            }
            return redirect()->back()->with('error', 'URL da reunião não encontrada.');
        }

        if (($metadata['status'] ?? '') === 'ended') {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Esta reunião já foi encerrada.'], 422);
            }
            return redirect()->back()->with('error', 'Esta reunião já foi encerrada.');
        }

        $solicitation = $message->solicitation;
        if ($solicitation && $solicitation->status === 'na_fila') {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Não é possível acessar a chamada de vídeo antes do atendimento ser iniciado pelo atendente.'], 422);
            }
            return redirect()->back()->with('error', 'Não é possível acessar a chamada de vídeo antes do atendimento ser iniciado pelo atendente.');
        }

        // Se for um link do Jitsi, gera o JWT dinâmico para o usuário atual
        $roomId = $metadata['room_id'] ?? null;
        if ($roomId && $this->isJitsiUrl($targetUrl)) {
            $jwt = $this->generateJitsiJwt($roomId);
            if ($jwt) {
                $query = parse_url($targetUrl, PHP_URL_QUERY);
                $separator = $query ? '&' : '?';
                $targetUrl .= $separator . 'jwt=' . $jwt;
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'url' => $targetUrl]);
        }

        return redirect($targetUrl);
    }

    private function isJitsiUrl($url)
    {
        if (!$url) return false;
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? '';
        $jitsiDomain = config('services.jitsi.domain', 'meet.jit.si');
        return $host === 'meet.jit.si' || $host === $jitsiDomain;
    }

    /**
     * Cria uma sala no Google Meet usando credenciais de Conta de Serviço (JWT).
     */
    private function createGoogleMeetSpace()
    {
        $serviceAccountPath = storage_path('app/private/google-service-account.json');
        
        if (!file_exists($serviceAccountPath)) {
            logger('Google Service Account JSON não encontrado em: ' . $serviceAccountPath);
            return null;
        }

        try {
            $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
            if (!$serviceAccount) {
                logger('Arquivo Google Service Account JSON é inválido.');
                return null;
            }

            $privateKey = $serviceAccount['private_key'];
            $clientEmail = $serviceAccount['client_email'];

            $base64UrlEncode = function ($data) {
                return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
            };

            $header = $base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            
            $payloadData = [
                'iss' => $clientEmail,
                'scope' => 'https://www.googleapis.com/auth/meetings.space.created',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => time() + 3600,
                'iat' => time()
            ];

            // Tenta usar o e-mail de personificação do Google Workspace
            $impersonateEmail = config('services.google.impersonate_email');
            if ($impersonateEmail) {
                $payloadData['sub'] = $impersonateEmail;
            }

            $payload = $base64UrlEncode(json_encode($payloadData));
            $signatureInput = $header . '.' . $payload;
            $signature = '';

            if (!openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
                logger('Falha ao assinar JWT da conta de serviço Google.');
                return null;
            }

            $jwt = $signatureInput . '.' . $base64UrlEncode($signature);

            // Troca o assertion JWT pelo Token de Acesso
            $response = \Illuminate\Support\Facades\Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]);

            if (!$response->successful()) {
                logger('[GoogleMeet] Falha ao obter token — HTTP ' . $response->status() . ': ' . $response->body());
                return null;
            }
            logger('[GoogleMeet] Token de acesso obtido com sucesso.');

            $tokenData = $response->json();
            $accessToken = $tokenData['access_token'] ?? null;

            if (!$accessToken) {
                logger('Token de acesso não encontrado na resposta do Google.');
                return null;
            }

            // Cria o espaço do Meet
            $meetResponse = \Illuminate\Support\Facades\Http::withToken($accessToken)
                ->post('https://meet.googleapis.com/v2/spaces', [
                    'config' => [
                        'accessType' => 'OPEN'
                    ]
                ]);

            if ($meetResponse->successful()) {
                $spaceData = $meetResponse->json();
                logger('[GoogleMeet] Espaço criado com sucesso: ' . ($spaceData['meetingUri'] ?? 'sem URI'));
                return $spaceData;
            }

            logger('[GoogleMeet] Falha ao criar espaço — HTTP ' . $meetResponse->status() . ': ' . $meetResponse->body());
        } catch (\Throwable $e) {
            logger('[GoogleMeet] Exceção: ' . $e->getMessage() . ' em ' . $e->getFile() . ':' . $e->getLine());
        }

        return null;
    }

    /**
     * Inicia videochamada sem OAuth (para teste simples - Jitsi Meet).
     */
    public function initiateCall(Request $request)
    {
        $request->validate(["solicitation_id" => "required|exists:solicitations,id"]);
        
        $solicitationId = $request->input("solicitation_id");
        $solicitation = Solicitation::find($solicitationId);

        if ($solicitation->status === "na_fila") {
            return response()->json(["error" => "Não é possível iniciar chamada de vídeo antes do atendimento ser iniciado pelo atendente."], 422);
        }

        // Verifica se já existe uma chamada de vídeo ativa para este chamado
        $activeCallExists = Message::where('solicitation_id', $solicitationId)
            ->where('type', 'videocall')
            ->where('metadata->status', 'active')
            ->exists();

        if ($activeCallExists) {
            return response()->json(["error" => "Não é possível iniciar uma nova chamada enquanto a chamada atual estiver ativa."], 422);
        }

        $roomId = "prisma-" . $solicitationId . "-" . substr(md5(time() . auth()->id()), 0, 8);
        $jitsiDomain = config('services.jitsi.domain', 'meet.jit.si');
        $meetUrl = "https://" . $jitsiDomain . "/" . $roomId;
        $fallbackMeetUrl = null;

        $spaceData = $this->createGoogleMeetSpace();
        if ($spaceData) {
            $fallbackMeetUrl = $spaceData['meetingUri'] ?? null;
        }

        $message = Message::create([
            "solicitation_id" => $solicitationId,
            "user_id" => auth()->id(),
            "text" => null,
            "type" => "videocall",
            "metadata" => [
                "room_id" => $roomId,
                "meet_url" => $meetUrl,
                "fallback_meet_url" => $fallbackMeetUrl,
                "initiated_by" => auth()->user()->name,
                "initiated_by_role" => auth()->user()->role,
                "status" => "active",
                "started_at" => now()->toISOString(),
            ],
            "reactions" => [],
        ]);

        $message->load(["user"]);

        return response()->json([
            "success" => true,
            "meet_url" => $meetUrl,
            "join_url" => route('videocall.join', $message),
            "message" => [
                "id" => $message->id,
                "type" => "videocall",
                "sender" => strtoupper($message->user->name),
                "sender_id" => $message->user_id,
                "is_user" => $message->user_id === auth()->id(),
                "time" => $message->created_at->format("d/m - H:i"),
                "metadata" => $message->metadata,
                "reactions" => [],
                "read_at" => null,
            ]
        ]);
    }

    /**
     * Encerra a chamada de vídeo (atualiza status do metadata).
     */
    public function endCall(Request $request, Message $message)
    {
        if ($message->type !== 'videocall') {
            return response()->json(["error" => "Mensagem não é uma chamada de vídeo."], 422);
        }

        $metadata = $message->metadata ?: [];
        $metadata['status'] = 'ended';
        $metadata['ended_at'] = now()->toISOString();
        $metadata['ended_by'] = auth()->user()->name;

        $message->update([
            'metadata' => $metadata
        ]);

        return response()->json([
            "success" => true,
            "message" => [
                "id" => $message->id,
                "type" => "videocall",
                "metadata" => $message->metadata,
            ]
        ]);
    }

    /**
     * API endpoint to retrieve the signed Jitsi URL for a given solicitation.
     * Returns JSON { url: "https://..." }.
     */
    public function generateJitsiUrl($solicitationId)
    {
        $sol = Solicitation::findOrFail($solicitationId);
        // Busca a chamada de vídeo ativa associada à solicitação
        $message = Message::where('solicitation_id', $solicitationId)
            ->where('type', 'videocall')
            ->where('metadata->status', 'active')
            ->latest()
            ->first();

        if (!$message) {
            return response()->json(['error' => 'Chamada não encontrada.'], 404);
        }

        $roomId = $message->metadata['room_id'] ?? null;
        $meetUrl = $message->metadata['meet_url'] ?? null;
        if (!$roomId || !$meetUrl) {
            return response()->json(['url' => $meetUrl]);
        }

        // Gera JWT, se configurado
        $jwt = $this->generateJitsiJwt($roomId);
        if ($jwt) {
            $separator = strpos($meetUrl, '?') !== false ? '&' : '?';
            $meetUrl .= $separator . 'jwt=' . $jwt;
        }

        return response()->json(['url' => $meetUrl]);
    }

    private function generateJitsiJwt($roomName)
    {
        $appId = config('services.jitsi.app_id');
        $appSecret = config('services.jitsi.secret');
        $domain = config('services.jitsi.domain', 'meet.jit.si');

        if (!$appId || !$appSecret) {
            return null;
        }

        $user = auth()->user();
        $isAtendente = $user->role === 'atendente';

        $payload = [
            'iss' => $appId,
            'aud' => 'jitsi',
            'sub' => $domain,
            'room' => $roomName,
            'iat' => time(),
            'exp' => time() + 300, // 5 minutes expiration
            'context' => [
                'user' => [
                    'id' => (string) $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => null,
                    'moderator' => $isAtendente,
                    'affiliation' => $isAtendente ? 'owner' : 'member',
                ],
                'features' => [
                    'recording' => true,
                    'livestreaming' => true,
                    'screen-sharing' => true,
                ]
            ]
        ];

        return $this->encodeJwt($payload, $appSecret);
    }

    private function encodeJwt(array $payload, string $secret): string
    {
        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
        
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
}