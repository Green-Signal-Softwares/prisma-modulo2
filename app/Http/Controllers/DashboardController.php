<?php

namespace App\Http\Controllers;

use App\Models\Solicitation;
use App\Models\SolicitationChecklist;
use App\Models\SolicitationEvaluation;
use App\Models\User;
use App\Models\Preset;
use App\Models\Tag;
use App\Models\AccessProfile;
use App\Models\ActivityLog;
use App\Models\TriageFlowConfig;
use App\Models\Message;
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
            $query = Solicitation::query();
            $this->applyQueueTypeFilterForAttendant($query);
            $solicitations = $query->orderBy('created_at', 'desc')->get();
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

        $this->applyQueueTypeFilterForAttendant($query);

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

    /**
     * Show a single support ticket.
     */
    public function showTicket(Solicitation $solicitation)
    {
        $solicitation->load(['user', 'atendente', 'checklists.atendente', 'messages.user']);
        return view('tickets.show', compact('solicitation'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'nullable|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'triage_path' => 'nullable|string|max:1000',
            'attendant_type' => 'nullable|string|max:50',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip,txt|max:10240',
        ]);

        $description = $request->description;
        if ($request->filled('category')) {
            $description = "[" . $request->category . "] - " . $description;
        }
        if ($request->filled('attendant_type')) {
            $description .= "\n\nTipo de Atendimento: " . $request->attendant_type;
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

        // Cria mensagem automática de abertura de chamado para o histórico
        Message::create([
            'solicitation_id' => $solicitation->id,
            'user_id' => auth()->id(),
            'text' => "Chamado ID {$ticketNumber} aberto para atendimento.",
            'type' => 'internal'
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
     * Returns triage flow tree for client opening wizard.
     */
    public function getSupportTriageFlow()
    {
        try {
            $config = TriageFlowConfig::first();
            $flows = $config && is_array($config->data) ? $config->data : $this->getDefaultTriageFlow();

            return response()->json([
                'success' => true,
                'flows' => $flows,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => true,
                'flows' => $this->getDefaultTriageFlow(),
            ]);
        }
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

        // Cria mensagem automática de início de atendimento para o histórico
        Message::create([
            'solicitation_id' => $solicitation->id,
            'user_id' => auth()->id(),
            'text' => "Chamado ID {$solicitation->ticket_number} atribuído a você para atendimento.",
            'type' => 'internal'
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

        $updateData = [
            'status' => $newStatus,
        ];

        if ($validated['solucao_aplicada'] === 'encaminhado') {
            $enc = trim((string) ($validated['encaminhamento'] ?? ''));
            if (str_starts_with($enc, 'Pessoa: ')) {
                $personName = substr($enc, strlen('Pessoa: '));
                $targetUser = User::where('role', 'atendente')
                    ->where('name', $personName)
                    ->first();
                if ($targetUser) {
                    $updateData['atendente_id'] = $targetUser->id;
                    
                    // Cria log do sistema de transferência no chat
                    Message::create([
                        'solicitation_id' => $solicitation->id,
                        'user_id' => auth()->id(),
                        'text' => "Chamado ID {$solicitation->ticket_number} transferido e atribuído a {$targetUser->name} para atendimento.",
                        'type' => 'internal'
                    ]);
                }
            } else {
                $updateData['atendente_id'] = null;
                
                // Cria log do sistema de transferência no chat
                Message::create([
                    'solicitation_id' => $solicitation->id,
                    'user_id' => auth()->id(),
                    'text' => "Chamado ID {$solicitation->ticket_number} transferido para a fila: {$enc}.",
                    'type' => 'internal'
                ]);
            }

            ActivityLog::writeLog('Transferência', 'CHAMADO', "Transferiu o chamado #{$solicitation->ticket_number} para {$enc}");
        }

        $solicitation->update($updateData);

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
     * Admin Dashboard view.
     */
    public function adminDashboard()
    {
        $solicitations = Solicitation::with(['user', 'atendente', 'checklists'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.dashboard', compact('solicitations'));
    }

    /**
     * Gestão de Atendimento view.
     */
    public function gestaoAtendimento()
    {
        $config = TriageFlowConfig::first();

        if (!$config) {
            $config = TriageFlowConfig::create([
                'key' => 'default',
                'data' => $this->getDefaultTriageFlow(),
            ]);
        }

        return view('admin.gestao_atendimento', [
            'triageFlows' => $config->data,
        ]);
    }

    /**
     * Persist triage flow tree from admin UI.
     */
    public function saveTriageFlow(Request $request)
    {
        $data = $request->validate([
            'flows' => 'required|array',
        ]);

        TriageFlowConfig::updateOrCreate(
            ['key' => 'default'],
            ['data' => $data['flows']]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Default triage flow used on first load.
     */
    private function getDefaultTriageFlow(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Promoções',
                'type' => 'setor',
                'n1' => true,
                'n2' => true,
                'children' => [
                    [
                        'id' => 2,
                        'name' => 'Promoções 1',
                        'type' => 'fila',
                        'n1' => true,
                        'n2' => true,
                        'children' => [],
                    ],
                    [
                        'id' => 3,
                        'name' => 'Promoções 2',
                        'type' => 'fila',
                        'n1' => true,
                        'n2' => false,
                        'children' => [],
                    ],
                ],
            ],
            [
                'id' => 9,
                'name' => 'Técnico',
                'type' => 'setor',
                'n1' => true,
                'n2' => true,
                'children' => [],
            ],
            [
                'id' => 10,
                'name' => 'Vendas',
                'type' => 'setor',
                'n1' => true,
                'n2' => true,
                'children' => [],
            ],
        ];
    }

    /**
     * Restrict queue visibility for attendants according to access profile levels.
     */
    private function applyQueueTypeFilterForAttendant($query): void
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'atendente') {
            return;
        }

        $profile = $user->accessProfile;
        if (!$profile) {
            return;
        }

        $canN1 = (bool) $profile->nivel_n1;
        $canN2 = (bool) $profile->nivel_n2;

        if ($canN1 && $canN2) {
            return;
        }

        $query->where(function ($outer) use ($canN1, $canN2) {
            // Itens fora da fila continuam visíveis normalmente.
            $outer->where('status', '!=', 'na_fila')
                ->orWhere(function ($queueScoped) use ($canN1, $canN2) {
                    $queueScoped->where('status', 'na_fila')
                        ->where(function ($typeMatch) use ($canN1, $canN2) {
                            // Backward compatibility for chamados sem metadado de tipo.
                            $typeMatch->where('description', 'not like', '%Tipo de Atendimento:%')
                                ->orWhere('description', 'like', '%Tipo de Atendimento: N1/N2%');

                            if ($canN1) {
                                $typeMatch->orWhere('description', 'like', '%Tipo de Atendimento: N1%');
                            }

                            if ($canN2) {
                                $typeMatch->orWhere('description', 'like', '%Tipo de Atendimento: N2%');
                            }
                        });
                });
        });
    }

    public function gestaoUsuarios(Request $request)
    {
        $profiles = AccessProfile::all();

        $query = User::where('role', '!=', 'admin');

        // Apply filters
        if ($request->filled('nome')) {
            $query->where('name', 'like', '%' . $request->input('nome') . '%');
        }
        if ($request->filled('telefone')) {
            $query->where('phone', 'like', '%' . $request->input('telefone') . '%');
        }
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->input('email') . '%');
        }
        if ($request->filled('login')) {
            $query->where('login', 'like', '%' . $request->input('login') . '%');
        }
        if ($request->filled('perfil_id')) {
            $query->where('access_profile_id', $request->input('perfil_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('log_de')) {
            $query->whereDate('updated_at', '>=', $request->input('log_de'));
        }
        if ($request->filled('log_ate')) {
            $query->whereDate('updated_at', '<=', $request->input('log_ate'));
        }

        $users = $query->with('accessProfile')->orderBy('name', 'asc')->get();

        return view('users.index', compact('profiles', 'users'));
    }

    // Access Profiles CRUD
    public function storeAccessProfile(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'nivel_n1' => 'boolean',
            'nivel_n2' => 'boolean',
            'fila' => 'boolean',
        ]);

        $profile = AccessProfile::create([
            'name' => $data['name'],
            'nivel_n1' => $request->boolean('nivel_n1'),
            'nivel_n2' => $request->boolean('nivel_n2'),
            'fila' => $request->boolean('fila'),
        ]);

        ActivityLog::writeLog('Criação', 'PERFIL', "Criou o perfil de acesso: {$profile->name}");

        return response()->json([
            'success' => true,
            'message' => 'Perfil de acesso criado com sucesso!',
            'profile' => $profile
        ]);
    }

    public function updateAccessProfile(Request $request, AccessProfile $profile)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'nivel_n1' => 'boolean',
            'nivel_n2' => 'boolean',
            'fila' => 'boolean',
        ]);

        $profile->update([
            'name' => $data['name'],
            'nivel_n1' => $request->boolean('nivel_n1'),
            'nivel_n2' => $request->boolean('nivel_n2'),
            'fila' => $request->boolean('fila'),
        ]);

        ActivityLog::writeLog('Atualização', 'PERFIL', "Atualizou o perfil de acesso: {$profile->name}");

        return response()->json([
            'success' => true,
            'message' => 'Perfil de acesso atualizado com sucesso!',
            'profile' => $profile
        ]);
    }

    public function toggleAccessProfile(Request $request, AccessProfile $profile)
    {
        $data = $request->validate([
            'field' => 'required|string|in:nivel_n1,nivel_n2,fila',
            'value' => 'required|boolean'
        ]);

        $profile->update([
            $data['field'] => $data['value']
        ]);

        $statusStr = $data['value'] ? 'Ativou' : 'Desativou';
        ActivityLog::writeLog('Atualização', 'PERFIL', "{$statusStr} a permissão {$data['field']} no perfil: {$profile->name}");

        return response()->json([
            'success' => true,
            'message' => 'Permissão alterada com sucesso!',
            'profile' => $profile
        ]);
    }

    public function destroyAccessProfile(AccessProfile $profile)
    {
        ActivityLog::writeLog('Exclusão', 'PERFIL', "Excluiu o perfil de acesso: {$profile->name}");

        $profile->delete();

        return response()->json([
            'success' => true,
            'message' => 'Perfil de acesso removido com sucesso!'
        ]);
    }

    // Users CRUD
    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:255',
            'login' => 'nullable|string|max:255',
            'access_profile_id' => 'nullable|exists:access_profiles,id',
            'status' => 'required|string|in:ativo,ausente,inativo,bloqueado',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'login' => $data['login'] ?? null,
            'access_profile_id' => $data['access_profile_id'] ?? null,
            'status' => $data['status'],
            'role' => 'atendente', // Default to atendente for gestao
            'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
        ]);

        ActivityLog::writeLog('Criação', 'USUÁRIO', "Criou o usuário: {$user->name} ({$user->email})");

        return response()->json([
            'success' => true,
            'message' => 'Usuário criado com sucesso!',
            'user' => $user->load('accessProfile')
        ]);
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:255',
            'login' => 'nullable|string|max:255',
            'access_profile_id' => 'nullable|exists:access_profiles,id',
            'status' => 'required|string|in:ativo,ausente,inativo,bloqueado',
            'password' => 'nullable|string|min:6',
        ]);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'login' => $data['login'] ?? null,
            'access_profile_id' => $data['access_profile_id'] ?? null,
            'status' => $data['status'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
        }

        $user->update($updateData);

        ActivityLog::writeLog('Atualização', 'USUÁRIO', "Atualizou o usuário: {$user->name} ({$user->email})");

        return response()->json([
            'success' => true,
            'message' => 'Usuário atualizado com sucesso!',
            'user' => $user->load('accessProfile')
        ]);
    }

    public function destroyUser(User $user)
    {
        ActivityLog::writeLog('Exclusão', 'USUÁRIO', "Excluiu o usuário: {$user->name} ({$user->email})");

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuário removido com sucesso!'
        ]);
    }

    public function presetsGlobais()
    {
        $presets = Preset::whereNull('user_id')->orderBy('created_at', 'desc')->get();
        $tags = Tag::orderBy('name', 'asc')->get();
        return view('admin.presets_globais', compact('presets', 'tags'));
    }

    // Admin Presets
    public function storePreset(Request $request)
    {
        $data = $request->validate([
            'shortcut' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'text' => 'required|string',
        ]);

        $preset = Preset::create($data);

        ActivityLog::writeLog('Criação', 'FAVORITO', "Criou o preset global: {$preset->shortcut}");

        return redirect()->back()->with('success', 'Preset criado com sucesso!');
    }

    public function updatePreset(Request $request, Preset $preset)
    {
        $data = $request->validate([
            'shortcut' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'text' => 'required|string',
        ]);

        $preset->update($data);

        ActivityLog::writeLog('Atualização', 'FAVORITO', "Atualizou o preset global: {$preset->shortcut}");

        return redirect()->back()->with('success', 'Preset atualizado com sucesso!');
    }

    public function destroyPreset(Preset $preset)
    {
        ActivityLog::writeLog('Exclusão', 'FAVORITO', "Excluiu o preset global: {$preset->shortcut}");

        $preset->delete();
        return redirect()->back()->with('success', 'Preset removido com sucesso!');
    }

    // Admin Tags
    public function storeTag(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:50',
        ]);

        $tag = Tag::create($data);

        ActivityLog::writeLog('Criação', 'INDICADORES', "Criou a tag global: {$tag->name}");

        return redirect()->back()->with('success', 'Tag criada com sucesso!');
    }

    public function updateTag(Request $request, Tag $tag)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:50',
        ]);

        $tag->update($data);

        ActivityLog::writeLog('Atualização', 'INDICADORES', "Atualizou a tag global: {$tag->name}");

        return redirect()->back()->with('success', 'Tag atualizada com sucesso!');
    }

    public function destroyTag(Tag $tag)
    {
        ActivityLog::writeLog('Exclusão', 'INDICADORES', "Excluiu a tag global: {$tag->name}");

        $tag->delete();
        return redirect()->back()->with('success', 'Tag removida com sucesso!');
    }

    // Chat API Presets & Tags
    public function storeChatPreset(Request $request)
    {
        $data = $request->validate([
            'shortcut' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'text' => 'required|string',
            'parent_id' => 'nullable|integer',
        ]);

        $preset = Preset::create([
            'shortcut' => $data['shortcut'],
            'title' => $data['title'] ?? 'Custom Preset',
            'text' => $data['text'],
            'user_id' => auth()->id(),
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'preset' => $preset,
        ]);
    }

    public function updateChatPreset(Request $request, $id)
    {
        $data = $request->validate([
            'shortcut' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'text' => 'required|string',
        ]);

        $preset = Preset::find($id);

        if ($preset && $preset->user_id === auth()->id()) {
            // Se for do próprio usuário, atualiza
            $preset->update($data);
        } else {
            // Se for global, cria ou atualiza um override individual
            $override = Preset::where('user_id', auth()->id())
                ->where('parent_id', $id)
                ->first();

            if ($override) {
                $override->update([
                    'shortcut' => $data['shortcut'],
                    'text' => $data['text'],
                ]);
                $preset = $override;
            } else {
                $preset = Preset::create([
                    'shortcut' => $data['shortcut'],
                    'title' => $data['title'] ?? ($preset ? $preset->title : 'Custom Preset'),
                    'text' => $data['text'],
                    'user_id' => auth()->id(),
                    'parent_id' => $id,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'preset' => $preset,
        ]);
    }

    public function destroyChatPreset($id)
    {
        $preset = Preset::find($id);

        if ($preset && $preset->user_id === auth()->id()) {
            $preset->delete();
        } else {
            // Se for global e quiserem desfazer/deletar override
            $override = Preset::where('user_id', auth()->id())
                ->where('parent_id', $id)
                ->first();
            if ($override) {
                $override->delete();
            }
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function updateSolicitationTag(Request $request, Solicitation $solicitation)
    {
        $data = $request->validate([
            'tag_id' => 'nullable|exists:tags,id',
        ]);

        $solicitation->update([
            'tag_id' => $data['tag_id'],
        ]);

        return response()->json([
            'success' => true,
            'solicitation' => $solicitation,
        ]);
    }
    /**
     * Log de Atividades view.
     */
    public function logAtividades(Request $request)
    {
        $query = ActivityLog::query();

        // Apply filters
        if ($request->filled('data_de')) {
            $query->whereDate('created_at', '>=', $request->data_de);
        }
        if ($request->filled('data_ate')) {
            $query->whereDate('created_at', '<=', $request->data_ate);
        }
        if ($request->filled('atividade')) {
            $query->where('activity', $request->atividade);
        }
        if ($request->filled('tipo')) {
            $query->where('type', $request->tipo);
        }
        if ($request->filled('nome')) {
            $query->where('user_name', 'like', '%' . $request->nome . '%');
        }
        if ($request->filled('pdv')) {
            $query->where('pdv', 'like', '%' . $request->pdv . '%');
        }

        // CSV export
        if ($request->has('download_csv')) {
            $headers = [
                "Content-type" => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename=log_atividades_" . date('Ymd_His') . ".csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];

            $callback = function () use ($query) {
                $file = fopen('php://output', 'w');
                // UTF-8 BOM for Excel compatibility
                fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                fputcsv($file, ['Data/Hora', 'Atividade', 'Tipo', 'Nome', 'PDV', 'Detalhes']);

                $query->orderBy('created_at', 'desc')->chunk(100, function ($logs) use ($file) {
                    foreach ($logs as $log) {
                        fputcsv($file, [
                            $log->created_at->format('d/m/Y H:i'),
                            $log->activity,
                            $log->type,
                            $log->user_name,
                            $log->pdv,
                            $log->details
                        ]);
                    }
                });
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Distinct activities and types for filters dropdowns
        $activities = ActivityLog::select('activity')->distinct()->orderBy('activity')->pluck('activity');
        $types = ActivityLog::select('type')->distinct()->orderBy('type')->pluck('type');

        $perPage = (int) $request->input('per_page', 20);
        $logs = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        return view('admin.log_atividades', compact('logs', 'activities', 'types'));
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

    /**
     * Show User Central profile view.
     */
    public function centralUsuario()
    {
        $user = auth()->user();
        return view('profile.central', compact('user'));
    }

    /**
     * Update user profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'login' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $request->input('name');
        if ($request->has('phone')) {
            $user->phone = $request->input('phone');
        }
        if ($request->has('login')) {
            $user->login = $request->input('login');
        }
        if ($request->has('email')) {
            $user->email = $request->input('email');
        }

        // Handle password update if fields are filled
        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'A senha atual está incorreta.']);
            }
            $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        }

        $user->save();

        return redirect()->route('profile.central')->with('success', 'Informações atualizadas com sucesso!');
    }

    public function systemNotifications()
    {
        $notifications = \App\Models\SystemNotification::orderBy('created_at', 'desc')->get();
        return view('admin.notifications.index', compact('notifications'));
    }

    public function storeSystemNotification(Request $request)
    {
        $validated = $request->validate([
            'send_to' => 'required|string',
            'type' => 'required|string|in:push,system,email',
            'status' => 'nullable|string|in:active,inactive',
            'start_date' => 'required|date',
            'start_time' => 'required|string',
            'end_date' => 'required|date',
            'end_time' => 'required|string',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $startAt = \Carbon\Carbon::parse($validated['start_date'] . ' ' . $validated['start_time'], 'America/Sao_Paulo')->setTimezone('UTC');
        $endAt = \Carbon\Carbon::parse($validated['end_date'] . ' ' . $validated['end_time'], 'America/Sao_Paulo')->setTimezone('UTC');

        \App\Models\SystemNotification::create([
            'send_to' => $validated['send_to'],
            'type' => $validated['type'],
            'status' => $validated['status'] ?? 'active',
            'start_at' => $startAt,
            'end_at' => $endAt,
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        return redirect()->route('admin.notifications.index')->with('success', 'Notificação enviada/programada com sucesso!');
    }

    public function updateSystemNotification(Request $request, \App\Models\SystemNotification $notification)
    {
        $validated = $request->validate([
            'send_to' => 'required|string',
            'type' => 'required|string|in:push,system,email',
            'status' => 'nullable|string|in:active,inactive',
            'start_date' => 'required|date',
            'start_time' => 'required|string',
            'end_date' => 'required|date',
            'end_time' => 'required|string',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $startAt = \Carbon\Carbon::parse($validated['start_date'] . ' ' . $validated['start_time'], 'America/Sao_Paulo')->setTimezone('UTC');
        $endAt = \Carbon\Carbon::parse($validated['end_date'] . ' ' . $validated['end_time'], 'America/Sao_Paulo')->setTimezone('UTC');

        $notification->update([
            'send_to' => $validated['send_to'],
            'type' => $validated['type'],
            'status' => $validated['status'] ?? 'active',
            'start_at' => $startAt,
            'end_at' => $endAt,
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        return redirect()->route('admin.notifications.index')->with('success', 'Notificação atualizada com sucesso!');
    }

    public function destroySystemNotification(\App\Models\SystemNotification $notification)
    {
        $notification->delete();
        return redirect()->route('admin.notifications.index')->with('success', 'Notificação excluída com sucesso!');
    }
}
