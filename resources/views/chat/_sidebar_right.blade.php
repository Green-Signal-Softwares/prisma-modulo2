@php
    $rightAvatarUser = $isStaffUser ? ($activeSolicitation->user ?? null) : ($activeSolicitation->atendente ?? null);
    $rightAvatarName = $rightAvatarUser ? $rightAvatarUser->name : 'Suporte Claro';
    $rightAvatarBg   = ($rightAvatarUser && $rightAvatarUser->role === 'atendente') ? 'EAA8A8' : 'D1E7DD';
    $rightAvatarColor= ($rightAvatarUser && $rightAvatarUser->role === 'atendente') ? '86131E' : '0F5132';

    $solicitationFiles = ($activeSolicitation && $activeSolicitation->file_path && is_array($activeSolicitation->file_path)) ? $activeSolicitation->file_path : [];
    $messageFiles = [];
    if ($activeSolicitation && $activeSolicitation->messages) {
        foreach ($activeSolicitation->messages as $msg) {
            foreach ($msg->files as $f) {
                if (!empty($f['path']) && is_string($f['path'])) {
                    $messageFiles[] = $f['path'];
                }
            }
        }
    }
    $totalAttachmentsCount = count($solicitationFiles) + count($messageFiles);

    $allSidebarImages = [];
    foreach ($solicitationFiles as $p) {
        $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) $allSidebarImages[] = Storage::url($p);
    }
    foreach ($messageFiles as $p) {
        $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) $allSidebarImages[] = asset('storage/'.$p);
    }
    $internalNotes = $isStaffUser ? ($activeSolicitation->internalNotes ?? collect()) : collect();
    $sessionStart  = $activeSolicitation->created_at;
    $sessionEnd    = in_array($activeSolicitation->status, ['resolvida','finalizada','cancelada']) ? $activeSolicitation->updated_at : null;
@endphp

