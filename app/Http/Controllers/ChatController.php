<?php

namespace App\Http\Controllers;

use App\Models\Solicitation;
use App\Models\Message;
use App\Models\Preset;
use App\Models\Tag;
use App\Notifications\SolicitationNotification;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Display the chat/messages view.
     */
    public function index($id = null)
    {
        if (in_array(auth()->user()->role, ['atendente', 'admin'])) {
            $solicitations = Solicitation::withCount(['messages as unread_messages_count' => function ($query) {
                $query->where('user_id', '!=', auth()->id())->whereNull('read_at');
            }])->orderBy('created_at', 'desc')->get();
        } else {
            $solicitations = Solicitation::where('user_id', auth()->id())
                ->withCount(['messages as unread_messages_count' => function ($query) {
                    $query->where('user_id', '!=', auth()->id())->whereNull('read_at');
                }])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $activeSolicitation = null;
        if ($id) {
            $activeSolicitation = $solicitations->where('id', $id)->first();
        } else {
            $activeSolicitation = $solicitations->first();
        }

        if ($activeSolicitation) {
            // Carrega mensagens e atendente do banco de dados
            $activeSolicitation->load(['messages.user', 'messages.parent.user', 'atendente', 'evaluations', 'tag']);
            
            // Marca mensagens do outro participante como lidas
            $activeSolicitation->messages()
                ->where('user_id', '!=', auth()->id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        // Calcula posição na fila (apenas para cliente)
        $queuePosition = null;
        if ($activeSolicitation && $activeSolicitation->status === 'na_fila' && !in_array(auth()->user()->role, ['atendente', 'admin'])) {
            $queuePosition = Solicitation::where('status', 'na_fila')
                ->where('created_at', '<=', $activeSolicitation->created_at)
                ->count();
        }

        // Carrega Presets e Tags para exibição
        $tags = Tag::orderBy('name', 'asc')->get();
        $presets = collect();
        if (in_array(auth()->user()->role, ['atendente', 'admin'])) {
            $globalPresets = Preset::whereNull('user_id')->orderBy('created_at', 'desc')->get();
            $userOverrides = Preset::where('user_id', auth()->id())->get()->keyBy('parent_id');
            
            $presets = $globalPresets->map(function ($preset) use ($userOverrides) {
                $overridden = $preset->replicate();
                $overridden->id = $preset->id;
                if (isset($userOverrides[$preset->id])) {
                    $overridden->text = $userOverrides[$preset->id]->text;
                    $overridden->is_customized = true;
                    $overridden->customized_id = $userOverrides[$preset->id]->id;
                } else {
                    $overridden->is_customized = false;
                    $overridden->customized_id = null;
                }
                return $overridden;
            });

            $personalPresets = Preset::where('user_id', auth()->id())->whereNull('parent_id')->get();
            $presets = $presets->concat($personalPresets);
        }

        return view('chat.index', compact('solicitations', 'activeSolicitation', 'queuePosition', 'presets', 'tags'));
    }

    /**
     * Store a new message in database.
     */
    public function storeMessage(Request $request, Solicitation $solicitation)
    {
        // Bloqueia envio enquanto o chamado está na fila
        if ($solicitation->status === 'na_fila') {
            return response()->json(['error' => 'Aguardando atendente. O chamado ainda está na fila.'], 422);
        }

        $request->validate([
            'text' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
            'parent_id' => 'nullable|exists:messages,id',
        ]);

        $filePath = null;
        $fileName = null;

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            $filePath = $file->store('messages', 'public');
            $fileName = $file->getClientOriginalName();
        }

        if (!$request->filled('text') && !$filePath) {
            return response()->json(['error' => 'Message cannot be empty.'], 422);
        }

        $message = Message::create([
            'solicitation_id' => $solicitation->id,
            'user_id' => auth()->id(),
            'text' => $request->input('text'),
            'file_path' => $filePath,
            'file_name' => $fileName,
            'parent_id' => $request->input('parent_id'),
            'reactions' => [],
        ]);

        // Se atendente/admin responder chamado em atendimento, atualiza para 'em_replica'
        if (in_array(auth()->user()->role, ['atendente', 'admin']) && in_array($solicitation->status, ['aberta', 'nova', 'em_atendimento'])) {
            $solicitation->update(['status' => 'em_replica']);
        }

        // Dispara notificação de resposta
        try {
            if (auth()->user()->role === 'user' && $solicitation->atendente) {
                // Cliente envia, notifica o atendente atribuído
                $solicitation->atendente->notify(new SolicitationNotification(
                    'Nova mensagem recebida',
                    'Você recebeu uma resposta no chamado #' . $solicitation->ticket_number . ' de ' . auth()->user()->name . '.',
                    $solicitation->id,
                    'resposta'
                ));
            } elseif (in_array(auth()->user()->role, ['atendente', 'admin']) && $solicitation->user) {
                // Atendente/Admin envia, notifica o cliente
                $senderName = auth()->user()->role === 'admin' ? 'Administrador' : 'O atendente ' . auth()->user()->name;
                $solicitation->user->notify(new SolicitationNotification(
                    'Nova mensagem recebida',
                    $senderName . ' respondeu no chamado #' . $solicitation->ticket_number . '.',
                    $solicitation->id,
                    'resposta'
                ));
            }
        } catch (\Exception $e) {
            // Silencia
        }

        $message->load(['user', 'parent.user']);

        return response()->json([
            'success' => true,
            'message' => $this->formatMessageForResponse($message)
        ]);
    }

    /**
     * Fetch new messages and update states for real-time synchronization.
     */
    public function getUpdates(Request $request, Solicitation $solicitation)
    {
        $lastId = $request->query('last_id', 0);

        $queuePosition = null;
        if ($solicitation->status === 'na_fila' && auth()->user()->role !== 'atendente') {
            $queuePosition = Solicitation::where('status', 'na_fila')
                ->where('created_at', '<=', $solicitation->created_at)
                ->count();
        }

        // Busca novas mensagens criadas após o last_id
        $newMessages = $solicitation->messages()
            ->with(['user', 'parent.user'])
            ->where('id', '>', $lastId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Marcar mensagens recebidas como lidas
        if ($newMessages->isNotEmpty()) {
            $solicitation->messages()
                ->where('user_id', '!=', auth()->id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        // Monta o estado atualizado de todas as mensagens
        $allMessages = $solicitation->messages()->get();
        $updatedStates = [];
        foreach ($allMessages as $msg) {
            $formattedReactions = [];
            if ($msg->reactions) {
                foreach ($msg->reactions as $emoji => $users) {
                    $formattedReactions[] = [
                        'emoji' => $emoji,
                        'count' => count($users),
                        'user_reacted' => in_array(auth()->id(), $users)
                    ];
                }
            }

            $updatedStates[$msg->id] = [
                'type' => $msg->type ?? 'text',
                'metadata' => $msg->metadata,
                'reactions' => $formattedReactions,
                'read_at' => $msg->read_at ? $msg->read_at->format('d/m - H:i') : null,
                'text' => $msg->text,
                'time' => $msg->created_at->format('d/m - H:i') . ($msg->updated_at->gt($msg->created_at) ? ' (EDITADA)' : ''),
            ];
        }

        $formattedNew = $newMessages->map(function ($msg) {
            return $this->formatMessageForResponse($msg);
        });

        return response()->json([
            'success' => true,
            'new_messages' => $formattedNew,
            'updated_states' => $updatedStates,
            'solicitation_status' => $solicitation->status,
            'queue_position' => $queuePosition,
            'atendente_name' => $solicitation->atendente ? $solicitation->atendente->name : null,
        ]);
    }

    /**
     * Toggle emoji reaction on message.
     */
    public function toggleReaction(Request $request, Message $message)
    {
        $request->validate([
            'emoji' => 'required|string',
        ]);

        $emoji = $request->input('emoji');
        $reactions = $message->reactions ?: [];
        $userId = auth()->id();

        if (isset($reactions[$emoji])) {
            $users = $reactions[$emoji];
            if (in_array($userId, $users)) {
                $users = array_values(array_diff($users, [$userId]));
                if (empty($users)) {
                    unset($reactions[$emoji]);
                } else {
                    $reactions[$emoji] = $users;
                }
            } else {
                $users[] = $userId;
                $reactions[$emoji] = $users;
            }
        } else {
            $reactions[$emoji] = [$userId];
        }

        $message->update(['reactions' => $reactions]);

        $formatted = [];
        foreach ($reactions as $e => $users) {
            $formatted[] = [
                'emoji' => $e,
                'count' => count($users),
                'user_reacted' => in_array($userId, $users)
            ];
        }

        return response()->json([
            'success' => true,
            'reactions' => $formatted
        ]);
    }

    /**
     * Edit message text.
     */
    public function editMessage(Request $request, Message $message)
    {
        if ($message->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'text' => 'required|string',
        ]);

        $message->update([
            'text' => $request->input('text'),
        ]);

        return response()->json([
            'success' => true,
            'text' => $message->text,
        ]);
    }

    /**
     * Delete message.
     */
    public function deleteMessage(Message $message)
    {
        if ($message->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $message->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Format message for API responses.
     */
    private function formatMessageForResponse($msg)
    {
        $formattedReactions = [];
        if ($msg->reactions) {
            foreach ($msg->reactions as $emoji => $users) {
                $formattedReactions[] = [
                    'emoji' => $emoji,
                    'count' => count($users),
                    'user_reacted' => in_array(auth()->id(), $users)
                ];
            }
        }

        return [
            'id' => $msg->id,
            'type' => $msg->type ?? 'text',
            'text' => $msg->text,
            'metadata' => $msg->metadata,
            'file_url' => $msg->file_path ? asset('storage/' . $msg->file_path) : null,
            'file_name' => $msg->file_name,
            'file_type' => $msg->file_path ? $this->getFileType($msg->file_path) : null,
            'sender' => strtoupper($msg->user->name),
            'sender_id' => $msg->user_id,
            'is_user' => $msg->user_id === auth()->id(),
            'time' => $msg->created_at->format('d/m - H:i') . ($msg->updated_at->gt($msg->created_at) ? ' (EDITADA)' : ''),
            'parent' => $msg->parent ? [
                'id' => $msg->parent->id,
                'text' => $msg->parent->text ? (strlen($msg->parent->text) > 40 ? substr($msg->parent->text, 0, 40) . '...' : $msg->parent->text) : 'Arquivo',
                'sender' => $msg->parent->user ? strtoupper($msg->parent->user->name) : 'SISTEMA',
            ] : null,
            'reactions' => $formattedReactions,
            'read_at' => $msg->read_at ? $msg->read_at->format('d/m - H:i') : null,
        ];
    }

    /**
     * Get attachment type based on path extension.
     */
    private function getFileType($path)
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return 'image';
        }
        return 'document';
    }
}
