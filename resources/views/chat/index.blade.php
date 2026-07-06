@extends('layouts.app')

@section('title', 'Mensagens')

@section('content')
    <style>
        /* Hide layout footer and reset main padding for the Chat route to prevent screen-level scrollbar */
        html,
        body,
        .content-wrapper {
            height: 100vh !important;
            overflow: hidden !important;
        }

        main {
            padding: 0 !important;
            height: calc(100vh - 64px) !important;
            overflow: hidden !important;
            display: flex;
            flex-direction: column;
        }

        footer {
            display: none !important;
        }

        /* Custom Red scrollbar (fita vermelha / roll bar) */
        .chat-thread-list::-webkit-scrollbar {
            width: 6px;
        }

        .chat-thread-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-thread-list::-webkit-scrollbar-thumb {
            background: #DA291C;
            border-radius: 3px;
        }

        /* Elegant thin scrollbar for Chat and Info panel */
        .chat-scroll-clean::-webkit-scrollbar {
            width: 6px;
        }

        .chat-scroll-clean::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-scroll-clean::-webkit-scrollbar-thumb {
            background: #E5E7EB;
            border-radius: 3px;
        }

        /* Figma-like red scrollbar for quick replies editor */
        .quick-replies-red-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .quick-replies-red-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .quick-replies-red-scroll::-webkit-scrollbar-thumb {
            background: #DA291C;
            border-radius: 3px;
        }

        /* Custom Message Hover and Dropdown animations */
        .chat-message .message-bubble-content {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .chat-message:hover .message-bubble-content {
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.2s ease-out forwards;
        }

        /* Checklist modal fidelity styles */
        .checklist-figma-modal {
            background: #ececef;
            max-width: 640px;
            padding: 18px 22px 20px;
            gap: 12px;
        }

        .checklist-figma-title {
            font-size: 44px;
            line-height: 1;
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
            padding: 0;
            border: none;
            border-radius: 0;
            background: transparent;
            color: #5b5b5b;
            font-size: 13px;
            font-weight: 500;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: flex-start;
            transition: all 160ms ease;
        }

        .checklist-option-dot {
            position: static;
            width: 10px !important;
            height: 10px !important;
            border-radius: 9999px;
            border: 1.5px solid #c3c3c8 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 4px;
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
        }

        .client-closure-card {
            background: transparent;
            border-radius: 0;
            padding: 4px 0 8px;
            text-align: center;
        }

        .client-closure-text {
            font-size: 12px;
            line-height: 1.35;
            font-style: italic;
            font-weight: 600;
            color: #4b5563;
        }

        .attendant-closure-text {
            font-size: 16px;
            line-height: 1.2;
            font-weight: 700;
            text-align: center;
            background: linear-gradient(90deg, #a01724 0%, #da291c 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .client-evaluate-btn {
            width: 255px;
            height: 34px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(90deg, #a01724 0%, #da291c 100%);
        }

        .evaluation-modal-shell {
            background: #f3f3f3;
            width: 100%;
            max-width: 596px;
            border-radius: 16px;
            padding: 20px 18px 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: relative;
        }

        .evaluation-title {
            font-size: 36px;
            line-height: 1.2;
            font-weight: 700;
            text-align: center;
            letter-spacing: 0;
            background: linear-gradient(90deg, #a01724 0%, #da291c 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .evaluation-subtitle {
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            line-height: 1.3;
            font-weight: 500;
            margin-top: -1px;
        }

        .evaluation-stars-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 4px 0 2px;
        }

        .evaluation-question-row {
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 12px;
            color: #4b5563;
            font-weight: 700;
        }

        .evaluation-question-label {
            color: #1f2937;
            font-size: 22px;
            font-weight: 800;
            line-height: 1;
        }

        .evaluation-choice-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 500;
            color: #6b7280;
            transition: color 120ms ease;
        }

        .evaluation-choice-btn:hover {
            color: #374151;
        }

        .evaluation-choice-dot {
            width: 8px;
            height: 8px;
            border-radius: 9999px;
            border: 1px solid #9ca3af;
        }

        .evaluation-star {
            width: 40px;
            height: 40px;
            color: #6b7280;
            transition: all 140ms ease;
        }

        .evaluation-star path {
            fill: transparent;
            stroke: currentColor;
            stroke-width: 1.4;
        }

        .evaluation-star.active {
            color: #d0a30b;
        }

        .evaluation-star.active path {
            fill: #f8c948;
            stroke: #d0a30b;
        }

        .evaluation-submit-btn {
            width: 100%;
            height: 36px;
            border-radius: 8px;
            font-size: 24px;
            line-height: 1;
            font-weight: 800;
            background: linear-gradient(90deg, #a01724 0%, #da291c 100%);
            color: #fff;
        }

        .evaluation-submit-btn:disabled {
            background: #d99395;
            cursor: not-allowed;
        }

        .evaluation-cancel-btn {
            text-align: center;
            font-size: 18px;
            color: #4b5563;
            font-weight: 500;
            line-height: 1;
        }

        @media (max-width: 640px) {
            .client-closure-text {
                font-size: 11px;
            }

            .client-evaluate-btn {
                width: 220px;
            }

            .evaluation-modal-shell {
                max-width: 360px;
                padding: 14px 14px 12px;
                gap: 8px;
            }

            .evaluation-title {
                font-size: 28px;
            }

            .evaluation-subtitle {
                font-size: 11px;
            }

            .evaluation-stars-wrap {
                gap: 8px;
            }

            .evaluation-star {
                width: 30px;
                height: 30px;
            }

            .evaluation-question-row {
                flex-wrap: wrap;
                gap: 4px 8px;
            }

            .evaluation-question-label {
                width: 100%;
                text-align: center;
                font-size: 16px;
            }

            .evaluation-submit-btn {
                font-size: 18px;
                height: 36px;
            }

            .evaluation-cancel-btn {
                font-size: 14px;
            }
        }

        /* ============================================================
           VIDEO CALL CARD STYLES
        ============================================================ */
        .videocall-card {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            border-radius: 20px;
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            min-width: 240px;
            max-width: 300px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
            position: relative;
            overflow: hidden;
        }

        .videocall-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 70% 20%, rgba(218, 41, 28, 0.18) 0%, transparent 60%);
            pointer-events: none;
        }

        .videocall-card-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #DA291C, #ff6b5b);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(218, 41, 28, 0.4);
            animation: vcPulse 2s ease-in-out infinite;
        }

        @keyframes vcPulse {

            0%,
            100% {
                box-shadow: 0 4px 16px rgba(218, 41, 28, 0.4);
                transform: scale(1);
            }

            50% {
                box-shadow: 0 6px 24px rgba(218, 41, 28, 0.7);
                transform: scale(1.05);
            }
        }

        .videocall-card-title {
            font-size: 14px;
            font-weight: 800;
            color: #ffffff;
            text-align: center;
            line-height: 1.2;
        }

        .videocall-card-subtitle {
            font-size: 10px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.55);
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .videocall-join-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(90deg, #DA291C 0%, #ff6b5b 100%);
            color: #fff;
            font-size: 13px;
            font-weight: 800;
            padding: 10px 24px;
            border-radius: 50px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(218, 41, 28, 0.35);
        }

        .videocall-join-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(218, 41, 28, 0.5);
            color: #fff;
        }

        .videocall-join-btn:active {
            transform: translateY(0);
        }

        .videocall-initiating {
            opacity: 0.7;
            pointer-events: none;
        }

        #videocall-toast {
            position: fixed;
            bottom: 32px;
            right: 32px;
            background: #1a1a2e;
            color: #fff;
            padding: 14px 20px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            z-index: 9999;
            transform: translateY(80px);
            opacity: 0;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #videocall-toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        /* ESTADO ENCERRADO */
        .videocall-card.ended {
            background: linear-gradient(135deg, #2b2b2b 0%, #1f1f1f 100%);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .videocall-card.ended::before {
            background: none;
        }

        .videocall-card.ended .videocall-card-icon {
            background: #555;
            box-shadow: none;
            animation: none;
        }

        .videocall-end-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #ff6b5b;
            font-size: 11px;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 4px;
        }

        .videocall-end-btn:hover {
            background: rgba(218, 41, 28, 0.15);
            border-color: rgba(218, 41, 28, 0.3);
        }
    </style>

    <div class="w-full flex-1 bg-[#F5F6F8] flex items-stretch select-none overflow-hidden min-h-0"
        style="font-family: 'AMX', sans-serif;">

        <!-- COLUNA ESQUERDA: LISTA DE CONVERSAS -->
        <div
            class="w-[320px] lg:w-[350px] bg-white flex flex-col flex-shrink-0 relative border-r border-gray-200 select-none overflow-hidden">

            <!-- Título e Botão Nova Conversa -->
            <div class="p-6 pb-3 flex flex-col gap-1">
                <span class="text-[11px] font-bold text-gray-400 tracking-wider">Claro Prisma > Mensagens</span>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl lg:text-3xl font-extrabold tracking-tight text-[#DA291C] flex items-center gap-2">
                        Mensagens
                    </h1>
                    <button onclick="openSupportModal()"
                        class="w-6 h-6 rounded-full bg-[#DA291C] hover:bg-[#B31D14] text-white flex items-center justify-center transition-all cursor-pointer shadow-sm active:scale-95 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                            stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                </div>
            </div>
            <!-- Barra de Pesquisa -->
            <div class="px-6 mb-4">
                <div class="relative">
                    <input id="chat-search-input" oninput="applyChatFilters()" type="text" placeholder="Pesquisar mensagens"
                        class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-full text-sm placeholder-gray-400 focus:outline-none focus:border-[#DA291C] focus:ring-1 focus:ring-[#DA291C] transition-all bg-white">
                    <div class="absolute right-3.5 top-2 w-5 h-5 flex items-center justify-center opacity-60">
                        <img src="/icones/Icone Buscar.png" alt="Buscar" class="w-full h-full object-contain">
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="px-6 mb-4">
                <div class="bg-gray-200/70 p-1 rounded-xl flex items-center justify-between gap-1">
                    <button id="filter-all-btn" onclick="setChatFilter('all')"
                        class="flex-1 py-1.5 rounded-lg text-xs font-bold bg-[#DA291C] text-white transition-all focus:outline-none cursor-pointer text-center shadow-sm">
                        Todas mensagens
                    </button>
                    <button id="filter-unread-btn" onclick="setChatFilter('unread')"
                        class="flex-1 py-1.5 rounded-lg text-xs font-bold bg-transparent text-gray-600 hover:text-gray-900 transition-all focus:outline-none cursor-pointer text-center">
                        Mensagens não lidas
                    </button>
                </div>
            </div>

            <!-- Lista de Threads (com Roll Bar Vermelho personalizado e itens edge-to-edge) -->
            <div class="flex-1 overflow-y-auto pb-6 chat-thread-list border-t border-gray-200">
                @forelse($solicitations as $sol)
                    @php
                        $isActive = ($activeSolicitation && $activeSolicitation->id === $sol->id);
                        $hasUnread = ($sol->unread_messages_count ?? 0) > 0;
                        $cleanDesc = str_contains($sol->description, '] - ') ? explode('] - ', $sol->description, 2)[1] : $sol->description;
                    @endphp
                    <a href="{{ auth()->user()->role === 'atendente' ? route('atendente.chat.index', $sol->id) : route('chat.index', $sol->id) }}"
                        class="chat-thread-item flex items-center gap-3 px-5 py-4 transition-all cursor-pointer select-none border-b border-gray-300 {{ $isActive ? 'bg-[#2E2E2E] text-white' : 'bg-[#E5E5E5] text-gray-800 hover:bg-[#DCDCDC]' }}"
                        data-unread="{{ $hasUnread ? 'true' : 'false' }}" data-title="{{ strtolower($sol->title) }}"
                        data-desc="{{ strtolower($cleanDesc) }}">
                        <!-- Avatar / Ícone -->
                        @php
                            $targetUser = null;
                            if (auth()->user()->role === 'atendente') {
                                // O atendente vê a foto do cliente que enviou a solicitação
                                $targetUser = $sol->user;
                            } else {
                                // O cliente vê a foto do atendente atribuído à solicitação
                                $targetUser = $sol->atendente;
                            }
                        @endphp

                        @if(!$targetUser && auth()->user()->role === 'user')
                            <!-- Se o cliente não tiver atendente ainda, mostra ícone padrão da Claro -->
                            <div
                                class="w-12 h-12 rounded-full bg-white flex items-center justify-center flex-shrink-0 border p-2.5 {{ $isActive ? 'border-white shadow-sm' : 'border-gray-300 shadow-sm' }}">
                                <img src="/icones/Icone Ticket.png" alt="Ticket" class="w-full h-full object-contain">
                            </div>
                        @else
                            @php
                                $avatarName = $targetUser ? $targetUser->name : ($sol->user->name ?? 'Usuário');
                                $avatarBg = ($targetUser && $targetUser->role === 'atendente') ? 'EAA8A8' : 'D1E7DD';
                                $avatarColor = ($targetUser && $targetUser->role === 'atendente') ? '86131E' : '0F5132';
                            @endphp
                            <div class="relative flex-shrink-0 w-12 h-12">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($avatarName) }}&background={{ $avatarBg }}&color={{ $avatarColor }}&bold=true&rounded=true"
                                    alt="Avatar"
                                    class="w-12 h-12 rounded-full object-cover border {{ $isActive ? 'border-white' : 'border-gray-400' }}">
                                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full">
                                </div>
                            </div>
                        @endif

                        <!-- Detalhes do Chat -->
                        <div class="flex-1 min-w-0">
                            <h4
                                class="text-[13px] font-extrabold truncate pr-1 {{ $isActive ? 'text-white' : 'text-gray-900' }}">
                                {{ $sol->title }}
                            </h4>
                            <p class="text-xs truncate font-medium mt-0.5 {{ $isActive ? 'text-gray-300' : 'text-gray-600' }}">
                                {{ $cleanDesc }}
                            </p>
                        </div>

                        <!-- Data e Notificações (Alinhados à direita) -->
                        <div class="flex flex-col items-end gap-1.5 flex-shrink-0 font-bold">
                            @php
                                $displayTime = $sol->updated_at ?? $sol->created_at;
                            @endphp
                            <span class="text-[10px] {{ $isActive ? 'text-gray-400' : 'text-gray-500' }}">
                                {{ $displayTime->format('d/m') }}
                            </span>
                            @if($hasUnread)
                                <div
                                    class="w-4 h-4 bg-[#DA291C] rounded-full flex items-center justify-center text-[9px] font-bold text-white shadow-sm">
                                    {{ $sol->unread_messages_count }}
                                </div>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="text-center py-12 px-6">
                        <p class="text-sm text-gray-400 font-medium">Nenhum chamado aberto.</p>
                    </div>
                @endforelse
            </div>
        </div>


        <!-- CONTAINER PARA O CONTEÚDO FLUTUANTE (CENTRO E DIREITA) -->
        <div class="flex-1 p-6 flex justify-between items-stretch gap-6 overflow-hidden min-h-0">

            <!-- COLUNA CENTRAL: JANELA DE CHAT -->
            <div
                class="flex-1 min-w-[500px] bg-white rounded-[24px] shadow-md flex flex-col justify-between border border-gray-200/80 overflow-hidden pb-4 min-h-0">
                @if($activeSolicitation)
                    <!-- Header do Chat -->
                    <div class="h-16 px-6 bg-white border-b border-gray-100 flex items-center justify-between select-none">
                        <div class="flex items-center gap-3">
                            @php
                                $activeAvatarUser = (auth()->user()->role === 'atendente') ? ($activeSolicitation->user ?? null) : ($activeSolicitation->atendente ?? null);
                                $activeAvatarName = $activeAvatarUser ? $activeAvatarUser->name : 'Suporte Claro';
                                $activeAvatarBg = ($activeAvatarUser && $activeAvatarUser->role === 'atendente') ? 'EAA8A8' : 'D1E7DD';
                                $activeAvatarColor = ($activeAvatarUser && $activeAvatarUser->role === 'atendente') ? '86131E' : '0F5132';
                            @endphp
                            @if(!$activeAvatarUser && auth()->user()->role === 'user')
                                <!-- Ícone de ticket padrão Claro se o cliente ainda não tiver atendente -->
                                <div
                                    class="w-10 h-10 rounded-full bg-white flex items-center justify-center flex-shrink-0 border p-2 shadow-sm">
                                    <img src="/icones/Icone Ticket.png" alt="Ticket" class="w-full h-full object-contain">
                                </div>
                            @else
                                <div class="relative w-10 h-10 flex-shrink-0">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($activeAvatarName) }}&background={{ $activeAvatarBg }}&color={{ $activeAvatarColor }}&bold=true&rounded=true"
                                        alt="Avatar" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                    <div
                                        class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full">
                                    </div>
                                </div>
                            @endif
                            <div class="flex items-center gap-1.5">
                                <h2 class="text-sm font-extrabold text-gray-800 uppercase tracking-tight">
                                    @if(auth()->user()->role === 'atendente')
                                        {{ strtoupper($activeSolicitation->user->name ?? 'MAURO FILHO') }}
                                    @else
                                        {{ strtoupper($activeSolicitation->atendente->name ?? 'SUPORTE PRISMA') }}
                                    @endif
                                </h2>
                                <span
                                    class="w-4 h-4 bg-[#DA291C] rounded-full flex items-center justify-center text-[9px] font-bold text-white">3</span>
                                <span class="text-xs font-bold text-gray-550">@</span>
                                <span id="current-ticket-tag"
                                    class="hidden items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold text-white uppercase"></span>
                            </div>
                        </div>

                        <!-- Ações do Header -->
                        <div class="flex items-center gap-4 text-gray-700">
                            @if(auth()->user()->role === 'atendente')
                                <!-- 1. Swap/Transfer -->
                                <div class="relative">
                                    <button id="btn-transfer-menu" onclick="toggleTransferMenu(event)"
                                        class="hover:opacity-85 transition-opacity focus:outline-none cursor-pointer flex items-center justify-center"
                                        title="Transferir chamado">
                                        <img src="/icones/Icone Transferir.png" alt="Transferir" class="w-5.5 h-5.5 object-contain">
                                    </button>

                                    <!-- Dropdown de Transferência -->
                                    <div id="transfer-dropdown"
                                        class="hidden absolute top-full right-0 mt-3 z-50 w-72 bg-[#2D2D30] text-white rounded-2xl shadow-2xl border border-white/10 flex flex-col max-h-[480px] overflow-hidden transition-all duration-200">
                                        <!-- Campo de Pesquisa Interna -->
                                        <div class="p-3 border-b border-white/5">
                                            <div class="relative">
                                                <input id="transfer-search" type="text" placeholder="Pesquisar"
                                                    class="w-full h-8 rounded-full bg-[#1E1E20] border border-white/20 pl-4 pr-9 text-xs text-white placeholder-white/40 focus:outline-none focus:border-[#DA291C] focus:ring-1 focus:ring-[#DA291C]"
                                                    oninput="filterTransferMenu(this.value)">
                                                <div class="absolute right-3.5 top-2 w-4 h-4 opacity-40">
                                                    <img src="/icones/Icone Buscar.png" alt="Buscar"
                                                        class="w-full h-full object-contain invert">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Lista de Itens (Scrollable) -->
                                        <div
                                            class="flex-1 overflow-y-auto p-3 space-y-4 chat-scroll-clean text-left select-none max-h-[380px]">
                                            <!-- Setor -->
                                            <div>
                                                <div
                                                    class="flex items-center gap-2 text-white/50 text-[10px] font-extrabold tracking-wider uppercase mb-1.5 px-2">
                                                    <svg class="w-3.5 h-3.5 text-white/50" fill="none" stroke="currentColor"
                                                        stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                                    </svg>
                                                    Setor
                                                </div>
                                                <div class="space-y-0.5">
                                                    <button onclick="transferTo('Setor', 'Técnico')"
                                                        class="transfer-item w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold text-white/90 hover:bg-white/10 hover:text-white transition-all">
                                                        Técnico
                                                    </button>
                                                    <button onclick="transferTo('Setor', 'Comercial')"
                                                        class="transfer-item w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold text-white/90 hover:bg-white/10 hover:text-white transition-all">
                                                        Comercial
                                                    </button>
                                                    <button onclick="transferTo('Setor', 'Suporte')"
                                                        class="transfer-item w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold text-white/90 hover:bg-white/10 hover:text-white transition-all">
                                                        Suporte
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Filas -->
                                            <div>
                                                <div
                                                    class="flex items-center gap-2 text-white/50 text-[10px] font-extrabold tracking-wider uppercase mb-1.5 px-2">
                                                    <svg class="w-3.5 h-3.5 text-white/50" fill="none" stroke="currentColor"
                                                        stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.625-5.25L12 3m0 0-1.125 1.5M12 3v13.5m0-13.5h.008v.008H12V3Zm0 13.5v.75m0-.75a2.25 2.25 0 0 1-2.25-2.25v-.75m2.25 3a2.25 2.25 0 0 0 2.25-2.25v-.75" />
                                                    </svg>
                                                    Filas
                                                </div>
                                                <div class="space-y-0.5">
                                                    <!-- Fila 1 -->
                                                    <div>
                                                        <div onclick="toggleSubmenu('fila-1-sub', event)"
                                                            class="transfer-item flex items-center justify-between w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold text-white/90 hover:bg-white/10 hover:text-white transition-all cursor-pointer">
                                                            <span>Fila 1</span>
                                                            <svg class="w-3 h-3 text-white/60 transition-transform duration-200"
                                                                fill="none" stroke="currentColor" stroke-width="2.5"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                            </svg>
                                                        </div>
                                                        <div id="fila-1-sub"
                                                            class="hidden pl-4 pr-1 py-1 space-y-0.5 bg-black/10 rounded-lg mt-0.5">
                                                            <button onclick="transferTo('Fila 1', 'Nível 1')"
                                                                class="w-full text-left px-2.5 py-1 rounded text-[11px] font-semibold text-white/80 hover:bg-white/10 hover:text-white transition-all">Nível
                                                                1</button>
                                                            <button onclick="transferTo('Fila 1', 'Nível 2')"
                                                                class="w-full text-left px-2.5 py-1 rounded text-[11px] font-semibold text-white/80 hover:bg-white/10 hover:text-white transition-all">Nível
                                                                2</button>
                                                        </div>
                                                    </div>
                                                    <!-- Fila 2 -->
                                                    <div>
                                                        <div onclick="toggleSubmenu('fila-2-sub', event)"
                                                            class="transfer-item flex items-center justify-between w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold text-white/90 hover:bg-white/10 hover:text-white transition-all cursor-pointer">
                                                            <span>Fila 2</span>
                                                            <svg class="w-3 h-3 text-white/60 transition-transform duration-200"
                                                                fill="none" stroke="currentColor" stroke-width="2.5"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                            </svg>
                                                        </div>
                                                        <div id="fila-2-sub"
                                                            class="hidden pl-4 pr-1 py-1 space-y-0.5 bg-black/10 rounded-lg mt-0.5">
                                                            <button onclick="transferTo('Fila 2', 'Nível 1')"
                                                                class="w-full text-left px-2.5 py-1 rounded text-[11px] font-semibold text-white/80 hover:bg-white/10 hover:text-white transition-all">Nível
                                                                1</button>
                                                            <button onclick="transferTo('Fila 2', 'Nível 2')"
                                                                class="w-full text-left px-2.5 py-1 rounded text-[11px] font-semibold text-white/80 hover:bg-white/10 hover:text-white transition-all">Nível
                                                                2</button>
                                                        </div>
                                                    </div>
                                                    <!-- Fila 3 -->
                                                    <div>
                                                        <div onclick="toggleSubmenu('fila-3-sub', event)"
                                                            class="transfer-item flex items-center justify-between w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold text-white/90 hover:bg-white/10 hover:text-white transition-all cursor-pointer">
                                                            <span>Fila 3</span>
                                                            <svg class="w-3 h-3 text-white/60 transition-transform duration-200"
                                                                fill="none" stroke="currentColor" stroke-width="2.5"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                            </svg>
                                                        </div>
                                                        <div id="fila-3-sub"
                                                            class="hidden pl-4 pr-1 py-1 space-y-0.5 bg-black/10 rounded-lg mt-0.5">
                                                            <button onclick="transferTo('Fila 3', 'Nível 1')"
                                                                class="w-full text-left px-2.5 py-1 rounded text-[11px] font-semibold text-white/80 hover:bg-white/10 hover:text-white transition-all">Nível
                                                                1</button>
                                                            <button onclick="transferTo('Fila 3', 'Nível 2')"
                                                                class="w-full text-left px-2.5 py-1 rounded text-[11px] font-semibold text-white/80 hover:bg-white/10 hover:text-white transition-all">Nível
                                                                2</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Pessoas -->
                                            <div>
                                                <div
                                                    class="flex items-center gap-2 text-white/50 text-[10px] font-extrabold tracking-wider uppercase mb-1.5 px-2">
                                                    <svg class="w-3.5 h-3.5 text-white/50" fill="none" stroke="currentColor"
                                                        stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                                    </svg>
                                                    Pessoas
                                                </div>
                                                <div class="space-y-0.5">
                                                    <!-- Ana Silva -->
                                                    <button onclick="transferTo('Pessoa', 'ANA SILVA')"
                                                        class="transfer-item w-full flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white/90 hover:bg-white/10 hover:text-white transition-all">
                                                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&auto=format&fit=crop&q=80"
                                                            alt="Ana Silva" class="w-6 h-6 rounded-full object-cover">
                                                        <span>ANA SILVA</span>
                                                    </button>
                                                    <!-- Abner Junior -->
                                                    <button onclick="transferTo('Pessoa', 'ABNER JÚNIOR')"
                                                        class="transfer-item w-full flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white/90 hover:bg-white/10 hover:text-white transition-all">
                                                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&auto=format&fit=crop&q=80"
                                                            alt="Abner Junior" class="w-6 h-6 rounded-full object-cover">
                                                        <span>ABNER JÚNIOR</span>
                                                    </button>
                                                    <!-- Bruna Ferreira -->
                                                    <button onclick="transferTo('Pessoa', 'BRUNA FERREIRA')"
                                                        class="transfer-item w-full flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white/90 hover:bg-white/10 hover:text-white transition-all">
                                                        <div
                                                            class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0 text-[10px] font-bold text-white">
                                                            BF</div>
                                                        <span>BRUNA FERREIRA</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- 2. PIP/Expand -->
                                <button class="hover:text-[#DA291C] transition-colors focus:outline-none cursor-pointer"
                                    title="Minimizar/Expandir">
                                    <svg class="w-5.5 h-5.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2.2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25A2.25 2.25 0 0 1 5.25 3h13.5A2.25 2.25 0 0 1 21 5.25Zm-9.5-3v14.25" />
                                    </svg>
                                </button>
                                <!-- 3. Tag/Label -->
                                <button id="tag-menu-trigger" onclick="toggleTagMenu(event)"
                                    class="hover:opacity-85 transition-opacity focus:outline-none cursor-pointer flex items-center justify-center"
                                    title="Adicionar etiqueta">
                                    <img src="/icones/Icone Tag.png" alt="Tag" class="w-5.5 h-5.5 object-contain">
                                </button>
                                <!-- 4. Pesquisar -->
                                <button onclick="toggleChatSearch()"
                                    class="hover:opacity-85 transition-opacity focus:outline-none cursor-pointer flex items-center justify-center"
                                    title="Pesquisar mensagens">
                                    <img src="/icones/Icone Buscar.png" alt="Buscar" class="w-5 h-5 object-contain">
                                </button>
                                <!-- 5. Chamada de vídeo -->
                                <button id="btn-videocall-atendente" onclick="initiateVideoCall({{ $activeSolicitation->id }})"
                                    class="hover:opacity-85 transition-opacity focus:outline-none cursor-pointer flex items-center justify-center {{ $activeSolicitation->status === 'na_fila' ? 'hidden' : '' }}"
                                    title="Iniciar chamada de vídeo">
                                    <img src="/icones/Icone Video Chamada.png" alt="Vídeo Chamada"
                                        class="w-5.5 h-5.5 object-contain">
                                </button>
                                <!-- 6. Chamada de voz -->
                                <button
                                    class="hover:opacity-85 transition-opacity focus:outline-none cursor-pointer relative flex items-center justify-center"
                                    title="Iniciar chamada de voz">
                                    <img src="/icones/Icone Chamada.png" alt="Chamada de Voz" class="w-5.5 h-5.5 object-contain">
                                    <span
                                        class="absolute -top-1.2 -right-1 text-[9px] font-extrabold text-gray-700 bg-white rounded-full px-0.5 border border-white">+</span>
                                </button>
                                <!-- 7. Finalizar atendimento -->
                                <button type="button" onclick="openChecklistModal()"
                                    class="hover:text-[#DA291C] transition-colors focus:outline-none cursor-pointer"
                                    title="Finalizar atendimento">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2"
                                        stroke="currentColor" class="w-5.5 h-5.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75 11.25 15 15 9.75m6 2.25a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </button>
                            @else
                                <button onclick="toggleChatSearch()"
                                    class="hover:opacity-85 transition-opacity focus:outline-none cursor-pointer flex items-center justify-center"
                                    title="Pesquisar mensagens">
                                    <img src="/icones/Icone Buscar.png" alt="Buscar" class="w-5 h-5 object-contain">
                                </button>
                                <button id="btn-videocall-user" onclick="initiateVideoCall({{ $activeSolicitation->id }})"
                                    class="hover:opacity-85 transition-opacity focus:outline-none cursor-pointer flex items-center justify-center {{ $activeSolicitation->status === 'na_fila' ? 'hidden' : '' }}"
                                    title="Iniciar chamada de vídeo">
                                    <img src="/icones/Icone Video Chamada.png" alt="Vídeo Chamada"
                                        class="w-5.5 h-5.5 object-contain">
                                </button>
                                <button
                                    class="hover:opacity-85 transition-opacity focus:outline-none cursor-pointer relative flex items-center justify-center"
                                    title="Iniciar chamada de voz">
                                    <img src="/icones/Icone Chamada.png" alt="Chamada de Voz" class="w-5.5 h-5.5 object-contain">
                                    <span
                                        class="absolute -top-1.2 -right-1 text-[9px] font-extrabold text-gray-700 bg-white rounded-full px-0.5 border border-white">+</span>
                                </button>
                            @endif
                        </div>
                    </div>
                    <!-- Barra de Busca de Mensagens Interna -->
                    <div id="chat-search-bar"
                        class="hidden px-6 py-3 bg-[#EDEDED] border-b border-gray-300 flex items-center justify-between gap-4 transition-all">
                        <div class="relative flex-1">
                            <input id="chat-search-input" type="text" oninput="filterChatMessages(this.value)"
                                placeholder="Buscar nas mensagens deste chat..."
                                class="w-full pl-9 pr-9 py-1.5 border border-gray-300 rounded-full text-xs placeholder-gray-400 focus:outline-none focus:border-[#DA291C] focus:ring-1 focus:ring-[#DA291C] transition-all bg-white">
                            <div class="absolute left-3.5 top-2.5 w-3.5 h-3.5 opacity-40">
                                <img src="/icones/Icone Buscar.png" alt="Buscar" class="w-full h-full object-contain">
                            </div>
                            <button id="chat-search-clear"
                                onclick="document.getElementById('chat-search-input').value = ''; filterChatMessages(''); document.getElementById('chat-search-input').focus();"
                                class="absolute right-3.5 top-2 text-gray-400 hover:text-gray-600 focus:outline-none hidden">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <button onclick="toggleChatSearch()"
                            class="text-xs text-gray-500 hover:text-gray-800 font-extrabold transition-colors">
                            FECHAR
                        </button>
                    </div>
                    <!-- Área de Mensagens (Scrollable) -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-6 chat-scroll-clean" id="chat-messages-container">
                        @if($activeSolicitation)
                            @php
                                $solicitationFiles = ($activeSolicitation && $activeSolicitation->file_path && is_array($activeSolicitation->file_path)) ? $activeSolicitation->file_path : [];
                                $messageFiles = ($activeSolicitation && $activeSolicitation->messages) ? $activeSolicitation->messages->where('file_path', '!=', '')->whereNotNull('file_path')->pluck('file_path')->toArray() : [];
                                $totalAttachmentsCount = count($solicitationFiles) + count($messageFiles);
                            @endphp
                            <!-- Mensagem de Abertura (Descrição da Demanda) -->
                            <div class="group flex items-center {{ auth()->user()->role === 'atendente' ? 'justify-start mr-auto' : 'justify-end ml-auto' }} gap-3 max-w-[85%] relative chat-message"
                                data-message-id="opening">
                                <!-- Bubble wrapper -->
                                <div
                                    class="flex flex-col {{ auth()->user()->role === 'atendente' ? 'items-start' : 'items-end' }} gap-1 max-w-[90%] relative">
                                    <div
                                        class="relative p-4 {{ auth()->user()->role === 'atendente' ? 'bg-[#EDEDED] text-gray-800 rounded-2xl rounded-tl-none border border-transparent' : 'text-white bg-[#DA291C] rounded-2xl rounded-tr-none' }} shadow-md flex flex-col gap-2 pr-8 message-bubble-content transition-all duration-300">
                                        <span class="message-text">
                                            @if(str_contains($activeSolicitation->description, '] - '))
                                                {{ explode('] - ', $activeSolicitation->description, 2)[1] }}
                                            @else
                                                {{ $activeSolicitation->description }}
                                            @endif
                                        </span>
                                        <div
                                            class="flex justify-between items-center text-[9px] font-extrabold tracking-wider {{ auth()->user()->role === 'atendente' ? 'text-gray-500 border-gray-300/30' : 'text-white/90 border-white/10' }} uppercase border-t pt-1.5 gap-4">
                                            <span
                                                class="message-sender">{{ strtoupper($activeSolicitation->user->name ?? 'Cliente') }}</span>
                                            <span
                                                class="message-time">{{ $activeSolicitation->created_at->format('d/m - H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Anexos de Abertura (se houver) -->
                            @if($activeSolicitation->file_path && is_array($activeSolicitation->file_path) && count($activeSolicitation->file_path) > 0)
                                <div
                                    class="flex flex-col {{ auth()->user()->role === 'atendente' ? 'items-start mr-auto' : 'items-end ml-auto' }} gap-2 max-w-[80%] mt-2 chat-message">
                                    @foreach($activeSolicitation->file_path as $path)
                                        @php
                                            $extension = pathinfo($path, PATHINFO_EXTENSION);
                                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        @endphp
                                        <div
                                            class="p-3 {{ auth()->user()->role === 'atendente' ? 'bg-[#EDEDED] text-gray-800 border border-gray-300/30' : 'bg-[#DA291C] text-white' }} rounded-2xl shadow-md flex flex-col gap-2 max-w-sm">
                                            @if($isImage)
                                                <a href="javascript:void(0)" onclick="openImageLightbox('{{ Storage::url($path) }}')">
                                                    <img src="{{ Storage::url($path) }}" alt="Preview"
                                                        class="max-w-[200px] rounded-lg hover:opacity-95 transition-opacity cursor-pointer">
                                                </a>
                                            @else
                                                <div
                                                    class="p-4 flex items-center gap-3 {{ auth()->user()->role === 'atendente' ? 'bg-black/5 text-gray-800' : 'bg-white/15 text-white' }} rounded-xl">
                                                    <svg class="w-8 h-8 {{ auth()->user()->role === 'atendente' ? 'text-gray-600' : 'text-white' }}"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5-3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                    </svg>
                                                    <div
                                                        class="text-left {{ auth()->user()->role === 'atendente' ? 'text-gray-800' : 'text-white' }}">
                                                        <p class="text-xs font-bold truncate max-w-[150px]">{{ basename($path) }}</p>
                                                        <a href="{{ Storage::url($path) }}" download
                                                            class="{{ auth()->user()->role === 'atendente' ? 'text-gray-600 hover:text-gray-850' : 'text-white hover:underline' }} text-[10px] font-bold uppercase">Baixar
                                                            arquivo</a>
                                                    </div>
                                                </div>
                                            @endif
                                            <div
                                                class="flex justify-between items-center text-[9px] font-extrabold tracking-wider {{ auth()->user()->role === 'atendente' ? 'text-gray-500 border-gray-300/30' : 'text-white/90 border-white/10' }} uppercase border-t pt-1.5 gap-4">
                                                <span>{{ strtoupper($activeSolicitation->user->name ?? 'MAURO FILHO') }}</span>
                                                <span>{{ $activeSolicitation->created_at->format('d/m - H:i') }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Mensagem Automática 1 -->
                            <div class="flex flex-col items-center justify-center text-center py-4 px-6 gap-1.5 chat-message">
                                <div class="flex items-center gap-2 text-gray-500 italic text-sm font-semibold justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" class="w-4 h-4 text-gray-400">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    @if(auth()->user()->role === 'atendente')
                                        <span>Chamado ID {{ $activeSolicitation->ticket_number }} atribuído a você para
                                            atendimento.</span>
                                    @else
                                        @if($activeSolicitation->status === 'na_fila')
                                            <span id="queue-status-inline">Chamado recebido ID {{ $activeSolicitation->ticket_number }}!
                                                Você é o {{ $queuePosition ?? 1 }}º na fila. Tempo médio de espera: 20 minutos</span>
                                        @else
                                            <span>Chamado recebido ID {{ $activeSolicitation->ticket_number }}! Aguardando atualização do
                                                atendimento.</span>
                                        @endif
                                    @endif
                                </div>
                                <p class="text-[9px] font-extrabold text-gray-450 tracking-wider uppercase">
                                    MENSAGEM AUTOMÁTICA DO SISTEMA - {{ $activeSolicitation->created_at->format('d/m - H:i') }}
                                </p>
                            </div>

                            <!-- Mensagem Automática 2 -->
                            <div id="sistema-atendimento-iniciado"
                                class="text-center py-1 {{ in_array($activeSolicitation->status, ['na_fila', 'aberta', 'nova']) ? 'hidden' : '' }} chat-message">
                                <p class="text-xs font-bold text-gray-550 italic">
                                    @if(auth()->user()->role === 'atendente')
                                        <span id="atendimento-iniciado-text">Você iniciou o atendimento com
                                            {{ $activeSolicitation->user->name ?? 'Cliente' }}</span>
                                    @else
                                        <span
                                            id="atendimento-iniciado-text">{{ $activeSolicitation->atendente->name ?? 'LUCAS R. - SUPORTE PRISMA' }}
                                            irá dar continuidade ao seu atendimento</span>
                                    @endif
                                </p>
                            </div>

                            <!-- Mensagens do Banco de Dados -->
                            @if($activeSolicitation->messages)
                                @foreach($activeSolicitation->messages as $msg)
                                    @php
                                        $isCurrentUser = $msg->user_id === auth()->id();
                                    @endphp

                                    {{-- Card especial de Videochamada --}}
                                    @if(($msg->type ?? 'text') === 'videocall')
                                        @php
                                            $meta = $msg->metadata ?? [];
                                            $meetUrl = $meta['meet_url'] ?? '#';
                                            $initiatedBy = strtoupper($meta['initiated_by'] ?? $msg->user->name);
                                            $isEnded = ($meta['status'] ?? '') === 'ended';
                                        @endphp
                                        <div class="flex justify-center chat-message" data-message-id="{{ $msg->id }}"
                                            data-videocall-active="{{ $isEnded ? '0' : '1' }}" data-videocall-meet-url="{{ $meetUrl }}"
                                            data-videocall-is-user="{{ $isCurrentUser ? '1' : '0' }}">
                                            <div class="videocall-card {{ $isEnded ? 'ended' : '' }}">
                                                <div class="videocall-card-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" class="w-6 h-6">
                                                        <path
                                                            d="M4.5 4.5A2.25 2.25 0 0 0 2.25 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-10.5A2.25 2.25 0 0 0 15 4.5H4.5ZM19.24 7.63l-2.99 2.24v4.26l2.99 2.24A1.5 1.5 0 0 0 21.75 15.1V8.9a1.5 1.5 0 0 0-2.51-1.27Z" />
                                                    </svg>
                                                </div>
                                                <div class="videocall-card-title">
                                                    {{ $isEnded ? 'Chamada encerrada' : 'Videochamada iniciada' }}
                                                </div>
                                                <div class="videocall-card-subtitle">
                                                    por {{ $initiatedBy }} &bull; {{ $msg->created_at->format('d/m - H:i') }}
                                                </div>
                                                @if(!$isEnded)
                                                    <button type="button" class="videocall-join-btn"
                                                        onclick="joinVideoCall('{{ $meetUrl }}', '{{ route('videocall.join', $msg->id) }}')">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" class="w-4 h-4">
                                                            <path
                                                                d="M4.5 4.5A2.25 2.25 0 0 0 2.25 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-10.5A2.25 2.25 0 0 0 15 4.5H4.5ZM19.24 7.63l-2.99 2.24v4.26l2.99 2.24A1.5 1.5 0 0 0 21.75 15.1V8.9a1.5 1.5 0 0 0-2.51-1.27Z" />
                                                        </svg>
                                                        Entrar na Reunião
                                                    </button>

                                                    {{-- Apenas o atendente ou o criador da chamada pode encerrar --}}
                                                    @if(auth()->user()->role === 'atendente' || $isCurrentUser)
                                                        <button type="button" onclick="showEndCallModal({{ $msg->id }})" class="videocall-end-btn">
                                                            Encerrar chamada
                                                        </button>
                                                    @endif
                                                @else
                                                    <span style="font-size: 11px; color: rgba(255,255,255,0.4); font-weight:700; margin-top:4px;">
                                                        Encerrada por {{ $meta['ended_by'] ?? 'Sistema' }} às
                                                        {{ isset($meta['ended_at']) ? date('H:i', strtotime($meta['ended_at'])) : '' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="group flex items-center {{ $isCurrentUser ? 'justify-end ml-auto' : 'justify-start mr-auto' }} gap-3 max-w-[85%] relative chat-message"
                                            data-message-id="{{ $msg->id }}">

                                            @if($isCurrentUser)
                                                <div
                                                    class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                                                    <button onclick="handleMenuOption('delete')"
                                                        class="text-gray-400 hover:text-red-500 transition-colors p-1.5 rounded-full hover:bg-gray-100 focus:outline-none cursor-pointer"
                                                        title="Apagar">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                            stroke-width="2.2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                        </svg>
                                                    </button>
                                                    <button onclick="handleMenuOption('edit')"
                                                        class="text-gray-400 hover:text-gray-755 transition-colors p-1.5 rounded-full hover:bg-gray-100 focus:outline-none cursor-pointer"
                                                        title="Editar">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                            stroke-width="2.2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                        </svg>
                                                    </button>
                                                    <button onclick="replyToMessage('{{ $msg->id }}')"
                                                        class="text-gray-400 hover:text-gray-750 transition-colors p-1.5 rounded-full hover:bg-gray-100 focus:outline-none cursor-pointer"
                                                        title="Responder">
                                                        <svg class="w-4 h-4 transform scale-x-[-1]" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2.2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endif

                                            <!-- Bubble wrapper -->
                                            <div
                                                class="flex flex-col {{ $isCurrentUser ? 'items-end' : 'items-start' }} gap-1 max-w-[90%] relative">
                                                <div
                                                    class="relative p-4 {{ $isCurrentUser ? 'text-white bg-[#DA291C] rounded-2xl rounded-tr-none' : 'bg-[#EDEDED] text-gray-800 rounded-2xl rounded-tl-none border border-transparent' }} shadow-md flex flex-col gap-2 pr-8 message-bubble-content transition-all duration-300">
                                                    <!-- Chevron button -->
                                                    <button onclick="toggleMessageMenu(event, '{{ $msg->id }}')"
                                                        class="absolute top-3.5 right-3 {{ $isCurrentUser ? 'text-white/70 hover:text-white' : 'text-gray-400 hover:text-gray-750' }} focus:outline-none cursor-pointer transition-opacity opacity-0 group-hover:opacity-100">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                            stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                        </svg>
                                                    </button>

                                                    @if($msg->parent)
                                                        @php
                                                            $replyBg = $isCurrentUser ? 'bg-white/15' : 'bg-black/5';
                                                            $replyTextClass = $isCurrentUser ? 'text-white/80' : 'text-gray-650';
                                                            $replySenderClass = $isCurrentUser ? 'text-white' : 'text-[#DA291C]';
                                                            $borderCol = $isCurrentUser ? 'border-white' : 'border-[#DA291C]';
                                                        @endphp
                                                        <div class="px-3 py-1.5 {{ $replyBg }} rounded-lg border-l-2 {{ $borderCol }} text-xs mb-1.5 flex flex-col opacity-85 cursor-pointer hover:opacity-100 transition-opacity"
                                                            onclick="scrollToMessage('{{ $msg->parent->id }}')">
                                                            <span
                                                                class="font-extrabold text-[10px] {{ $replySenderClass }} uppercase leading-none mb-0.5">{{ strtoupper($msg->parent->user?->name ?? 'SISTEMA') }}</span>
                                                            <span
                                                                class="truncate {{ $replyTextClass }} leading-tight">{{ $msg->parent->text ?? 'Arquivo' }}</span>
                                                        </div>
                                                    @endif

                                                    @if($msg->file_path)
                                                        @php
                                                            $extension = pathinfo($msg->file_path, PATHINFO_EXTENSION);
                                                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                        @endphp
                                                        @if($isImage)
                                                            <a href="javascript:void(0)"
                                                                onclick="openImageLightbox('{{ asset('storage/' . $msg->file_path) }}')">
                                                                <img src="{{ asset('storage/' . $msg->file_path) }}" alt="Anexo"
                                                                    class="w-full max-w-xs object-cover rounded-xl border {{ $isCurrentUser ? 'border-white/10' : 'border-gray-300/30' }} mb-1 hover:opacity-95 transition-opacity cursor-pointer">
                                                            </a>
                                                        @else
                                                            @php
                                                                $downloadIconColor = $isCurrentUser ? 'text-white' : 'text-gray-700';
                                                                $iconBg = $isCurrentUser ? 'bg-white/15' : 'bg-black/5';
                                                            @endphp
                                                            <a href="{{ asset('storage/' . $msg->file_path) }}" download="{{ $msg->file_name }}"
                                                                class="p-3 flex items-center gap-3 {{ $iconBg }} rounded-xl mb-1 hover:opacity-95 transition-opacity">
                                                                <svg class="w-8 h-8 {{ $downloadIconColor }}" xmlns="http://www.w3.org/2000/svg"
                                                                    fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5-3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                                </svg>
                                                                <div class="text-left {{ $isCurrentUser ? 'text-white' : 'text-gray-800' }}">
                                                                    <p class="text-xs font-bold truncate max-w-[150px]">{{ $msg->file_name }}</p>
                                                                    <span class="text-[10px] font-bold opacity-80 uppercase">Download</span>
                                                                </div>
                                                            </a>
                                                        @endif
                                                    @endif

                                                    @if($msg->text)
                                                        <span class="message-text">{{ $msg->text }}</span>
                                                    @endif

                                                    <div
                                                        class="flex justify-between items-center text-[9px] font-extrabold tracking-wider {{ $isCurrentUser ? 'text-white/90 border-white/10' : 'text-gray-500 border-gray-300/30' }} uppercase border-t pt-1.5 gap-4">
                                                        <span class="message-sender">{{ strtoupper($msg->user->name) }}</span>
                                                        <span class="message-time">
                                                            {{ $msg->created_at->format('d/m - H:i') }}
                                                            @if($msg->updated_at->gt($msg->created_at))
                                                                (EDITADA)
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-1 mt-1 empty:hidden message-reactions-container">
                                                    @if($msg->reactions)
                                                        @foreach($msg->reactions as $emoji => $users)
                                                            @php
                                                                $userReacted = in_array(auth()->id(), $users);
                                                            @endphp
                                                            <button onclick="event.stopPropagation(); removeReaction('{{ $msg->id }}', '{{ $emoji }}')"
                                                                class="flex items-center gap-1.5 px-2.5 py-0.5 rounded-full {{ $userReacted ? 'bg-[#DA291C]/10 border-[#DA291C]/35 text-[#DA291C]' : 'bg-gray-100 border-gray-200 text-gray-700' }} hover:bg-gray-200 border text-xs font-bold transition-all cursor-pointer active:scale-95 focus:outline-none">
                                                                <span>{{ $emoji }}</span> <span
                                                                    class="text-gray-450 font-extrabold text-[10px]">{{ count($users) }}</span>
                                                            </button>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>

                                            @if(!$isCurrentUser)
                                                <div
                                                    class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                                                    <button onclick="reactToMessage(event, '{{ $msg->id }}')"
                                                        class="text-gray-400 hover:text-gray-755 transition-colors p-1.5 rounded-full hover:bg-gray-100 focus:outline-none cursor-pointer"
                                                        title="Reagir">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                            stroke-width="2.2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                                                        </svg>
                                                    </button>
                                                    <button onclick="replyToMessage('{{ $msg->id }}')"
                                                        class="text-gray-400 hover:text-gray-755 transition-colors p-1.5 rounded-full hover:bg-gray-100 focus:outline-none cursor-pointer"
                                                        title="Responder">
                                                        <svg class="w-4 h-4 transform scale-x-[-1]" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2.2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>{{-- /group div mensagem normal --}}
                                    @endif
                                @endforeach
                            @endif

                            <!-- Typing indicator -->
                            <div id="typing-indicator-wrapper"
                                class="flex items-center gap-2 text-gray-400 select-none animate-pulse hidden">
                                @if(auth()->user()->role === 'atendente')
                                    <div
                                        class="relative w-5 h-5 rounded-full bg-gray-150 flex items-center justify-center text-gray-650 border border-gray-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                                            stroke="currentColor" class="w-3 h-3">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-[9px] font-extrabold uppercase tracking-wider text-gray-400">{{ $activeSolicitation->user->name ?? 'Cliente' }}
                                        está digitando...</span>
                                @else
                                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=60"
                                        alt="Lucas Agent" class="w-5 h-5 rounded-full object-cover">
                                    <span class="text-[9px] font-extrabold uppercase tracking-wider text-gray-400">Lucas R. está
                                        digitando...</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Input do Chat -->
                    <div class="px-6 py-2 relative">
                        @php
                            $closedStatuses = ['resolvida', 'nao_resolvida', 'não resolvida'];
                            $isClosedForClient = auth()->user()->role === 'user' && in_array($activeSolicitation->status, $closedStatuses, true);
                            $isClosedForAttendant = auth()->user()->role === 'atendente' && in_array($activeSolicitation->status, $closedStatuses, true);
                            $existingClientEvaluation = auth()->user()->role === 'user'
                                ? $activeSolicitation->evaluations->firstWhere('user_id', auth()->id())
                                : null;
                        @endphp

                        @if(auth()->user()->role === 'atendente' && $activeSolicitation->status === 'na_fila')
                            <!-- Botão Iniciar Atendimento (Atendente) -->
                            <div id="iniciar-atendimento-wrapper"
                                class="flex flex-col items-center justify-center py-4 bg-white select-none">
                                <button type="button" onclick="iniciarAtendimentoChat({{ $activeSolicitation->id }})"
                                    class="px-16 py-3.5 bg-[#DA291C] hover:bg-[#B31D14] text-white font-extrabold rounded-full text-sm uppercase transition-all shadow-md active:scale-95 cursor-pointer text-center animate-pulse"
                                    style="font-family: 'AMX', sans-serif;">
                                    Iniciar atendimento
                                </button>
                            </div>
                        @endif

                        @if(auth()->user()->role === 'user')
                            <div id="client-closure-wrapper" class="{{ $isClosedForClient ? '' : 'hidden' }} mb-3">
                                <div class="client-closure-card" style="font-family: 'AMX', sans-serif;">
                                    <p class="client-closure-text">
                                        O atendimento foi finalizado. Por favor avalie o atendimento clicando no botão abaixo:
                                    </p>

                                    <button id="open-evaluation-btn" type="button" onclick="openEvaluationModal()"
                                        class="mt-2 client-evaluate-btn evaluation-submit-btn {{ $existingClientEvaluation ? 'hidden' : '' }}">
                                        Avaliar
                                    </button>

                                    <p id="evaluation-sent-msg"
                                        class="text-[11px] font-bold text-green-700 {{ $existingClientEvaluation ? '' : 'hidden' }}">
                                        Avaliação enviada. Obrigado pelo feedback!
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if(auth()->user()->role === 'atendente')
                            <div id="attendant-closure-wrapper" class="{{ $isClosedForAttendant ? '' : 'hidden' }} mb-3">
                                <div class="client-closure-card" style="font-family: 'AMX', sans-serif;">
                                    <p class="attendant-closure-text">Atendimento Encerrado</p>
                                </div>
                            </div>
                        @endif

                        <div id="chat-input-form-wrapper"
                            class="{{ ($activeSolicitation->status === 'na_fila' || $isClosedForClient || $isClosedForAttendant) ? 'hidden' : '' }}">
                            <!-- Reply Preview Pill -->
                            <div id="reply-preview-pill"
                                class="hidden items-center justify-between bg-white border border-gray-200 shadow-sm px-4 py-2 rounded-full text-xs font-bold text-gray-700 mb-2 w-max max-w-full animate-fade-in">
                                <div class="flex items-center gap-2 truncate">
                                    <svg class="w-3.5 h-3.5 text-[#DA291C] flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                    </svg>
                                    <span class="truncate text-gray-650">
                                        Em resposta a <span id="reply-sender"
                                            class="font-extrabold text-[#DA291C] uppercase"></span>:
                                        <span id="reply-body" class="font-semibold italic text-gray-500"></span>
                                    </span>
                                </div>
                                <button type="button" onclick="cancelReply(event)"
                                    class="ml-3 text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Edit Preview Pill -->
                            <div id="edit-preview-pill"
                                class="hidden items-center justify-between bg-white border border-gray-200 shadow-sm px-4 py-2 rounded-full text-xs font-bold text-gray-700 mb-2 w-max max-w-full animate-fade-in">
                                <div class="flex items-center gap-2 truncate">
                                    <svg class="w-3.5 h-3.5 text-[#DA291C] flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                    </svg>
                                    <span class="truncate text-gray-650">
                                        Editando mensagem: <span id="edit-body"
                                            class="font-semibold italic text-gray-500"></span>
                                    </span>
                                </div>
                                <button type="button" onclick="cancelEdit(event)"
                                    class="ml-3 text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Attachment Preview Pill -->
                            <div id="attachment-preview-pill"
                                class="hidden items-center justify-between bg-white border border-gray-200 shadow-sm px-4 py-2 rounded-full text-xs font-bold text-gray-700 mb-2 w-max max-w-full animate-fade-in">
                                <div class="flex items-center gap-2 truncate">
                                    <svg class="w-3.5 h-3.5 text-[#DA291C] flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2.2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                                    </svg>
                                    <span class="truncate text-gray-650">
                                        Anexo: <span id="attachment-filename" class="font-extrabold text-[#DA291C]"></span>
                                        <span id="attachment-filesize"
                                            class="text-gray-400 font-semibold text-[10px] ml-1"></span>
                                    </span>
                                </div>
                                <button type="button" onclick="cancelAttachment(event)"
                                    class="ml-3 text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form id="chat-input-form" onsubmit="sendChatMessage(event)"
                                class="m-0 flex items-center justify-between gap-3 px-6 py-3 rounded-full bg-[#EDEDED] focus-within:ring-1 focus-within:ring-[#DA291C]/30 transition-all">
                                <!-- Input de Arquivo Oculto -->
                                <input type="file" id="chat-file-input" class="hidden" onchange="handleFileSelected(event)">

                                <input type="text" id="chat-message-input" placeholder="Digite sua mensagem"
                                    class="flex-1 bg-transparent border-none focus:outline-none focus:ring-0 text-sm text-gray-850 placeholder-gray-500"
                                    style="font-family: 'AMX', sans-serif;">
                                <div class="flex items-center gap-3 text-gray-500 flex-shrink-0">
                                    <!-- Emoji picker trigger -->
                                    <button type="button" onclick="toggleInputEmojiPicker(event)"
                                        class="hover:text-gray-800 transition-colors focus:outline-none cursor-pointer">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                                        </svg>
                                    </button>
                                    @if(auth()->user()->role === 'atendente')
                                        <!-- Quick Replies trigger -->
                                        <button type="button" onclick="toggleQuickRepliesMenu(event)"
                                            class="hover:text-gray-800 transition-colors focus:outline-none cursor-pointer"
                                            title="Respostas rápidas">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M13 3 4 14h6l-1 7 9-11h-6l1-7Z" />
                                            </svg>
                                        </button>
                                    @endif
                                    <!-- Attachment trigger -->
                                    <button type="button" onclick="triggerFileInput()"
                                        class="hover:text-gray-800 transition-colors focus:outline-none cursor-pointer">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                                        </svg>
                                    </button>
                                    <button type="submit"
                                        class="w-8 h-8 rounded-full bg-[#DA291C] hover:bg-[#B31D14] text-white flex items-center justify-center transition-all cursor-pointer shadow-md active:scale-95 focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2.2" stroke="currentColor"
                                            class="w-4 h-4 transform -rotate-45 translate-x-px -translate-y-px">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                                        </svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Popup Seletor de Emojis do Input -->
                    <div id="input-emoji-picker"
                        class="fixed hidden z-[95] bg-[#303030] rounded-2xl shadow-2xl p-3 flex flex-wrap gap-2 border border-white/10 w-48 justify-center select-none animate-fade-in">
                        <button type="button" onclick="insertEmoji('😀')"
                            class="text-lg hover:scale-125 transition-transform focus:outline-none cursor-pointer">😀</button>
                        <button type="button" onclick="insertEmoji('😂')"
                            class="text-lg hover:scale-125 transition-transform focus:outline-none cursor-pointer">😂</button>
                        <button type="button" onclick="insertEmoji('❤️')"
                            class="text-lg hover:scale-125 transition-transform focus:outline-none cursor-pointer">❤️</button>
                        <button type="button" onclick="insertEmoji('👍')"
                            class="text-lg hover:scale-125 transition-transform focus:outline-none cursor-pointer">👍</button>
                        <button type="button" onclick="insertEmoji('🔥')"
                            class="text-lg hover:scale-125 transition-transform focus:outline-none cursor-pointer">🔥</button>
                        <button type="button" onclick="insertEmoji('🎉')"
                            class="text-lg hover:scale-125 transition-transform focus:outline-none cursor-pointer">🎉</button>
                        <button type="button" onclick="insertEmoji('🙏')"
                            class="text-lg hover:scale-125 transition-transform focus:outline-none cursor-pointer">🙏</button>
                        <button type="button" onclick="insertEmoji('🚀')"
                            class="text-lg hover:scale-125 transition-transform focus:outline-none cursor-pointer">🚀</button>
                    </div>

                    <!-- Quick Replies Popup (Atendente) -->
                    @if(auth()->user()->role === 'atendente')
                        <div id="quick-replies-menu"
                            class="fixed hidden z-[98] w-[300px] bg-[#303030] text-white rounded-2xl shadow-2xl p-3 border border-white/10 select-none animate-fade-in">
                            <div class="relative mb-2">
                                <input id="quick-replies-search" type="text" placeholder="Pesquisar"
                                    class="w-full h-8 rounded-full bg-[#3A3A3F] border border-white/20 pl-3 pr-9 text-xs placeholder-white/45 focus:outline-none">
                                <svg class="w-3.5 h-3.5 text-white/45 absolute right-3 top-2.5" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <div id="quick-replies-menu-list" class="max-h-56 overflow-y-auto space-y-1 pr-1 chat-scroll-clean">
                            </div>
                            <button type="button" onclick="openQuickRepliesEditor()"
                                class="mt-2 w-full text-center text-[10px] font-bold text-white/70 hover:text-white transition-colors">
                                Editar respostas rápidas
                            </button>
                        </div>
                    @endif

                    <!-- Tag Menu (Atendente) -->
                    <div id="ticket-tag-menu"
                        class="fixed hidden z-[97] w-60 bg-[#3A3A3F] text-white rounded-2xl shadow-2xl p-3 border border-white/10 select-none animate-fade-in">
                        <button type="button" onclick="openTagEditorModal()"
                            class="w-full flex items-center gap-2 px-2 py-1.5 text-left text-[17px] font-bold hover:bg-white/10 rounded-lg transition-colors">
                            <span class="text-xl leading-none">+</span>
                            <span>Adicionar nova</span>
                        </button>
                        <div id="ticket-tag-list" class="mt-1 space-y-1"></div>
                    </div>

                    <!-- Add/Edit Tag Modal -->
                    <div id="tag-editor-modal"
                        class="fixed inset-0 z-[110] hidden bg-black/60 backdrop-blur-sm items-center justify-center p-4 select-none">
                        <div id="tag-editor-modal-content"
                            class="bg-white w-full max-w-[620px] rounded-[24px] shadow-2xl p-7 relative flex flex-col gap-5 transform scale-95 opacity-0 transition-all duration-300">
                            <button type="button" onclick="closeTagEditorModal()"
                                class="absolute top-5 right-5 text-gray-500 hover:text-gray-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <h3 class="text-[30px] font-extrabold tracking-tight text-[#DA291C]"
                                style="font-family: 'AMX', sans-serif;">Adicionar tag</h3>

                            <input id="tag-name-input" type="text" placeholder="Nome da tag" maxlength="40"
                                class="w-full h-14 px-4 border border-gray-200 rounded-xl text-sm placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#DA291C]">

                            <div class="flex flex-wrap items-center gap-2" id="tag-color-palette"></div>

                            <div class="text-sm font-bold text-[#A01724]" style="font-family: 'AMX', sans-serif;">Personalizar
                                cor</div>

                            <button type="button" onclick="saveTagFromModal()"
                                class="w-full h-14 rounded-2xl text-white text-2xl font-extrabold tracking-tight"
                                style="font-family: 'AMX', sans-serif; background: linear-gradient(89.24deg, #A01724 0%, #DA291C 100%);">
                                Adicionar tag
                            </button>

                            <button type="button" onclick="closeTagEditorModal()"
                                class="text-gray-600 hover:text-gray-800 text-xl font-medium transition-colors"
                                style="font-family: 'AMX', sans-serif;">
                                Cancelar
                            </button>
                        </div>
                    </div>

                    <!-- Add Quick Reply Modal -->
                    <div id="quick-reply-add-modal"
                        class="fixed inset-0 z-[112] hidden bg-black/60 backdrop-blur-sm items-center justify-center p-4 select-none">
                        <div id="quick-reply-add-modal-content"
                            class="bg-white w-full max-w-[640px] rounded-[18px] shadow-2xl p-6 relative flex flex-col gap-3 transform scale-95 opacity-0 transition-all duration-300">
                            <button type="button" onclick="closeQuickReplyAddModal()"
                                class="absolute top-4 right-4 text-black hover:text-gray-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2"
                                    stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <h3 class="text-3xl font-extrabold text-[#DA291C] leading-none">Adicionar resposta rápida</h3>

                            <input id="quick-reply-add-name" type="text"
                                placeholder="Nome da resposta/atalho (disponível só para você)" maxlength="80"
                                class="w-full h-12 rounded-lg border border-gray-300 px-3 text-sm text-gray-700 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#DA291C]">

                            <textarea id="quick-reply-add-text" placeholder="Resposta rápida"
                                class="w-full min-h-[110px] rounded-lg border border-gray-300 px-3 py-3 text-sm text-gray-700 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#DA291C]"></textarea>

                            <button type="button" onclick="submitQuickReplyAdd()"
                                class="w-full h-12 rounded-xl text-white text-xl font-extrabold hover:opacity-95 transition-all"
                                style="background: linear-gradient(89.24deg, #A01724 0%, #DA291C 100%);">
                                Adicionar resposta rápida
                            </button>

                            <button type="button" onclick="closeQuickReplyAddModal()"
                                class="text-gray-700 text-base font-medium hover:text-gray-900 transition-colors">
                                Cancelar
                            </button>
                        </div>
                    </div>

                    <!-- Edit Quick Reply Modal -->
                    <div id="quick-reply-edit-modal"
                        class="fixed inset-0 z-[112] hidden bg-black/60 backdrop-blur-sm items-center justify-center p-4 select-none">
                        <div id="quick-reply-edit-modal-content"
                            class="bg-white w-full max-w-[640px] rounded-[18px] shadow-2xl p-6 relative flex flex-col gap-3 transform scale-95 opacity-0 transition-all duration-300">
                            <button type="button" onclick="closeQuickReplyEditModal()"
                                class="absolute top-4 right-4 text-black hover:text-gray-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2"
                                    stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <h3 class="text-3xl font-extrabold text-[#DA291C] leading-none">Editar resposta rápida</h3>

                            <input id="quick-reply-edit-name" type="text" placeholder="Nome da resposta/atalho" maxlength="80"
                                class="w-full h-12 rounded-lg border border-gray-300 px-3 text-sm text-gray-700 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#DA291C]">

                            <textarea id="quick-reply-edit-text" placeholder="Resposta rápida"
                                class="w-full min-h-[110px] rounded-lg border border-gray-300 px-3 py-3 text-sm text-gray-700 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#DA291C]"></textarea>

                            <button type="button" onclick="submitQuickReplyEdit()"
                                class="w-full h-12 rounded-xl text-white text-xl font-extrabold hover:opacity-95 transition-all"
                                style="background: linear-gradient(89.24deg, #A01724 0%, #DA291C 100%);">
                                Salvar alterações
                            </button>

                            <button type="button" onclick="closeQuickReplyEditModal()"
                                class="text-gray-700 text-base font-medium hover:text-gray-900 transition-colors">
                                Cancelar
                            </button>
                        </div>
                    </div>

                    <!-- Checklist de Solução Modal -->
                    @if(auth()->user()->role === 'atendente')
                        <div id="checklist-modal"
                            class="fixed inset-0 z-[113] hidden bg-black/60 backdrop-blur-sm items-center justify-center p-4 select-none">
                            <div id="checklist-modal-content"
                                class="checklist-figma-modal w-full max-w-[596px] rounded-[24px] shadow-2xl p-6 md:p-7 relative flex flex-col gap-6 transform scale-95 opacity-0 transition-all duration-300">
                                <button type="button" onclick="closeChecklistModal()"
                                    class="absolute top-5 right-5 text-gray-500 hover:text-gray-700 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2"
                                        stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <h3 class="checklist-figma-title" style="font-family: 'AMX', sans-serif;">Checklist de Solução</h3>

                                <div class="flex flex-col gap-5">
                                    <div class="flex flex-col gap-2.5">
                                        <p class="checklist-figma-question" style="font-family: 'AMX', sans-serif;">O problema foi
                                            identificado?</p>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 checklist-compact-options"
                                            id="checklist-problema-options">
                                            <button type="button" data-group="problema" data-value="sim"
                                                class="checklist-option checklist-option-row"
                                                style="font-family: 'AMX', sans-serif;">
                                                <span
                                                    class="checklist-option-dot w-4 h-4 rounded-full border-2 border-[#B7B7BF] flex items-center justify-center flex-shrink-0">
                                                    <span
                                                        class="checklist-option-dot-inner w-2 h-2 rounded-full bg-transparent"></span>
                                                </span>
                                                <span>Sim</span>
                                            </button>
                                            <button type="button" data-group="problema" data-value="nao"
                                                class="checklist-option checklist-option-row"
                                                style="font-family: 'AMX', sans-serif;">
                                                <span
                                                    class="checklist-option-dot w-4 h-4 rounded-full border-2 border-[#B7B7BF] flex items-center justify-center flex-shrink-0">
                                                    <span
                                                        class="checklist-option-dot-inner w-2 h-2 rounded-full bg-transparent"></span>
                                                </span>
                                                <span>Não</span>
                                            </button>
                                            <button type="button" data-group="problema" data-value="parcialmente"
                                                class="checklist-option checklist-option-row"
                                                style="font-family: 'AMX', sans-serif;">
                                                <span
                                                    class="checklist-option-dot w-4 h-4 rounded-full border-2 border-[#B7B7BF] flex items-center justify-center flex-shrink-0">
                                                    <span
                                                        class="checklist-option-dot-inner w-2 h-2 rounded-full bg-transparent"></span>
                                                </span>
                                                <span>Parcialmente</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-2.5">
                                        <p class="checklist-figma-question" style="font-family: 'AMX', sans-serif;">A solução foi
                                            aplicada?</p>
                                        <div class="grid grid-cols-1 gap-2.5 checklist-compact-options"
                                            id="checklist-solucao-options">
                                            <button type="button" data-group="solucao" data-value="sim"
                                                class="checklist-option checklist-option-row"
                                                style="font-family: 'AMX', sans-serif;">
                                                <span
                                                    class="checklist-option-dot w-4 h-4 rounded-full border-2 border-[#B7B7BF] flex items-center justify-center flex-shrink-0">
                                                    <span
                                                        class="checklist-option-dot-inner w-2 h-2 rounded-full bg-transparent"></span>
                                                </span>
                                                <span class="text-left">Sim, resolvido neste atendimento</span>
                                            </button>
                                            <button type="button" data-group="solucao" data-value="encaminhado"
                                                class="checklist-option checklist-option-row"
                                                style="font-family: 'AMX', sans-serif;">
                                                <span
                                                    class="checklist-option-dot w-4 h-4 rounded-full border-2 border-[#B7B7BF] flex items-center justify-center flex-shrink-0">
                                                    <span
                                                        class="checklist-option-dot-inner w-2 h-2 rounded-full bg-transparent"></span>
                                                </span>
                                                <span class="text-left">Encaminhado para outro setor/fila/pessoa</span>
                                            </button>
                                            <button type="button" data-group="solucao" data-value="nao_resolvida"
                                                class="checklist-option checklist-option-row"
                                                style="font-family: 'AMX', sans-serif;">
                                                <span
                                                    class="checklist-option-dot w-4 h-4 rounded-full border-2 border-[#B7B7BF] flex items-center justify-center flex-shrink-0">
                                                    <span
                                                        class="checklist-option-dot-inner w-2 h-2 rounded-full bg-transparent"></span>
                                                </span>
                                                <span class="text-left">Não foi possível resolver</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div id="checklist-encaminhamento-wrapper" class="hidden flex-col gap-1.5">
                                        <label for="checklist-encaminhamento" class="checklist-figma-label"
                                            style="font-family: 'AMX', sans-serif;">Setor/fila/pessoa de encaminhamento</label>
                                        <input id="checklist-encaminhamento" type="text" maxlength="255"
                                            class="w-full h-11 rounded-[12px] border border-[#B9BEC8] bg-[#F4F4F6] px-3 text-sm font-semibold text-gray-700 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#DA291C]"
                                            placeholder="Informe o setor/fila para qual foi encaminhado o caso">
                                    </div>

                                    <div class="flex flex-col gap-1.5">
                                        <label for="checklist-descricao" class="checklist-figma-label"
                                            style="font-family: 'AMX', sans-serif;">Descreva o atendimento</label>
                                        <textarea id="checklist-descricao"
                                            class="w-full min-h-[100px] rounded-[12px] border border-[#B9BEC8] bg-[#F4F4F6] px-3 py-3 text-sm font-semibold text-gray-700 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#DA291C]"
                                            placeholder="Descreva o que foi analisado e quais ações foram tomadas"></textarea>
                                    </div>
                                </div>

                                <button id="checklist-submit-btn" type="button" onclick="submitChecklistModal()" disabled
                                    class="checklist-submit-btn checklist-submit-disabled w-full"
                                    style="font-family: 'AMX', sans-serif;">
                                    Enviar resposta
                                </button>

                                <button type="button" onclick="closeChecklistModal()"
                                    class="checklist-cancel-btn hover:text-gray-700 transition-colors"
                                    style="font-family: 'AMX', sans-serif;">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    @endif

                    @if(auth()->user()->role === 'user')
                        <div id="evaluation-modal"
                            class="fixed inset-0 z-[114] hidden bg-black/60 backdrop-blur-sm items-center justify-center p-4 select-none">
                            <div id="evaluation-modal-content"
                                class="evaluation-modal-shell shadow-2xl transform scale-95 opacity-0 transition-all duration-300"
                                style="font-family: 'AMX', sans-serif;">
                                <button type="button" onclick="closeEvaluationModal()"
                                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2"
                                        stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <h3 class="evaluation-title">Como foi o seu atendimento?</h3>

                                <p class="evaluation-subtitle">
                                    Sua opinião é muito importante para continuarmos melhorando o suporte do PRISMA.
                                </p>

                                <div class="evaluation-stars-wrap" id="evaluation-stars-wrap">
                                    <button type="button" class="evaluation-star" data-rating="1" onclick="setEvaluationRating(1)">
                                        <svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full">
                                            <path
                                                d="M12 2.5l2.95 5.98 6.6.96-4.78 4.66 1.13 6.57L12 17.57l-5.9 3.1 1.13-6.57L2.45 9.44l6.6-.96L12 2.5z" />
                                        </svg>
                                    </button>
                                    <button type="button" class="evaluation-star" data-rating="2" onclick="setEvaluationRating(2)">
                                        <svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full">
                                            <path
                                                d="M12 2.5l2.95 5.98 6.6.96-4.78 4.66 1.13 6.57L12 17.57l-5.9 3.1 1.13-6.57L2.45 9.44l6.6-.96L12 2.5z" />
                                        </svg>
                                    </button>
                                    <button type="button" class="evaluation-star" data-rating="3" onclick="setEvaluationRating(3)">
                                        <svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full">
                                            <path
                                                d="M12 2.5l2.95 5.98 6.6.96-4.78 4.66 1.13 6.57L12 17.57l-5.9 3.1 1.13-6.57L2.45 9.44l6.6-.96L12 2.5z" />
                                        </svg>
                                    </button>
                                    <button type="button" class="evaluation-star" data-rating="4" onclick="setEvaluationRating(4)">
                                        <svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full">
                                            <path
                                                d="M12 2.5l2.95 5.98 6.6.96-4.78 4.66 1.13 6.57L12 17.57l-5.9 3.1 1.13-6.57L2.45 9.44l6.6-.96L12 2.5z" />
                                        </svg>
                                    </button>
                                    <button type="button" class="evaluation-star" data-rating="5" onclick="setEvaluationRating(5)">
                                        <svg viewBox="0 0 24 24" fill="currentColor" class="w-full h-full">
                                            <path
                                                d="M12 2.5l2.95 5.98 6.6.96-4.78 4.66 1.13 6.57L12 17.57l-5.9 3.1 1.13-6.57L2.45 9.44l6.6-.96L12 2.5z" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="evaluation-question-row">
                                    <span class="evaluation-question-label">Seu problema foi resolvido?</span>
                                    <button type="button" id="evaluation-resolved-no" onclick="setEvaluationResolved(false)"
                                        class="evaluation-choice-btn">
                                        <span class="evaluation-choice-dot" id="evaluation-resolved-no-dot"></span>
                                        <span>Não foi resolvido</span>
                                    </button>
                                    <button type="button" id="evaluation-resolved-yes" onclick="setEvaluationResolved(true)"
                                        class="evaluation-choice-btn">
                                        <span class="evaluation-choice-dot" id="evaluation-resolved-yes-dot"></span>
                                        <span>Sim, foi resolvido</span>
                                    </button>
                                </div>

                                <textarea id="evaluation-comment"
                                    class="w-full min-h-[74px] rounded-[8px] border border-[#B9BEC8] bg-[#F6F6F6] px-3 py-2.5 text-[12px] text-gray-700 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#DA291C]"
                                    placeholder="Deixe um comentário sobre o atendimento recebido"></textarea>

                                <button id="evaluation-submit-btn" type="button" onclick="submitEvaluation()" disabled
                                    class="evaluation-submit-btn">
                                    Enviar avaliação
                                </button>

                                <button type="button" onclick="closeEvaluationModal()"
                                    class="evaluation-cancel-btn hover:text-gray-900 transition-colors">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Custom Message Context Menu -->
                    <div id="message-context-menu"
                        class="fixed hidden z-[90] w-52 bg-[#303030] text-white rounded-2xl shadow-2xl py-2 flex flex-col gap-0.5 select-none transition-all duration-100"
                        style="font-family: 'AMX', sans-serif;">
                        <button onclick="handleMenuOption('info')"
                            class="flex items-center gap-3 px-4 py-2 text-xs font-bold text-left hover:bg-white/10 transition-colors w-full cursor-pointer">
                            <svg class="w-4 h-4 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11.25 11.25l.041-.02a.75.75 0 111.063.852l-.708 2.836a.75.75 0 001.063.852l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                            <span>Dados da mensagem</span>
                        </button>
                        <button onclick="handleMenuOption('edit')"
                            class="flex items-center gap-3 px-4 py-2 text-xs font-bold text-left hover:bg-white/10 transition-colors w-full cursor-pointer">
                            <svg class="w-4 h-4 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                            </svg>
                            <span>Editar</span>
                        </button>
                        <button onclick="handleMenuOption('reply')"
                            class="flex items-center gap-3 px-4 py-2 text-xs font-bold text-left hover:bg-white/10 transition-colors w-full cursor-pointer">
                            <svg class="w-4 h-4 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                            </svg>
                            <span>Responder</span>
                        </button>
                        <button onclick="handleMenuOption('react_trigger', event)" id="menu-react-btn"
                            class="flex items-center gap-3 px-4 py-2 text-xs font-bold text-left hover:bg-white/10 transition-colors w-full cursor-pointer justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                                </svg>
                                <span>Reagir</span>
                            </div>
                            <span class="text-[10px] text-white/45">➔</span>
                        </button>
                        <button onclick="handleMenuOption('copy')"
                            class="flex items-center gap-3 px-4 py-2 text-xs font-bold text-left hover:bg-white/10 transition-colors w-full cursor-pointer">
                            <svg class="w-4 h-4 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H5.25m11.9-3.664A2.251 2.251 0 0015 2.25h-1.5a2.251 2.251 0 00-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M19.5 3.577a2.25 2.25 0 011.955 2.186v11.487m0 0a2.25 2.25 0 01-1.955 2.186m0-15.859A2.25 2.25 0 0018 2.25h-1.5A2.25 2.25 0 0014.25 4.5v.75m4.5 0a2.25 2.25 0 01-2.25 2.25h-3a2.25 2.25 0 01-2.25-2.25v-.75" />
                            </svg>
                            <span>Copiar</span>
                        </button>
                        <button onclick="handleMenuOption('delete')"
                            class="flex items-center gap-3 px-4 py-2 text-xs font-bold text-left hover:bg-white/10 text-red-400 hover:text-red-350 transition-colors w-full cursor-pointer">
                            <svg class="w-4 h-4 text-red-400/80" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            <span>Apagar</span>
                        </button>
                    </div>

                    <!-- Emoji Reaction Popup Sub-menu -->
                    <div id="emoji-reaction-popup"
                        class="fixed hidden z-[95] bg-[#303030] rounded-full shadow-2xl px-3 py-1.5 flex items-center gap-2 border border-white/10">
                        <button onclick="handleReactionSelect('👍')"
                            class="text-base hover:scale-125 transition-transform focus:outline-none cursor-pointer">👍</button>
                        <button onclick="handleReactionSelect('❤️')"
                            class="text-base hover:scale-125 transition-transform focus:outline-none cursor-pointer">❤️</button>
                        <button onclick="handleReactionSelect('😂')"
                            class="text-base hover:scale-125 transition-transform focus:outline-none cursor-pointer">😂</button>
                        <button onclick="handleReactionSelect('😮')"
                            class="text-base hover:scale-125 transition-transform focus:outline-none cursor-pointer">😮</button>
                        <button onclick="handleReactionSelect('😢')"
                            class="text-base hover:scale-125 transition-transform focus:outline-none cursor-pointer">😢</button>
                        <button onclick="handleReactionSelect('🙏')"
                            class="text-base hover:scale-125 transition-transform focus:outline-none cursor-pointer">🙏</button>
                    </div>

                    <!-- Modal Dados da Mensagem -->
                    <div id="msg-info-modal"
                        class="fixed inset-0 z-[100] hidden bg-black/60 backdrop-blur-sm items-center justify-center p-4 select-none">
                        <div id="msg-info-modal-content"
                            class="bg-white w-full max-w-[400px] rounded-2xl shadow-2xl p-6 relative flex flex-col gap-4 transform scale-95 opacity-0 transition-all duration-300">
                            <button onclick="closeMsgInfoModal()"
                                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors focus:outline-none cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                    stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <h3 class="text-base font-extrabold text-[#DA291C] uppercase tracking-wider"
                                style="font-family: 'AMX', sans-serif;">Dados da mensagem</h3>
                            <div class="flex flex-col gap-3 text-sm text-gray-700 font-semibold"
                                style="font-family: 'AMX', sans-serif;">
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Remetente</span>
                                    <span id="info-modal-sender" class="font-extrabold text-gray-800"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Enviado em</span>
                                    <span id="info-modal-time" class="font-bold text-gray-800"></span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Status da
                                        entrega</span>
                                    <span class="font-bold text-green-600 flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Entregue e visualizada
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Toast Notification Container -->
                    <div id="toast-container"
                        class="fixed top-5 left-1/2 -translate-x-1/2 z-[150] pointer-events-none flex flex-col gap-2"></div>
                @else
                    <!-- Estado Vazio -->
                    <div class="flex-1 flex flex-col items-center justify-center p-6 text-center select-none">
                        <div class="w-16 h-16 rounded-full bg-red-50 flex items-center justify-center text-[#DA291C] mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                                stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Nenhum chat ativo</h3>
                        <p class="text-sm text-gray-500 max-w-sm">Abra o menu lateral ou clique no botão de suporte para iniciar
                            uma nova conversa.</p>
                    </div>
                @endif
            </div>{{-- fecha coluna central --}}

            <!-- COLUNA DIREITA: INFORMAÇÕES DO ATENDENTE & DOCUMENTOS -->
            @if($activeSolicitation)
                <div
                    class="w-[320px] lg:w-[350px] bg-white rounded-[24px] shadow-md flex flex-col border border-gray-200/80 p-6 pt-10 gap-6 flex-shrink-0 select-none overflow-y-auto hidden lg:flex chat-scroll-clean min-h-0">
                    <div id="right-sidebar-default-content" class="contents">
                        <!-- Info do Contato -->
                        <div class="text-center pb-6 border-b border-gray-250">
                            @php
                                $rightAvatarUser = (auth()->user()->role === 'atendente') ? ($activeSolicitation->user ?? null) : ($activeSolicitation->atendente ?? null);
                                $rightAvatarName = $rightAvatarUser ? $rightAvatarUser->name : 'Suporte Claro';
                                $rightAvatarBg = ($rightAvatarUser && $rightAvatarUser->role === 'atendente') ? 'EAA8A8' : 'D1E7DD';
                                $rightAvatarColor = ($rightAvatarUser && $rightAvatarUser->role === 'atendente') ? '86131E' : '0F5132';
                            @endphp
                            @if(!$rightAvatarUser && auth()->user()->role === 'user')
                                <!-- Ícone de ticket padrão Claro se o cliente ainda não tiver atendente -->
                                <div
                                    class="w-20 h-20 rounded-full bg-white flex items-center justify-center flex-shrink-0 border p-4 shadow-sm mx-auto mb-4">
                                    <img src="/icones/Icone Ticket.png" alt="Ticket" class="w-full h-full object-contain">
                                </div>
                            @else
                                <div class="relative w-20 h-20 mx-auto mb-4 flex-shrink-0">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($rightAvatarName) }}&background={{ $rightAvatarBg }}&color={{ $rightAvatarColor }}&bold=true&rounded=true"
                                        alt="Avatar" class="w-20 h-20 rounded-full object-cover border border-gray-255 shadow-sm">
                                </div>
                            @endif
                            <h3 class="text-xs font-extrabold text-gray-800 uppercase tracking-wider">
                                @if(auth()->user()->role === 'atendente')
                                    {{ strtoupper($activeSolicitation->user->name ?? 'MAURO FILHO') }}
                                @else
                                    {{ strtoupper($activeSolicitation->atendente->name ?? 'SUPORTE PRISMA') }}
                                @endif
                            </h3>
                        </div>

                        <!-- Seção Mídias e Documentos -->
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center justify-between">
                                <span
                                    class="text-xs font-extrabold text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2"
                                        stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375 3.75 0 1 1-.75 0 .375 3.75 0 0 1 .75 0Z" />
                                    </svg>
                                    Imagens e documentos
                                </span>
                                <span id="sidebar-attachments-count" class="text-xs font-extrabold text-gray-400">
                                    {{ $totalAttachmentsCount ?? 0 }}
                                </span>
                            </div>

                            <!-- Grid de Mídias (Horizontal Row) -->
                            <div id="sidebar-attachments-container" class="flex items-center gap-2 overflow-x-auto pb-2">
                                {{-- Arquivos da Solicitação --}}
                                @foreach($solicitationFiles ?? [] as $path)
                                    @php
                                        $extension = pathinfo($path, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    @endphp
                                    @if($isImage)
                                        <a href="javascript:void(0)" onclick="openImageLightbox('{{ Storage::url($path) }}')"
                                            class="w-[84px] aspect-square rounded-xl overflow-hidden border border-gray-250 hover:opacity-90 transition-all flex-shrink-0">
                                            <img src="{{ Storage::url($path) }}" alt="Preview" class="w-full h-full object-cover">
                                        </a>
                                    @else
                                        <a href="{{ Storage::url($path) }}" download
                                            class="w-[84px] aspect-square rounded-xl bg-gray-50 flex flex-col items-center justify-center border border-gray-250 hover:bg-gray-100 transition-colors flex-shrink-0">
                                            <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5-3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                            <span class="text-[8px] font-bold text-gray-500 uppercase mt-1">DOC</span>
                                        </a>
                                    @endif
                                @endforeach

                                {{-- Arquivos das Mensagens --}}
                                @foreach($messageFiles ?? [] as $path)
                                    @php
                                        $extension = pathinfo($path, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    @endphp
                                    @if($isImage)
                                        <a href="javascript:void(0)" onclick="openImageLightbox('{{ asset('storage/' . $path) }}')"
                                            class="w-[84px] aspect-square rounded-xl overflow-hidden border border-gray-250 hover:opacity-90 transition-all flex-shrink-0">
                                            <img src="{{ asset('storage/' . $path) }}" alt="Preview" class="w-full h-full object-cover">
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $path) }}" download
                                            class="w-[84px] aspect-square rounded-xl bg-gray-50 flex flex-col items-center justify-center border border-gray-250 hover:bg-gray-100 transition-colors flex-shrink-0">
                                            <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5-3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                            <span class="text-[8px] font-bold text-gray-500 uppercase mt-1">DOC</span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->role === 'atendente')
                        <div id="quick-replies-editor" class="hidden flex-col gap-3 pt-1 relative flex-1 min-h-0">
                            <button type="button" onclick="closeQuickRepliesEditor()"
                                class="absolute top-0 right-0 text-gray-500 hover:text-gray-800 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2"
                                    stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <h4 class="text-xl leading-none font-extrabold text-[#243447] text-center mt-1">Respostas rápidas</h4>

                            <div class="relative mt-1">
                                <input id="quick-replies-editor-search" type="text" placeholder="Pesquisar"
                                    class="w-full h-10 rounded-full border border-gray-400 bg-transparent px-4 pr-10 text-sm placeholder-gray-600 focus:outline-none focus:ring-1 focus:ring-[#DA291C]">
                                <svg class="w-4 h-4 text-gray-600 absolute right-4 top-3.5" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>

                            <div class="h-px bg-gray-300 my-1"></div>

                            <div id="quick-replies-editor-list"
                                class="flex flex-col gap-3 flex-1 overflow-y-auto pr-2 quick-replies-red-scroll"></div>

                            <button type="button" onclick="openQuickReplyAddModal()"
                                class="w-full h-10 rounded-xl text-white text-base font-extrabold hover:opacity-95 transition-all"
                                style="background: linear-gradient(89.24deg, #A01724 0%, #DA291C 100%);">
                                Adicionar resposta rápida
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </div>{{-- fecha flex-1 p-6 --}}
    </div>{{-- fecha w-full flex-1 wrapper externo --}}

    {{-- Toast de Videochamada --}}
    <div id="videocall-toast">
        <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" class="w-5 h-5 flex-shrink-0">
            <path
                d="M4.5 4.5A2.25 2.25 0 0 0 2.25 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-10.5A2.25 2.25 0 0 0 15 4.5H4.5ZM19.24 7.63l-2.99 2.24v4.26l2.99 2.24A1.5 1.5 0 0 0 21.75 15.1V8.9a1.5 1.5 0 0 0-2.51-1.27Z" />
        </svg>
        <div>
            <div
                style="font-size:11px;font-weight:800;letter-spacing:0.05em;color:rgba(255,255,255,0.6);text-transform:uppercase;">
                Videochamada Ativa</div>
            <div style="font-size:13px;font-weight:700;">Sala criada com sucesso!</div>
        </div>
        <button type="button" class="vc-toast-link"
            style="background:linear-gradient(90deg,#DA291C,#ff6b5b);color:#fff;font-size:11px;font-weight:800;padding:6px 14px;border-radius:20px;text-decoration:none;white-space:nowrap;flex-shrink:0;cursor:pointer;">
            Entrar agora
        </button>
    </div>

    <!-- Modal de Videochamada (Jitsi embutido) -->
    <div id="jitsi-modal" class="fixed inset-0 z-[120] hidden bg-black/80 backdrop-blur-sm p-2 sm:p-4">
        <div id="jitsi-modal-content"
            class="relative w-full h-full rounded-2xl bg-[#111827] border border-white/10 shadow-2xl overflow-hidden">
            <button type="button" onclick="closeJitsiModal()"
                class="absolute top-3 right-3 z-10 bg-black/55 hover:bg-black/75 text-white rounded-full p-2 transition-colors"
                title="Fechar chamada">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div id="jitsi-container" class="w-full h-full"></div>
        </div>
    </div>

    <!-- Modal de Visualização de Imagem (Lightbox) -->
    <div id="image-lightbox-modal"
        class="hidden fixed inset-0 z-[100] bg-black/90 backdrop-blur flex flex-col items-center justify-center p-4 transition-all duration-300">
        <button type="button" onclick="closeImageLightbox()"
            class="absolute top-6 right-6 text-white hover:text-gray-300 transition-colors p-2 focus:outline-none cursor-pointer">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <div class="relative max-w-[90vw] max-h-[85vh] flex items-center justify-center">
            <img id="lightbox-image-el" src="" alt="Visualização"
                class="max-w-full max-h-full object-contain rounded-xl shadow-2xl border border-white/10">
        </div>
        <div class="mt-4 text-center">
            <a id="lightbox-download-link" href="" download
                class="px-5 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-full text-xs font-extrabold uppercase tracking-wider transition-all inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Baixar Imagem
            </a>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://meet.jit.si/external_api.js"></script>
    <script>
        // Global chat state variables
        let currentMessageId = null;
        let replyingToMessage = null;
        let editingMessageId = null;
        let attachedFile = null;
        let sidebarFilesCount = {{ $totalAttachmentsCount ?? 0 }};
        const messageReactions = {};
        let lastMessageId = {{ $activeSolicitation && $activeSolicitation->messages && $activeSolicitation->messages->isNotEmpty() ? $activeSolicitation->messages->max('id') : 0 }};
        const isAtendenteUser = {{ \Illuminate\Support\Js::from(auth()->user()->role === 'atendente') }};
        const isClienteUser = {{ \Illuminate\Support\Js::from(auth()->user()->role === 'user') }};
        const activeSolicitationUserName = {{ \Illuminate\Support\Js::from($activeSolicitation?->user?->name ?? 'Cliente') }};
        const activeSolicitationTicket = {{ \Illuminate\Support\Js::from($activeSolicitation?->ticket_number ?? '') }};
        const activeSolicitationId = {{ \Illuminate\Support\Js::from($activeSolicitation?->id) }};
        const currentUserDisplayName = {{ \Illuminate\Support\Js::from(auth()->user()->name ?? 'Usuário') }};
        const currentUserEmail = {{ \Illuminate\Support\Js::from(auth()->user()->email ?? '') }};
        const AUTO_JOIN_STORAGE_KEY = 'prisma_auto_joined_videocalls_v1';
        let solicitationEvaluationSent = {{ \Illuminate\Support\Js::from(isset($existingClientEvaluation) && $existingClientEvaluation !== null) }};
        const TAG_STORE_KEY = 'prisma_chat_tags_v1';
        const TAG_COLORS = [
            '#EF4444', '#F97316', '#EAB308', '#84CC16', '#06B6D4', '#3B82F6', '#A855F7', '#EC4899',
            '#22C55E', '#14B8A6', '#0EA5E9', '#6366F1', '#8B5CF6', '#D946EF', '#F43F5E', '#64748B',
            '#000000', '#FFFFFF'
        ];
        const QUICK_REPLIES_STORE_KEY = 'prisma_quick_replies_v1';
        let selectedTagColor = TAG_COLORS[0];
        let editingTagId = null;

        // Database-backed presets and tags
        const INITIAL_PRESETS = @json($presets ?? []);
        let quickRepliesList = INITIAL_PRESETS.map(preset => ({
            id: preset.id,
            name: preset.shortcut,
            text: preset.text,
            user_id: preset.user_id,
            parent_id: preset.parent_id
        }));
        let quickRepliesDraft = [...quickRepliesList];
        let quickReplyModalEditIndex = null;
        const tagsList = @json($tags ?? []);
        let activeTicketTagId = @json($activeSolicitation ? $activeSolicitation->tag_id : null);
        let checklistState = {
            problema: null,
            solucao: null,
            encaminhamento: '',
            descricao: ''
        };
        let evaluationState = {
            rating: 0,
            problemaResolvido: null,
            comentario: ''
        };
        let jitsiApi = null;
        let autoJoinedVideoCalls = new Set();
        try {
            const storedAutoJoin = JSON.parse(sessionStorage.getItem(AUTO_JOIN_STORAGE_KEY) || '[]');
            if (Array.isArray(storedAutoJoin)) {
                autoJoinedVideoCalls = new Set(storedAutoJoin.map(id => String(id)));
            }
        } catch (error) {
            autoJoinedVideoCalls = new Set();
        }

        // Chat internal message search functionality
        function toggleChatSearch() {
            const searchBar = document.getElementById('chat-search-bar');
            const searchInput = document.getElementById('chat-search-input');
            if (searchBar) {
                if (searchBar.classList.contains('hidden')) {
                    searchBar.classList.remove('hidden');
                    if (searchInput) {
                        searchInput.value = '';
                        filterChatMessages('');
                        searchInput.focus();
                    }
                } else {
                    searchBar.classList.add('hidden');
                    if (searchInput) {
                        searchInput.value = '';
                        filterChatMessages('');
                    }
                }
            }
        }

        function filterChatMessages(query) {
            query = query.toLowerCase().trim();
            const messages = document.querySelectorAll('.chat-message');
            const clearBtn = document.getElementById('chat-search-clear');

            if (clearBtn) {
                if (query.length > 0) {
                    clearBtn.classList.remove('hidden');
                } else {
                    clearBtn.classList.add('hidden');
                }
            }

            messages.forEach(msg => {
                const textContent = msg.textContent.toLowerCase();
                if (query === '' || textContent.includes(query)) {
                    msg.style.setProperty('display', '', 'important');
                } else {
                    msg.style.setProperty('display', 'none', 'important');
                }
            });
        }

        // Transfer chat functionality
        function toggleTransferMenu(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('transfer-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
                if (!dropdown.classList.contains('hidden')) {
                    const search = document.getElementById('transfer-search');
                    if (search) {
                        search.value = '';
                        filterTransferMenu('');
                        search.focus();
                    }
                }
            }
        }

        function toggleSubmenu(id, event) {
            event.stopPropagation();
            const sub = document.getElementById(id);
            if (sub) {
                sub.classList.toggle('hidden');
                const trigger = event.currentTarget;
                const icon = trigger.querySelector('svg');
                if (icon) {
                    if (sub.classList.contains('hidden')) {
                        icon.style.transform = '';
                    } else {
                        icon.style.transform = 'rotate(180deg)';
                    }
                }
            }
        }

        function filterTransferMenu(query) {
            query = query.toLowerCase().trim();
            const items = document.querySelectorAll('.transfer-item');
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (query === '' || text.includes(query)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function transferTo(type, name) {
            showToast(`Chamado transferido para ${type}: ${name}`);
            const dropdown = document.getElementById('transfer-dropdown');
            if (dropdown) dropdown.classList.add('hidden');
        }

        // Close transfer dropdown when clicking outside
        document.addEventListener('click', function (e) {
            const dropdown = document.getElementById('transfer-dropdown');
            const trigger = document.getElementById('btn-transfer-menu');
            if (dropdown && !dropdown.classList.contains('hidden')) {
                if (!dropdown.contains(e.target) && (!trigger || !trigger.contains(e.target))) {
                    dropdown.classList.add('hidden');
                }
            }
        });

        function isClosedStatus(status) {
            return ['resolvida', 'nao_resolvida', 'não resolvida'].includes(status || '');
        }

        function updateClientClosureUI(status) {
            const closed = isClosedStatus(status);
            const closureWrapper = document.getElementById('client-closure-wrapper');
            const attendantClosureWrapper = document.getElementById('attendant-closure-wrapper');
            const formWrapper = document.getElementById('chat-input-form-wrapper');

            if (closureWrapper && isClienteUser) {
                if (closed) {
                    closureWrapper.classList.remove('hidden');
                } else {
                    closureWrapper.classList.add('hidden');
                }
            }

            if (attendantClosureWrapper && isAtendenteUser) {
                if (closed) {
                    attendantClosureWrapper.classList.remove('hidden');
                } else {
                    attendantClosureWrapper.classList.add('hidden');
                }
            }

            if (formWrapper) {
                if (closed || status === 'na_fila') {
                    formWrapper.classList.add('hidden');
                } else {
                    formWrapper.classList.remove('hidden');
                }
            }
        }

        function getDefaultTagStore() {
            return {
                tags: tagsList,
                ticketTags: {}
            };
        }

        function getTagStore() {
            return {
                tags: tagsList,
                ticketTags: {}
            };
        }

        function saveTagStore(store) {
            // No-op
        }

        function getCurrentTicketTag() {
            if (!activeSolicitationId || !activeTicketTagId) return null;
            return tagsList.find(tag => tag.id === activeTicketTagId) || null;
        }

        function renderCurrentTagBadge() {
            const badge = document.getElementById('current-ticket-tag');
            if (!badge || !isAtendenteUser || !activeSolicitationId) return;

            const currentTag = getCurrentTicketTag();
            if (!currentTag) {
                badge.classList.add('hidden');
                return;
            }

            badge.textContent = currentTag.name;
            badge.style.backgroundColor = currentTag.color;
            badge.classList.remove('hidden');
            badge.classList.add('inline-flex');
        }

        function renderTagMenu() {
            const list = document.getElementById('ticket-tag-list');
            if (!list || !isAtendenteUser) return;

            const currentTag = getCurrentTicketTag();
            list.innerHTML = '';

            tagsList.forEach(tag => {
                const row = document.createElement('button');
                row.type = 'button';
                row.className = `w-full flex items-center justify-between gap-2 px-2 py-1.5 rounded-lg transition-colors ${currentTag && currentTag.id === tag.id ? 'bg-white/12' : 'hover:bg-white/10'}`;
                row.onclick = () => selectTagForTicket(tag.id);

                const left = document.createElement('div');
                left.className = 'flex items-center gap-2';
                const dot = document.createElement('span');
                dot.className = 'w-2.5 h-2.5 rounded-full';
                dot.style.backgroundColor = tag.color;

                const name = document.createElement('span');
                name.className = 'text-base font-semibold';
                name.textContent = tag.name;

                left.appendChild(dot);
                left.appendChild(name);

                row.appendChild(left);
                list.appendChild(row);
            });
        }

        function selectTagForTicket(tagId) {
            if (!activeSolicitationId) return;

            const alreadySelected = activeTicketTagId === tagId;
            const newTagId = alreadySelected ? null : tagId;

            fetch(`/chat/solicitations/${activeSolicitationId}/tag`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    tag_id: newTagId
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        activeTicketTagId = newTagId;
                        renderTagMenu();
                        renderCurrentTagBadge();
                        showToast(alreadySelected ? 'Etiqueta removida deste chamado.' : 'Etiqueta atualizada.');
                        closeAllMenus();
                    } else {
                        showToast('Erro ao atualizar etiqueta.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Erro ao salvar etiqueta.');
                });
        }

        function toggleTagMenu(event) {
            if (!isAtendenteUser) return;
            event.stopPropagation();

            const menu = document.getElementById('ticket-tag-menu');
            if (!menu) return;

            const isHidden = menu.classList.contains('hidden');
            closeAllMenus();

            if (isHidden) {
                renderTagMenu();
                menu.classList.remove('hidden');
                const rect = event.currentTarget.getBoundingClientRect();
                const menuWidth = menu.offsetWidth || 240;
                menu.style.top = `${rect.bottom + 10}px`;
                menu.style.left = `${Math.max(16, rect.right - menuWidth)}px`;
            }
        }

        function renderTagPalette() {
            const palette = document.getElementById('tag-color-palette');
            if (!palette) return;

            palette.innerHTML = '';
            TAG_COLORS.forEach(color => {
                const colorBtn = document.createElement('button');
                colorBtn.type = 'button';
                colorBtn.className = `w-10 h-10 rounded-full border-2 transition-all ${selectedTagColor === color ? 'border-gray-900 scale-105' : 'border-white'}`;
                colorBtn.style.backgroundColor = color;
                colorBtn.onclick = () => {
                    selectedTagColor = color;
                    renderTagPalette();
                };
                palette.appendChild(colorBtn);
            });
        }

        function openTagEditorModal(tagId = null) {
            if (!isAtendenteUser) return;
            closeAllMenus();

            editingTagId = tagId;
            const store = getTagStore();
            const currentTag = tagId ? store.tags.find(tag => tag.id === tagId) : null;

            const title = document.querySelector('#tag-editor-modal-content h3');
            const submit = document.querySelector('#tag-editor-modal-content button[onclick="saveTagFromModal()"]');
            const input = document.getElementById('tag-name-input');

            if (title) title.textContent = currentTag ? 'Editar tag' : 'Adicionar tag';
            if (submit) submit.textContent = currentTag ? 'Salvar tag' : 'Adicionar tag';
            if (input) input.value = currentTag ? currentTag.name : '';

            selectedTagColor = currentTag ? currentTag.color : TAG_COLORS[0];
            renderTagPalette();

            const modal = document.getElementById('tag-editor-modal');
            const content = document.getElementById('tag-editor-modal-content');
            if (!modal || !content) return;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeTagEditorModal() {
            const modal = document.getElementById('tag-editor-modal');
            const content = document.getElementById('tag-editor-modal-content');
            if (!modal || !content) return;

            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 250);
        }

        function saveTagFromModal() {
            const input = document.getElementById('tag-name-input');
            if (!input || !activeSolicitationId) return;

            const tagName = input.value.trim();
            if (!tagName) {
                showToast('Informe um nome para a tag.');
                input.focus();
                return;
            }

            const store = getTagStore();
            const ticketKey = String(activeSolicitationId);
            let tagId = editingTagId;

            if (editingTagId) {
                store.tags = store.tags.map(tag => tag.id === editingTagId ? { ...tag, name: tagName, color: selectedTagColor } : tag);
            } else {
                tagId = `tag_${Date.now()}`;
                store.tags.push({ id: tagId, name: tagName, color: selectedTagColor });
            }

            store.ticketTags[ticketKey] = tagId;
            saveTagStore(store);
            closeTagEditorModal();
            renderTagMenu();
            renderCurrentTagBadge();
            showToast(editingTagId ? 'Tag atualizada com sucesso.' : 'Tag adicionada com sucesso.');
        }

        function initTagCustomization() {
            if (!isAtendenteUser) return;
            renderTagMenu();
            renderCurrentTagBadge();
        }

        function getDefaultQuickReplies() {
            return quickRepliesList;
        }

        function normalizeQuickReply(item) {
            return {
                id: item.id || null,
                name: (item && item.name ? String(item.name) : 'Mensagem inicial').trim() || 'Mensagem inicial',
                text: (item && item.text ? String(item.text) : '').trim(),
                user_id: item.user_id || null,
                parent_id: item.parent_id || null
            };
        }

        function getQuickReplies() {
            return quickRepliesList;
        }

        function saveQuickReplies(list) {
            // No-op - DB synchronization is handled by Ajax
        }

        function formatQuickReplyName(name) {
            const cleaned = (name || '').trim();
            return cleaned.startsWith('/') ? cleaned : `/${cleaned}`;
        }

        function renderQuickRepliesMenu(filter = '') {
            const list = document.getElementById('quick-replies-menu-list');
            if (!list || !isAtendenteUser) return;

            const normalizedFilter = filter.trim().toLowerCase();
            const replies = getQuickReplies().filter(reply => {
                return reply.name.toLowerCase().includes(normalizedFilter) || reply.text.toLowerCase().includes(normalizedFilter);
            });

            list.innerHTML = '';
            if (replies.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'text-[11px] text-white/50 py-3 text-center';
                empty.textContent = 'Nenhuma resposta encontrada.';
                list.appendChild(empty);
                return;
            }

            replies.forEach(reply => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'w-full text-left px-2 py-1.5 rounded-lg hover:bg-white/10 transition-colors';

                const label = document.createElement('span');
                label.className = 'block text-[10px] text-white/40';
                label.textContent = formatQuickReplyName(reply.name);

                const text = document.createElement('span');
                text.className = 'block text-xs font-semibold text-white/90 truncate';
                text.textContent = reply.text;

                btn.appendChild(label);
                btn.appendChild(text);
                btn.onclick = () => applyQuickReply(reply.text);
                list.appendChild(btn);
            });
        }

        function toggleQuickRepliesMenu(event) {
            if (!isAtendenteUser) return;
            event.stopPropagation();

            const menu = document.getElementById('quick-replies-menu');
            if (!menu) return;

            const isHidden = menu.classList.contains('hidden');
            closeAllMenus();

            if (isHidden) {
                renderQuickRepliesMenu();
                menu.classList.remove('hidden');

                const rect = event.currentTarget.getBoundingClientRect();
                const menuWidth = menu.offsetWidth || 300;
                const menuHeight = menu.offsetHeight || 220;
                const top = Math.max(16, rect.top - menuHeight - 10);
                const left = Math.max(16, rect.left - (menuWidth / 2) + (rect.width / 2));

                menu.style.top = `${top}px`;
                menu.style.left = `${left}px`;
            }
        }

        function applyQuickReply(text) {
            const input = document.getElementById('chat-message-input');
            if (!input) return;
            input.value = text;
            input.focus();
            closeAllMenus();
        }

        function openQuickRepliesEditor() {
            if (!isAtendenteUser) return;
            closeAllMenus();

            quickRepliesDraft = [...getQuickReplies()];
            quickReplyModalEditIndex = null;
            const editorSearch = document.getElementById('quick-replies-editor-search');
            if (editorSearch) editorSearch.value = '';
            renderQuickRepliesEditor();

            const panel = document.getElementById('quick-replies-editor');
            if (!panel) return;

            const defaultContent = document.getElementById('right-sidebar-default-content');
            if (defaultContent) defaultContent.classList.add('hidden');

            panel.classList.remove('hidden');
            panel.classList.add('flex');
            panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function closeQuickRepliesEditor() {
            const panel = document.getElementById('quick-replies-editor');
            if (!panel) return;
            quickReplyModalEditIndex = null;
            panel.classList.add('hidden');
            panel.classList.remove('flex');

            const defaultContent = document.getElementById('right-sidebar-default-content');
            if (defaultContent) defaultContent.classList.remove('hidden');
        }

        function renderQuickRepliesEditor(filter = '') {
            const list = document.getElementById('quick-replies-editor-list');
            if (!list || !isAtendenteUser) return;

            const normalizedFilter = (filter || '').trim().toLowerCase();
            const source = quickRepliesDraft
                .map((reply, index) => ({ reply, index }))
                .filter(item => {
                    return item.reply.name.toLowerCase().includes(normalizedFilter) || item.reply.text.toLowerCase().includes(normalizedFilter);
                });

            list.innerHTML = '';
            if (source.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'text-[12px] text-gray-500 italic py-4 text-center';
                empty.textContent = 'Nenhuma resposta rápida encontrada.';
                list.appendChild(empty);
                return;
            }

            source.forEach(({ reply, index }) => {
                const row = document.createElement('div');
                row.className = 'pb-3 border-b border-gray-300/70';

                const header = document.createElement('div');
                header.className = 'flex items-center justify-between mb-1';

                const label = document.createElement('span');
                label.className = 'text-[13px] font-bold text-gray-450';
                label.textContent = formatQuickReplyName(reply.name);

                const actions = document.createElement('div');
                actions.className = 'flex items-center gap-1.5';

                const editBtn = document.createElement('button');
                editBtn.type = 'button';
                editBtn.className = 'p-1 rounded hover:bg-orange-100 transition-colors';
                editBtn.innerHTML = `
                    <svg class="w-4 h-4 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487z" />
                    </svg>
                `;
                editBtn.onclick = () => openQuickReplyEditModal(index);

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'p-1 rounded hover:bg-red-100 transition-colors';
                removeBtn.innerHTML = `
                    <svg class="w-4 h-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79" />
                    </svg>
                `;
                removeBtn.onclick = () => removeQuickReplyItem(index, filter);

                actions.appendChild(editBtn);
                actions.appendChild(removeBtn);
                header.appendChild(label);
                header.appendChild(actions);
                row.appendChild(header);

                const body = document.createElement('p');
                body.className = 'text-sm leading-tight text-gray-800 whitespace-pre-line';
                body.textContent = reply.text;
                row.appendChild(body);

                list.appendChild(row);
            });
        }

        function removeQuickReplyItem(index, filter = '') {
            if (!isAtendenteUser) return;
            if (!confirm('Deseja realmente remover esta resposta rápida?')) return;

            const current = quickRepliesDraft[index];
            if (!current) return;

            fetch(`/chat/presets/${current.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const realIndex = quickRepliesList.findIndex(item => item.id === current.id);
                        if (realIndex !== -1) {
                            if (current.parent_id) {
                                const globalOriginal = INITIAL_PRESETS.find(item => item.id === current.parent_id);
                                if (globalOriginal) {
                                    quickRepliesList[realIndex] = {
                                        id: globalOriginal.id,
                                        name: globalOriginal.shortcut,
                                        text: globalOriginal.text,
                                        user_id: null,
                                        parent_id: null
                                    };
                                    showToast('Resposta personalizada removida. Restaurado para o padrão global.');
                                } else {
                                    quickRepliesList.splice(realIndex, 1);
                                    showToast('Resposta rápida removida.');
                                }
                            } else {
                                quickRepliesList.splice(realIndex, 1);
                                showToast('Resposta rápida removida.');
                            }
                        }
                        quickRepliesDraft = [...quickRepliesList];
                        renderQuickRepliesMenu();
                        renderQuickRepliesEditor(filter);
                    } else {
                        showToast('Erro ao remover.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Erro de conexão ao remover.');
                });
        }

        function openQuickReplyAddModal() {
            if (!isAtendenteUser) return;

            const nameInput = document.getElementById('quick-reply-add-name');
            const textInput = document.getElementById('quick-reply-add-text');
            if (nameInput) nameInput.value = '';
            if (textInput) textInput.value = '';

            const modal = document.getElementById('quick-reply-add-modal');
            const content = document.getElementById('quick-reply-add-modal-content');
            if (!modal || !content) return;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeQuickReplyAddModal() {
            const modal = document.getElementById('quick-reply-add-modal');
            const content = document.getElementById('quick-reply-add-modal-content');
            if (!modal || !content) return;

            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 250);
        }

        function submitQuickReplyAdd() {
            if (!isAtendenteUser) return;

            const nameInput = document.getElementById('quick-reply-add-name');
            const textInput = document.getElementById('quick-reply-add-text');
            if (!nameInput || !textInput) return;

            const name = (nameInput.value || '').trim() || 'Mensagem inicial';
            const text = (textInput.value || '').trim();

            if (!text) {
                showToast('Informe o conteúdo da resposta rápida.');
                textInput.focus();
                return;
            }

            fetch("{{ route('chat.presets.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    shortcut: name,
                    text: text
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        quickRepliesList.push({
                            id: data.preset.id,
                            name: data.preset.shortcut,
                            text: data.preset.text,
                            user_id: data.preset.user_id,
                            parent_id: data.preset.parent_id
                        });
                        quickRepliesDraft = [...quickRepliesList];
                        renderQuickRepliesMenu();
                        renderQuickRepliesEditor();
                        closeQuickReplyAddModal();
                        showToast('Resposta rápida adicionada.');
                    } else {
                        showToast('Erro ao salvar resposta rápida.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Erro de conexão ao salvar.');
                });
        }

        function openQuickReplyEditModal(index) {
            if (!isAtendenteUser) return;

            const current = quickRepliesDraft[index];
            if (!current) return;
            quickReplyModalEditIndex = index;

            const nameInput = document.getElementById('quick-reply-edit-name');
            const textInput = document.getElementById('quick-reply-edit-text');
            if (nameInput) nameInput.value = current.name || 'Mensagem inicial';
            if (textInput) textInput.value = current.text || '';

            const modal = document.getElementById('quick-reply-edit-modal');
            const content = document.getElementById('quick-reply-edit-modal-content');
            if (!modal || !content) return;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeQuickReplyEditModal() {
            const modal = document.getElementById('quick-reply-edit-modal');
            const content = document.getElementById('quick-reply-edit-modal-content');
            if (!modal || !content) return;

            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 250);
        }

        function submitQuickReplyEdit() {
            if (!isAtendenteUser || quickReplyModalEditIndex === null) return;

            const nameInput = document.getElementById('quick-reply-edit-name');
            const textInput = document.getElementById('quick-reply-edit-text');
            if (!nameInput || !textInput) return;

            const name = (nameInput.value || '').trim() || 'Mensagem inicial';
            const text = (textInput.value || '').trim();

            if (!text) {
                showToast('Informe o conteúdo da resposta rápida.');
                textInput.focus();
                return;
            }

            const current = quickRepliesDraft[quickReplyModalEditIndex];
            if (!current) return;

            fetch(`/chat/presets/${current.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    shortcut: name,
                    text: text
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        current.name = data.preset.shortcut;
                        current.text = data.preset.text;
                        if (data.preset.parent_id) {
                            current.id = data.preset.id;
                            current.user_id = data.preset.user_id;
                            current.parent_id = data.preset.parent_id;
                        }

                        quickRepliesDraft[quickReplyModalEditIndex] = current;
                        renderQuickRepliesMenu();
                        renderQuickRepliesEditor();
                        closeQuickReplyEditModal();
                        showToast('Alterações salvas com sucesso.');
                    } else {
                        showToast('Erro ao atualizar.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Erro de conexão ao salvar.');
                });
        }

        function initQuickReplies() {
            if (!isAtendenteUser) return;

            const menuSearch = document.getElementById('quick-replies-search');
            if (menuSearch) {
                menuSearch.addEventListener('input', function (event) {
                    renderQuickRepliesMenu(event.target.value || '');
                });
            }

            const editorSearch = document.getElementById('quick-replies-editor-search');
            if (editorSearch) {
                editorSearch.addEventListener('input', function (event) {
                    renderQuickRepliesEditor(event.target.value || '');
                });
            }
        }

        function syncChecklistOptionStyles() {
            const options = document.querySelectorAll('.checklist-option');
            options.forEach(option => {
                const group = option.dataset.group;
                const value = option.dataset.value;
                const isSelected = (group === 'problema' && checklistState.problema === value)
                    || (group === 'solucao' && checklistState.solucao === value);
                option.classList.toggle('checklist-selected', isSelected);
            });
        }

        function updateChecklistSubmitState() {
            const encaminhamentoWrapper = document.getElementById('checklist-encaminhamento-wrapper');
            const submitBtn = document.getElementById('checklist-submit-btn');

            if (!submitBtn) return;

            const requiresEncaminhamento = checklistState.solucao === 'encaminhado';
            if (encaminhamentoWrapper) {
                if (requiresEncaminhamento) {
                    encaminhamentoWrapper.classList.remove('hidden');
                    encaminhamentoWrapper.classList.add('flex');
                } else {
                    encaminhamentoWrapper.classList.add('hidden');
                    encaminhamentoWrapper.classList.remove('flex');
                }
            }

            const isValid = Boolean(checklistState.problema)
                && Boolean(checklistState.solucao)
                && checklistState.descricao.trim().length > 0
                && (!requiresEncaminhamento || checklistState.encaminhamento.trim().length > 0);

            submitBtn.disabled = !isValid;
            submitBtn.classList.toggle('checklist-submit-disabled', !isValid);
        }

        function resetChecklistState() {
            checklistState = {
                problema: null,
                solucao: null,
                encaminhamento: '',
                descricao: ''
            };

            const encaminhamentoInput = document.getElementById('checklist-encaminhamento');
            const descricaoInput = document.getElementById('checklist-descricao');
            if (encaminhamentoInput) encaminhamentoInput.value = '';
            if (descricaoInput) descricaoInput.value = '';

            syncChecklistOptionStyles();
            updateChecklistSubmitState();
        }

        function openChecklistModal() {
            if (!isAtendenteUser) return;
            if (!activeSolicitationId) {
                showToast('Selecione um chamado para finalizar o atendimento.');
                return;
            }

            resetChecklistState();

            const modal = document.getElementById('checklist-modal');
            const content = document.getElementById('checklist-modal-content');
            if (!modal || !content) return;

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
            if (!modal || !content) return;

            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 250);
        }

        function initChecklistModal() {
            if (!isAtendenteUser) return;

            const modal = document.getElementById('checklist-modal');
            if (modal) {
                modal.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        closeChecklistModal();
                    }
                });
            }

            const options = document.querySelectorAll('.checklist-option');
            options.forEach(option => {
                option.addEventListener('click', function () {
                    const group = this.dataset.group;
                    const value = this.dataset.value;
                    if (group === 'problema') checklistState.problema = value;
                    if (group === 'solucao') checklistState.solucao = value;
                    syncChecklistOptionStyles();
                    updateChecklistSubmitState();
                });
            });

            const encaminhamentoInput = document.getElementById('checklist-encaminhamento');
            if (encaminhamentoInput) {
                encaminhamentoInput.addEventListener('input', function () {
                    checklistState.encaminhamento = this.value || '';
                    updateChecklistSubmitState();
                });
            }

            const descricaoInput = document.getElementById('checklist-descricao');
            if (descricaoInput) {
                descricaoInput.addEventListener('input', function () {
                    checklistState.descricao = this.value || '';
                    updateChecklistSubmitState();
                });
            }

            resetChecklistState();
        }

        function submitChecklistModal() {
            if (!isAtendenteUser || !activeSolicitationId) return;

            const payload = {
                problema_identificado: checklistState.problema,
                solucao_aplicada: checklistState.solucao,
                encaminhamento: checklistState.encaminhamento.trim(),
                descricao: checklistState.descricao.trim(),
            };

            const requiresEncaminhamento = payload.solucao_aplicada === 'encaminhado';
            if (!payload.problema_identificado || !payload.solucao_aplicada || !payload.descricao || (requiresEncaminhamento && !payload.encaminhamento)) {
                showToast('Preencha todos os campos obrigatórios do checklist.');
                return;
            }

            const submitBtn = document.getElementById('checklist-submit-btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('checklist-submit-disabled');
            }

            fetch(`/atendente/solicitations/${activeSolicitationId}/finalizar`, {
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
                        throw new Error(data.message || data.error || 'Não foi possível finalizar o atendimento.');
                    }
                    return data;
                })
                .then(data => {
                    currentSolicitationStatus = data.status || currentSolicitationStatus;
                    closeChecklistModal();
                    showToast('Checklist enviado e atendimento finalizado.');

                    const formWrapper = document.getElementById('chat-input-form-wrapper');
                    if (formWrapper) formWrapper.classList.add('hidden');

                    setTimeout(() => {
                        window.location.reload();
                    }, 350);
                })
                .catch(error => {
                    console.error(error);
                    showToast(error.message || 'Erro ao finalizar atendimento.');
                })
                .finally(() => {
                    updateChecklistSubmitState();
                });
        }

        function syncEvaluationStars() {
            const stars = document.querySelectorAll('.evaluation-star');
            stars.forEach(star => {
                const value = Number(star.dataset.rating || '0');
                star.classList.toggle('active', value <= evaluationState.rating);
            });
        }

        function syncEvaluationResolvedOptions() {
            const yesDot = document.getElementById('evaluation-resolved-yes-dot');
            const noDot = document.getElementById('evaluation-resolved-no-dot');

            if (yesDot) {
                yesDot.classList.toggle('bg-[#DA291C]', evaluationState.problemaResolvido === true);
                yesDot.classList.toggle('border-[#DA291C]', evaluationState.problemaResolvido === true);
            }
            if (noDot) {
                noDot.classList.toggle('bg-[#DA291C]', evaluationState.problemaResolvido === false);
                noDot.classList.toggle('border-[#DA291C]', evaluationState.problemaResolvido === false);
            }
        }

        function updateEvaluationSubmitState() {
            const submitBtn = document.getElementById('evaluation-submit-btn');
            if (!submitBtn) return;

            const isValid = evaluationState.rating >= 1 && evaluationState.problemaResolvido !== null;
            submitBtn.disabled = !isValid;
        }

        function resetEvaluationState() {
            evaluationState = {
                rating: 0,
                problemaResolvido: null,
                comentario: ''
            };

            const commentInput = document.getElementById('evaluation-comment');
            if (commentInput) commentInput.value = '';

            syncEvaluationStars();
            syncEvaluationResolvedOptions();
            updateEvaluationSubmitState();
        }

        function setEvaluationRating(rating) {
            evaluationState.rating = Number(rating);
            syncEvaluationStars();
            updateEvaluationSubmitState();
        }

        function setEvaluationResolved(value) {
            evaluationState.problemaResolvido = Boolean(value);
            syncEvaluationResolvedOptions();
            updateEvaluationSubmitState();
        }

        function openEvaluationModal() {
            if (!isClienteUser || solicitationEvaluationSent || !activeSolicitationId) return;
            if (!isClosedStatus(currentSolicitationStatus)) return;

            resetEvaluationState();

            const modal = document.getElementById('evaluation-modal');
            const content = document.getElementById('evaluation-modal-content');
            if (!modal || !content) return;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeEvaluationModal() {
            const modal = document.getElementById('evaluation-modal');
            const content = document.getElementById('evaluation-modal-content');
            if (!modal || !content) return;

            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 250);
        }

        function initEvaluationModal() {
            if (!isClienteUser) return;

            const modal = document.getElementById('evaluation-modal');
            if (modal) {
                modal.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        closeEvaluationModal();
                    }
                });
            }

            const commentInput = document.getElementById('evaluation-comment');
            if (commentInput) {
                commentInput.addEventListener('input', function () {
                    evaluationState.comentario = this.value || '';
                });
            }

            resetEvaluationState();
            updateClientClosureUI(currentSolicitationStatus);
        }

        function submitEvaluation() {
            if (!isClienteUser || !activeSolicitationId || solicitationEvaluationSent) return;

            if (evaluationState.rating < 1 || evaluationState.problemaResolvido === null) {
                showToast('Preencha a nota e se o problema foi resolvido.');
                return;
            }

            const submitBtn = document.getElementById('evaluation-submit-btn');
            if (submitBtn) submitBtn.disabled = true;

            fetch(`/solicitations/${activeSolicitationId}/avaliacao`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    nota: evaluationState.rating,
                    problema_resolvido: evaluationState.problemaResolvido,
                    comentario: (evaluationState.comentario || '').trim(),
                })
            })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) {
                        throw new Error(data.message || data.error || 'Não foi possível enviar a avaliação.');
                    }
                    return data;
                })
                .then(() => {
                    solicitationEvaluationSent = true;
                    closeEvaluationModal();

                    const evaluateBtn = document.getElementById('open-evaluation-btn');
                    const sentMsg = document.getElementById('evaluation-sent-msg');
                    if (evaluateBtn) evaluateBtn.classList.add('hidden');
                    if (sentMsg) sentMsg.classList.remove('hidden');

                    showToast('Avaliação enviada com sucesso.');
                })
                .catch(error => {
                    console.error(error);
                    showToast(error.message || 'Erro ao enviar avaliação.');
                })
                .finally(() => {
                    updateEvaluationSubmitState();
                });
        }

        function iniciarAtendimentoChat(id) {
            fetch(`/atendente/solicitations/${id}/iniciar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const wrapper = document.getElementById('iniciar-atendimento-wrapper');
                        if (wrapper) wrapper.classList.add('hidden');
                        const formWrapper = document.getElementById('chat-input-form-wrapper');
                        if (formWrapper) formWrapper.classList.remove('hidden');
                        currentSolicitationStatus = 'em_atendimento';
                        setTimeout(() => {
                            window.location.reload();
                        }, 300);
                    } else {
                        showToast(data.message || 'Não foi possível puxar o atendimento.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Erro ao iniciar atendimento. Tente novamente.');
                });
        }

        // Add file to sidebar attachments dynamically
        function addFileToSidebar(file) {
            const container = document.getElementById('sidebar-attachments-container');
            const countEl = document.getElementById('sidebar-attachments-count');
            if (!container) return;

            sidebarFilesCount++;
            if (countEl) countEl.textContent = sidebarFilesCount;

            const isImage = file.type.startsWith('image/');
            const fileUrl = URL.createObjectURL(file);

            const aEl = document.createElement('a');

            if (isImage) {
                aEl.href = "javascript:void(0)";
                aEl.onclick = function () { openImageLightbox(fileUrl); };
                aEl.className = "w-[84px] aspect-square rounded-xl overflow-hidden border border-gray-250 hover:opacity-90 transition-all flex-shrink-0";
                aEl.innerHTML = `<img src="${fileUrl}" alt="Preview" class="w-full h-full object-cover">`;
            } else {
                aEl.href = fileUrl;
                aEl.download = file.name;
                aEl.className = "w-[84px] aspect-square rounded-xl bg-gray-50 flex flex-col items-center justify-center border border-gray-250 hover:bg-gray-100 transition-colors flex-shrink-0";
                aEl.innerHTML = `
                    <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5-3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <span class="text-[8px] font-bold text-gray-500 uppercase mt-1">DOC</span>
                `;
            }

            container.appendChild(aEl);
        }

        // Auto scroll at start and setup custom context menu
        document.addEventListener("DOMContentLoaded", function () {
            const container = document.getElementById('chat-messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
            startRealTimePolling();
            initTagCustomization();
            initQuickReplies();
            initChecklistModal();
            initEvaluationModal();
            // autoJoinExistingClientVideoCalls(); // Disabled – client must click "Participar"

            const tagModal = document.getElementById('tag-editor-modal');
            if (tagModal) {
                tagModal.addEventListener('click', function (event) {
                    if (event.target === tagModal) {
                        closeTagEditorModal();
                    }
                });
            }

            const quickReplyAddModal = document.getElementById('quick-reply-add-modal');
            if (quickReplyAddModal) {
                quickReplyAddModal.addEventListener('click', function (event) {
                    if (event.target === quickReplyAddModal) {
                        closeQuickReplyAddModal();
                    }
                });
            }

            const quickReplyEditModal = document.getElementById('quick-reply-edit-modal');
            if (quickReplyEditModal) {
                quickReplyEditModal.addEventListener('click', function (event) {
                    if (event.target === quickReplyEditModal) {
                        closeQuickReplyEditModal();
                    }
                });
            }

            const jitsiModal = document.getElementById('jitsi-modal');
            if (jitsiModal) {
                jitsiModal.addEventListener('click', function (event) {
                    if (event.target === jitsiModal) {
                        closeJitsiModal();
                    }
                });
            }

            if (container) {
                // Evento de clique com botão direito personalizado
                container.addEventListener('contextmenu', function (e) {
                    const bubble = e.target.closest('.message-bubble-content');
                    if (!bubble) return;

                    const msgEl = bubble.closest('[data-message-id]');
                    if (!msgEl) return;

                    e.preventDefault(); // Impede o menu nativo do navegador

                    const msgId = msgEl.getAttribute('data-message-id');
                    currentMessageId = msgId;

                    const menu = document.getElementById('message-context-menu');
                    closeAllMenus();

                    menu.classList.remove('hidden');

                    const menuWidth = menu.offsetWidth || 208;
                    const menuHeight = menu.offsetHeight || 220;
                    const windowWidth = window.innerWidth;
                    const windowHeight = window.innerHeight;

                    let posX = e.clientX;
                    let posY = e.clientY;

                    if (posX + menuWidth > windowWidth) {
                        posX = windowWidth - menuWidth - 10;
                    }
                    if (posY + menuHeight > windowHeight) {
                        posY = windowHeight - menuHeight - 10;
                    }

                    menu.style.left = `${posX}px`;
                    menu.style.top = `${posY}px`;
                });
            }
        });

        // Close menus on click outside
        window.addEventListener('click', function (e) {
            const menu = document.getElementById('message-context-menu');
            const emojiPopup = document.getElementById('emoji-reaction-popup');
            const inputEmojiPicker = document.getElementById('input-emoji-picker');
            const tagMenu = document.getElementById('ticket-tag-menu');
            const quickRepliesMenu = document.getElementById('quick-replies-menu');
            const isClickInsideMenu = menu && menu.contains(e.target);
            const isClickInsideEmoji = emojiPopup && emojiPopup.contains(e.target);
            const isClickInsideInputEmoji = inputEmojiPicker && inputEmojiPicker.contains(e.target);
            const isClickInsideTagMenu = tagMenu && tagMenu.contains(e.target);
            const isClickInsideQuickRepliesMenu = quickRepliesMenu && quickRepliesMenu.contains(e.target);
            if (!isClickInsideMenu && !isClickInsideEmoji && !isClickInsideInputEmoji && !isClickInsideTagMenu && !isClickInsideQuickRepliesMenu) {
                closeAllMenus();
            }
        });

        window.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const jitsiModal = document.getElementById('jitsi-modal');
                if (jitsiModal && !jitsiModal.classList.contains('hidden')) {
                    closeJitsiModal();
                }
            }
        });

        function closeAllMenus() {
            const menu = document.getElementById('message-context-menu');
            if (menu) menu.classList.add('hidden');

            const emojiPopup = document.getElementById('emoji-reaction-popup');
            if (emojiPopup) emojiPopup.classList.add('hidden');

            const inputEmojiPicker = document.getElementById('input-emoji-picker');
            if (inputEmojiPicker) inputEmojiPicker.classList.add('hidden');

            const tagMenu = document.getElementById('ticket-tag-menu');
            if (tagMenu) tagMenu.classList.add('hidden');

            const quickRepliesMenu = document.getElementById('quick-replies-menu');
            if (quickRepliesMenu) quickRepliesMenu.classList.add('hidden');
        }

        // Toggle message options dropdown
        function toggleMessageMenu(event, msgId) {
            event.stopPropagation();
            currentMessageId = msgId;

            const menu = document.getElementById('message-context-menu');
            const isHidden = menu.classList.contains('hidden');

            closeAllMenus();

            if (isHidden) {
                const rect = event.currentTarget.getBoundingClientRect();
                menu.classList.remove('hidden');

                // Check sender direction to align
                const msgEl = document.querySelector(`[data-message-id="${msgId}"]`);
                const isUser = msgEl && msgEl.classList.contains('justify-end');

                const menuWidth = menu.offsetWidth || 208;
                menu.style.top = `${rect.bottom + 4}px`;

                if (isUser) {
                    menu.style.left = `${rect.right - menuWidth}px`;
                } else {
                    menu.style.left = `${rect.left}px`;
                }
            }
        }

        // Toggle reaction popover on message hover button
        function reactToMessage(event, msgId) {
            event.stopPropagation();
            currentMessageId = msgId;
            closeAllMenus();

            const triggerBtn = event.currentTarget;
            const rect = triggerBtn.getBoundingClientRect();
            const emojiPopup = document.getElementById('emoji-reaction-popup');
            emojiPopup.classList.remove('hidden');

            const popupWidth = emojiPopup.offsetWidth || 230;
            const popupHeight = emojiPopup.offsetHeight || 38;

            emojiPopup.style.top = `${rect.top - popupHeight - 8}px`;
            emojiPopup.style.left = `${rect.left + (rect.width / 2) - (popupWidth / 2)}px`;
        }

        // Send or toggle reaction on server
        function toggleReactionOnServer(msgId, emoji) {
            fetch(`/messages/${msgId}/react`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ emoji: emoji })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageReactions[msgId] = data.reactions;
                        renderReactions(msgId);
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast("Erro ao processar reação.");
                });
        }

        // Select emoji reaction
        function handleReactionSelect(emoji) {
            if (!currentMessageId) return;
            toggleReactionOnServer(currentMessageId, emoji);
            closeAllMenus();
        }

        // Remove emoji reaction
        function removeReaction(msgId, emoji) {
            toggleReactionOnServer(msgId, emoji);
        }

        // Render reactions below message
        function renderReactions(msgId) {
            const el = document.querySelector(`[data-message-id="${msgId}"]`);
            if (!el) return;
            const container = el.querySelector('.message-reactions-container');
            if (!container) return;

            const reactions = messageReactions[msgId] || [];
            container.innerHTML = '';

            if (reactions.length === 0) {
                container.classList.add('hidden');
                return;
            }

            container.classList.remove('hidden');
            reactions.forEach(react => {
                const badge = document.createElement('button');
                const userReacted = react.user_reacted;
                badge.className = `flex items-center gap-1.5 px-2.5 py-0.5 rounded-full ${userReacted ? 'bg-[#DA291C]/10 border-[#DA291C]/35 text-[#DA291C]' : 'bg-gray-100 border-gray-200 text-gray-700'} hover:bg-gray-200 border text-xs font-bold transition-all cursor-pointer active:scale-95 focus:outline-none`;
                badge.innerHTML = `<span>${react.emoji}</span> <span class="text-gray-450 font-extrabold text-[10px]">${react.count}</span>`;
                badge.onclick = (e) => {
                    e.stopPropagation();
                    removeReaction(msgId, react.emoji);
                };
                container.appendChild(badge);
            });
        }

        // Context menu click options
        function handleMenuOption(option, event) {
            if (event) event.stopPropagation();

            if (!currentMessageId) return;

            const msgEl = document.querySelector(`[data-message-id="${currentMessageId}"]`);
            if (!msgEl) return;

            const textSpan = msgEl.querySelector('.message-text');
            const text = textSpan ? textSpan.textContent.trim() : '';
            const senderSpan = msgEl.querySelector('.message-sender');
            const sender = senderSpan ? senderSpan.textContent.trim() : '';
            const timeSpan = msgEl.querySelector('.message-time');
            const time = timeSpan ? timeSpan.textContent.trim() : '';

            if (option === 'info') {
                document.getElementById('info-modal-sender').textContent = sender;
                document.getElementById('info-modal-time').textContent = time;

                const modal = document.getElementById('msg-info-modal');
                const content = document.getElementById('msg-info-modal-content');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                }, 10);

                closeAllMenus();
            } else if (option === 'edit') {
                editingMessageId = currentMessageId;
                cancelReply(); // Clear reply if editing

                const input = document.getElementById('chat-message-input');
                input.value = text;
                input.focus();

                document.getElementById('edit-body').textContent = text.length > 50 ? text.substring(0, 50) + '...' : text;
                document.getElementById('edit-preview-pill').classList.remove('hidden');
                document.getElementById('edit-preview-pill').classList.add('flex');

                closeAllMenus();
            } else if (option === 'reply') {
                replyToMessage(currentMessageId);
            } else if (option === 'react_trigger') {
                const reactBtn = document.getElementById('menu-react-btn');
                const btnRect = reactBtn.getBoundingClientRect();
                const emojiPopup = document.getElementById('emoji-reaction-popup');
                emojiPopup.classList.remove('hidden');

                const popupWidth = emojiPopup.offsetWidth || 230;
                if (btnRect.left > popupWidth + 20) {
                    emojiPopup.style.left = `${btnRect.left - popupWidth - 8}px`;
                } else {
                    emojiPopup.style.left = `${btnRect.right + 8}px`;
                }
                emojiPopup.style.top = `${btnRect.top}px`;
            } else if (option === 'copy') {
                navigator.clipboard.writeText(text).then(() => {
                    showToast("Mensagem copiada!");
                }).catch(err => {
                    console.error("Erro ao copiar: ", err);
                });
                closeAllMenus();
            } else if (option === 'delete') {
                if (confirm("Deseja realmente apagar esta mensagem?")) {
                    fetch(`/messages/${currentMessageId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                msgEl.classList.add('transition-all', 'duration-300', 'opacity-0', 'scale-95');
                                setTimeout(() => {
                                    msgEl.remove();
                                    showToast("Mensagem apagada.");
                                }, 300);
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            showToast("Erro ao apagar mensagem.");
                        });
                }
                closeAllMenus();
            }
        }

        // Close Message Info Modal
        function closeMsgInfoModal() {
            const modal = document.getElementById('msg-info-modal');
            const content = document.getElementById('msg-info-modal-content');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }

        // Set reply state
        function replyToMessage(msgId) {
            cancelEdit(); // Clear edit if replying

            replyingToMessage = msgId;
            const msgEl = document.querySelector(`[data-message-id="${msgId}"]`);
            if (!msgEl) return;

            const textSpan = msgEl.querySelector('.message-text');
            const text = textSpan ? textSpan.textContent.trim() : '';
            const senderSpan = msgEl.querySelector('.message-sender');
            const sender = senderSpan ? senderSpan.textContent.trim() : '';

            document.getElementById('reply-sender').textContent = sender;
            document.getElementById('reply-body').textContent = text.length > 50 ? text.substring(0, 50) + '...' : text;
            document.getElementById('reply-preview-pill').classList.remove('hidden');
            document.getElementById('reply-preview-pill').classList.add('flex');

            document.getElementById('chat-message-input').focus();
            closeAllMenus();
        }

        // Cancel reply state
        function cancelReply(event) {
            if (event) event.stopPropagation();
            replyingToMessage = null;
            document.getElementById('reply-preview-pill').classList.remove('flex');
            document.getElementById('reply-preview-pill').classList.add('hidden');
        }

        // Cancel edit state
        function cancelEdit(event) {
            if (event) event.stopPropagation();
            editingMessageId = null;
            document.getElementById('edit-preview-pill').classList.remove('flex');
            document.getElementById('edit-preview-pill').classList.add('hidden');
            document.getElementById('chat-message-input').value = '';
        }

        // Toggle input emoji picker
        function toggleInputEmojiPicker(event) {
            event.stopPropagation();
            const picker = document.getElementById('input-emoji-picker');
            if (!picker) return;
            const isHidden = picker.classList.contains('hidden');

            closeAllMenus();

            if (isHidden) {
                const rect = event.currentTarget.getBoundingClientRect();
                picker.classList.remove('hidden');

                const pickerWidth = picker.offsetWidth || 192;
                const pickerHeight = picker.offsetHeight || 100;

                // Posiciona acima do botão
                picker.style.top = `${rect.top - pickerHeight - 8}px`;
                picker.style.left = `${rect.left + (rect.width / 2) - (pickerWidth / 2)}px`;
            }
        }

        // Insert selected emoji into input text
        function insertEmoji(emoji) {
            const input = document.getElementById('chat-message-input');
            if (input) {
                input.value += emoji;
                input.focus();
            }
            closeAllMenus();
        }

        // Trigger attachment input file click
        function triggerFileInput() {
            const fileInput = document.getElementById('chat-file-input');
            if (fileInput) fileInput.click();
        }

        // Handle file selection from filesystem
        function handleFileSelected(event) {
            const file = event.target.files[0];
            if (!file) return;

            attachedFile = file;

            let sizeStr = '';
            if (file.size < 1024 * 1024) {
                sizeStr = `${(file.size / 1024).toFixed(1)} KB`;
            } else {
                sizeStr = `${(file.size / (1024 * 1024)).toFixed(1)} MB`;
            }

            const filenameEl = document.getElementById('attachment-filename');
            const filesizeEl = document.getElementById('attachment-filesize');
            const previewEl = document.getElementById('attachment-preview-pill');

            if (filenameEl) filenameEl.textContent = file.name;
            if (filesizeEl) filesizeEl.textContent = `(${sizeStr})`;
            if (previewEl) {
                previewEl.classList.remove('hidden');
                previewEl.classList.add('flex');
            }
        }

        // Cancel / Remove attachment
        function cancelAttachment(event) {
            if (event) event.stopPropagation();
            attachedFile = null;
            const fileInput = document.getElementById('chat-file-input');
            if (fileInput) fileInput.value = '';

            const previewEl = document.getElementById('attachment-preview-pill');
            if (previewEl) {
                previewEl.classList.remove('flex');
                previewEl.classList.add('hidden');
            }
        }

        // Generate HTML for attachment bubbles
        function createAttachmentElement(id, file, time, sender, isUser) {
            const wrapper = document.createElement('div');
            wrapper.setAttribute('data-message-id', id);

            const isImage = file.type.startsWith('image/');
            let contentHtml = '';
            const bgClass = isUser ? 'bg-[#DA291C]' : 'bg-[#EDEDED]';
            const textClass = isUser ? 'text-white' : 'text-gray-800';
            const borderClass = isUser ? 'border-white/10' : 'border-gray-300/30';
            const subtextClass = isUser ? 'text-white/90' : 'text-gray-500';

            if (isImage) {
                const fileUrl = URL.createObjectURL(file);
                contentHtml = `<img src="${fileUrl}" alt="Anexo" class="w-full max-w-xs object-cover rounded-xl border ${borderClass} mb-1">`;
            } else {
                const downloadIconColor = isUser ? 'text-white' : 'text-gray-700';
                const iconBg = isUser ? 'bg-white/15' : 'bg-black/5';
                contentHtml = `
                    <div class="p-3 flex items-center gap-3 ${iconBg} rounded-xl mb-1">
                        <svg class="w-8 h-8 ${downloadIconColor}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5-3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <div class="text-left ${textClass}">
                            <p class="text-xs font-bold truncate max-w-[150px]">${file.name}</p>
                            <span class="text-[10px] font-bold opacity-80 uppercase">Arquivo anexado</span>
                        </div>
                    </div>
                `;
            }

            if (isUser) {
                wrapper.className = "group flex items-center justify-end gap-3 max-w-[85%] ml-auto relative chat-message animate-fade-in mt-2";
                wrapper.innerHTML = `
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                        <button onclick="replyToMessage('${id}')" class="text-gray-400 hover:text-gray-700 transition-colors p-1.5 rounded-full hover:bg-gray-100 focus:outline-none cursor-pointer" title="Responder">
                            <svg class="w-4 h-4 transform scale-x-[-1]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                            </svg>
                        </button>
                        <button onclick="reactToMessage(event, '${id}')" class="text-gray-400 hover:text-gray-700 transition-colors p-1.5 rounded-full hover:bg-gray-100 focus:outline-none cursor-pointer" title="Reagir">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex flex-col items-end gap-1 max-w-[90%] relative">
                        <div class="relative p-4 text-white text-sm font-semibold rounded-2xl rounded-tr-none shadow-md bg-[#DA291C] flex flex-col gap-2 pr-8 message-bubble-content transition-all duration-300">
                            <button onclick="toggleMessageMenu(event, '${id}')" class="absolute top-3.5 right-3 text-white/70 hover:text-white focus:outline-none cursor-pointer transition-opacity opacity-0 group-hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            ${contentHtml}
                            <div class="flex justify-between items-center text-[9px] font-extrabold tracking-wider text-white/90 uppercase border-t border-white/10 pt-1.5 gap-4">
                                <span class="message-sender">${sender}</span>
                                <span class="message-time">${time}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 mt-1 empty:hidden message-reactions-container"></div>
                    </div>
                `;
            } else {
                wrapper.className = "group flex items-center gap-3 max-w-[85%] relative chat-message animate-fade-in mt-2";
                wrapper.innerHTML = `
                    <div class="flex flex-col items-start gap-1 max-w-[90%] relative">
                        <div class="relative p-4 bg-[#EDEDED] text-gray-800 text-sm font-semibold rounded-2xl rounded-tl-none border border-transparent flex flex-col gap-2 pr-8 message-bubble-content transition-all duration-300">
                            <button onclick="toggleMessageMenu(event, '${id}')" class="absolute top-3.5 right-3 text-gray-400 hover:text-gray-755 focus:outline-none cursor-pointer transition-opacity opacity-0 group-hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            ${contentHtml}
                            <div class="flex justify-between items-center text-[9px] font-extrabold tracking-wider text-gray-500 uppercase border-t border-gray-300/30 pt-1.5 gap-4">
                                <span class="message-sender">${sender}</span>
                                <span class="message-time">${time}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 mt-1 empty:hidden message-reactions-container"></div>
                    </div>
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                        <button onclick="reactToMessage(event, '${id}')" class="text-gray-400 hover:text-gray-700 transition-colors p-1.5 rounded-full hover:bg-gray-100 focus:outline-none cursor-pointer" title="Reagir">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                            </svg>
                        </button>
                        <button onclick="replyToMessage('${id}')" class="text-gray-400 hover:text-gray-700 transition-colors p-1.5 rounded-full hover:bg-gray-100 focus:outline-none cursor-pointer" title="Responder">
                            <svg class="w-4 h-4 transform scale-x-[-1]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                            </svg>
                        </button>
                    </div>
                `;
            }
            return wrapper;
        }

        // Smooth scroll to referenced message and trigger high-end pulse animation
        function scrollToMessage(id) {
            const el = document.querySelector(`[data-message-id="${id}"]`);
            if (!el) return;
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });

            const bubble = el.querySelector('.message-bubble-content');
            if (bubble) {
                bubble.classList.add('ring-2', 'ring-[#DA291C]', 'ring-offset-2', 'scale-[1.02]');
                setTimeout(() => {
                    bubble.classList.remove('ring-2', 'ring-[#DA291C]', 'ring-offset-2', 'scale-[1.02]');
                }, 1500);
            }
        }

        // Toast alert builder
        function showToast(message) {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = "px-4 py-2.5 bg-gray-900/90 backdrop-blur text-white text-xs font-bold rounded-xl shadow-lg border border-white/10 transition-all duration-300 transform translate-y-[-10px] opacity-0 flex items-center gap-2";
            toast.innerHTML = `
                <span class="w-1.5 h-1.5 rounded-full bg-[#DA291C]"></span>
                <span>${message}</span>
            `;
            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('translate-y-[-10px]', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            }, 10);

            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-[-10px]', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 2500);
        }

        // Generate HTML for message bubbles
        function createMessageElement(id, text, time, sender, isUser, replyInfo = null) {
            const wrapper = document.createElement('div');
            wrapper.setAttribute('data-message-id', id);

            let replyHtml = '';
            if (replyInfo) {
                const replyBg = isUser ? 'bg-white/15' : 'bg-black/5';
                const replyTextClass = isUser ? 'text-white/80' : 'text-gray-650';
                const replySenderClass = isUser ? 'text-white' : 'text-[#DA291C]';
                const borderCol = isUser ? 'border-white' : 'border-[#DA291C]';
                replyHtml = `
                    <div class="px-3 py-1.5 ${replyBg} rounded-lg border-l-2 ${borderCol} text-xs mb-1.5 flex flex-col opacity-85 cursor-pointer hover:opacity-100 transition-opacity" onclick="scrollToMessage('${replyInfo.id}')">
                        <span class="font-extrabold text-[10px] ${replySenderClass} uppercase leading-none mb-0.5">${replyInfo.sender}</span>
                        <span class="truncate ${replyTextClass} leading-tight">${replyInfo.text}</span>
                    </div>
                `;
            }

            if (isUser) {
                wrapper.className = "group flex items-center justify-end gap-3 max-w-[85%] ml-auto relative chat-message animate-fade-in";
                wrapper.innerHTML = `
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                        <button onclick="replyToMessage('${id}')" class="text-gray-400 hover:text-gray-700 transition-colors p-1.5 rounded-full hover:bg-gray-100 focus:outline-none cursor-pointer" title="Responder">
                            <svg class="w-4 h-4 transform scale-x-[-1]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                            </svg>
                        </button>
                        <button onclick="reactToMessage(event, '${id}')" class="text-gray-400 hover:text-gray-700 transition-colors p-1.5 rounded-full hover:bg-gray-100 focus:outline-none cursor-pointer" title="Reagir">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex flex-col items-end gap-1 max-w-[90%] relative">
                        <div class="relative p-4 text-white text-sm font-semibold rounded-2xl rounded-tr-none shadow-md bg-[#DA291C] flex flex-col gap-2 pr-8 message-bubble-content transition-all duration-300">
                            <button onclick="toggleMessageMenu(event, '${id}')" class="absolute top-3.5 right-3 text-white/70 hover:text-white focus:outline-none cursor-pointer transition-opacity opacity-0 group-hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            ${replyHtml}
                            <span class="message-text">${text}</span>
                            <div class="flex justify-between items-center text-[9px] font-extrabold tracking-wider text-white/90 uppercase border-t border-white/10 pt-1.5">
                                <span class="message-sender">${sender}</span>
                                <span class="message-time">${time}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 mt-1 empty:hidden message-reactions-container"></div>
                    </div>
                `;
            } else {
                wrapper.className = "group flex items-center gap-3 max-w-[85%] relative chat-message animate-fade-in";
                wrapper.innerHTML = `
                    <div class="flex flex-col items-start gap-1 max-w-[90%] relative">
                        <div class="relative p-4 bg-[#EDEDED] text-gray-800 text-sm font-semibold rounded-2xl rounded-tl-none border border-transparent flex flex-col gap-2 pr-8 message-bubble-content transition-all duration-300">
                            <button onclick="toggleMessageMenu(event, '${id}')" class="absolute top-3.5 right-3 text-gray-400 hover:text-gray-755 focus:outline-none cursor-pointer transition-opacity opacity-0 group-hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            ${replyHtml}
                            <span class="message-text">${text}</span>
                            <div class="flex justify-between items-center text-[9px] font-extrabold tracking-wider text-gray-500 uppercase border-t border-gray-300/30 pt-1.5">
                                <span class="message-sender">${sender}</span>
                                <span class="message-time">${time}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1 mt-1 empty:hidden message-reactions-container"></div>
                    </div>
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                        <button onclick="reactToMessage(event, '${id}')" class="text-gray-400 hover:text-gray-700 transition-colors p-1.5 rounded-full hover:bg-gray-100 focus:outline-none cursor-pointer" title="Reagir">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                            </svg>
                        </button>
                        <button onclick="replyToMessage('${id}')" class="text-gray-400 hover:text-gray-700 transition-colors p-1.5 rounded-full hover:bg-gray-100 focus:outline-none cursor-pointer" title="Responder">
                            <svg class="w-4 h-4 transform scale-x-[-1]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                            </svg>
                        </button>
                    </div>
                `;
            }
            return wrapper;
        }

        // Set of added file urls to avoid duplicating in sidebar
        const addedFileUrls = new Set();

        // Initialize already existing file urls from PHP
        @if($activeSolicitation && $activeSolicitation->file_path && is_array($activeSolicitation->file_path))
            @foreach($activeSolicitation->file_path as $path)
                addedFileUrls.add("{{ Storage::url($path) }}");
            @endforeach
        @endif
        @if($activeSolicitation && $activeSolicitation->messages)
            @foreach($activeSolicitation->messages as $msg)
                @if($msg->file_path)
                    addedFileUrls.add("{{ asset('storage/' . $msg->file_path) }}");
                @endif
            @endforeach
        @endif

        // Initialize messageReactions from PHP
        @if($activeSolicitation && $activeSolicitation->messages)
            @foreach($activeSolicitation->messages as $msg)
                messageReactions['{{ $msg->id }}'] = [
                    @if($msg->reactions)
                        @foreach($msg->reactions as $emoji => $users)
                                        {
                            emoji: '{{ $emoji }}',
                            count: {{ count($users) }},
                            user_reacted: {{ in_array(auth()->id(), $users) ? 'true' : 'false' }}
                                        },
                        @endforeach
                    @endif
                        ];
            @endforeach
        @endif

            // Add file url to sidebar attachments dynamically
            function addFileUrlToSidebar(fileUrl, fileName, fileType) {
                if (addedFileUrls.has(fileUrl)) return;
                addedFileUrls.add(fileUrl);

                const container = document.getElementById('sidebar-attachments-container');
                const countEl = document.getElementById('sidebar-attachments-count');
                if (!container) return;

                sidebarFilesCount++;
                if (countEl) countEl.textContent = sidebarFilesCount;

                const aEl = document.createElement('a');

                if (fileType === 'image') {
                    aEl.href = "javascript:void(0)";
                    aEl.onclick = function () { openImageLightbox(fileUrl); };
                    aEl.className = "w-[84px] aspect-square rounded-xl overflow-hidden border border-gray-250 hover:opacity-90 transition-all flex-shrink-0";
                    aEl.innerHTML = `<img src="${fileUrl}" alt="Preview" class="w-full h-full object-cover">`;
                } else {
                    aEl.href = fileUrl;
                    aEl.download = fileName;
                    aEl.className = "w-[84px] aspect-square rounded-xl bg-gray-50 flex flex-col items-center justify-center border border-gray-250 hover:bg-gray-100 transition-colors flex-shrink-0";
                    aEl.innerHTML = `
                    <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5-3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <span class="text-[8px] font-bold text-gray-500 uppercase mt-1">DOC</span>
                `;
                }

                container.appendChild(aEl);
            }

        // Append a new message or update an existing message in DOM dynamically
        function appendOrUpdateMessage(msg) {
            const container = document.getElementById('chat-messages-container');
            if (!container) return;

            // Check if message is already in DOM
            let existingMsgEl = document.querySelector(`[data-message-id="${msg.id}"]`);
            if (existingMsgEl) {
                // Se for do tipo videocall, re-renderiza o HTML interno dele se o status mudou
                if (msg.type === 'videocall' && msg.metadata) {
                    // maybeAutoJoinClientVideoCall(msg); // Disabled: auto‑join removed
                    const card = existingMsgEl.querySelector('.videocall-card');
                    const isEnded = msg.metadata.status === 'ended';
                    if (card && isEnded && !card.classList.contains('ended')) {
                        existingMsgEl.setAttribute('data-videocall-active', '0');
                        const meta = msg.metadata;
                        const initiatedBy = (meta.initiated_by || msg.sender || 'Sistema').toUpperCase();
                        const endedAtStr = meta.ended_at ? new Date(meta.ended_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
                        existingMsgEl.innerHTML = `
                            <div class="videocall-card ended">
                                <div class="videocall-card-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" class="w-6 h-6">
                                        <path d="M4.5 4.5A2.25 2.25 0 0 0 2.25 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-10.5A2.25 2.25 0 0 0 15 4.5H4.5ZM19.24 7.63l-2.99 2.24v4.26l2.99 2.24A1.5 1.5 0 0 0 21.75 15.1V8.9a1.5 1.5 0 0 0-2.51-1.27Z"/>
                                    </svg>
                                </div>
                                <div class="videocall-card-title">Chamada encerrada</div>
                                <div class="videocall-card-subtitle">por ${initiatedBy} &bull; ${msg.time || ''}</div>
                                <span style="font-size: 11px; color: rgba(255,255,255,0.4); font-weight:700; margin-top:4px;">
                                    Encerrada por ${meta.ended_by || 'Sistema'} às ${endedAtStr}
                                </span>
                            </div>
                        `;
                    }
                } else {
                    // Update text/time if changed
                    const textSpan = existingMsgEl.querySelector('.message-text');
                    if (textSpan && msg.text && textSpan.textContent !== msg.text) {
                        textSpan.textContent = msg.text;
                    }
                    const timeSpan = existingMsgEl.querySelector('.message-time');
                    if (timeSpan && timeSpan.textContent !== msg.time) {
                        timeSpan.textContent = msg.time;
                    }
                }
                // Reapply search filter if active
                const searchInput = document.getElementById('chat-search-input');
                if (searchInput && searchInput.value.trim().length > 0) {
                    const query = searchInput.value.toLowerCase().trim();
                    const textContent = existingMsgEl.textContent.toLowerCase();
                    if (textContent.includes(query)) {
                        existingMsgEl.style.setProperty('display', '', 'important');
                    } else {
                        existingMsgEl.style.setProperty('display', 'none', 'important');
                    }
                }
                // Update reactions
                messageReactions[msg.id] = msg.reactions;
                renderReactions(msg.id);
                return;
            }

            // Create new message DOM element
            const wrapper = document.createElement('div');
            wrapper.setAttribute('data-message-id', msg.id);

            // === VIDEOCALL CARD ===
            if (msg.type === 'videocall' && msg.metadata) {
                const meta = msg.metadata;
                const meetUrl = meta.meet_url || '#';
                const initiatedBy = (meta.initiated_by || msg.sender || 'Sistema').toUpperCase();
                const isEnded = meta.status === 'ended';
                const isCurrentUser = !!msg.is_user;

                wrapper.className = 'flex justify-center animate-fade-in chat-message';
                wrapper.setAttribute('data-videocall-active', isEnded ? '0' : '1');
                wrapper.setAttribute('data-videocall-meet-url', meetUrl);
                wrapper.setAttribute('data-videocall-is-user', isCurrentUser ? '1' : '0');

                if (isEnded) {
                    const endedAtStr = meta.ended_at ? new Date(meta.ended_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
                    wrapper.innerHTML = `
                        <div class="videocall-card ended">
                            <div class="videocall-card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" class="w-6 h-6">
                                    <path d="M4.5 4.5A2.25 2.25 0 0 0 2.25 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-10.5A2.25 2.25 0 0 0 15 4.5H4.5ZM19.24 7.63l-2.99 2.24v4.26l2.99 2.24A1.5 1.5 0 0 0 21.75 15.1V8.9a1.5 1.5 0 0 0-2.51-1.27Z"/>
                                </svg>
                            </div>
                            <div class="videocall-card-title">Chamada encerrada</div>
                            <div class="videocall-card-subtitle">por ${initiatedBy} &bull; ${msg.time || ''}</div>
                            <span style="font-size: 11px; color: rgba(255,255,255,0.4); font-weight:700; margin-top:4px;">
                                Encerrada por ${meta.ended_by || 'Sistema'} às ${endedAtStr}
                            </span>
                        </div>
                    `;
                } else {
                    const endButtonHtml = (isAtendenteUser || isCurrentUser)
                        ? `<button type="button" onclick="showEndCallModal(${msg.id})" class="videocall-end-btn">Encerrar chamada</button>`
                        : '';

                    wrapper.innerHTML = `
                        <div class="videocall-card">
                            <div class="videocall-card-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" class="w-6 h-6">
                                    <path d="M4.5 4.5A2.25 2.25 0 0 0 2.25 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-10.5A2.25 2.25 0 0 0 15 4.5H4.5ZM19.24 7.63l-2.99 2.24v4.26l2.99 2.24A1.5 1.5 0 0 0 21.75 15.1V8.9a1.5 1.5 0 0 0-2.51-1.27Z"/>
                                </svg>
                            </div>
                            <div class="videocall-card-title">Videochamada iniciada</div>
                            <div class="videocall-card-subtitle">por ${initiatedBy} &bull; ${msg.time || ''}</div>
                            <button type="button" onclick="joinVideoCall('${meetUrl}', '/videocall/${msg.id}/join')" class="videocall-join-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" class="w-4 h-4">
                                    <path d="M4.5 4.5A2.25 2.25 0 0 0 2.25 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-10.5A2.25 2.25 0 0 0 15 4.5H4.5ZM19.24 7.63l-2.99 2.24v4.26l2.99 2.24A1.5 1.5 0 0 0 21.75 15.1V8.9a1.5 1.5 0 0 0-2.51-1.27Z"/>
                                </svg>
                                Entrar na Reunião
                            </button>
                            ${endButtonHtml}
                        </div>
                    `;
                }

                // Insert before typing indicator
                const typingIndicatorVC = document.getElementById('typing-indicator-wrapper');
                if (typingIndicatorVC) {
                    container.insertBefore(wrapper, typingIndicatorVC);
                } else {
                    container.appendChild(wrapper);
                }
                // maybeAutoJoinClientVideoCall(msg); // Disabled: auto‑join removed
                if (msg.id > lastMessageId) lastMessageId = msg.id;
                return;
            }
            // === END VIDEOCALL CARD ===

            const isCurrentUser = msg.is_user;
            const textClass = isCurrentUser ? 'text-white bg-[#DA291C] rounded-2xl rounded-tr-none' : 'bg-[#EDEDED] text-gray-800 rounded-2xl rounded-tl-none border border-transparent';
            const infoColorClass = isCurrentUser ? 'text-white/90 border-white/10' : 'text-gray-500 border-gray-300/30';

            // Build Reply HTML if parent exists
            let replyHtml = '';
            if (msg.parent) {
                const replyBg = isCurrentUser ? 'bg-white/15' : 'bg-black/5';
                const replyTextClass = isCurrentUser ? 'text-white/80' : 'text-gray-650';
                const replySenderClass = isCurrentUser ? 'text-white' : 'text-[#DA291C]';
                const borderCol = isCurrentUser ? 'border-white' : 'border-[#DA291C]';
                replyHtml = `
                    <div class="px-3 py-1.5 ${replyBg} rounded-lg border-l-2 ${borderCol} text-xs mb-1.5 flex flex-col opacity-85 cursor-pointer hover:opacity-100 transition-opacity" onclick="scrollToMessage('${msg.parent.id}')">
                        <span class="font-extrabold text-[10px] ${replySenderClass} uppercase leading-none mb-0.5">${msg.parent.sender}</span>
                        <span class="truncate ${replyTextClass} leading-tight">${msg.parent.text}</span>
                    </div>
                `;
            }

            // Build File HTML if file_url exists
            let fileHtml = '';
            if (msg.file_url) {
                if (msg.file_type === 'image') {
                    fileHtml = `
                        <a href="javascript:void(0)" onclick="openImageLightbox('${msg.file_url}')">
                            <img src="${msg.file_url}" alt="Anexo" class="w-full max-w-xs object-cover rounded-xl border ${isCurrentUser ? 'border-white/10' : 'border-gray-300/30'} mb-1 hover:opacity-95 transition-opacity cursor-pointer">
                        </a>
                    `;
                } else {
                    const downloadIconColor = isCurrentUser ? 'text-white' : 'text-gray-700';
                    const iconBg = isCurrentUser ? 'bg-white/15' : 'bg-black/5';
                    fileHtml = `
                        <a href="${msg.file_url}" download="${msg.file_name}" class="p-3 flex items-center gap-3 ${iconBg} rounded-xl mb-1 hover:opacity-95 transition-opacity">
                            <svg class="w-8 h-8 ${downloadIconColor}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5-3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            <div class="text-left ${isCurrentUser ? 'text-white' : 'text-gray-800'}">
                                <p class="text-xs font-bold truncate max-w-[150px]">${msg.file_name}</p>
                                <span class="text-[10px] font-bold opacity-80 uppercase">Download</span>
                            </div>
                        </a>
                    `;
                }

                // Dynamically add file to sidebar
                addFileUrlToSidebar(msg.file_url, msg.file_name, msg.file_type);
            }

            // Build main text content
            let textHtml = msg.text ? `<span class="message-text">${msg.text}</span>` : '';

            // Build Hover/Context Actions
            let hoverActionsHtml = '';
            if (isCurrentUser) {
                hoverActionsHtml = `
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                        <button onclick="currentMessageId = '${msg.id}'; handleMenuOption('delete')" class="text-gray-400 hover:text-red-500 transition-colors p-1.5 rounded-full hover:bg-gray-100 focus:outline-none cursor-pointer" title="Apagar">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                        <button onclick="currentMessageId = '${msg.id}'; handleMenuOption('edit')" class="text-gray-400 hover:text-gray-755 transition-colors p-1.5 rounded-full hover:bg-gray-100 focus:outline-none cursor-pointer" title="Editar">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        </button>
                        <button onclick="replyToMessage('${msg.id}')" class="text-gray-400 hover:text-gray-755 transition-colors p-1.5 rounded-full hover:bg-gray-100 focus:outline-none cursor-pointer" title="Responder">
                            <svg class="w-4 h-4 transform scale-x-[-1]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                            </svg>
                        </button>
                    </div>
                `;
            } else {
                hoverActionsHtml = `
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                        <button onclick="reactToMessage(event, '${msg.id}')" class="text-gray-400 hover:text-gray-755 transition-colors p-1.5 rounded-full hover:bg-gray-100 focus:outline-none cursor-pointer" title="Reagir">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                            </svg>
                        </button>
                        <button onclick="replyToMessage('${msg.id}')" class="text-gray-400 hover:text-gray-755 transition-colors p-1.5 rounded-full hover:bg-gray-100 focus:outline-none cursor-pointer" title="Responder">
                            <svg class="w-4 h-4 transform scale-x-[-1]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                            </svg>
                        </button>
                    </div>
                `;
            }

            wrapper.className = `group flex items-center ${isCurrentUser ? 'justify-end ml-auto' : 'justify-start mr-auto'} gap-3 max-w-[85%] relative chat-message animate-fade-in`;

            const bubbleHtml = `
                <div class="flex flex-col ${isCurrentUser ? 'items-end' : 'items-start'} gap-1 max-w-[90%] relative">
                    <div class="relative p-4 ${textClass} shadow-md flex flex-col gap-2 pr-8 message-bubble-content transition-all duration-300">
                        <button onclick="toggleMessageMenu(event, '${msg.id}')" class="absolute top-3.5 right-3 ${isCurrentUser ? 'text-white/70 hover:text-white' : 'text-gray-400 hover:text-gray-755'} focus:outline-none cursor-pointer transition-opacity opacity-0 group-hover:opacity-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                        ${replyHtml}
                        ${fileHtml}
                        ${textHtml}
                        <div class="flex justify-between items-center text-[9px] font-extrabold tracking-wider ${infoColorClass} uppercase border-t pt-1.5 gap-4">
                            <span class="message-sender">${msg.sender}</span>
                            <span class="message-time">${msg.time}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 mt-1 empty:hidden message-reactions-container"></div>
                </div>
            `;

            if (isCurrentUser) {
                wrapper.innerHTML = hoverActionsHtml + bubbleHtml;
            } else {
                wrapper.innerHTML = bubbleHtml + hoverActionsHtml;
            }

            // Insert before typing indicator
            const typingIndicator = document.getElementById('typing-indicator-wrapper');
            if (typingIndicator) {
                container.insertBefore(wrapper, typingIndicator);
            } else {
                container.appendChild(wrapper);
            }

            // Render reactions
            messageReactions[msg.id] = msg.reactions;
            renderReactions(msg.id);

            // Update lastMessageId
            if (msg.id > lastMessageId) {
                lastMessageId = msg.id;
            }

            // Apply active search filter to the new message element
            const searchInput = document.getElementById('chat-search-input');
            if (searchInput && searchInput.value.trim().length > 0) {
                const query = searchInput.value.toLowerCase().trim();
                const textContent = wrapper.textContent.toLowerCase();
                if (textContent.includes(query)) {
                    wrapper.style.setProperty('display', '', 'important');
                } else {
                    wrapper.style.setProperty('display', 'none', 'important');
                }
            }
        }

        // =========================================================
        // VIDEOCHAMADA: Initiate video call via API
        function initiateVideoCall(solicitationId) {
            const activeCall = document.querySelector('[data-videocall-active="1"]');
            if (activeCall) {
                showToast('Já existe uma chamada de vídeo ativa para este chamado. Encerre a chamada atual antes de iniciar outra.');
                return;
            }

            fetch('/videocall/initiate', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ solicitation_id: solicitationId })
            })
                .then(async (response) => {
                    const data = await response.json();
                    if (!response.ok || !data.success) {
                        throw new Error(data.error || 'Não foi possível iniciar videochamada.');
                    }
                    return data;
                })
                .then((data) => {
                    if (data.message) {
                        appendOrUpdateMessage(data.message);
                    }

                    if (data.meet_url) {
                        showVideoCallToast(data.meet_url, data.join_url || null);
                    }
                })
                .catch((error) => {
                    console.error(error);
                    showToast(error.message || 'Erro ao iniciar videochamada.');
                });
        }

        function showVideoCallToast(meetUrl, joinUrl = null) {
            let toast = document.getElementById('videocall-toast');
            if (!toast) return;
            const enterBtn = toast.querySelector('.vc-toast-link');
            if (enterBtn) {
                enterBtn.onclick = () => joinVideoCall(meetUrl, joinUrl || meetUrl);
            }
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 6000);
        }

        function isJitsiUrl(url) {
            if (!url) return false;
            try {
                const parsed = new URL(url, window.location.origin);
                return parsed.hostname.includes('meet.jit.si') || parsed.hostname.includes('jitsi');
            } catch (error) {
                return false;
            }
        }

        function joinVideoCall(meetUrl, fallbackUrl = null) {
            const targetUrl = fallbackUrl || meetUrl;
            if (!targetUrl) return;

            // Se tiver o fallbackUrl (rota de join do backend), busca a URL autorizada com JWT via JSON
            if (fallbackUrl) {
                fetch(fallbackUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Falha ao obter URL autorizada.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const authorizedUrl = data.url || meetUrl;
                        if (isJitsiUrl(authorizedUrl)) {
                            openJitsiModal(authorizedUrl);
                        } else {
                            window.open(authorizedUrl, '_blank', 'noopener,noreferrer');
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        if (isJitsiUrl(meetUrl)) {
                            openJitsiModal(meetUrl);
                        } else {
                            window.open(meetUrl, '_blank', 'noopener,noreferrer');
                        }
                    });
            } else {
                if (isJitsiUrl(meetUrl)) {
                    openJitsiModal(meetUrl);
                } else {
                    window.open(meetUrl, '_blank', 'noopener,noreferrer');
                }
            }
        }

        function openJitsiModal(meetUrl) {
            const modal = document.getElementById('jitsi-modal');
            const container = document.getElementById('jitsi-container');
            if (!modal || !container) return;

            if (typeof window.JitsiMeetExternalAPI !== 'function') {
                window.open(meetUrl, '_blank', 'noopener,noreferrer');
                return;
            }

            let parsed;
            try {
                parsed = new URL(meetUrl, window.location.origin);
            } catch (error) {
                window.open(meetUrl, '_blank', 'noopener,noreferrer');
                return;
            }

            const roomName = parsed.pathname.replace(/^\/+/, '').split('/')[0];
            if (!roomName) {
                window.open(meetUrl, '_blank', 'noopener,noreferrer');
                return;
            }

            if (jitsiApi) {
                jitsiApi.dispose();
                jitsiApi = null;
            }

            container.innerHTML = '';
            modal.classList.remove('hidden');

            const jwtToken = parsed.searchParams.get('jwt') || null;

            jitsiApi = new window.JitsiMeetExternalAPI(parsed.hostname, {
                roomName,
                jwt: jwtToken,
                parentNode: container,
                userInfo: {
                    displayName: currentUserDisplayName,
                    email: currentUserEmail,
                },
                configOverwrite: {
                    prejoinPageEnabled: false,
                    prejoinConfig: {
                        enabled: false,
                    },
                    disableProfile: true,
                    settingsSections: ['devices', 'language', 'sounds'],
                    disableDeepLinking: true,
                },
                interfaceConfigOverwrite: {
                    SHOW_JITSI_WATERMARK: false,
                    SHOW_WATERMARK_FOR_GUESTS: false,
                    SETTINGS_SECTIONS: ['devices', 'language', 'sounds'],
                },
            });

            jitsiApi.addListener('videoConferenceLeft', () => {
                closeJitsiModal();
            });
        }

        function closeJitsiModal() {
            const modal = document.getElementById('jitsi-modal');
            const container = document.getElementById('jitsi-container');

            if (jitsiApi) {
                jitsiApi.dispose();
                jitsiApi = null;
            }

            if (container) {
                container.innerHTML = '';
            }

            if (modal) {
                modal.classList.add('hidden');
            }
        }

        function persistAutoJoinedVideoCalls() {
            try {
                sessionStorage.setItem(AUTO_JOIN_STORAGE_KEY, JSON.stringify(Array.from(autoJoinedVideoCalls)));
            } catch (error) {
                // noop
            }
        }

        // ---------------------------------------------------------------------
        // Automatic client video‑call join disabled – users must click "Participar"
        // ---------------------------------------------------------------------
        // function maybeAutoJoinClientVideoCall(msg) {
        //     if (!isClienteUser || !msg || msg.type !== 'videocall' || !msg.metadata) return;
        //     if (msg.metadata.status === 'ended') return;
        //     if (msg.is_user) return;
        //
        //     const messageId = String(msg.id || '');
        //     if (!messageId || autoJoinedVideoCalls.has(messageId)) return;
        //
        //     autoJoinedVideoCalls.add(messageId);
        //     persistAutoJoinedVideoCalls();
        //
        //     setTimeout(() => {
        //         // Cliente entra automaticamente no Jitsi sem precisar clicar em botao.
        //         joinVideoCall(msg.metadata.meet_url, `/videocall/${messageId}/join`);
        //     }, 200);
        // }
        //
        // function autoJoinExistingClientVideoCalls() {
        //     if (!isClienteUser) return;
        //
        //     const cards = document.querySelectorAll('[data-message-id][data-videocall-active="1"]');
        //     cards.forEach((card) => {
        //         const messageId = String(card.getAttribute('data-message-id') || '');
        //         const meetUrl = card.getAttribute('data-videocall-meet-url') || '';
        //         const isUser = card.getAttribute('data-videocall-is-user') === '1';
        //
        //         if (!messageId || !meetUrl || isUser) return;
        //
        //         maybeAutoJoinClientVideoCall({
        //             id: messageId,
        //             type: 'videocall',
        //             is_user: false,
        //             metadata: {
        //                 status: 'active',
        //                 meet_url: meetUrl,
        //             },
        //         });
        //     });
        // }

        // endVideoCall delegates to the styled modal
        function endVideoCall(messageId) {
            showEndCallModal(messageId);
        }

        // Legacy alias removed – endVideoCall now delegates to showEndCallModal

        // Submit chat message (AJAX store/update)
        function sendChatMessage(event) {
            event.preventDefault();
            const input = document.getElementById('chat-message-input');
            const text = input.value.trim();

            // Se não houver texto E não houver anexo, não envia nada
            if (!text && !attachedFile) return;

            const container = document.getElementById('chat-messages-container');
            if (!container) return;

            // If in edit mode:
            if (editingMessageId) {
                fetch(`/messages/${editingMessageId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ text: text })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const targetEl = document.querySelector(`[data-message-id="${editingMessageId}"]`);
                            if (targetEl) {
                                const textSpan = targetEl.querySelector('.message-text');
                                if (textSpan) {
                                    textSpan.textContent = data.text;
                                }
                                const timeSpan = targetEl.querySelector('.message-time');
                                if (timeSpan && !timeSpan.textContent.includes('EDITADA')) {
                                    timeSpan.textContent += ' (EDITADA)';
                                }
                            }
                            showToast("Mensagem atualizada.");
                            cancelEdit();
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        showToast("Erro ao editar mensagem.");
                    });
                return;
            }

            // Send new message via AJAX
            const formData = new FormData();
            if (text) {
                formData.append('text', text);
            }
            if (attachedFile) {
                formData.append('file', attachedFile);
            }
            if (replyingToMessage) {
                formData.append('parent_id', replyingToMessage);
            }

            input.disabled = true;

            fetch(`/solicitations/{{ $activeSolicitation->id ?? 0 }}/messages`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(response => {
                    console.log("Send Message Response Status:", response.status, "URL:", response.url);
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.indexOf("application/json") !== -1) {
                        return response.json();
                    } else {
                        return response.text().then(text => {
                            console.error("Received non-JSON response:", text.substring(0, 800));
                            throw new Error("Resposta inesperada do servidor (HTML/Texto). URL: " + response.url);
                        });
                    }
                })
                .then(data => {
                    input.disabled = false;
                    input.focus();

                    if (data.success) {
                        // Clear inputs
                        input.value = '';
                        cancelAttachment();
                        cancelReply();

                        // Render dynamically
                        appendOrUpdateMessage(data.message);

                        // Scroll to bottom
                        container.scrollTop = container.scrollHeight;
                    } else {
                        showToast(data.error || "Erro ao enviar mensagem.");
                    }
                })
                .catch(err => {
                    input.disabled = false;
                    input.focus();
                    console.error("Erro no envio de mensagem:", err);
                    showToast("Erro de conexão ao enviar mensagem.");
                });
        }

        // Polling setup for real-time synchronization
        let isPolling = false;
        let pollingInterval = null;
        let currentSolicitationStatus = '{{ $activeSolicitation->status ?? "" }}';

        function startRealTimePolling() {
            if (pollingInterval) clearInterval(pollingInterval);
            pollingInterval = setInterval(pollUpdates, 3000);
        }

        function pollUpdates() {
            if (isPolling) return;
            isPolling = true;

            fetch(`/solicitations/{{ $activeSolicitation->id ?? 0 }}/messages/updates?last_id=${lastMessageId}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    isPolling = false;
                    if (data.success) {
                        // Detecta mudança de status na_fila → em_atendimento
                        if (currentSolicitationStatus === 'na_fila' && data.solicitation_status !== 'na_fila') {
                            currentSolicitationStatus = data.solicitation_status;

                            const formWrapper = document.getElementById('chat-input-form-wrapper');
                            if (formWrapper) formWrapper.classList.remove('hidden');

                            // Mostra mensagem de sistema de atendimento iniciado
                            const statusDiv = document.getElementById('sistema-atendimento-iniciado');
                            if (statusDiv) statusDiv.classList.remove('hidden');

                            const noticeText = document.getElementById('atendimento-iniciado-text');
                            if (noticeText && data.atendente_name) {
                                if (isAtendenteUser) {
                                    noticeText.textContent = `Você iniciou o atendimento com ${activeSolicitationUserName}`;
                                } else {
                                    noticeText.textContent = `${data.atendente_name} irá dar continuidade ao seu atendimento`;
                                }
                            }

                            const atendenteName = data.atendente_name || 'Atendente';
                            showToast(`${atendenteName} assumiu seu atendimento!`);
                        } else {
                            currentSolicitationStatus = data.solicitation_status;
                        }

                        // Sincroniza estado do input para ambos os lados (cliente e atendente)
                        const formWrapper = document.getElementById('chat-input-form-wrapper');
                        if (formWrapper) {
                            if (data.solicitation_status === 'na_fila') {
                                formWrapper.classList.add('hidden');
                            } else if (isClienteUser && isClosedStatus(data.solicitation_status)) {
                                formWrapper.classList.add('hidden');
                            } else {
                                formWrapper.classList.remove('hidden');
                            }
                        }

                        // Sincroniza botões de videochamada
                        const btnVcAtendente = document.getElementById('btn-videocall-atendente');
                        const btnVcUser = document.getElementById('btn-videocall-user');
                        if (data.solicitation_status === 'na_fila') {
                            if (btnVcAtendente) btnVcAtendente.classList.add('hidden');
                            if (btnVcUser) btnVcUser.classList.add('hidden');
                        } else {
                            if (btnVcAtendente) btnVcAtendente.classList.remove('hidden');
                            if (btnVcUser) btnVcUser.classList.remove('hidden');
                        }

                        updateClientClosureUI(data.solicitation_status);

                        // Atualiza aviso de atendimento iniciado com nome do atendente quando disponível
                        const noticeText = document.getElementById('atendimento-iniciado-text');
                        if (noticeText && data.atendente_name && data.solicitation_status !== 'na_fila') {
                            if (isAtendenteUser) {
                                noticeText.textContent = `Você iniciou o atendimento com ${activeSolicitationUserName}`;
                            } else {
                                noticeText.textContent = `${data.atendente_name} irá dar continuidade ao seu atendimento`;
                            }
                        }

                        // Atualiza texto inline da fila (seguindo layout do Figma)
                        const queueInlineEl = document.getElementById('queue-status-inline');
                        if (queueInlineEl && data.solicitation_status === 'na_fila' && data.queue_position) {
                            queueInlineEl.textContent = `Chamado recebido ID ${activeSolicitationTicket}! Você é o ${data.queue_position}º na fila. Tempo médio de espera: 20 minutos`;
                        }

                        // Render new messages
                        if (data.new_messages && data.new_messages.length > 0) {
                            data.new_messages.forEach(msg => {
                                appendOrUpdateMessage(msg);
                            });
                            const container = document.getElementById('chat-messages-container');
                            if (container) {
                                container.scrollTop = container.scrollHeight;
                            }
                        }

                        // Update states for all messages (reactions, edits, deleted)
                        if (data.updated_states) {
                            Object.keys(data.updated_states).forEach(msgId => {
                                const state = data.updated_states[msgId];
                                const msgEl = document.querySelector(`[data-message-id="${msgId}"]`);
                                if (msgEl) {
                                    if (state.type === 'videocall') {
                                        state.id = msgId;
                                        appendOrUpdateMessage(state);
                                    } else {
                                        // Update text
                                        const textSpan = msgEl.querySelector('.message-text');
                                        if (textSpan && state.text && textSpan.textContent !== state.text) {
                                            textSpan.textContent = state.text;
                                        }
                                        // Update time
                                        const timeSpan = msgEl.querySelector('.message-time');
                                        if (timeSpan && timeSpan.textContent !== state.time) {
                                            timeSpan.textContent = state.time;
                                        }
                                    }
                                }

                                // Update reactions
                                messageReactions[msgId] = state.reactions;
                                renderReactions(msgId);
                            });

                            // Remove messages that exist in DOM but are NOT in the database anymore (were deleted on server)
                            const domMessages = document.querySelectorAll('.chat-message');
                            domMessages.forEach(msgEl => {
                                const msgId = msgEl.getAttribute('data-message-id');
                                // Exclude non-database messages like 'opening'
                                if (msgId && msgId !== 'opening' && !data.updated_states[msgId]) {
                                    msgEl.remove();
                                }
                            });
                        }

                        // Sync solicitation status (ex: system automated messages showing up)
                        const statusDiv = document.getElementById('sistema-atendimento-iniciado');
                        if (statusDiv) {
                            const inQueue = data.solicitation_status === 'na_fila' || data.solicitation_status === 'aberta' || data.solicitation_status === 'nova';
                            if (!inQueue) {
                                statusDiv.classList.remove('hidden');
                            }
                        }
                    }
                })
                .catch(err => {
                    isPolling = false;
                    console.error("Erro no polling de atualizações: ", err);
                });
        }
        // Image Lightbox functions
        function openImageLightbox(src) {
            const modal = document.getElementById('image-lightbox-modal');
            const img = document.getElementById('lightbox-image-el');
            const link = document.getElementById('lightbox-download-link');
            if (modal && img) {
                img.src = src;
                if (link) link.href = src;
                modal.classList.remove('hidden');
            }
        }

        function closeImageLightbox() {
            const modal = document.getElementById('image-lightbox-modal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        // Permitir fechar o lightbox com a tecla ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeImageLightbox();
            }
        });

        // Filtros e Busca de Mensagens na Barra Lateral
        let activeChatFilter = 'all';

        function setChatFilter(filter) {
            activeChatFilter = filter;

            const btnAll = document.getElementById('filter-all-btn');
            const btnUnread = document.getElementById('filter-unread-btn');

            if (filter === 'all') {
                btnAll.className = "flex-1 py-1.5 rounded-lg text-xs font-bold bg-[#DA291C] text-white transition-all focus:outline-none cursor-pointer text-center shadow-sm";
                btnUnread.className = "flex-1 py-1.5 rounded-lg text-xs font-bold bg-transparent text-gray-600 hover:text-gray-900 transition-all focus:outline-none cursor-pointer text-center";
            } else {
                btnUnread.className = "flex-1 py-1.5 rounded-lg text-xs font-bold bg-[#DA291C] text-white transition-all focus:outline-none cursor-pointer text-center shadow-sm";
                btnAll.className = "flex-1 py-1.5 rounded-lg text-xs font-bold bg-transparent text-gray-600 hover:text-gray-900 transition-all focus:outline-none cursor-pointer text-center";
            }

            applyChatFilters();
        }

        function applyChatFilters() {
            const query = document.getElementById('chat-search-input').value.toLowerCase().trim();
            const items = document.querySelectorAll('.chat-thread-item');
            let hasVisible = false;

            items.forEach(item => {
                const isUnread = item.getAttribute('data-unread') === 'true';
                const title = item.getAttribute('data-title') || '';
                const desc = item.getAttribute('data-desc') || '';

                const matchesFilter = (activeChatFilter === 'all') || (activeChatFilter === 'unread' && isUnread);
                const matchesQuery = !query || title.includes(query) || desc.includes(query);

                if (matchesFilter && matchesQuery) {
                    item.classList.remove('hidden');
                    item.classList.add('flex');
                    hasVisible = true;
                } else {
                    item.classList.remove('flex');
                    item.classList.add('hidden');
                }
            });

            // Mostra ou oculta mensagem de lista vazia
            let emptyMsg = document.getElementById('empty-chat-threads-message');
            if (!emptyMsg) {
                const threadList = document.querySelector('.chat-thread-list');
                if (threadList) {
                    emptyMsg = document.createElement('div');
                    emptyMsg.id = 'empty-chat-threads-message';
                    emptyMsg.className = 'text-center py-12 px-6 hidden';
                    emptyMsg.innerHTML = '<p class="text-sm text-gray-400 font-medium">Nenhum chamado corresponde aos filtros.</p>';
                    threadList.appendChild(emptyMsg);
                }
            }

            if (emptyMsg) {
                if (hasVisible) {
                    emptyMsg.classList.add('hidden');
                } else {
                    emptyMsg.classList.remove('hidden');
                }
            }
        }

        // ---------- End Call Modal ----------
        function showEndCallModal(callId) {
            const modal = document.getElementById('end-call-modal');
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.dataset.callId = callId;
        }

        function closeEndCallModal() {
            const modal = document.getElementById('end-call-modal');
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            delete modal.dataset.callId;
        }

        function confirmEndCall() {
            const modal = document.getElementById('end-call-modal');
            const callId = modal?.dataset?.callId;
            if (!callId) return;

            const btn = document.getElementById('end-call-confirm-btn');
            if (btn) { btn.disabled = true; btn.textContent = 'Encerrando...'; }

            fetch(`/videocall/${callId}/end`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    if (data.message) appendOrUpdateMessage(data.message);
                    showToast('Chamada encerrada com sucesso.');
                } else {
                    showToast(data.error || 'Não foi possível encerrar a chamada.');
                }
            })
            .catch(() => showToast('Falha de conexão ao encerrar a chamada.'))
            .finally(() => closeEndCallModal());
        }

        document.addEventListener('click', function(e) {
            if (e.target.id === 'end-call-cancel-btn') closeEndCallModal();
            if (e.target.id === 'end-call-confirm-btn') confirmEndCall();
            // Fechar ao clicar fora do conteúdo
            const modal = document.getElementById('end-call-modal');
            if (modal && !modal.classList.contains('hidden') && e.target === modal) closeEndCallModal();
        });
    </script>

    {{-- Modal Encerrar Chamada - visual alinhado ao padrão do sistema (Checklist de Solução) --}}
    <div id="end-call-modal"
         class="hidden fixed inset-0 z-[130] bg-black/60 backdrop-blur-sm items-center justify-center p-4 select-none"
         style="font-family: 'AMX', sans-serif;">

        <div class="w-full max-w-[480px] rounded-[24px] shadow-2xl relative flex flex-col transform animate-fade-in overflow-hidden"
             style="background: #FEFEFE; padding: 28px 26px 24px; gap: 0;">

            {{-- Botão fechar --}}
            <button type="button"
                    onclick="closeEndCallModal()"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors focus:outline-none z-10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Título --}}
            <h3 class="checklist-figma-title mb-5">Encerrar Chamada</h3>

            {{-- Corpo: ícone + textos --}}
            <div class="flex flex-col items-center gap-3 mb-6">
                <div class="w-14 h-14 rounded-full flex items-center justify-center shadow-md"
                     style="background: linear-gradient(135deg, #a01724 0%, #DA291C 100%);">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" class="w-7 h-7">
                        <path d="M4.5 4.5A2.25 2.25 0 0 0 2.25 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-10.5A2.25 2.25 0 0 0 15 4.5H4.5ZM19.24 7.63l-2.99 2.24v4.26l2.99 2.24A1.5 1.5 0 0 0 21.75 15.1V8.9a1.5 1.5 0 0 0-2.51-1.27Z" />
                    </svg>
                </div>

                <p class="checklist-figma-question text-center">
                    Deseja realmente encerrar esta videochamada?
                </p>
                <p style="font-size:13px; color:#6b7280; font-weight:500; text-align:center; line-height:1.45;">
                    Esta ação encerrará a chamada para <strong style="color:#374151;">todos os participantes</strong>
                    e não poderá ser desfeita.
                </p>
            </div>

            {{-- Botão confirmar --}}
            <button id="end-call-confirm-btn"
                    type="button"
                    class="checklist-submit-btn w-full mb-3">
                Encerrar chamada
            </button>

            {{-- Cancelar (link discreto) --}}
            <button id="end-call-cancel-btn"
                    type="button"
                    class="checklist-cancel-btn w-full text-center hover:text-gray-800 transition-colors cursor-pointer">
                Cancelar
            </button>
        </div>
    </div>

@endsection