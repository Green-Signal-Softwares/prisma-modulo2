@extends('layouts.app')

@section('content')
<!-- Full-width wrapper with padding matching the main layout -->
<div class="w-full px-6 py-6 select-none">
    <!-- Breadcrumbs & Header -->
    <div class="mb-6">
        <div class="flex items-center gap-1 text-xs text-gray-400 font-semibold mb-1">
            <span>Claro Prisma</span>
            <span>&gt;</span>
            <span>Legendas</span>
        </div>
        <div class="flex items-center justify-between mt-2">
            <h1 class="text-3xl font-extrabold text-[#DA291C]" style="font-family: 'AMX', sans-serif;">Notificações</h1>
            <button onclick="openModal()" 
                class="bg-[#DA291C] hover:bg-[#b31d14] text-white font-bold text-sm px-6 py-2.5 rounded-full transition-all shadow-md cursor-pointer select-none">
                + Adicionar nova
            </button>
        </div>
    </div>

    <!-- Session Feedback Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl font-semibold text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Notifications List (Stretching wider) -->
    <div class="space-y-4 w-full">
        @forelse($notifications as $notification)
            <div class="bg-white rounded-[16px] border border-gray-200 p-6 flex items-start justify-between shadow-sm hover:shadow-md transition-shadow relative">
                <div class="flex-1 pr-12">
                    <!-- Date -->
                    <span class="text-xs text-gray-400 font-bold block mb-1">
                        {{ $notification->start_at->setTimezone('America/Sao_Paulo')->format('d/m/Y') }}
                    </span>
                    
                    <!-- Title & Type Badge -->
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <h3 class="text-lg font-bold text-[#DA291C]" style="font-family: 'AMX', sans-serif;">
                            {{ $notification->title }}
                        </h3>
                        @if($notification->type === 'push')
                            <span class="bg-[#FD7E14] text-white text-[10px] font-extrabold px-3 py-0.5 rounded-full select-none uppercase">
                                Notificação Push
                            </span>
                        @elseif($notification->type === 'system')
                            <span class="bg-gray-400 text-white text-[10px] font-extrabold px-3 py-0.5 rounded-full select-none uppercase">
                                Padrão
                            </span>
                        @else
                            <span class="bg-[#007BFF] text-white text-[10px] font-extrabold px-3 py-0.5 rounded-full select-none uppercase">
                                E-mail
                            </span>
                        @endif

                        @if($notification->status === 'inactive')
                            <span class="bg-red-100 text-red-700 text-[10px] font-extrabold px-3 py-0.5 rounded-full select-none uppercase">
                                Inativa
                            </span>
                        @endif

                        <span class="bg-red-50 text-[#DA291C] border border-red-100 text-[10px] font-extrabold px-3 py-0.5 rounded-full select-none uppercase">
                            Envio: {{ $notification->send_to === 'all' ? 'Todos' : ($notification->send_to === 'atendente' ? 'Atendentes' : 'Clientes') }}
                        </span>
                    </div>

                    <!-- Content Description -->
                    <p class="text-gray-500 text-sm leading-relaxed whitespace-pre-line">
                        {{ $notification->content }}
                    </p>
                </div>

                <!-- Actions (Edit & Delete) -->
                <div class="flex items-center gap-4 flex-shrink-0 pt-2 select-none">
                    <!-- Pencil Icon: Orange circular background -->
                    <button onclick="editNotification({{ json_encode($notification) }})" 
                        class="p-2 bg-[#FD7E14] hover:bg-[#e06b0d] rounded-full text-white transition-all cursor-pointer shadow-sm flex items-center justify-center" title="Editar Notificação">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <!-- Trash Icon: Clean dark gray trash SVG -->
                    <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" class="m-0" onsubmit="return confirm('Deseja realmente excluir esta notificação?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-gray-800 hover:text-red-600 transition-all cursor-pointer flex items-center justify-center" title="Excluir Notificação">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-[24px] border border-gray-200 p-12 text-center shadow-sm select-none w-full">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <h3 class="text-lg font-bold text-gray-700 mb-1" style="font-family: 'AMX', sans-serif;">Nenhuma notificação cadastrada</h3>
                <p class="text-gray-400 text-sm">Clique em "+ Adicionar nova" para configurar e enviar uma nova notificação.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Container (Larger width max-w-2xl, background backdrop-blur) -->
<div id="notification-modal" class="fixed inset-0 z-50 overflow-y-auto hidden select-none">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" onclick="closeModal()"></div>

    <!-- Modal Content wrapper -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-[24px] bg-white p-6 shadow-2xl transition-all border border-gray-100">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-extrabold text-[#DA291C]" id="modal-title" style="font-family: 'AMX', sans-serif;">
                    Adicionar notificação
                </h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors cursor-pointer text-2xl font-normal">
                    &times;
                </button>
            </div>

            <!-- Modal Form -->
            <form id="notification-form" action="{{ route('admin.notifications.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">

                <div class="flex flex-col gap-3.5">
                    <!-- Status Dropdown (Topmost) -->
                    <div class="flex flex-col gap-1">
                        <select name="status" id="field-status" required
                            class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-700 outline-none focus:border-[#DA291C] focus:bg-white transition-all cursor-pointer">
                            <option value="active">Notificação ativa</option>
                            <option value="inactive">Notificação inativa</option>
                        </select>
                    </div>

                    <!-- Envio and Tipo de notificação Grid (Side-by-side) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Envio (Target dropdown) -->
                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Envio</label>
                            <select name="send_to" id="field-send-to" required
                                class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-700 outline-none focus:border-[#DA291C] focus:bg-white transition-all cursor-pointer">
                                <option value="all">Todos os usuários</option>
                                <option value="atendente">Apenas atendentes</option>
                                <option value="user">Apenas clientes</option>
                            </select>
                        </div>

                        <!-- Tipo de notificação -->
                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tipo de notificação</label>
                            <select name="type" id="field-type" required
                                class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-700 outline-none focus:border-[#DA291C] focus:bg-white transition-all cursor-pointer">
                                <option value="system">Tela do sistema</option>
                                <option value="push">Notificação Push</option>
                                <option value="email">E-mail</option>
                            </select>
                        </div>
                    </div>

                    <!-- Start date and time grid (Side-by-side) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Escolher data de início</label>
                            <div class="relative flex items-center">
                                <input type="date" name="start_date" id="field-start-date" required
                                    class="w-full h-10 pl-3 pr-12 bg-gray-50 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-700 outline-none focus:border-[#DA291C] focus:bg-white transition-all">
                                <div class="absolute right-0 top-0 bottom-0 w-11 bg-gray-100 border-l border-gray-200 rounded-r-[12px] flex items-center justify-center text-gray-550 pointer-events-none">
                                    <!-- Calendar Icon -->
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Escolher hora de início</label>
                            <div class="relative flex items-center">
                                <input type="time" name="start_time" id="field-start-time" required
                                    class="w-full h-10 pl-3 pr-12 bg-gray-50 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-700 outline-none focus:border-[#DA291C] focus:bg-white transition-all">
                                <div class="absolute right-0 top-0 bottom-0 w-11 bg-gray-100 border-l border-gray-200 rounded-r-[12px] flex items-center justify-center text-gray-550 pointer-events-none">
                                    <!-- Clock Icon -->
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- End date and time grid (Side-by-side) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Escolher data de término</label>
                            <div class="relative flex items-center">
                                <input type="date" name="end_date" id="field-end-date" required
                                    class="w-full h-10 pl-3 pr-12 bg-gray-50 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-700 outline-none focus:border-[#DA291C] focus:bg-white transition-all">
                                <div class="absolute right-0 top-0 bottom-0 w-11 bg-gray-100 border-l border-gray-200 rounded-r-[12px] flex items-center justify-center text-gray-550 pointer-events-none">
                                    <!-- Calendar Icon -->
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Escolher hora de término</label>
                            <div class="relative flex items-center">
                                <input type="time" name="end_time" id="field-end-time" required
                                    class="w-full h-10 pl-3 pr-12 bg-gray-50 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-700 outline-none focus:border-[#DA291C] focus:bg-white transition-all">
                                <div class="absolute right-0 top-0 bottom-0 w-11 bg-gray-100 border-l border-gray-200 rounded-r-[12px] flex items-center justify-center text-gray-550 pointer-events-none">
                                    <!-- Clock Icon -->
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Título da notificação</label>
                        <input type="text" name="title" id="field-title" required placeholder="Título da notificação"
                            class="w-full h-10 px-3 bg-gray-50 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-700 outline-none focus:border-[#DA291C] focus:bg-white transition-all">
                    </div>

                    <!-- Content (Rich-text lookalike) -->
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Texto da notificação</label>
                        <!-- Premium Toolbar styled like the Figma screenshot -->
                        <div class="flex items-center gap-3 p-2.5 bg-gray-50 border border-b-0 border-gray-200 rounded-t-[12px] flex-wrap text-gray-600 text-sm">
                            <span class="font-bold cursor-pointer hover:text-black">T</span>
                            <span class="w-4 h-4 bg-black rounded-full border border-gray-300 cursor-pointer block"></span>
                            <span class="w-[1px] h-4 bg-gray-300"></span>
                            <span class="font-bold cursor-pointer hover:text-black">B</span>
                            <span class="italic font-serif cursor-pointer hover:text-black">I</span>
                            <span class="underline cursor-pointer hover:text-black">U</span>
                            <span class="line-through cursor-pointer hover:text-black">S</span>
                            <span class="w-[1px] h-4 bg-gray-300"></span>
                            <!-- Align Icons -->
                            <span class="cursor-pointer hover:text-black">≡</span>
                            <span class="cursor-pointer hover:text-black">⍓</span>
                            <span class="cursor-pointer hover:text-black">⍝</span>
                            <span class="w-[1px] h-4 bg-gray-300"></span>
                            <span class="cursor-pointer hover:text-black">🔗</span>
                            <span class="cursor-pointer hover:text-black">🖼️</span>
                            <span class="cursor-pointer hover:text-black">❞</span>
                        </div>
                        <textarea name="content" id="field-content" required rows="3" placeholder="Texto da notificação"
                            class="w-full p-3 bg-gray-50 border border-gray-200 rounded-b-[12px] text-sm font-semibold text-gray-700 outline-none focus:border-[#DA291C] focus:bg-white transition-all resize-none"></textarea>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="mt-6 flex flex-col gap-2 items-center">
                    <button type="submit" id="btn-submit"
                        class="w-full bg-[#DA291C] hover:bg-[#b31d14] text-white font-bold py-2.5 px-4 rounded-full transition-all cursor-pointer shadow-md select-none text-center">
                        Enviar
                    </button>
                    <button type="button" onclick="closeModal()"
                        class="text-gray-500 hover:text-gray-700 font-bold text-sm transition-colors cursor-pointer select-none">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('notification-modal');
    const form = document.getElementById('notification-form');
    const modalTitle = document.getElementById('modal-title');
    const btnSubmit = document.getElementById('btn-submit');
    const formMethod = document.getElementById('form-method');

    function openModal() {
        modalTitle.textContent = 'Adicionar notificação';
        btnSubmit.textContent = 'Enviar';
        form.action = "{{ route('admin.notifications.store') }}";
        formMethod.value = 'POST';
        
        // Reset fields
        document.getElementById('field-status').value = 'active';
        document.getElementById('field-send-to').value = 'all';
        document.getElementById('field-type').value = 'system';
        document.getElementById('field-start-date').value = '';
        document.getElementById('field-start-time').value = '';
        document.getElementById('field-end-date').value = '';
        document.getElementById('field-end-time').value = '';
        document.getElementById('field-title').value = '';
        document.getElementById('field-content').value = '';

        modal.classList.remove('hidden');
    }

    function convertUTCToLocal(dateString) {
        if (!dateString) return { date: '', time: '' };
        
        // If the date string doesn't contain 'Z' or a timezone offset, append 'Z' to treat it as UTC
        let formattedString = dateString;
        if (!dateString.includes('Z') && !dateString.includes('+') && !/-\d{2}:\d{2}$/.test(dateString)) {
            // Replace space with T for ISO format
            formattedString = dateString.replace(' ', 'T') + 'Z';
        }
        
        const date = new Date(formattedString);
        if (isNaN(date.getTime())) return { date: '', time: '' };
        
        const optionsDate = { year: 'numeric', month: '2-digit', day: '2-digit', timeZone: 'America/Sao_Paulo' };
        const optionsTime = { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'America/Sao_Paulo' };
        
        const formatterDate = new Intl.DateTimeFormat('en-US', optionsDate);
        const formatterTime = new Intl.DateTimeFormat('en-US', optionsTime);
        
        const partsDate = formatterDate.formatToParts(date);
        const hashDate = {};
        partsDate.forEach(p => hashDate[p.type] = p.value);
        const dateStr = `${hashDate.year}-${hashDate.month}-${hashDate.day}`;
        
        const partsTime = formatterTime.formatToParts(date);
        const hashTime = {};
        partsTime.forEach(p => hashTime[p.type] = p.value);
        const timeStr = `${hashTime.hour}:${hashTime.minute}`;
        
        return { date: dateStr, time: timeStr };
    }

    function editNotification(notification) {
        modalTitle.textContent = 'Editar notificação';
        btnSubmit.textContent = 'Salvar';
        form.action = `/admin/notifications/${notification.id}`;
        formMethod.value = 'PUT';

        let startDateStr = '';
        let startTimeStr = '';
        if (notification.start_at) {
            const localStart = convertUTCToLocal(notification.start_at);
            startDateStr = localStart.date;
            startTimeStr = localStart.time;
        }

        let endDateStr = '';
        let endTimeStr = '';
        if (notification.end_at) {
            const localEnd = convertUTCToLocal(notification.end_at);
            endDateStr = localEnd.date;
            endTimeStr = localEnd.time;
        }

        document.getElementById('field-status').value = notification.status || 'active';
        document.getElementById('field-send-to').value = notification.send_to;
        document.getElementById('field-type').value = notification.type;
        document.getElementById('field-start-date').value = startDateStr;
        document.getElementById('field-start-time').value = startTimeStr;
        document.getElementById('field-end-date').value = endDateStr;
        document.getElementById('field-end-time').value = endTimeStr;
        document.getElementById('field-title').value = notification.title;
        document.getElementById('field-content').value = notification.content;

        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }
</script>
@endsection
