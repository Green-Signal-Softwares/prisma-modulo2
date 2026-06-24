@extends('layouts.app')

@section('title', 'Tickets de suporte - PRISMA Claro')

@section('content')
    <!-- Breadcrumbs -->
    <div class="mb-4">
        <div class="text-xs text-gray-500 mb-1 select-none">
            <span>Claro Prisma</span> &gt; <span class="font-medium text-gray-700">Tickets de suporte</span>
        </div>
    </div>

    <!-- Header com Título e Ações -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 select-none">
        <h1 class="text-3xl font-extrabold text-[#DA291C] tracking-tight">Tickets de suporte</h1>

        <div class="flex items-center gap-3">
            <!-- Histórico Completo Button -->
            <a href="{{ route('atendente.historico') }}"
                style="background: linear-gradient(89.24deg, #A01724 0%, #DA291C 100%);"
                class="flex items-center gap-2.5 hover:opacity-90 text-white font-bold px-6 py-3.5 rounded-[16px] text-sm transition-all shadow-md shadow-red-950/15 cursor-pointer">
                <span>Histórico completo</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-9-9c2.53 0 4.757 1.048 6.34 2.74L21 8.25" />
                </svg>
            </a>

            <!-- Central de Mensagens Button -->
            <a href="{{ route('atendente.chat.index') }}"
                style="background: linear-gradient(89.24deg, #A01724 0%, #DA291C 100%);"
                class="flex items-center gap-2.5 hover:opacity-90 text-white font-bold px-6 py-3.5 rounded-[16px] text-sm transition-all shadow-md shadow-red-950/15 cursor-pointer">
                <span>Central de mensagens</span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd"
                        d="M4.848 2.771A49.144 49.144 0 0 1 12 2.25c2.43 0 4.817.178 7.152.52 1.237.18 2.098 1.222 2.098 2.47v6.75c0 1.248-.86 2.29-2.098 2.47a48.547 48.547 0 0 1-5.187.365c-.173.01-.341.074-.467.19a48.7 48.7 0 0 1-2.9 2.5a.75.75 0 0 1-1.27-.549v-1.87a.75.75 0 0 0-.75-.75 48.735 48.735 0 0 1-4.83-.564C2.86 13.918 2 12.875 2 11.625V5.241c0-1.248.86-2.29 2.098-2.47a49.144 49.144 0 0 1 .75-.07ZM14.25 15v1.859c.277-.28.539-.569.78-.868a48.243 48.243 0 0 0 2.122-2.736.75.75 0 0 1 1.03-.22 47.054 47.054 0 0 0 4.068-3.07.75.75 0 0 1 1.157.6v6.241c0 1.248-.86 2.29-2.098 2.47a47.316 47.316 0 0 1-3.666.363.75.75 0 0 0-.667.75v1.859a.75.75 0 0 1-1.28.55l-2.906-2.772a.75.75 0 0 0-.513-.213 46.857 46.857 0 0 1-1.637-.156.75.75 0 0 1-.61-.75Z"
                        clip-rule="evenodd" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Formulário Único de Filtros e Paginação -->
    <form id="tickets-filter-form" method="GET" action="{{ route('atendente.tickets') }}">
        <!-- Barra de Paginação / Filtros -->
        <div
            class="bg-white rounded-3xl border border-gray-200/80 shadow-sm p-4 mb-6 flex flex-wrap items-center justify-between gap-4 select-none">
            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600 font-semibold">
                <span>Página</span>
                <input type="number" name="page" id="pagination-page-input" value="{{ $solicitations->currentPage() }}"
                    min="1" max="{{ $solicitations->lastPage() }}"
                    class="w-12 h-9 border border-gray-200 rounded-lg text-center font-bold focus:outline-none focus:ring-1 focus:ring-[#DA291C]">

                <!-- Controles de Navegação -->
                <div class="flex items-center gap-1">
                    @if($solicitations->onFirstPage())
                        <span
                            class="w-8 h-8 rounded-lg border border-gray-150 flex items-center justify-center text-gray-300 cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                            </svg>
                        </span>
                    @else
                        <button type="button" onclick="goToPage({{ $solicitations->currentPage() - 1 }})"
                            class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 text-gray-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                            </svg>
                        </button>
                    @endif

                    @php
                        $start = max(1, $solicitations->currentPage() - 2);
                        $end = min($solicitations->lastPage(), $solicitations->currentPage() + 2);
                    @endphp

                    @if($start > 1)
                        <button type="button" onclick="goToPage(1)"
                            class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 text-gray-600 font-bold transition-colors">1</button>
                        @if($start > 2)
                            <span class="text-gray-400 px-0.5">...</span>
                        @endif
                    @endif

                    @for($i = $start; $i <= $end; $i++)
                        @if($i == $solicitations->currentPage())
                            <span
                                class="w-8 h-8 rounded-lg bg-[#A01724] text-white flex items-center justify-center font-bold">{{ $i }}</span>
                        @else
                            <button type="button" onclick="goToPage({{ $i }})"
                                class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 text-gray-600 font-bold transition-colors">{{ $i }}</button>
                        @endif
                    @endfor

                    @if($end < $solicitations->lastPage())
                        @if($end < $solicitations->lastPage() - 1)
                            <span class="text-gray-400 px-0.5">...</span>
                        @endif
                        <button type="button" onclick="goToPage({{ $solicitations->lastPage() }})"
                            class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 text-gray-600 font-bold transition-colors">{{ $solicitations->lastPage() }}</button>
                    @endif

                    @if($solicitations->hasMorePages())
                        <button type="button" onclick="goToPage({{ $solicitations->currentPage() + 1 }})"
                            class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 text-gray-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                    @else
                        <span
                            class="w-8 h-8 rounded-lg border border-gray-150 flex items-center justify-center text-gray-300 cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                                stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </span>
                    @endif
                </div>

                <!-- Page Size -->
                <select name="per_page" onchange="this.form.submit()"
                    class="h-9 px-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none font-semibold text-gray-600 cursor-pointer">
                    <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20 / Pág</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / Pág</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / Pág</option>
                </select>
                <span class="text-xs font-bold text-gray-400">
                    @if($solicitations->total() > 0)
                        {{ ($solicitations->currentPage() - 1) * $solicitations->perPage() + 1 }} -
                        {{ min($solicitations->currentPage() * $solicitations->perPage(), $solicitations->total()) }} de
                        {{ $solicitations->total() }}
                    @else
                        0 de 0
                    @endif
                </span>
            </div>

            <!-- Ações do Filtro -->
            <div class="flex items-center gap-3">
                <a href="{{ route('atendente.tickets') }}"
                    class="text-sm font-bold text-gray-500 hover:text-gray-700 cursor-pointer transition-colors no-underline">Cancelar</a>
                <button type="button" onclick="clearFilters()"
                    class="px-5 py-2.5 bg-red-50 hover:bg-red-100 text-[#DA291C] font-extrabold rounded-full text-xs transition-colors cursor-pointer border-0">Limpar
                    filtros</button>
                <button type="submit"
                    class="px-6 py-2.5 bg-[#DA291C] hover:bg-[#B31D14] text-white font-extrabold rounded-full text-xs transition-colors shadow-md shadow-red-900/10 cursor-pointer border-0">Filtrar</button>
            </div>
        </div>

        <!-- Tabela Principal -->
        <div
            class="bg-white rounded-[20px] border border-gray-200 shadow-sm overflow-hidden select-none mb-10 pt-[40px] pr-[32px] pb-[40px] pl-[32px] flex flex-col gap-[24px]">
            <div class="overflow-x-auto">
                <table class="w-full text-center border-collapse min-w-[1300px]">
                    <thead>
                        <!-- Cabeçalho Principal (Fundo Branco, Bordas Sutis) -->
                        <tr style="font-family: 'AMX', sans-serif; font-weight: 700; font-size: 14px;"
                            class="bg-white text-gray-800 uppercase border-b border-gray-200">
                            <th class="py-4 px-4 w-[140px] text-center">
                                <div class="flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-85">
                                    <span>ID</span>
                                    <svg class="w-3.5 h-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                    </svg>
                                </div>
                            </th>
                            <th class="py-4 px-4 w-[220px] text-center">
                                <div class="flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-85">
                                    <span>Setor</span>
                                    <svg class="w-3.5 h-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                    </svg>
                                </div>
                            </th>
                            <th class="py-4 px-4 w-[130px] text-center">
                                <div class="flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-85">
                                    <span>Fila</span>
                                    <svg class="w-3.5 h-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                    </svg>
                                </div>
                            </th>
                            <th class="py-4 px-4 w-[240px] text-left pl-8">
                                <div class="flex items-center justify-start gap-1.5 cursor-pointer hover:opacity-85">
                                    <span>Responsável</span>
                                    <svg class="w-3.5 h-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                    </svg>
                                </div>
                            </th>
                            <th class="py-4 px-4 text-left">
                                <div class="flex items-center justify-start gap-1.5 cursor-pointer hover:opacity-85">
                                    <span>Assunto</span>
                                    <svg class="w-3.5 h-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                    </svg>
                                </div>
                            </th>
                            <th class="py-4 px-4 w-[160px] text-center">
                                <div class="flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-85">
                                    <span>Status</span>
                                    <svg class="w-3.5 h-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                    </svg>
                                </div>
                            </th>
                            <th class="py-4 px-4 w-[200px] text-center">
                                <div class="flex items-center justify-center gap-1.5 cursor-pointer hover:opacity-85">
                                    <span>Última Atualização</span>
                                    <svg class="w-3.5 h-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                    </svg>
                                </div>
                            </th>
                        </tr>

                        <!-- Linha de Inputs de Filtros (Estilo Figma) -->
                        <tr class="bg-white border-b border-gray-150 text-[11px]">
                            <!-- ID (De/Até) -->
                            <td class="py-3 px-3">
                                <div
                                    class="flex flex-col gap-1 text-gray-500 font-extrabold text-[9px] uppercase tracking-wide">
                                    <div class="flex items-center gap-1">
                                        <span class="w-6 text-right">De:</span>
                                        <input type="text" name="id_de" value="{{ request('id_de') }}"
                                            class="w-full h-8 px-2 border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-[#DA291C] bg-white font-medium">
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="w-6 text-right">Até:</span>
                                        <input type="text" name="id_ate" value="{{ request('id_ate') }}"
                                            class="w-full h-8 px-2 border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-[#DA291C] bg-white font-medium">
                                    </div>
                                </div>
                            </td>
                            <!-- Setor -->
                            <td class="py-3 px-3">
                                <input type="text" name="setor" value="{{ request('setor') }}"
                                    class="w-full h-8 px-2 border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-[#DA291C] bg-white font-medium">
                            </td>
                            <!-- Fila -->
                            <td class="py-3 px-3">
                                <input type="text" name="fila" value="{{ request('fila') }}"
                                    class="w-full h-8 px-2 border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-[#DA291C] bg-white font-medium">
                            </td>
                            <!-- Responsável -->
                            <td class="py-3 px-3">
                                <input type="text" name="responsavel" value="{{ request('responsavel') }}"
                                    class="w-full h-8 px-2 border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-[#DA291C] bg-white font-medium">
                            </td>
                            <!-- Assunto -->
                            <td class="py-3 px-3">
                                <input type="text" name="assunto" value="{{ request('assunto') }}"
                                    class="w-full h-8 px-2 border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-[#DA291C] bg-white font-medium">
                            </td>
                            <!-- Status -->
                            <td class="py-3 px-3">
                                <input type="text" name="status" value="{{ request('status') }}"
                                    class="w-full h-8 px-2 border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-[#DA291C] bg-white font-medium">
                            </td>
                            <!-- Última Atualização (De/Até) -->
                            <td class="py-3 px-3">
                                <div
                                    class="flex flex-col gap-1 text-gray-500 font-extrabold text-[9px] uppercase tracking-wide">
                                    <div class="flex items-center gap-1">
                                        <span class="w-6 text-right">De:</span>
                                        <input type="date" name="atualizacao_de" value="{{ request('atualizacao_de') }}"
                                            class="w-full h-8 px-1 border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-[#DA291C] bg-white font-medium">
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="w-6 text-right">Até:</span>
                                        <input type="date" name="atualizacao_ate" value="{{ request('atualizacao_ate') }}"
                                            class="w-full h-8 px-1 border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-[#DA291C] bg-white font-medium">
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </thead>
                    <tbody class="text-xs text-gray-700 divide-y divide-gray-100 font-semibold">
                        @forelse($solicitations as $solicitation)
                            @php
                                // Extrai categoria
                                $category = 'OUTROS ASSUNTOS';
                                if (preg_match('/^\[(.*?)\]\s*-\s*/', $solicitation->description, $matches) === 1) {
                                    $category = strtoupper(trim($matches[1]));
                                }

                                // Determina fila
                                $checklistEncaminhado = $solicitation->checklists()->whereNotNull('encaminhamento')->latest()->first();
                                $fila = $checklistEncaminhado ? $checklistEncaminhado->encaminhamento : ($solicitation->status === 'na_fila' ? 'Fila de Entrada' : 'Fila Geral');

                                // Limpa descrição do assunto
                                $assunto = preg_replace('/^\[(.*?)\]\s*-\s*/', '', $solicitation->description);
                                if (empty($assunto)) {
                                    $assunto = $solicitation->title;
                                }
                            @endphp
                            <!-- Linha de Registro Clicável para abrir o chat -->
                            <tr class="hover:bg-[#F8F9FA]/80 transition-colors cursor-pointer"
                                onclick="goToChat(event, '{{ route('atendente.chat.index', $solicitation->id) }}')">

                                <!-- ID com botão de copiar -->
                                <td class="py-4 px-4 font-bold text-gray-900 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <span>{{ $solicitation->ticket_number }}</span>
                                        <button type="button"
                                            onclick="copyTicketNumber(event, '{{ $solicitation->ticket_number }}')"
                                            class="text-gray-400 hover:text-gray-700 cursor-pointer transition-colors p-1"
                                            title="Copiar ID">
                                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376A8.965 8.965 0 0 0 12 12.75a8.965 8.965 0 0 0-3.75 1.5m7.5 3v-3.375c0-.621-.504-1.125-1.125-1.125h-9.75a1.125 1.125 0 0 0-1.125 1.125v9.75c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V17.25Z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>

                                <!-- Setor -->
                                <td
                                    class="py-4 px-4 font-extrabold text-[11px] text-center tracking-tight text-gray-700 uppercase">
                                    {{ $category }}
                                </td>

                                <!-- Fila -->
                                <td class="py-4 px-4 text-center font-bold text-gray-500">
                                    {{ $fila }}
                                </td>

                                <!-- Responsável com Foto de Perfil -->
                                <td class="py-4 px-4 text-left pl-8">
                                    @if($solicitation->atendente)
                                        <div class="flex items-center gap-2">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($solicitation->atendente->name) }}&background=EAA8A8&color=86131E&bold=true&rounded=true"
                                                alt="Avatar" class="w-6 h-6 rounded-full object-cover">
                                            <span
                                                class="font-extrabold text-gray-800 text-[10px] tracking-tight uppercase">{{ $solicitation->atendente->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 font-bold pl-5">-</span>
                                    @endif
                                </td>

                                <!-- Assunto -->
                                <td class="py-4 px-4 text-left text-gray-500 max-w-[320px] truncate" title="{{ $assunto }}">
                                    {{ $assunto }}
                                </td>

                                <!-- Status Badge Premium -->
                                <td class="py-4 px-4 text-center">
                                    @if($solicitation->status === 'respondida')
                                        <!-- Respondida: badge cinza -->
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-[#8E8E93] text-white font-bold text-[9px] uppercase tracking-wider select-none">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8Z" />
                                            </svg>
                                            Respondida
                                        </span>
                                    @elseif($solicitation->status === 'resolvida')
                                        <!-- Resolvida: badge verde -->
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-[#28CD41] text-white font-bold text-[9px] uppercase tracking-wider select-none">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M12 18.75a6.75 6.75 0 1 0 0-13.5 6.75 6.75 0 0 0 0 13.5ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75s.168-.75.375-.75.375.336.375.75Zm6 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Z" />
                                            </svg>
                                            Resolvida
                                        </span>
                                    @elseif(in_array($solicitation->status, ['em_replica', 'em_atendimento']))
                                        <!-- Em Réplica: badge laranja -->
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-[#FF9500] text-white font-bold text-[9px] uppercase tracking-wider select-none">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                                            </svg>
                                            Em réplica
                                        </span>
                                    @elseif(in_array($solicitation->status, ['nao_resolvida', 'não resolvida']))
                                        <!-- Não Resolvida: badge vermelho -->
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-[#FF3B30] text-white font-bold text-[9px] uppercase tracking-wider select-none">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.182 16.318A4.486 4.486 0 0 0 12.016 15a4.486 4.486 0 0 0-3.198 1.318M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75s.168-.75.375-.75.375.336.375.75Zm6 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Z" />
                                            </svg>
                                            Não resolvida
                                        </span>
                                    @else
                                        <!-- Nova Demanda (na_fila / aberta): badge preto -->
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-[#1C1C1E] text-white font-bold text-[9px] uppercase tracking-wider select-none">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                            Nova demanda
                                        </span>
                                    @endif
                                </td>

                                <!-- Última Atualização -->
                                <td class="py-4 px-4 text-gray-500 text-center font-bold">
                                    {{ $solicitation->updated_at->format('d/m/Y') }} às
                                    {{ $solicitation->updated_at->format('H\hi') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-gray-500 font-bold">Nenhum ticket encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    <!-- Toast de Confirmação de Cópia -->
    <div id="copy-toast"
        class="fixed bottom-6 right-6 z-[100] bg-gray-900 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-lg flex items-center gap-2 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
        </svg>
        <span>ID do chamado copiado!</span>
    </div>

    <script>
        // Função para submeter para página específica
        function goToPage(page) {
            document.getElementById('pagination-page-input').value = page;
            document.getElementById('tickets-filter-form').submit();
        }

        // Função para ir ao chat correspondente
        function goToChat(event, url) {
            window.location.href = url;
        }

        // Função para limpar todos os inputs de filtros e submeter
        function clearFilters() {
            const form = document.getElementById('tickets-filter-form');
            const inputs = form.querySelectorAll('input[type="text"], input[type="date"]');
            inputs.forEach(input => input.value = '');
            form.submit();
        }

        // Copiar o número do ticket
        function copyTicketNumber(event, number) {
            event.stopPropagation(); // Impede de abrir o chat ao clicar no copiar
            navigator.clipboard.writeText(number).then(() => {
                const toast = document.getElementById('copy-toast');
                toast.classList.remove('translate-y-20', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');

                setTimeout(() => {
                    toast.classList.remove('translate-y-0', 'opacity-100');
                    toast.classList.add('translate-y-20', 'opacity-0');
                }, 2000);
            });
        }
    </script>
@endsection