<style>
    .note-card { background:#FFFBEB; border:1.5px solid #FDE68A; border-radius:12px; padding:10px 12px; position:relative; }
    .note-card.pinned { border-color:#F59E0B; background:#FEF3C7; }
    .hist-item { display:flex; align-items:flex-start; gap:8px; padding:8px 0; border-bottom:1px solid #f3f4f6; }
    .hist-item:last-child { border-bottom:none; }
    .session-row { display:flex; align-items:center; justify-content:space-between; padding:6px 0; border-bottom:1px solid #f3f4f6; font-size:11px; }
    .session-row:last-child { border-bottom:none; }
</style>

{{-- 1. Avatar + Nome --}}
<div class="text-center pb-4 border-b border-gray-200 flex-shrink-0">
    @if(!$rightAvatarUser && auth()->user()->role === 'user')
        <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center border p-3 shadow-sm mx-auto mb-3">
            <img src="/icones/Icone Ticket.png" alt="Ticket" class="w-full h-full object-contain">
        </div>
    @else
        <div class="relative w-16 h-16 mx-auto mb-3 flex-shrink-0">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($rightAvatarName) }}&background={{ $rightAvatarBg }}&color={{ $rightAvatarColor }}&bold=true&rounded=true"
                 alt="Avatar" class="w-16 h-16 rounded-full object-cover border border-gray-200 shadow-sm">
        </div>
    @endif
    <h3 class="text-xs font-extrabold text-gray-800 uppercase tracking-wider leading-tight">
        @if($isStaffUser)
            {{ strtoupper($activeSolicitation->user->name ?? '—') }}
        @else
            {{ strtoupper($activeSolicitation->atendente->name ?? 'SUPORTE PRISMA') }}
        @endif
    </h3>
    <p class="text-[10px] text-gray-400 font-semibold mt-0.5">#{{ $activeSolicitation->ticket_number }}</p>
</div>

{{-- 2. Imagens e Documentos --}}
<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
        <span class="text-xs font-extrabold text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375 3.75 0 1 1-.75 0 .375 3.75 0 0 1 .75 0Z" />
            </svg>
            Imagens e documentos
        </span>
        <span id="sidebar-attachments-count" class="text-xs font-extrabold text-gray-400">{{ $totalAttachmentsCount ?? 0 }}</span>
    </div>

    <div id="sidebar-attachments-container" class="flex items-center gap-2 overflow-x-auto pb-2">
        @foreach($solicitationFiles ?? [] as $path)
            @php
                $extension = pathinfo($path, PATHINFO_EXTENSION);
                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            @endphp
            @if($isImage)
                <a href="javascript:void(0)" onclick="openImageLightbox('{{ Storage::url($path) }}', {{ json_encode($allSidebarImages) }})"
                    class="w-[84px] aspect-square rounded-xl overflow-hidden border border-gray-250 hover:opacity-90 transition-all flex-shrink-0">
                    <img src="{{ Storage::url($path) }}" alt="Preview" class="w-full h-full object-cover">
                </a>
            @else
                <a href="{{ Storage::url($path) }}" download
                    class="w-[84px] aspect-square rounded-xl bg-gray-50 flex flex-col items-center justify-center border border-gray-250 hover:bg-gray-100 transition-colors flex-shrink-0">
                    <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5-3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <span class="text-[8px] font-bold text-gray-500 uppercase mt-1">DOC</span>
                </a>
            @endif
        @endforeach

        @foreach($messageFiles ?? [] as $path)
            @php
                $extension = pathinfo($path, PATHINFO_EXTENSION);
                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            @endphp
            @if($isImage)
                <a href="javascript:void(0)" onclick="openImageLightbox('{{ asset('storage/' . $path) }}', {{ json_encode($allSidebarImages) }})"
                    class="w-[84px] aspect-square rounded-xl overflow-hidden border border-gray-250 hover:opacity-90 transition-all flex-shrink-0">
                    <img src="{{ asset('storage/' . $path) }}" alt="Preview" class="w-full h-full object-cover">
                </a>
            @else
                <a href="{{ asset('storage/' . $path) }}" download
                    class="w-[84px] aspect-square rounded-xl bg-gray-50 flex flex-col items-center justify-center border border-gray-250 hover:bg-gray-100 transition-colors flex-shrink-0">
                    <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5-3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <span class="text-[8px] font-bold text-gray-500 uppercase mt-1">DOC</span>
                </a>
            @endif
        @endforeach

        <p id="sidebar-no-attachments-msg" class="text-xs text-gray-400 italic {{ $totalAttachmentsCount > 0 ? 'hidden' : '' }}">Nenhum arquivo enviado.</p>
    </div>
</div>

{{-- 3. Sessão --}}
<div class="flex flex-col gap-0.5 pt-2 border-t border-gray-100">
    <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider flex items-center gap-1.5 mb-1">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
        Sessão
    </span>
    <div class="session-row"><span class="font-bold text-gray-500">Abertura</span><span class="font-semibold text-gray-700">{{ $sessionStart->format('d/m/Y H:i') }}</span></div>
    <div class="session-row"><span class="font-bold text-gray-500">Encerramento</span><span class="font-semibold {{ $sessionEnd ? 'text-gray-700' : 'text-green-600' }}">{{ $sessionEnd ? $sessionEnd->format('d/m/Y H:i') : 'Em andamento' }}</span></div>
    @if($sessionEnd)
    <div class="session-row"><span class="font-bold text-gray-500">Duração</span><span class="font-semibold text-gray-700">{{ gmdate('H\h i\m', $sessionStart->diffInSeconds($sessionEnd)) }}</span></div>
    @endif
    <div class="session-row">
        <span class="font-bold text-gray-500">Status</span>
        @php
            $stColors = ['na_fila'=>'text-yellow-600 bg-yellow-50','em_atendimento'=>'text-blue-600 bg-blue-50','em_replica'=>'text-indigo-600 bg-indigo-50','resolvida'=>'text-green-600 bg-green-50','finalizada'=>'text-gray-500 bg-gray-100','cancelada'=>'text-red-600 bg-red-50'];
            $stLabels = ['na_fila'=>'Na fila','em_atendimento'=>'Em atendimento','em_replica'=>'Em réplica','resolvida'=>'Resolvida','finalizada'=>'Finalizada','cancelada'=>'Cancelada'];
            $stCl = $stColors[$activeSolicitation->status] ?? 'text-gray-500 bg-gray-100';
            $stLb = $stLabels[$activeSolicitation->status] ?? $activeSolicitation->status;
        @endphp
        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $stCl }}">{{ $stLb }}</span>
    </div>
    @if($isStaffUser && $activeSolicitation->atendente)
    <div class="session-row"><span class="font-bold text-gray-500">Atendente</span><span class="font-semibold text-gray-700 truncate max-w-[120px]">{{ $activeSolicitation->atendente->name }}</span></div>
    @endif
</div>

