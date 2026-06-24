<?php

namespace App\Http\Controllers;

use App\Models\Solicitation;
use App\Models\SolicitationChecklist;
use App\Models\SolicitationEvaluation;
use App\Models\User;
use App\Notifications\SolicitationNotification;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        if (auth()->user()->role === 'atendente') {
            // Busca todas as solicitações do sistema para o atendente
            $solicitations = Solicitation::orderBy('created_at', 'desc')->get();
        } else {
            // Busca apenas as solicitações reais do usuário do banco de dados
            $solicitations = Solicitation::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('dashboard.index', compact('solicitations'));
    }

    /**
     * Show the complete history page.
     */
    public function historico()
    {
        $solicitations = Solicitation::with(['atendente', 'checklists'])->orderBy('created_at', 'desc')->get();
        return view('dashboard.historico', compact('solicitations'));
    }

    /**
     * Show the tickets list page.
     */
    public function tickets(Request $request)
    {
        $query = Solicitation::query();

        // Filtro de ID (De/Até ou parcial)
        if ($request->filled('id_de')) {
            $query->where('ticket_number', '>=', $request->id_de);
        }
        if ($request->filled('id_ate')) {
            $query->where('ticket_number', '<=', $request->id_ate);
        }

        // Filtro de Setor (categoria)
        if ($request->filled('setor')) {
            $query->where('description', 'like', '%' . $request->setor . '%');
        }

        // Filtro de Fila
        if ($request->filled('fila')) {
            $filaVal = $request->fila;
            $query->where(function ($q) use ($filaVal) {
                $q->whereHas('checklists', function ($sq) use ($filaVal) {
                    $sq->where('encaminhamento', 'like', '%' . $filaVal . '%');
                });
                if (stripos('Fila de Entrada', $filaVal) !== false || stripos('entrada', $filaVal) !== false) {
                    $q->orWhere('status', 'na_fila');
                }
                if (stripos('Fila Geral', $filaVal) !== false || stripos('geral', $filaVal) !== false) {
                    $q->orWhere('status', '!=', 'na_fila');
                }
            });
        }

        // Filtro de Responsável
        if ($request->filled('responsavel')) {
            $query->whereHas('atendente', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->responsavel . '%');
            });
        }

        // Filtro de Assunto
        if ($request->filled('assunto')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->assunto . '%')
                  ->orWhere('description', 'like', '%' . $request->assunto . '%');
            });
        }

        // Filtro de Status
        if ($request->filled('status')) {
            $statusInput = $request->status;
            // Mapeia termos comuns para os status reais do banco de dados
            $statusMap = [
                'nova' => 'na_fila',
                'nova demanda' => 'na_fila',
                'fila' => 'na_fila',
                'atendimento' => 'em_atendimento',
                'replica' => 'em_replica',
                'réplica' => 'em_replica',
                'resolvida' => 'resolvida',
                'respondida' => 'respondida',
                'nao resolvida' => 'nao_resolvida',
                'não resolvida' => 'nao_resolvida',
            ];
            
            $normalizedInput = strtolower(trim($statusInput));
            if (isset($statusMap[$normalizedInput])) {
                $query->where('status', $statusMap[$normalizedInput]);
            } else {
                $query->where('status', 'like', '%' . $statusInput . '%');
            }
        }

        // Filtro de Última Atualização
        if ($request->filled('atualizacao_de')) {
            $query->whereDate('updated_at', '>=', $request->atualizacao_de);
        }
        if ($request->filled('atualizacao_ate')) {
            $query->whereDate('updated_at', '<=', $request->atualizacao_ate);
        }

        $perPage = $request->input('per_page', 20);
        $solicitations = $query->with(['atendente', 'checklists'])->orderBy('updated_at', 'desc')->paginate($perPage)->withQueryString();

        return view('tickets.index', compact('solicitations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'nullable|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip,txt|max:10240',
        ]);

        $description = $request->description;
        if ($request->filled('category')) {
            $description = "[" . $request->category . "] - " . $description;
        }

        $filePaths = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    $filePaths[] = $file->store('solicitations', 'public');
                }
            }
        }

        $ticketNumber = str_pad(random_int(100000000, 999999999), 9, '0', STR_PAD_LEFT);

        $solicitation = Solicitation::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $description,
            'status' => 'na_fila',
            'ticket_number' => $ticketNumber,
            'file_path' => $filePaths,
        ]);

        // Notifica todos os atendentes sobre o novo chamado na fila
        try {
            $atendentes = User::where('role', 'atendente')->get();
            $title = 'Nova demanda na fila';
            $messageText = 'O chamado #' . $ticketNumber . ' está aguardando atendimento na fila.';
            foreach ($atendentes as $atendente) {
                $atendente->notify(new SolicitationNotification($title, $messageText, $solicitation->id, 'novo_chamado'));
            }
        } catch (\Exception $e) {
            // Silencia erro para não quebrar a criação caso ocorra algum problema
        }

        return redirect()->route('chat.index', $solicitation->id)->with('success', 'Solicitação de suporte enviada com sucesso! Ticket #' . $ticketNumber);
    }

    /**
     * Start attendance on a solicitation (atendente pulls from queue).
     */
    public function iniciarAtendimento(Solicitation $solicitation)
    {
        if ($solicitation->status !== 'na_fila') {
            return response()->json(['success' => false, 'message' => 'Chamado não está na fila.'], 422);
        }

        $solicitation->update([
            'status' => 'em_atendimento',
            'atendente_id' => auth()->id(),
        ]);

        // Notifica o cliente que o chamado foi assumido
        try {
            $cliente = $solicitation->user;
            if ($cliente) {
                $title = 'Chamado em atendimento';
                $messageText = 'Seu chamado #' . $solicitation->ticket_number . ' foi assumido pelo atendente ' . auth()->user()->name . '.';
                $cliente->notify(new SolicitationNotification($title, $messageText, $solicitation->id, 'status'));
            }
        } catch (\Exception $e) {
            // Silencia
        }

        return response()->json(['success' => true]);
    }

    /**
     * Finaliza o atendimento com checklist de solução.
     */
    public function finalizarAtendimento(Request $request, Solicitation $solicitation)
    {
        if ($solicitation->atendente_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Você não está atribuído a este chamado.'], 403);
        }

        $validated = $request->validate([
            'problema_identificado' => 'required|in:sim,nao,parcialmente',
            'solucao_aplicada' => 'required|in:sim,encaminhado,nao_resolvida',
            'descricao' => 'required|string|max:2000',
            'encaminhamento' => 'nullable|string|max:255',
        ]);

        if ($validated['solucao_aplicada'] === 'encaminhado' && empty(trim((string) ($validated['encaminhamento'] ?? '')))) {
            return response()->json(['success' => false, 'message' => 'Informe o setor/fila para encaminhamento.'], 422);
        }

        $newStatus = match ($validated['solucao_aplicada']) {
            'sim' => 'resolvida',
            'encaminhado' => 'em_replica',
            default => 'nao_resolvida',
        };

        $checklist = SolicitationChecklist::create([
            'solicitation_id' => $solicitation->id,
            'atendente_id' => auth()->id(),
            'category' => $this->extractCategoryFromDescription($solicitation->description),
            'problema_identificado' => $validated['problema_identificado'],
            'solucao_aplicada' => $validated['solucao_aplicada'],
            'encaminhamento' => !empty($validated['encaminhamento']) ? trim($validated['encaminhamento']) : null,
            'descricao' => trim($validated['descricao']),
        ]);

        $solicitation->update([
            'status' => $newStatus,
        ]);

        // Notifica o cliente que o status do chamado foi atualizado
        try {
            $cliente = $solicitation->user;
            if ($cliente) {
                $statusText = match ($newStatus) {
                    'resolvida' => 'resolvido',
                    'em_replica' => 'encaminhado',
                    default => 'não resolvido',
                };
                $title = 'Chamado finalizado';
                $messageText = 'Seu chamado #' . $solicitation->ticket_number . ' foi marcado como ' . $statusText . '.';
                $cliente->notify(new SolicitationNotification($title, $messageText, $solicitation->id, 'status'));
            }
        } catch (\Exception $e) {
            // Silencia
        }

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'checklist_id' => $checklist->id,
        ]);
    }

    /**
     * Cliente avalia o atendimento após encerramento.
     */
    public function avaliarAtendimento(Request $request, Solicitation $solicitation)
    {
        if ($solicitation->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Você não possui acesso a este chamado.'], 403);
        }

        if (!in_array($solicitation->status, ['resolvida', 'nao_resolvida', 'não resolvida'], true)) {
            return response()->json(['success' => false, 'message' => 'A avaliação só pode ser enviada após o encerramento do atendimento.'], 422);
        }

        $validated = $request->validate([
            'nota' => 'required|integer|min:1|max:5',
            'problema_resolvido' => 'required|boolean',
            'comentario' => 'nullable|string|max:2000',
        ]);

        $evaluation = SolicitationEvaluation::updateOrCreate(
            [
                'solicitation_id' => $solicitation->id,
                'user_id' => auth()->id(),
            ],
            [
                'nota' => $validated['nota'],
                'problema_resolvido' => (bool) $validated['problema_resolvido'],
                'comentario' => !empty($validated['comentario']) ? trim($validated['comentario']) : null,
            ]
        );

        return response()->json([
            'success' => true,
            'evaluation_id' => $evaluation->id,
        ]);
    }

    /**
     * Get unread notifications for logged user.
     */
    public function getNotifications()
    {
        $notifications = auth()->user()->unreadNotifications->map(function ($notif) {
            return [
                'id' => $notif->id,
                'title' => $notif->data['title'] ?? 'Notificação',
                'message' => $notif->data['message'] ?? '',
                'solicitation_id' => $notif->data['solicitation_id'] ?? null,
                'type' => $notif->data['type'] ?? 'info',
                'time' => $notif->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'count' => $notifications->count()
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function readAllNotifications()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function readNotification($id)
    {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['success' => true]);
    }

    /**
     * Extrai a categoria quando a descrição está no formato: [Categoria] - texto.
     */
    private function extractCategoryFromDescription(string $description): ?string
    {
        if (preg_match('/^\[(.*?)\]\s*-\s*/', $description, $matches) === 1) {
            $category = trim($matches[1]);
            return $category !== '' ? $category : null;
        }

        return null;
    }
}
