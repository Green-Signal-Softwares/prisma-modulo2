<?php

namespace App\Http\Controllers;

use App\Models\Solicitation;
use App\Models\Message;
use App\Models\InternalNote;
use App\Models\Preset;
use App\Models\Tag;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\TriageFlowConfig;
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
            $user = auth()->user();
            $query = Solicitation::withCount(['messages as unread_messages_count' => function ($query) {
                $query->where('user_id', '!=', auth()->id())->whereNull('read_at');
            }]);

            if ($user->role === 'atendente') {
                $query->where(function ($q) use ($user) {
                    // 1. Chamados na Fila (aguardando atendimento)
                    $q->where(function ($sub) use ($user) {
                        $sub->where('status', 'na_fila');
                        
                        // Só pode visualizar se não estiver com atendente ou se for o atendente atribuído
                        $sub->where(function ($sq) use ($user) {
                            $sq->whereNull('atendente_id')
                               ->orWhere('atendente_id', $user->id);
                        });

                        // Restringe por N1/N2
                        $this->applyQueueTypeFilterForAttendant($sub);
                    })
                    // 2. Chamados em atendimento atribuídos ao atendente logado
                    ->orWhere(function ($sub) use ($user) {
                        $sub->where('atendente_id', $user->id)
                            ->whereNotIn('status', ['resolvida', 'finalizada', 'cancelada']);
                    });
                });
            }

            $solicitations = $query->orderBy('created_at', 'desc')->get();
        } else {
            $solicitations = Solicitation::where('user_id', auth()->id())
                ->withCount(['messages as unread_messages_count' => function ($query) {
                    $query->where('user_id', '!=', auth()->id())
                        ->whereNotIn('type', ['internal', 'whisper'])
                        ->whereNull('read_at');
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
            // Carrega mensagens e atendente do banco de dados com filtro de mensagens internas e sussurros para clientes
            $activeSolicitation->load(['messages' => function ($query) {
                if (!in_array(auth()->user()->role, ['atendente', 'admin'], true)) {
                    $query->where('type', '!=', 'whisper');
                }
            }, 'messages.user', 'messages.parent.user', 'atendente', 'evaluations', 'tag', 'internalNotes']);
            
            // Marca mensagens do outro participante como lidas
            $readQuery = $activeSolicitation->messages()
                ->where('user_id', '!=', auth()->id())
                ->whereNull('read_at');
            if (!in_array(auth()->user()->role, ['atendente', 'admin'], true)) {
                $readQuery->whereNotIn('type', ['internal', 'whisper']);
            }
            $readQuery->update(['read_at' => now()]);
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
        // Bloqueia envio de clientes enquanto o chamado está na fila
        if ($solicitation->status === 'na_fila' && !in_array(auth()->user()->role, ['atendente', 'admin'], true)) {
            return response()->json(['error' => 'Aguardando atendente. O chamado ainda está na fila.'], 422);
        }

        // Sanitiza parent_id se não for numérico (ex: "opening" da mensagem de abertura)
        if ($request->has('parent_id') && (!is_numeric($request->input('parent_id')) || (int)$request->input('parent_id') <= 0)) {
            $request->merge(['parent_id' => null]);
        }

        $request->validate([
            'text' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
            'files.*' => 'nullable|file|max:10240',
            'files' => 'nullable|array|max:5',
            'parent_id' => 'nullable|exists:messages,id',
            'type' => 'nullable|string|in:text,whisper',
        ]);

        $msgType = $request->input('type', 'text');
        if ($msgType === 'whisper') {
            if (!in_array(auth()->user()->role, ['atendente', 'admin'], true)) {
                return response()->json(['error' => 'Apenas a equipe pode enviar comentários internos.'], 403);
            }
        }

        $files = [];
        if ($request->hasFile('files')) {
            $uploaded = $request->file('files');
            if (is_array($uploaded)) {
                $files = array_merge($files, $uploaded);
            }
        }
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $files[] = $request->file('file');
        }

        if (count($files) > 5) {
            return response()->json(['error' => 'Máximo de 5 anexos permitidos por mensagem.'], 422);
        }

        $filePaths = [];
        $fileNames = [];
        foreach ($files as $f) {
            if ($f && $f->isValid()) {
                $filePaths[] = $f->store('messages', 'public');
                $fileNames[] = $f->getClientOriginalName();
            }
        }

        if (!$request->filled('text') && empty($filePaths)) {
            return response()->json(['error' => 'Message cannot be empty.'], 422);
        }

        $isOpening = $request->boolean('is_opening') || $request->input('parent_id') === 'opening';
        $parentId = $request->input('parent_id');
        if (!is_numeric($parentId) || (int)$parentId <= 0) {
            $parentId = null;
        }

        $meta = [];
        if ($isOpening) {
            $meta['is_opening'] = true;
            $meta['opening_title'] = $solicitation->title;
            $meta['opening_sender'] = $solicitation->user ? $solicitation->user->name : 'Cliente';
        }

        $message = Message::create([
            'solicitation_id' => $solicitation->id,
            'user_id' => auth()->id(),
            'text' => $request->input('text'),
            'file_path' => count($filePaths) > 0 ? (count($filePaths) === 1 ? $filePaths[0] : $filePaths) : null,
            'file_name' => count($fileNames) > 0 ? (count($fileNames) === 1 ? $fileNames[0] : $fileNames) : null,
            'parent_id' => $parentId,
            'type' => $msgType,
            'metadata' => !empty($meta) ? $meta : null,
            'reactions' => [],
        ]);

        // Se a mensagem for normal (não sussurro), processa alteração de status e notificação
        if ($msgType !== 'whisper') {
            // Se atendente/admin responder chamado em atendimento ou fila, atualiza para 'em_replica'
            if (in_array(auth()->user()->role, ['atendente', 'admin']) && in_array($solicitation->status, ['na_fila', 'aberta', 'nova', 'em_atendimento'])) {
                $solicitation->update([
                    'status' => 'em_replica',
                    'atendente_id' => $solicitation->atendente_id ?? auth()->id()
                ]);
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
        $newMessagesQuery = $solicitation->messages()
            ->with(['user', 'parent.user'])
            ->where('id', '>', $lastId);

        if (!in_array(auth()->user()->role, ['atendente', 'admin'], true)) {
            $newMessagesQuery->where('type', '!=', 'whisper');
        }

        $newMessages = $newMessagesQuery->orderBy('created_at', 'asc')->get();

        // Marcar mensagens recebidas como lidas
        if ($newMessages->isNotEmpty()) {
            $readQuery = $solicitation->messages()
                ->where('user_id', '!=', auth()->id())
                ->whereNull('read_at');
            if (!in_array(auth()->user()->role, ['atendente', 'admin'], true)) {
                $readQuery->whereNotIn('type', ['internal', 'whisper']);
            }
            $readQuery->update(['read_at' => now()]);
        }

        // Monta o estado atualizado de todas as mensagens
        $allMessagesQuery = $solicitation->messages();
        if (!in_array(auth()->user()->role, ['atendente', 'admin'], true)) {
            $allMessagesQuery->where('type', '!=', 'whisper');
        }
        $allMessages = $allMessagesQuery->get();
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

            $files = $msg->files;
            $firstFile = $files[0] ?? null;

            $updatedStates[$msg->id] = [
                'type' => $msg->type ?? 'text',
                'metadata' => $msg->metadata,
                'reactions' => $formattedReactions,
                'read_at' => $msg->read_at ? $msg->read_at->format('d/m - H:i') : null,
                'text' => $msg->text,
                'time' => $msg->created_at->format('d/m - H:i') . ($msg->updated_at->gt($msg->created_at) ? ' (EDITADA)' : ''),
                'files' => $files,
                'file_url' => $firstFile ? $firstFile['url'] : null,
                'file_name' => $firstFile ? $firstFile['name'] : null,
                'file_type' => $firstFile ? $firstFile['type'] : null,
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
            'text' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
            'files.*' => 'nullable|file|max:10240',
            'files' => 'nullable|array|max:5',
        ]);

        $existingFiles = $message->files;
        if ($request->has('keep_files') || $request->has('keep_files_sent')) {
            $keepPaths = $request->input('keep_files', []);
            if (is_string($keepPaths)) {
                $keepPaths = json_decode($keepPaths, true) ?? [];
            }
            if (!is_array($keepPaths)) {
                $keepPaths = [];
            }
            $filteredExisting = array_filter($existingFiles, function($item) use ($keepPaths) {
                $itemPath = ltrim($item['path'] ?? '', '/');
                foreach ($keepPaths as $kp) {
                    if (ltrim($kp, '/') === $itemPath) {
                        return true;
                    }
                }
                return false;
            });
            $existingPaths = array_values(array_map(fn($item) => $item['path'], $filteredExisting));
            $existingNames = array_values(array_map(fn($item) => $item['name'], $filteredExisting));
        } else {
            $existingPaths = array_map(fn($item) => $item['path'], $existingFiles);
            $existingNames = array_map(fn($item) => $item['name'], $existingFiles);
        }

        $newFiles = [];
        if ($request->hasFile('files')) {
            $uploaded = $request->file('files');
            if (is_array($uploaded)) {
                $newFiles = array_merge($newFiles, $uploaded);
            }
        }
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $newFiles[] = $request->file('file');
        }

        if (count($existingPaths) + count($newFiles) > 5) {
            return response()->json(['error' => 'Máximo de 5 anexos permitidos por mensagem.'], 422);
        }

        $filePaths = $existingPaths;
        $fileNames = $existingNames;
        foreach ($newFiles as $f) {
            if ($f && $f->isValid()) {
                $filePaths[] = $f->store('messages', 'public');
                $fileNames[] = $f->getClientOriginalName();
            }
        }

        $newText = $request->has('text') ? $request->input('text') : $message->text;

        if (!$newText && empty($filePaths)) {
            return response()->json(['error' => 'Message cannot be empty.'], 422);
        }

        $message->update([
            'text' => $newText,
            'file_path' => count($filePaths) > 0 ? (count($filePaths) === 1 ? $filePaths[0] : $filePaths) : null,
            'file_name' => count($fileNames) > 0 ? (count($fileNames) === 1 ? $fileNames[0] : $fileNames) : null,
        ]);

        $message->load(['user', 'parent.user']);

        return response()->json([
            'success' => true,
            'text' => $message->text,
            'message' => $this->formatMessageForResponse($message),
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

        $files = $msg->files;
        $firstFile = $files[0] ?? null;

        return [
            'id' => $msg->id,
            'type' => $msg->type ?? 'text',
            'text' => $msg->text,
            'metadata' => $msg->metadata,
            'files' => $files,
            'file_url' => $firstFile ? $firstFile['url'] : null,
            'file_name' => $firstFile ? $firstFile['name'] : null,
            'file_type' => $firstFile ? $firstFile['type'] : null,
            'sender' => strtoupper($msg->user ? $msg->user->name : 'SISTEMA'),
            'sender_id' => $msg->user_id,
            'is_user' => (function() use ($msg) {
                $currentUserRole = auth()->user()->role;
                $msgSenderRole = $msg->user ? $msg->user->role : 'system';
                if (in_array($currentUserRole, ['atendente', 'admin'], true)) {
                    return in_array($msgSenderRole, ['atendente', 'admin'], true);
                } else {
                    return ($msgSenderRole === 'user');
                }
            })(),
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

    /**
     * Abre um ticket/transferência associado a um chamado ativo.
     */
    public function openTicket(Request $request, Solicitation $solicitation)
    {
        // Apenas atendentes e admins podem abrir ticket
        if (!in_array(auth()->user()->role, ['atendente', 'admin'], true)) {
            return response()->json(['success' => false, 'message' => 'Não autorizado.'], 403);
        }

        $request->validate([
            'destination' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip,txt|max:10240',
        ]);

        $destination = $request->input('destination');
        $title = $request->input('title');
        $description = $request->input('description');

        $targetAttendantId = null;
        $attendantType = null;

        if (str_starts_with($destination, 'Pessoa: ')) {
            $personName = substr($destination, strlen('Pessoa: '));
            $targetUser = User::with('accessProfile')
                ->where('role', 'atendente')
                ->where('name', $personName)
                ->first();
            if ($targetUser) {
                $targetAttendantId = $targetUser->id;
                if ($targetUser->accessProfile) {
                    $n1 = (bool) $targetUser->accessProfile->nivel_n1;
                    $n2 = (bool) $targetUser->accessProfile->nivel_n2;
                    if ($n1 && $n2) {
                        $attendantType = 'N1/N2';
                    } elseif ($n1) {
                        $attendantType = 'N1';
                    } elseif ($n2) {
                        $attendantType = 'N2';
                    }
                }
            }
        } else {
            // Sector or Fila. Let's find the matching node in TriageFlowConfig.
            $parts = explode(': ', $destination);
            $targetName = count($parts) > 1 ? end($parts) : $destination;

            // Search in TriageFlowConfig
            $config = TriageFlowConfig::first();
            if ($config && is_array($config->data)) {
                $foundNode = null;
                $searchNode = function($nodes) use (&$searchNode, &$foundNode, $targetName) {
                    foreach ($nodes as $node) {
                        if (($node['name'] ?? '') === $targetName) {
                            $foundNode = $node;
                            return;
                        }
                        if (!empty($node['children'])) {
                            $searchNode($node['children']);
                            if ($foundNode) return;
                        }
                    }
                };
                $searchNode($config->data);

                if ($foundNode) {
                    $n1 = !empty($foundNode['n1']);
                    $n2 = !empty($foundNode['n2']);
                    if ($n1 && $n2) {
                        $attendantType = 'N1/N2';
                    } elseif ($n1) {
                        $attendantType = 'N1';
                    } elseif ($n2) {
                        $attendantType = 'N2';
                    }
                }
            }
        }

        // Processar upload de novos arquivos se houver
        $filePaths = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    $filePaths[] = $file->store('solicitations', 'public');
                }
            }
        }

        // Leva junto os arquivos anexados à solicitação original
        $originalFiles = is_array($solicitation->file_path) ? $solicitation->file_path : [];
        $filePaths = array_merge($originalFiles, $filePaths);

        // Gera novo número de ticket único de 9 dígitos
        $ticketNumber = str_pad(random_int(100000000, 999999999), 9, '0', STR_PAD_LEFT);

        // Prepara a descrição do novo chamado, incluindo o contexto completo da solicitação de origem
        $formattedDescription = "[" . $destination . "] " . $description;
        $formattedDescription .= "\n\n--- Contexto do Chamado de Origem (#" . $solicitation->ticket_number . ") ---";
        $formattedDescription .= "\nTítulo Original: " . $solicitation->title;
        $formattedDescription .= "\nDescrição Original: " . $solicitation->description;

        if ($attendantType) {
            $formattedDescription .= "\n\nTipo de Atendimento: " . $attendantType;
        }

        // Cria a nova solicitação
        $newSolicitation = Solicitation::create([
            'user_id' => $solicitation->user_id, // pertence ao mesmo cliente
            'atendente_id' => $targetAttendantId,
            'title' => $title,
            'description' => $formattedDescription,
            'status' => 'na_fila', // status inicial na fila
            'ticket_number' => $ticketNumber,
            'file_path' => $filePaths,
        ]);

        // Copia todo o histórico de mensagens do chamado original para o novo chamado (exclui mensagens do sistema para evitar duplicação de logs antigos)
        $originalMessages = Message::where('solicitation_id', $solicitation->id)
            ->where('type', '!=', 'internal')
            ->orderBy('id', 'asc')
            ->get();
        $messageIdMapping = [];
        foreach ($originalMessages as $msg) {
            $newParentId = null;
            if ($msg->parent_id && isset($messageIdMapping[$msg->parent_id])) {
                $newParentId = $messageIdMapping[$msg->parent_id];
            }

            $newMsg = Message::create([
                'solicitation_id' => $newSolicitation->id,
                'user_id' => $msg->user_id,
                'text' => $msg->text,
                'file_path' => $msg->file_path,
                'file_name' => $msg->file_name,
                'parent_id' => $newParentId,
                'type' => $msg->type,
                'metadata' => $msg->metadata,
                'reactions' => $msg->reactions,
                'read_at' => $msg->read_at,
            ]);

            $messageIdMapping[$msg->id] = $newMsg->id;
        }

        // Adiciona uma mensagem informativa na nova solicitação indicando a importação do histórico
        Message::create([
            'solicitation_id' => $newSolicitation->id,
            'user_id' => auth()->id(),
            'text' => "Histórico de atendimento importado do chamado de origem #{$solicitation->ticket_number}.",
            'type' => 'internal'
        ]);

        if ($targetAttendantId) {
            $targetUser = User::find($targetAttendantId);
            $targetName = $targetUser ? $targetUser->name : 'atendente';
            Message::create([
                'solicitation_id' => $newSolicitation->id,
                'user_id' => auth()->id(),
                'text' => "Chamado ID {$ticketNumber} transferido e atribuído a {$targetName} para atendimento.",
                'type' => 'internal'
            ]);
        } else {
            Message::create([
                'solicitation_id' => $newSolicitation->id,
                'user_id' => auth()->id(),
                'text' => "Chamado ID {$ticketNumber} transferido para a fila: {$destination}.",
                'type' => 'internal'
            ]);
        }

        // Cria uma mensagem do sistema no chat do chamado original para registrar a transferência/abertura do ticket
        Message::create([
            'solicitation_id' => $solicitation->id,
            'user_id' => auth()->id(),
            'text' => "Novo ticket #{$ticketNumber} aberto e encaminhado para: {$destination}.",
            'type' => 'internal'
        ]);

        // Salva logs de atividade
        ActivityLog::writeLog(
            'Transferência',
            'CHAMADO',
            "Abriu novo ticket #{$ticketNumber} a partir do chamado #{$solicitation->ticket_number} encaminhado para {$destination}"
        );

        // Marca o chamado original como resolvido para que saia do fluxo ativo do atendente que o transferiu
        $solicitation->update([
            'status' => 'resolvida',
        ]);

        // Se o destino for uma pessoa específica, notificar
        try {
            if ($targetAttendantId) {
                $targetUser = User::find($targetAttendantId);
                if ($targetUser) {
                    $notifTitle = 'Novo ticket atribuído';
                    $notifMessageText = 'O ticket #' . $ticketNumber . ' foi encaminhado diretamente para você.';
                    $targetUser->notify(new SolicitationNotification($notifTitle, $notifMessageText, $newSolicitation->id, 'novo_chamado'));
                }
            } else {
                // Notifica atendentes no geral
                $atendentes = User::where('role', 'atendente')->get();
                $notifTitle = 'Nova demanda na fila';
                $notifMessageText = 'O chamado #' . $ticketNumber . ' está aguardando atendimento na fila.';
                foreach ($atendentes as $atendente) {
                    $atendente->notify(new SolicitationNotification($notifTitle, $notifMessageText, $newSolicitation->id, 'novo_chamado'));
                }
            }
        } catch (\Exception $e) {
            // Silencia
        }

        return response()->json([
            'success' => true,
            'message' => 'Ticket cadastrado com sucesso!',
            'ticket_number' => $ticketNumber,
            'id' => $newSolicitation->id
        ]);
    }

    /**
     * Restringe a visibilidade da fila para o atendente logado no chat.
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

        $query->where(function ($typeMatch) use ($canN1, $canN2) {
            // Compatibilidade com chamados sem tipo de atendimento
            $typeMatch->where('description', 'not like', '%Tipo de Atendimento:%')
                ->orWhere('description', 'like', '%Tipo de Atendimento: N1/N2%');

            if ($canN1) {
                $typeMatch->orWhere('description', 'like', '%Tipo de Atendimento: N1%');
            }

            if ($canN2) {
                $typeMatch->orWhere('description', 'like', '%Tipo de Atendimento: N2%');
            }
        });
    }

    // -------------------------------------------------------
    // INTERNAL NOTES
    // -------------------------------------------------------

    /**
     * Store a new internal note for the given solicitation.
     */
    public function storeInternalNote(Request $request, Solicitation $solicitation)
    {
        if (!in_array(auth()->user()->role, ['atendente', 'admin'], true)) {
            return response()->json(['error' => 'Não autorizado.'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:3000',
        ]);

        $note = InternalNote::create([
            'solicitation_id' => $solicitation->id,
            'user_id'        => auth()->id(),
            'content'        => $request->input('content'),
            'is_pinned'      => false,
        ]);

        $note->load('user');

        return response()->json([
            'success' => true,
            'note'    => [
                'id'         => $note->id,
                'content'    => $note->content,
                'is_pinned'  => $note->is_pinned,
                'author'     => $note->user->name ?? 'Atendente',
                'created_at' => $note->created_at->format('d/m/Y H:i'),
            ],
        ]);
    }

    /**
     * Toggle pin status of an internal note.
     */
    public function togglePinNote(InternalNote $note)
    {
        if (!in_array(auth()->user()->role, ['atendente', 'admin'], true)) {
            return response()->json(['error' => 'Não autorizado.'], 403);
        }

        $note->update(['is_pinned' => !$note->is_pinned]);

        return response()->json([
            'success'   => true,
            'is_pinned' => $note->is_pinned,
        ]);
    }

    /**
     * Delete an internal note.
     */
    public function destroyInternalNote(InternalNote $note)
    {
        if (!in_array(auth()->user()->role, ['atendente', 'admin'], true)) {
            return response()->json(['error' => 'Não autorizado.'], 403);
        }

        // Only the author or an admin can delete
        if ($note->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Não autorizado.'], 403);
        }

        $note->delete();

        return response()->json(['success' => true]);
    }
}
