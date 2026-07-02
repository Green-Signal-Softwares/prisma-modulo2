@extends('layouts.app')

@section('title', 'Ticket #' . $solicitation->ticket_number . ' - PRISMA Claro')

@section('content')
    <!-- Breadcrumbs -->
    <div class="mb-4 select-none">
        <div class="text-xs text-gray-500 mb-1">
            <span>Claro Prisma</span> &gt; <span class="font-medium text-gray-700">Tickets de suporte</span>
        </div>
    </div>

    <!-- Header com Título e Ações -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 select-none">
        <h1 class="text-3xl font-extrabold text-[#DA291C] tracking-tight">Tickets de suporte</h1>
        
        <div class="flex items-center gap-3">
            <!-- Histórico Completo Button -->
            <a href="{{ route('atendente.historico') }}"
                style="background: linear-gradient(89.24deg, #A01724 0%, #DA291C 100%); width: 286.04px; height: 48px; border-radius: 11.82px; gap: 8px;"
                class="flex items-center justify-center hover:opacity-90 text-white font-bold text-sm transition-all shadow-md shadow-red-950/15 cursor-pointer">
                <span>Histórico completo</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-9-9c2.53 0 4.757 1.048 6.34 2.74L21 8.25" />
                </svg>
            </a>

            <!-- Central de Mensagens Button -->
            <a href="{{ route('atendente.chat.index') }}"
                style="background: linear-gradient(89.24deg, #A01724 0%, #DA291C 100%); width: 286.04px; height: 48px; border-radius: 11.82px; gap: 8px;"
                class="flex items-center justify-center hover:opacity-90 text-white font-bold text-sm transition-all shadow-md shadow-red-950/15 cursor-pointer">
                <span>Central de mensagens</span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M4.848 2.771A49.144 49.144 0 0 1 12 2.25c2.43 0 4.817.178 7.152.52 1.237.18 2.098 1.222 2.098 2.47v6.75c0 1.248-.86 2.29-2.098 2.47a48.547 48.547 0 0 1-5.187.365c-.173.01-.341.074-.467.19a48.7 48.7 0 0 1-2.9 2.5a.75.75 0 0 1-1.27-.549v-1.87a.75.75 0 0 0-.75-.75 48.735 48.735 0 0 1-4.83-.564C2.86 13.918 2 12.875 2 11.625V5.241c0-1.248.86-2.29 2.098-2.47a49.144 49.144 0 0 1 .75-.07ZM14.25 15v1.859c.277-.28.539-.569.78-.868a48.243 48.243 0 0 0 2.122-2.736.75.75 0 0 1 1.03-.22 47.054 47.054 0 0 0 4.068-3.07.75.75 0 0 1 1.157.6v6.241c0 1.248-.86 2.29-2.098 2.47a47.316 47.316 0 0 1-3.666.363.75.75 0 0 0-.667.75v1.859a.75.75 0 0 1-1.28.55l-2.906-2.772a.75.75 0 0 0-.513-.213 46.857 46.857 0 0 1-1.637-.156.75.75 0 0 1-.61-.75Z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Container do Ticket Principal -->
    <div class="bg-white rounded-[20px] border border-gray-200 shadow-sm p-6 md:p-8 flex flex-col gap-6 mb-10 select-none">
        
        <!-- Topo do Card: Ticket ID e Botão Voltar -->
        <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-extrabold text-[#DA291C] tracking-tight">
                    Ticket #{{ $solicitation->ticket_number }}
                </h2>
                <!-- Copiar -->
                <button type="button" onclick="copyTicketNumber('{{ $solicitation->ticket_number }}')" class="text-gray-400 hover:text-gray-700 transition-colors p-1" title="Copiar ID">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376A8.965 8.965 0 0 0 12 12.75a8.965 8.965 0 0 0-3.75 1.5m7.5 3v-3.375c0-.621-.504-1.125-1.125-1.125h-9.75a1.125 1.125 0 0 0-1.125 1.125v9.75c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V17.25Z" />
                    </svg>
                </button>
                <!-- Transferir -->
                <button type="button" onclick="openTransferMenuDirectly(event)" class="text-gray-400 hover:text-gray-700 transition-colors p-1" title="Transferir chamado">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                    </svg>
                </button>
                <!-- Círculo Status Dot -->
                @php
                    $statusColor = match ($solicitation->status) {
                        'resolvida' => '#28CD41',
                        'em_replica', 'em_atendimento' => '#FF9500',
                        'nao_resolvida', 'não resolvida' => '#FF3B30',
                        'respondida' => '#8E8E93',
                        default => '#1C1C1E',
                    };
                @endphp
                <span class="w-3.5 h-3.5 rounded-full inline-block" style="background-color: {{ $statusColor }};" title="Status: {{ ucfirst(str_replace('_', ' ', $solicitation->status)) }}"></span>
            </div>

            <!-- Botão Voltar -->
            <a href="{{ route('atendente.tickets') }}" 
                class="px-6 py-1.5 bg-[#A01724] hover:bg-[#DA291C] text-white text-xs font-bold rounded-lg transition-colors cursor-pointer border-0 select-none flex items-center justify-center">
                Voltar
            </a>
        </div>

        <!-- Histórico/Timeline de Mensagens -->
        <div class="flex flex-col gap-5">
            
            <!-- Mensagem 1: Solicitação Original do Cliente -->
            <div class="border border-gray-200/80 rounded-2xl p-5 bg-white shadow-sm flex flex-col gap-3">
                <div class="flex items-center gap-3">
                    <!-- User Avatar -->
                    <div class="w-9 h-9 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-extrabold text-gray-800 tracking-tight">
                            {{ strtoupper($solicitation->user->name) }} - PLANEJAMENTO CLARO
                        </span>
                        <span class="text-[10px] font-bold text-gray-400 mt-0.5">
                            {{ $solicitation->created_at->format('d/m/Y \À\S H\Hi') }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5 mt-1">
                    <h3 class="text-[#DA291C] font-extrabold text-base leading-snug">
                        {{ $solicitation->title }}
                    </h3>
                    <p class="text-sm font-semibold text-gray-700 leading-relaxed">
                        {{ preg_replace('/^\[(.*?)\]\s*-\s*/', '', $solicitation->description) }}
                    </p>
                </div>

                <!-- Seção Imagens e Documentos -->
                @if($solicitation->ticket_number == '123456789')
                    <!-- Mock de Imagens e Documentos conforme o Print para este ticket específico -->
                    <div class="mt-2 flex flex-col gap-2">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                            Imagens e documentos
                        </span>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Thumbnail 1 -->
                            <div class="border border-gray-200 rounded-xl p-2 bg-[#F8F9FA] flex flex-col gap-1.5 shadow-sm max-w-[200px]">
                                <div class="w-full h-24 bg-white rounded-lg border border-gray-150 overflow-hidden p-1 flex flex-col justify-between">
                                    <span class="text-[7px] font-extrabold text-[#DA291C] uppercase leading-none">Satisfação Claro</span>
                                    <div class="flex items-end justify-between gap-1 flex-1 py-1">
                                        <div class="w-2.5 bg-gray-200 rounded-t h-[30%]"></div>
                                        <div class="w-2.5 bg-gray-300 rounded-t h-[45%]"></div>
                                        <div class="w-2.5 bg-[#DA291C] rounded-t h-[75%]"></div>
                                        <div class="w-2.5 bg-gray-250 rounded-t h-[60%]"></div>
                                        <div class="w-2.5 bg-gray-400 rounded-t h-[90%]"></div>
                                    </div>
                                    <div class="border-t border-gray-100 pt-0.5 flex justify-between text-[5px] text-gray-400 font-bold uppercase">
                                        <span>07/jan</span>
                                        <span>15/jan</span>
                                        <span>22/jan</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Thumbnail 2 -->
                            <div class="border border-gray-200 rounded-xl p-2 bg-[#F8F9FA] flex flex-col gap-1.5 shadow-sm max-w-[200px]">
                                <div class="w-full h-24 bg-white rounded-lg border border-gray-150 overflow-hidden p-1 flex flex-col justify-between">
                                    <span class="text-[7px] font-extrabold text-[#DA291C] uppercase leading-none">Metas regionais</span>
                                    <div class="flex items-end justify-between gap-1 flex-1 py-1">
                                        <div class="w-2.5 bg-gray-300 rounded-t h-[50%]"></div>
                                        <div class="w-2.5 bg-[#A01724] rounded-t h-[65%]"></div>
                                        <div class="w-2.5 bg-gray-200 rounded-t h-[40%]"></div>
                                        <div class="w-2.5 bg-gray-400 rounded-t h-[80%]"></div>
                                        <div class="w-2.5 bg-[#DA291C] rounded-t h-[95%]"></div>
                                    </div>
                                    <div class="border-t border-gray-100 pt-0.5 flex justify-between text-[5px] text-gray-400 font-bold uppercase">
                                        <span>sul</span>
                                        <span>norte</span>
                                        <span>leste</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Thumbnail 3 -->
                            <div class="border border-gray-200 rounded-xl p-2 bg-[#F8F9FA] flex flex-col gap-1.5 shadow-sm max-w-[200px]">
                                <div class="w-full h-24 bg-white rounded-lg border border-gray-150 overflow-hidden p-1 flex flex-col justify-between">
                                    <span class="text-[7px] font-extrabold text-[#DA291C] uppercase leading-none">Ofertas Box</span>
                                    <div class="flex items-end justify-between gap-1 flex-1 py-1">
                                        <div class="w-2.5 bg-gray-200 rounded-t h-[40%]"></div>
                                        <div class="w-2.5 bg-gray-300 rounded-t h-[55%]"></div>
                                        <div class="w-2.5 bg-[#DA291C] rounded-t h-[80%]"></div>
                                        <div class="w-2.5 bg-gray-350 rounded-t h-[70%]"></div>
                                        <div class="w-2.5 bg-[#A01724] rounded-t h-[85%]"></div>
                                    </div>
                                    <div class="border-t border-gray-100 pt-0.5 flex justify-between text-[5px] text-gray-400 font-bold uppercase">
                                        <span>vendas</span>
                                        <span>metas</span>
                                        <span>prev</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif(!empty($solicitation->file_path))
                    <div class="mt-2 flex flex-col gap-2">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                            Imagens e documentos
                        </span>
                        <div class="flex flex-wrap gap-3">
                            @foreach($solicitation->file_path as $path)
                                @php
                                    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                    $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                @endphp
                                <a href="{{ asset('storage/' . $path) }}" target="_blank" class="border border-gray-200 rounded-xl p-2 bg-[#F8F9FA] hover:bg-gray-100 transition-colors flex items-center gap-2 max-w-[240px] shadow-sm font-bold text-xs text-gray-700">
                                    @if($isImg)
                                        <img src="{{ asset('storage/' . $path) }}" class="w-10 h-10 object-cover rounded-lg">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-[#DA291C]">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5-3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <span class="truncate max-w-[150px]">{{ basename($path) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Checklists (Histórico de Encerramentos / Transferências) -->
            @foreach($solicitation->checklists as $checklist)
                <div class="border border-gray-200/80 rounded-2xl p-5 bg-white shadow-sm flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <!-- User Avatar -->
                        <div class="w-9 h-9 rounded-full bg-red-50 flex items-center justify-center text-[#DA291C] font-bold text-sm">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($checklist->atendente->name) }}&background=EAA8A8&color=86131E&bold=true&rounded=true" class="w-full h-full rounded-full object-cover">
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-extrabold text-gray-800 tracking-tight">
                                {{ strtoupper($checklist->atendente->name) }} - SUPORTE PRISMA
                            </span>
                            <span class="text-[10px] font-bold text-gray-400 mt-0.5">
                                {{ $checklist->created_at->format('d/m/Y \À\S H\Hi') }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5 mt-1">
                        <h3 class="text-[#DA291C] font-extrabold text-base leading-snug">
                            {{ $checklist->solucao_aplicada === 'encaminhado' ? 'Transferência de departamento' : 'Checklist de Solução' }}
                        </h3>
                        <p class="text-sm font-semibold text-gray-700 leading-relaxed">
                            {{ $checklist->descricao }}
                        </p>
                        @if($checklist->encaminhamento)
                            <div class="mt-1.5 inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-gray-50 border border-gray-150 text-[10px] font-extrabold uppercase text-gray-500 tracking-wider w-fit">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                                </svg>
                                Encaminhado para: {{ $checklist->encaminhamento }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Seção de Mensagens Regulares do Chat da Solicitação -->
            @foreach($solicitation->messages as $msg)
                <div class="border border-gray-200/80 rounded-2xl p-5 bg-white shadow-sm flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <!-- User Avatar -->
                        @php
                            $userBg = $msg->user->role === 'atendente' ? 'EAA8A8' : 'D1E7DD';
                            $userColor = $msg->user->role === 'atendente' ? '86131E' : '0F5132';
                        @endphp
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-gray-500 font-bold text-sm">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($msg->user->name) }}&background={{ $userBg }}&color={{ $userColor }}&bold=true&rounded=true" class="w-full h-full rounded-full object-cover">
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-extrabold text-gray-800 tracking-tight">
                                {{ strtoupper($msg->user->name) }} - {{ $msg->user->role === 'atendente' ? 'SUPORTE PRISMA' : 'PLANEJAMENTO CLARO' }}
                            </span>
                            <span class="text-[10px] font-bold text-gray-400 mt-0.5">
                                {{ $msg->created_at->format('d/m/Y \À\S H\Hi') }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5 mt-1">
                        <p class="text-sm font-semibold text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $msg->text }}</p>
                        @if($msg->file_path)
                            @php
                                $ext = strtolower(pathinfo($msg->file_path, PATHINFO_EXTENSION));
                                $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            @endphp
                            <div class="mt-2">
                                <a href="{{ asset('storage/' . $msg->file_path) }}" target="_blank" class="border border-gray-200 rounded-xl p-2 bg-[#F8F9FA] hover:bg-gray-100 transition-colors flex items-center gap-2 max-w-[240px] shadow-sm font-bold text-xs text-gray-700">
                                    @if($isImg)
                                        <img src="{{ asset('storage/' . $msg->file_path) }}" class="w-10 h-10 object-cover rounded-lg">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-[#DA291C]">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5-3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <span class="truncate max-w-[150px]">{{ $msg->file_name ?? basename($msg->file_path) }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

        </div>

        <!-- Formulário de Nova Mensagem/Notificação -->
        <form id="new-notification-form" class="mt-4 flex flex-col gap-4 border-t border-gray-100 pt-6">
            <!-- Barra de Ferramentas / Rich Text Editor Visual Mock -->
            <div class="flex flex-col rounded-xl overflow-hidden border border-gray-200">
                <div class="bg-[#F8F9FA] border-b border-gray-200 px-4 py-2.5 flex flex-wrap items-center gap-3.5 select-none">
                    <!-- Text formatting buttons -->
                    <button type="button" class="text-gray-500 hover:text-gray-800 transition-colors p-1" title="Tamanho do Texto">
                        <span class="font-extrabold text-xs">T</span>
                    </button>
                    <!-- Color Picker Dot -->
                    <button type="button" class="w-3.5 h-3.5 rounded-full bg-black hover:scale-105 transition-transform" title="Cor do Texto"></button>
                    <div class="w-px h-4 bg-gray-300"></div>
                    
                    <!-- Bold -->
                    <button type="button" class="text-gray-500 hover:text-gray-800 font-bold transition-colors p-0.5" title="Negrito">
                        <span class="font-extrabold text-xs">B</span>
                    </button>
                    <!-- Italic -->
                    <button type="button" class="text-gray-500 hover:text-gray-800 italic transition-colors p-0.5" title="Itálico">
                        <span class="font-extrabold text-xs">I</span>
                    </button>
                    <!-- Underline -->
                    <button type="button" class="text-gray-500 hover:text-gray-800 underline transition-colors p-0.5" title="Sublinhado">
                        <span class="font-extrabold text-xs">U</span>
                    </button>
                    <!-- Strikethrough -->
                    <button type="button" class="text-gray-500 hover:text-gray-800 line-through transition-colors p-0.5" title="Riscado">
                        <span class="font-extrabold text-xs">S</span>
                    </button>
                    <div class="w-px h-4 bg-gray-300"></div>

                    <!-- Alignment -->
                    <button type="button" class="text-gray-500 hover:text-gray-800 transition-colors" title="Alinhar à Esquerda">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" /></svg>
                    </button>
                    <button type="button" class="text-gray-500 hover:text-gray-800 transition-colors" title="Alinhar ao Centro">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h11.25m-11.25 5.25h16.5" /></svg>
                    </button>
                    <button type="button" class="text-gray-500 hover:text-gray-800 transition-colors" title="Alinhar à Direita">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M7.5 12h12.75m-12.75 5.25h12.75" /></svg>
                    </button>
                    <div class="w-px h-4 bg-gray-300"></div>

                    <!-- Attach / Link -->
                    <button type="button" class="text-gray-500 hover:text-gray-800 transition-colors" title="Remover formatação">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </button>
                    <button type="button" class="text-gray-500 hover:text-gray-800 transition-colors" title="Listas">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                    </button>
                    <div class="w-px h-4 bg-gray-300"></div>

                    <button type="button" class="text-gray-500 hover:text-gray-800 transition-colors" title="Inserir Imagem">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                    </button>
                    <button type="button" class="text-gray-500 hover:text-gray-800 transition-colors" title="Inserir Link">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" /></svg>
                    </button>
                </div>
                
                <!-- Textarea -->
                <textarea 
                    id="notification-text" 
                    placeholder="Texto da notificação"
                    class="w-full min-h-[140px] px-4 py-3 bg-white text-sm text-gray-700 placeholder-gray-400 focus:outline-none font-semibold resize-none"
                    required
                ></textarea>
            </div>

            <!-- Upload do arquivo -->
            <div>
                <label 
                    for="notification-file-input" 
                    class="w-full flex flex-col items-center justify-center py-4 px-6 border-2 border-dashed border-gray-300 hover:border-[#DA291C] rounded-xl bg-gray-50 hover:bg-red-50/5 cursor-pointer transition-all select-none"
                >
                    <span class="text-xs font-bold text-gray-500" id="file-label-text">
                        Arraste ou selecione um arquivo <span class="italic font-normal text-gray-400">(opcional)</span>
                    </span>
                    <input 
                        type="file" 
                        name="file" 
                        id="notification-file-input" 
                        class="hidden"
                        onchange="handleFileSelected(this)"
                    >
                </label>
                <!-- Attached Files Tag Preview -->
                <div id="attached-files-preview" class="hidden flex-wrap gap-2 mt-3"></div>
            </div>

            <!-- Botões de Ação -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Enviar -->
                <button 
                    type="submit" 
                    class="px-8 py-3 bg-[#DA291C] hover:bg-[#B31D14] text-white text-xs font-extrabold rounded-lg transition-all shadow-md cursor-pointer border-0"
                >
                    Enviar
                </button>
                <!-- Cancelar -->
                <button 
                    type="button" 
                    onclick="clearNotificationForm()"
                    class="px-8 py-3 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-800 text-xs font-extrabold rounded-lg transition-all cursor-pointer"
                >
                    Cancelar
                </button>
                <!-- Encerrar Ticket -->
                <button 
                    type="button" 
                    onclick="openChecklistModal()"
                    class="px-8 py-3 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-gray-800 text-xs font-extrabold rounded-lg transition-all cursor-pointer flex items-center gap-2"
                >
                    <!-- Stop Circle Icon -->
                    <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Encerrar ticket</span>
                </button>
            </div>
        </form>

    </div>

    <!-- Checklist de Solução Modal -->
    <div id="checklist-modal" class="fixed inset-0 z-[113] hidden bg-black/60 backdrop-blur-sm items-center justify-center p-4 select-none">
        <div id="checklist-modal-content" class="checklist-figma-modal w-full max-w-[596px] rounded-[24px] shadow-2xl p-6 md:p-7 relative flex flex-col gap-6 transform scale-95 opacity-0 transition-all duration-300">
            <button type="button" onclick="closeChecklistModal()" class="absolute top-5 right-5 text-gray-500 hover:text-gray-700 transition-colors border-0 bg-transparent">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>

            <h3 class="checklist-figma-title" style="font-family: 'AMX', sans-serif;">Checklist de Solução</h3>

            <div class="flex flex-col gap-5 text-left">
                <div class="flex flex-col gap-2.5">
                    <p class="checklist-figma-question" style="font-family: 'AMX', sans-serif;">O problema foi identificado?</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 checklist-compact-options" id="checklist-problema-options">
                        <button type="button" data-group="problema" data-value="sim" class="checklist-option checklist-option-row" style="font-family: 'AMX', sans-serif;">
                            <span class="checklist-option-dot w-4 h-4 rounded-full border-2 border-[#B7B7BF] flex items-center justify-center flex-shrink-0">
                                <span class="checklist-option-dot-inner w-2 h-2 rounded-full bg-transparent"></span>
                            </span>
                            <span>Sim</span>
                        </button>
                        <button type="button" data-group="problema" data-value="nao" class="checklist-option checklist-option-row" style="font-family: 'AMX', sans-serif;">
                            <span class="checklist-option-dot w-4 h-4 rounded-full border-2 border-[#B7B7BF] flex items-center justify-center flex-shrink-0">
                                <span class="checklist-option-dot-inner w-2 h-2 rounded-full bg-transparent"></span>
                            </span>
                            <span>Não</span>
                        </button>
                        <button type="button" data-group="problema" data-value="parcialmente" class="checklist-option checklist-option-row" style="font-family: 'AMX', sans-serif;">
                            <span class="checklist-option-dot w-4 h-4 rounded-full border-2 border-[#B7B7BF] flex items-center justify-center flex-shrink-0">
                                <span class="checklist-option-dot-inner w-2 h-2 rounded-full bg-transparent"></span>
                            </span>
                            <span>Parcialmente</span>
                        </button>
                    </div>
                </div>

                <div class="flex flex-col gap-2.5">
                    <p class="checklist-figma-question" style="font-family: 'AMX', sans-serif;">A solução foi aplicada?</p>
                    <div class="grid grid-cols-1 gap-2.5 checklist-compact-options" id="checklist-solucao-options">
                        <button type="button" data-group="solucao" data-value="sim" class="checklist-option checklist-option-row" style="font-family: 'AMX', sans-serif;">
                            <span class="checklist-option-dot w-4 h-4 rounded-full border-2 border-[#B7B7BF] flex items-center justify-center flex-shrink-0">
                                <span class="checklist-option-dot-inner w-2 h-2 rounded-full bg-transparent"></span>
                            </span>
                            <span class="text-left">Sim, resolvido neste atendimento</span>
                        </button>
                        <button type="button" data-group="solucao" data-value="encaminhado" class="checklist-option checklist-option-row" style="font-family: 'AMX', sans-serif;">
                            <span class="checklist-option-dot w-4 h-4 rounded-full border-2 border-[#B7B7BF] flex items-center justify-center flex-shrink-0">
                                <span class="checklist-option-dot-inner w-2 h-2 rounded-full bg-transparent"></span>
                            </span>
                            <span class="text-left">Encaminhado para outro setor/fila/pessoa</span>
                        </button>
                        <button type="button" data-group="solucao" data-value="nao_resolvida" class="checklist-option checklist-option-row" style="font-family: 'AMX', sans-serif;">
                            <span class="checklist-option-dot w-4 h-4 rounded-full border-2 border-[#B7B7BF] flex items-center justify-center flex-shrink-0">
                                <span class="checklist-option-dot-inner w-2 h-2 rounded-full bg-transparent"></span>
                            </span>
                            <span class="text-left">Não foi possível resolver</span>
                        </button>
                    </div>
                </div>

                <div id="checklist-encaminhamento-wrapper" class="hidden flex-col gap-1.5">
                    <label for="checklist-encaminhamento" class="checklist-figma-label" style="font-family: 'AMX', sans-serif;">Setor/fila/pessoa de encaminhamento</label>
                    <input id="checklist-encaminhamento" type="text" maxlength="255" class="w-full h-11 rounded-[12px] border border-[#B9BEC8] bg-[#F4F4F6] px-3 text-sm font-semibold text-gray-700 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#DA291C]" placeholder="Informe o setor/fila para qual foi encaminhado o caso">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="checklist-descricao" class="checklist-figma-label" style="font-family: 'AMX', sans-serif;">Descreva o atendimento</label>
                    <textarea id="checklist-descricao" class="w-full min-h-[100px] rounded-[12px] border border-[#B9BEC8] bg-[#F4F4F6] px-3 py-3 text-sm font-semibold text-gray-700 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#DA291C]" placeholder="Descreva o que foi analisado e quais ações foram tomadas"></textarea>
                </div>
            </div>

            <button id="checklist-submit-btn" type="button" onclick="submitChecklistModal()" disabled class="checklist-submit-btn checklist-submit-disabled w-full" style="font-family: 'AMX', sans-serif;">
                Enviar resposta
            </button>

            <button type="button" onclick="closeChecklistModal()" class="checklist-cancel-btn hover:text-gray-700 transition-colors border-0 bg-transparent" style="font-family: 'AMX', sans-serif;">
                Cancelar
            </button>
        </div>
    </div>

    <!-- Dropdown de Transferência Flutuante (usado pelo botão transferir topo) -->
    <div id="transfer-dropdown-show" class="hidden fixed bg-[#2D2D30] text-white rounded-2xl shadow-2xl border border-white/10 flex flex-col z-[100] w-72 max-h-[480px] overflow-hidden">
        <div class="p-3 border-b border-white/10">
            <input 
                id="transfer-search-show" 
                type="text" 
                placeholder="Pesquisar..." 
                class="w-full h-9 bg-white/10 border border-white/10 rounded-xl px-3 text-xs text-white placeholder-white/50 focus:outline-none focus:ring-1 focus:ring-[#DA291C]"
                oninput="filterTransferMenuShow(this.value)"
            >
        </div>
        <div class="overflow-y-auto p-2 flex-1 flex flex-col gap-2">
            <!-- Setores -->
            <div class="flex flex-col">
                <span class="text-[9px] font-extrabold text-white/40 uppercase tracking-widest px-3 py-1">Setores</span>
                <div class="flex flex-col gap-0.5">
                    <button onclick="transferTo('Setor', 'Técnico')" class="transfer-item-show w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold text-white/90 hover:bg-white/10 hover:text-white transition-all bg-transparent border-0 cursor-pointer">Setor Técnico</button>
                    <button onclick="transferTo('Setor', 'Comercial')" class="transfer-item-show w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold text-white/90 hover:bg-white/10 hover:text-white transition-all bg-transparent border-0 cursor-pointer">Setor Comercial</button>
                    <button onclick="transferTo('Setor', 'Suporte')" class="transfer-item-show w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold text-white/90 hover:bg-white/10 hover:text-white transition-all bg-transparent border-0 cursor-pointer">Setor Suporte</button>
                </div>
            </div>
            <!-- Filas -->
            <div class="flex flex-col">
                <span class="text-[9px] font-extrabold text-white/40 uppercase tracking-widest px-3 py-1">Filas</span>
                <div class="flex flex-col gap-0.5">
                    <button onclick="transferTo('Fila 1', 'Nível 1')" class="transfer-item-show w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold text-white/90 hover:bg-white/10 hover:text-white transition-all bg-transparent border-0 cursor-pointer">Fila 1: Nível 1</button>
                    <button onclick="transferTo('Fila 1', 'Nível 2')" class="transfer-item-show w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold text-white/90 hover:bg-white/10 hover:text-white transition-all bg-transparent border-0 cursor-pointer">Fila 1: Nível 2</button>
                    <button onclick="transferTo('Fila 2', 'Nível 1')" class="transfer-item-show w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold text-white/90 hover:bg-white/10 hover:text-white transition-all bg-transparent border-0 cursor-pointer">Fila 2: Nível 1</button>
                    <button onclick="transferTo('Fila 2', 'Nível 2')" class="transfer-item-show w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold text-white/90 hover:bg-white/10 hover:text-white transition-all bg-transparent border-0 cursor-pointer">Fila 2: Nível 2</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast de Confirmação -->
    <div id="toast-message" class="fixed bottom-6 right-6 z-[120] bg-gray-900 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-lg flex items-center gap-2 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
        </svg>
        <span id="toast-text">Mensagem enviada!</span>
    </div>

    <!-- Styles dedicados ao Modal do Checklist (Fidelidade ao Figma) -->
    <style>
        .checklist-figma-modal {
            background: #ececef;
            max-width: 640px;
            padding: 18px 22px 20px;
            gap: 12px;
        }

        .checklist-figma-title {
            font-size: 32px;
            line-height: 1.1;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #d7281f;
            text-align: center;
        }

        .checklist-figma-question {
            font-size: 17px;
            line-height: 1.15;
            font-weight: 700;
            color: #1f2937;
        }

        .checklist-figma-label {
            font-size: 11px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #4b5563;
        }

        .checklist-compact-options {
            display: flex !important;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .checklist-option-row {
            min-height: 28px;
            height: 28px;
            padding: 0 8px;
            border: none;
            border-radius: 4px;
            background: transparent;
            color: #5b5b5b;
            font-size: 13px;
            font-weight: 500;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: flex-start;
            transition: all 160ms ease;
            cursor: pointer;
        }

        .checklist-option-row:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        .checklist-option-dot {
            position: static;
            width: 12px !important;
            height: 12px !important;
            border-radius: 9999px;
            border: 1.5px solid #c3c3c8 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 6px;
            flex-shrink: 0;
        }

        .checklist-option-dot-inner {
            width: 6px !important;
            height: 6px !important;
            border-radius: 9999px;
            background: transparent;
        }

        .checklist-option-row.checklist-selected {
            color: #404040;
            font-weight: 600;
        }

        .checklist-option-row.checklist-selected .checklist-option-dot {
            border-color: #da291c !important;
        }

        .checklist-option-row.checklist-selected .checklist-option-dot-inner {
            background: #da291c;
        }

        .checklist-submit-btn {
            height: 44px;
            border-radius: 12px;
            font-size: 16px;
            line-height: 1;
            font-weight: 800;
            color: #fff;
            transition: all 180ms ease;
            background: linear-gradient(90deg, #a01724 0%, #da291c 100%);
            cursor: pointer;
            border: 0;
        }

        .checklist-submit-btn.checklist-submit-disabled {
            background: #d99395;
            opacity: 1;
            cursor: not-allowed;
        }

        .checklist-cancel-btn {
            font-size: 14px;
            line-height: 1;
            font-weight: 500;
            text-align: center;
            color: #6b7280;
            cursor: pointer;
        }
    </style>

    <script>
        const solicitationId = '{{ $solicitation->id }}';

        // Mensagens Toast
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast-message');
            const toastText = document.getElementById('toast-text');
            toastText.textContent = message;
            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3000);
        }

        // Copiar ID do Ticket
        function copyTicketNumber(number) {
            navigator.clipboard.writeText(number).then(() => {
                showToast('ID do chamado copiado!');
            });
        }

        // Upload de Arquivos
        let selectedFile = null;
        function handleFileSelected(input) {
            const preview = document.getElementById('attached-files-preview');
            preview.innerHTML = '';
            
            if (input.files && input.files[0]) {
                selectedFile = input.files[0];
                preview.classList.remove('hidden');
                preview.classList.add('flex');
                
                const tag = document.createElement('div');
                tag.className = "flex items-center gap-1.5 px-3 py-1.5 border border-gray-200 rounded-lg bg-white text-xs font-bold text-gray-750 shadow-sm";
                tag.innerHTML = `
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5-3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <span class="truncate max-w-[150px]">${selectedFile.name}</span>
                    <button type="button" onclick="removeSelectedFile()" class="text-gray-400 hover:text-red-500 transition-colors ml-1 cursor-pointer border-0 bg-transparent">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                `;
                preview.appendChild(tag);
            } else {
                selectedFile = null;
                preview.classList.add('hidden');
            }
        }

        function removeSelectedFile() {
            const input = document.getElementById('notification-file-input');
            input.value = '';
            selectedFile = null;
            document.getElementById('attached-files-preview').classList.add('hidden');
        }

        // Limpar Formulário de Notificação
        function clearNotificationForm() {
            document.getElementById('notification-text').value = '';
            removeSelectedFile();
        }

        // Enviar Mensagem/Notificação
        document.getElementById('new-notification-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const text = document.getElementById('notification-text').value.trim();
            if (!text && !selectedFile) {
                showToast('Escreva alguma mensagem antes de enviar!');
                return;
            }

            const formData = new FormData();
            formData.append('text', text);
            if (selectedFile) {
                formData.append('file', selectedFile);
            }

            fetch(`/solicitations/${solicitationId}/messages`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.error || 'Erro ao enviar.');
                }
                return data;
            })
            .then(() => {
                showToast('Mensagem enviada com sucesso!');
                clearNotificationForm();
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            })
            .catch(err => {
                showToast(err.message || 'Erro ao enviar notificação.');
            });
        });

        // Controle do Modal de Checklist
        const checklistState = {
            problema: null,
            solucao: null,
            encaminhamento: '',
            descricao: ''
        };

        function openChecklistModal() {
            const modal = document.getElementById('checklist-modal');
            const content = document.getElementById('checklist-modal-content');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeChecklistModal() {
            const modal = document.getElementById('checklist-modal');
            const content = document.getElementById('checklist-modal-content');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                resetChecklistState();
            }, 300);
        }

        function resetChecklistState() {
            checklistState.problema = null;
            checklistState.solucao = null;
            checklistState.encaminhamento = '';
            checklistState.descricao = '';
            
            document.querySelectorAll('.checklist-option').forEach(btn => {
                btn.classList.remove('checklist-selected');
            });
            document.getElementById('checklist-encaminhamento-wrapper').classList.add('hidden');
            document.getElementById('checklist-encaminhamento').value = '';
            document.getElementById('checklist-descricao').value = '';
            updateChecklistSubmitButton();
        }

        function updateChecklistSubmitButton() {
            const btn = document.getElementById('checklist-submit-btn');
            const isProblemaFilled = !!checklistState.problema;
            const isSolucaoFilled = !!checklistState.solucao;
            const isDescricaoFilled = checklistState.descricao.trim().length > 0;
            const isEncaminhamentoOk = (checklistState.solucao !== 'encaminhado') || (checklistState.encaminhamento.trim().length > 0);

            if (isProblemaFilled && isSolucaoFilled && isDescricaoFilled && isEncaminhamentoOk) {
                btn.disabled = false;
                btn.classList.remove('checklist-submit-disabled');
            } else {
                btn.disabled = true;
                btn.classList.add('checklist-submit-disabled');
            }
        }

        // Adiciona listeners para opções do checklist
        document.querySelectorAll('.checklist-option').forEach(btn => {
            btn.addEventListener('click', function() {
                const group = this.dataset.group;
                const value = this.dataset.value;

                // Desmarcar outros do mesmo grupo
                document.querySelectorAll(`.checklist-option[data-group="${group}"]`).forEach(sibling => {
                    sibling.classList.remove('checklist-selected');
                });

                this.classList.add('checklist-selected');
                
                if (group === 'problema') {
                    checklistState.problema = value;
                } else if (group === 'solucao') {
                    checklistState.solucao = value;
                    const wrapper = document.getElementById('checklist-encaminhamento-wrapper');
                    if (value === 'encaminhado') {
                        wrapper.classList.remove('hidden');
                    } else {
                        wrapper.classList.add('hidden');
                        checklistState.encaminhamento = '';
                        document.getElementById('checklist-encaminhamento').value = '';
                    }
                }
                updateChecklistSubmitButton();
            });
        });

        document.getElementById('checklist-encaminhamento').addEventListener('input', function() {
            checklistState.encaminhamento = this.value;
            updateChecklistSubmitButton();
        });

        document.getElementById('checklist-descricao').addEventListener('input', function() {
            checklistState.descricao = this.value;
            updateChecklistSubmitButton();
        });

        function submitChecklistModal() {
            const payload = {
                problema_identificado: checklistState.problema,
                solucao_aplicada: checklistState.solucao,
                encaminhamento: checklistState.encaminhamento.trim(),
                descricao: checklistState.descricao.trim()
            };

            const submitBtn = document.getElementById('checklist-submit-btn');
            submitBtn.disabled = true;
            submitBtn.classList.add('checklist-submit-disabled');

            fetch(`/atendente/solicitations/${solicitationId}/finalizar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Erro ao fechar o chamado.');
                }
                return data;
            })
            .then(() => {
                closeChecklistModal();
                showToast('Checklist enviado e atendimento encerrado.');
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            })
            .catch(err => {
                showToast(err.message || 'Erro ao processar requisição.');
                submitBtn.disabled = false;
                submitBtn.classList.remove('checklist-submit-disabled');
            });
        }

        // Menu de Transferência Rápida Flutuante (botão de topo)
        function openTransferMenuDirectly(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('transfer-dropdown-show');
            const rect = event.currentTarget.getBoundingClientRect();
            
            // Posiciona o menu logo abaixo do botão
            dropdown.style.top = `${rect.bottom + window.scrollY + 6}px`;
            dropdown.style.left = `${rect.left + window.scrollX - 220}px`;
            dropdown.classList.toggle('hidden');
            
            if (!dropdown.classList.contains('hidden')) {
                document.getElementById('transfer-search-show').value = '';
                filterTransferMenuShow('');
                document.getElementById('transfer-search-show').focus();
            }
        }

        function filterTransferMenuShow(query) {
            query = query.toLowerCase().trim();
            document.querySelectorAll('.transfer-item-show').forEach(item => {
                const text = item.textContent.toLowerCase();
                if (query === '' || text.includes(query)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function transferTo(type, name) {
            // Executa transferência rápida simulada abrindo o modal de checklist pré-preenchido para Encaminhado
            document.getElementById('transfer-dropdown-show').classList.add('hidden');
            openChecklistModal();
            
            // Simula clique nas opções
            const problemaSimBtn = document.querySelector('.checklist-option[data-group="problema"][data-value="sim"]');
            if (problemaSimBtn) problemaSimBtn.click();
            
            const solucaoEncBtn = document.querySelector('.checklist-option[data-group="solucao"][data-value="encaminhado"]');
            if (solucaoEncBtn) solucaoEncBtn.click();
            
            const encInput = document.getElementById('checklist-encaminhamento');
            encInput.value = `${type}: ${name}`;
            checklistState.encaminhamento = `${type}: ${name}`;
            
            const descInput = document.getElementById('checklist-descricao');
            descInput.value = `Transferência realizada para ${type}: ${name}.`;
            checklistState.descricao = `Transferência realizada para ${type}: ${name}.`;
            
            updateChecklistSubmitButton();
        }

        // Fechar dropdowns ao clicar fora
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('transfer-dropdown-show');
            if (dropdown && !dropdown.classList.contains('hidden') && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
@endsection
