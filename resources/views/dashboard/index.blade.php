@extends('layouts.app')

@section('title', 'Painel Principal - PRISMA Claro')

@section('content')
    <!-- Breadcrumbs & Greeting -->
    <div class="mb-6">
        <div class="text-xs text-gray-500 mb-1">
            <span>Claro Prisma</span> &gt; <span class="font-medium text-gray-700">Home</span>
        </div>
        <h1 class="text-3xl font-bold text-[#A01724]">Bom dia, {{ Auth::user()->name ?? 'Usuário' }}!</h1>
    </div>

    <!-- Banner de Boas-vindas -->
    <div
        class="relative rounded-[32px] border border-[#A01724] overflow-hidden bg-[#86131E] w-full min-h-[220px] lg:h-[340px] mb-8 shadow-sm flex items-center z-0 pt-8 pb-10 pl-6 pr-4 md:pt-[76px] md:pb-[104px] md:pl-[120px] md:pr-[28px]">
        <!-- Imagem de Fundo (Banner) posicionada à esquerda e abaixo -->
        <img src="/img/Banner Bem Vindo.png" alt="Banner Bem Vindo"
            class="absolute inset-0 w-full h-full object-cover object-[70%_100%] select-none pointer-events-none z-0">

        <!-- Painel de Gradiente Vermelho Figma -->
        <div class="absolute inset-0 z-10 pointer-events-none"
            style="background: linear-gradient(269.15deg, rgba(178, 26, 40, 0) 57.62%, rgba(160, 23, 36, 0.5) 67.61%, #86131E 82.88%);">
        </div>

        <!-- Conteúdo do Banner -->
        <div class="relative z-20 max-w-xl text-white flex flex-col gap-[15px]">
            <h2 class="text-3xl md:text-[40px] font-extrabold tracking-tight leading-tight select-none">Seja bem-vindo!</h2>
            @if(Auth::check() && Auth::user()->role !== 'atendente')
            <div>
                <button
                    onclick="openSupportModal()"
                    class="inline-flex items-center gap-2 bg-[#DA291C] hover:bg-[#B31D14] text-white text-xs md:text-sm font-bold px-6 py-3 rounded-full transition-all shadow-lg shadow-red-950/20 active:scale-[0.98] cursor-pointer"
                >
                    <!-- Ícone de suporte/wifi/chat -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.114 5.636a9 9 0 0 1 0 12.728M16.463 8.288a5.25 5.25 0 0 1 0 7.424M6.75 8.25l4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z" />
                    </svg>
                    <span>Para pedir suporte online clique aqui</span>
                </button>
            </div>
            @else
            <div>
                <a
                    href="{{ route('atendente.chat.index') }}"
                    class="inline-flex items-center gap-2 bg-[#DA291C] hover:bg-[#B31D14] text-white text-xs md:text-sm font-bold px-6 py-3 rounded-full transition-all shadow-lg shadow-red-950/20 active:scale-[0.98] cursor-pointer"
                >
                    <!-- Ícone de balões de chat -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                    </svg>
                    <span>Acessar painel de atendimento</span>
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Secção: Últimas Solicitações -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800">Últimas solicitações</h2>
            <!-- Central de Mensagens Button -->
            <a href="{{ Auth::check() && Auth::user()->role === 'atendente' ? route('atendente.chat.index') : route('chat.index') }}"
                style="background: linear-gradient(89.24deg, #A01724 0%, #DA291C 100%);"
                class="flex items-center gap-2 hover:opacity-90 text-white font-semibold px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all shadow-md shadow-red-950/15 cursor-pointer">
                <span>Central de mensagens</span>
                <!-- Ícone de balões de chat -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
            </a>
        </div>

        <!-- Lista de Solicitações -->
        <div class="space-y-6">
            @forelse($solicitations as $solicitation)
                <div
                    class="bg-white p-6 rounded-[24px] border border-gray-100 shadow-sm space-y-4 hover:shadow-md transition-all">

                    <!-- Primeira Linha: Badge & Metadados -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 select-none">
                        <!-- Status Badge -->
                        <div>
                            @if(in_array($solicitation->status, ['na_fila', 'nova', 'aberta']))
                                <span
                                    class="inline-flex items-center gap-1.5 bg-[#1C1C1E] text-white border border-[#1C1C1E] px-3 py-1 rounded-full text-xs font-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                        stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span>Nova Solicitação</span>
                                </span>
                            @elseif(in_array($solicitation->status, ['em_atendimento', 'em_replica']))
                                <span
                                    class="inline-flex items-center gap-1.5 bg-orange-50 text-orange-600 border border-orange-100 px-3 py-1 rounded-full text-xs font-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                        stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                    <span>Em réplica</span>
                                </span>
                            @elseif($solicitation->status === 'respondida')
                                <span
                                    class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                        stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                    </svg>
                                    <span>Respondida</span>
                                </span>
                            @elseif(in_array($solicitation->status, ['nao_resolvida', 'não resolvida']))
                                <span
                                    class="inline-flex items-center gap-1.5 bg-red-50 text-red-600 border border-red-100 px-3 py-1 rounded-full text-xs font-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                        stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 6.75h.008v.008H12v-.008Z" />
                                    </svg>
                                    <span>Não resolvida</span>
                                </span>
                            @elseif($solicitation->status === 'resolvida')
                                <span
                                    class="inline-flex items-center gap-1.5 bg-green-50 text-green-600 border border-green-100 px-3 py-1 rounded-full text-xs font-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                        stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span>Resolvida</span>
                                </span>
                            @endif
                        </div>

                        <!-- Metadados (Data, Hora, ID) -->
                        <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 font-medium">
                            <!-- Data -->
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                                    stroke="currentColor" class="w-4 h-4 text-red-600">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                                </svg>
                                <span>{{ $solicitation->created_at->format('d/m/Y') }}</span>
                            </span>
                            <!-- Hora -->
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                                    stroke="currentColor" class="w-4 h-4 text-red-600">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                <span>{{ $solicitation->created_at->format('H:i:s') }}</span>
                            </span>
                            <!-- ID/Número do Ticket -->
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                                    stroke="currentColor" class="w-4 h-4 text-red-600">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0v3.75" />
                                </svg>
                                <span>{{ $solicitation->ticket_number }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Conteúdo: Título e Descrição -->
                    <div class="space-y-2">
                        <h3 class="text-base font-bold text-gray-900">{{ $solicitation->title }}</h3>
                        <p class="text-xs md:text-sm text-gray-500 font-medium leading-relaxed">{{ $solicitation->description }}
                        </p>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex items-center gap-3 pt-2">
                        @if($solicitation->status === 'resolvida')
                            <button
                                class="bg-[#DA291C] hover:bg-[#B31D14] text-white px-5 py-2 rounded-xl text-xs font-semibold shadow-sm transition-all cursor-pointer">
                                Avaliar
                            </button>
                        @endif
                        @if(Auth::check() && Auth::user()->role === 'atendente' && $solicitation->status === 'na_fila')
                            <form action="{{ route('atendente.solicitations.iniciar', $solicitation) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="bg-[#DA291C] hover:bg-[#B31D14] text-white px-5 py-2 rounded-xl text-xs font-semibold shadow-sm transition-all cursor-pointer">
                                    Iniciar atendimento
                                </button>
                            </form>
                        @endif
                        <a href="{{ Auth::check() && Auth::user()->role === 'atendente' ? route('atendente.chat.index', $solicitation->id) : route('chat.index', $solicitation->id) }}"
                            class="border border-gray-300 hover:bg-gray-50 text-gray-700 px-5 py-2 rounded-xl text-xs font-semibold transition-all cursor-pointer inline-block">
                            Ver chat
                        </a>
                    </div>

                </div>
            @empty
                <div class="bg-white p-8 rounded-[24px] border border-gray-100 shadow-sm text-center text-gray-500 font-medium">
                    Nenhuma solicitação em andamento no momento.
                </div>
            @endforelse
        </div>
    </div>
@endsection