{{-- 4. Notas Internas (staff only) --}}
@if($isStaffUser)
<div class="flex flex-col gap-2 pt-2 border-t border-gray-100">
    <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider flex items-center gap-1.5 mb-1">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
        Notas internas
    </span>
    <div class="flex flex-col gap-2">
        <textarea id="new-internal-note-input" rows="3" placeholder="Escrever nota interna..." maxlength="3000"
            class="w-full rounded-xl border border-amber-300 bg-amber-50 text-xs text-gray-800 placeholder-amber-400 p-3 resize-none focus:outline-none focus:ring-2 focus:ring-amber-400 transition-all"></textarea>
        <button onclick="submitInternalNote()" id="btn-submit-internal-note"
            class="w-full h-9 rounded-xl text-white text-xs font-extrabold transition-all hover:opacity-90 active:scale-[0.98]"
            style="background:linear-gradient(90deg,#B45309,#F59E0B);">+ Adicionar nota</button>
    </div>
    <div id="internal-notes-list" class="flex flex-col gap-2 mt-1">
        @forelse($internalNotes as $note)
            <div class="note-card {{ $note->is_pinned ? 'pinned' : '' }}" id="note-card-{{ $note->id }}" data-note-id="{{ $note->id }}">
                @if($note->is_pinned)
                    <span class="pinned-badge absolute top-2 right-2 text-amber-500 text-[10px] font-extrabold flex items-center gap-0.5">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/></svg>Fixada</span>
                @endif
                <p class="text-xs text-gray-800 leading-relaxed whitespace-pre-wrap pr-4">{{ $note->content }}</p>
                <div class="flex items-center justify-between mt-2 pt-1.5 border-t border-amber-200">
                    <div class="flex items-center gap-1.5">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($note->user->name ?? 'A') }}&background=FDE68A&color=92400E&bold=true&rounded=true&size=20" class="w-4 h-4 rounded-full" />
                        <span class="text-[10px] font-bold text-amber-800">{{ $note->user->name ?? 'Atendente' }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="text-[10px] text-gray-400">{{ $note->created_at->format('d/m H:i') }}</span>
                        <button onclick="togglePinNote({{ $note->id }})" title="{{ $note->is_pinned ? 'Desafixar' : 'Fixar' }}" class="btn-pin-note ml-1 text-amber-400 hover:text-amber-600 transition-colors focus:outline-none">
                            <svg class="w-3.5 h-3.5" fill="{{ $note->is_pinned ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 12V4h1V2H7v2h1v8l-2 2v2h5.2v6h1.6v-6H18v-2l-2-2z"/></svg>
                        </button>
                        @if($note->user_id === auth()->id() || auth()->user()->role === 'admin')
                        <button onclick="deleteInternalNote({{ $note->id }})" title="Excluir" class="text-red-300 hover:text-red-500 transition-colors focus:outline-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div id="notes-empty-state" class="flex flex-col items-center justify-center py-6 gap-2 text-center">
                <svg class="w-8 h-8 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/></svg>
                <p class="text-xs text-gray-400 font-medium">Nenhuma nota interna ainda.</p>
            </div>
        @endforelse
    </div>
</div>
@endif

{{-- 5. Histórico --}}
<div class="flex flex-col gap-2 pt-2 border-t border-gray-100">
    <span class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider flex items-center gap-1.5 mb-1">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/></svg>
        Histórico
    </span>
    <div class="flex flex-col gap-0.5">
        @php $histMessages = ($activeSolicitation->messages ?? collect())->filter(fn($m) => $m->type === 'internal'); @endphp
        @forelse($histMessages as $hm)
            <div class="hist-item">
                <div class="w-5 h-5 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-3 h-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] text-gray-700 font-medium leading-snug">{{ $hm->text }}</p>
                    <p class="text-[9px] text-gray-400 font-bold mt-0.5">{{ $hm->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-8 gap-2 text-center">
                <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/></svg>
                <p class="text-xs text-gray-400 font-medium">Nenhum evento no histórico.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Quick-replies editor (staff) --}}
@if($isStaffUser)
<div id="quick-replies-editor" class="hidden flex-col gap-3 pt-1 relative flex-1 min-h-0">
    <button type="button" onclick="closeQuickRepliesEditor()" class="absolute top-0 right-0 text-gray-500 hover:text-gray-800 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
    </button>
    <h4 class="text-xl leading-none font-extrabold text-[#243447] text-center mt-1">Respostas rápidas</h4>
    <div class="relative mt-1">
        <input id="quick-replies-editor-search" type="text" placeholder="Pesquisar"
            class="w-full h-10 rounded-full border border-gray-400 bg-transparent px-4 pr-10 text-sm placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#DA291C]">
        <svg class="w-4 h-4 text-gray-600 absolute right-4 top-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
    </div>
    <div class="h-px bg-gray-300 my-1"></div>
    <div id="quick-replies-editor-list" class="flex flex-col gap-3 flex-1 overflow-y-auto pr-2 quick-replies-red-scroll"></div>
    <button type="button" onclick="openQuickReplyAddModal()" class="w-full h-10 rounded-xl text-white text-base font-extrabold hover:opacity-95 transition-all" style="background: linear-gradient(89.24deg, #A01724 0%, #DA291C 100%);">Adicionar resposta rápida</button>
</div>
@endif
