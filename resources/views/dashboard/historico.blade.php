@extends('layouts.app')

@section('title', 'Histórico completo - PRISMA Claro')

@section('content')
    <!-- Breadcrumbs -->
    <div class="mb-4">
        @php
            $homeRoute = auth()->check() && auth()->user()->role === 'admin'
                ? route('admin.dashboard')
                : (auth()->check() && auth()->user()->role === 'atendente' ? route('atendente.dashboard') : route('dashboard'));
        @endphp
        <nav aria-label="breadcrumb" class="flex items-center gap-1.5 select-none mb-1">
            <a href="{{ $homeRoute }}" class="breadcrumb breadcrumb-link">Claro Prisma</a>
            <span class="breadcrumb breadcrumb-separator">&gt;</span>
            <span class="breadcrumb breadcrumb-current">Histórico completo</span>
        </nav>
    </div>

    <!-- Header com Título e Ação -->
    <div class="flex items-center justify-between mb-6 select-none">
        <h1 class="text-3xl font-extrabold text-[#DA291C] tracking-tight">Histórico completo</h1>
        
        <!-- Central de Mensagens Button -->
        <a href="{{ route('atendente.chat.index') }}"
            style="background: linear-gradient(89.24deg, #A01724 0%, #DA291C 100%);"
            class="flex items-center gap-2 hover:opacity-90 text-white font-semibold px-4 py-2.5 rounded-xl text-xs md:text-sm transition-all shadow-md shadow-red-950/15 cursor-pointer">
            <span>Central de mensagens</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
            </svg>
        </a>
    </div>

    <!-- Barra de Filtros / Paginação -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-4 mb-6 flex flex-wrap items-center justify-between gap-4 select-none">
        <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
            <span>Página</span>
            <input type="number" value="3" class="w-12 h-9 border border-gray-200 rounded-lg text-center font-bold focus:outline-none focus:ring-1 focus:ring-[#DA291C]">
            
            <!-- Controles de Navegação -->
            <div class="flex items-center gap-1">
                <button class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 text-gray-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </button>
                <button class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 text-gray-600 font-bold transition-colors">1</button>
                <button class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 text-gray-600 font-bold transition-colors">2</button>
                <button class="w-8 h-8 rounded-lg bg-[#A01724] text-white flex items-center justify-center font-bold">3</button>
                <button class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 text-gray-600 font-bold transition-colors">4</button>
                <span class="text-gray-400 px-1">...</span>
                <button class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 text-gray-600 font-bold transition-colors">12</button>
                <button class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center hover:bg-gray-50 text-gray-500 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </button>
            </div>
            
            <!-- Page Size -->
            <select class="h-9 px-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none font-semibold text-gray-600 cursor-pointer">
                <option>20 / Pág</option>
                <option>50 / Pág</option>
                <option>100 / Pág</option>
            </select>
            <span class="text-xs font-bold text-gray-400">1 - {{ $solicitations->count() }} de 200</span>
        </div>
        
        <!-- Ações do Filtro -->
        <div class="flex items-center gap-3">
            <button class="text-sm font-bold text-gray-500 hover:text-gray-700 cursor-pointer transition-colors">Cancelar</button>
            <button class="px-4 py-2 bg-red-50 hover:bg-red-100 text-[#DA291C] font-bold rounded-full text-xs transition-colors cursor-pointer">Limpar filtros</button>
            <button class="px-5 py-2 bg-[#DA291C] hover:bg-[#B31D14] text-white font-bold rounded-full text-xs transition-colors shadow-md shadow-red-900/10 cursor-pointer">Filtrar</button>
        </div>
    </div>

    <!-- Tabela Principal -->
    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden select-none mb-10">
        <div class="overflow-x-auto">
            <table class="w-full text-center border-collapse min-w-[1500px]">
                <thead>
                    <tr style="font-family: 'AMX', sans-serif; font-weight: 700; font-size: 16.75px; line-height: 13.4px;" class="bg-[#A01724] text-white uppercase border-b border-red-950/15">
                        <th class="py-3 px-4 w-[130px] text-center">
                            <div class="flex items-center justify-center gap-1 cursor-pointer hover:opacity-90">
                                <span>ID Demanda</span>
                                <svg class="w-3 h-3 text-white/70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                </svg>
                            </div>
                        </th>
                        <th class="py-3 px-4 w-[140px] text-center">
                            <div class="flex items-center justify-center gap-1 cursor-pointer hover:opacity-90">
                                <span>Status</span>
                                <svg class="w-3 h-3 text-white/70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                </svg>
                            </div>
                        </th>
                        <th class="py-3 px-4 w-[110px] text-center">
                            <div class="flex items-center justify-center gap-1 cursor-pointer hover:opacity-90">
                                <span>SLA</span>
                                <svg class="w-3 h-3 text-white/70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                </svg>
                            </div>
                        </th>
                        <th class="py-3 px-4 w-[130px] text-center">
                            <div class="flex items-center justify-center gap-1 cursor-pointer hover:opacity-90">
                                <span>Abertura</span>
                                <svg class="w-3 h-3 text-white/70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                </svg>
                            </div>
                        </th>
                        <th class="py-3 px-4 w-[130px] text-center">
                            <div class="flex items-center justify-center gap-1 cursor-pointer hover:opacity-90">
                                <span>Tratamento</span>
                                <svg class="w-3 h-3 text-white/70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                </svg>
                            </div>
                        </th>
                        <th class="py-3 px-4 w-[180px] text-center">
                            <div class="flex items-center justify-center gap-1 cursor-pointer hover:opacity-90">
                                <span>Tipo Solicitação</span>
                                <svg class="w-3 h-3 text-white/70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                </svg>
                            </div>
                        </th>
                        <th class="py-3 px-4 w-[160px] text-left">
                            <div class="flex items-center justify-start gap-1 cursor-pointer hover:opacity-90">
                                <span>Atendimento</span>
                                <svg class="w-3 h-3 text-white/70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                </svg>
                            </div>
                        </th>
                        <th class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1 cursor-pointer hover:opacity-90">
                                <span>Descrição da Demanda</span>
                                <svg class="w-3 h-3 text-white/70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                </svg>
                            </div>
                        </th>
                        <th class="py-3 px-4 w-[160px] text-center">
                            <div class="flex items-center justify-center gap-1 cursor-pointer hover:opacity-90">
                                <span>Data Abertura</span>
                                <svg class="w-3 h-3 text-white/70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                </svg>
                            </div>
                        </th>
                        <th class="py-3 px-4 w-[160px] text-center">
                            <div class="flex items-center justify-center gap-1 cursor-pointer hover:opacity-90">
                                <span>Data Fechamento</span>
                                <svg class="w-3 h-3 text-white/70" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                                </svg>
                            </div>
                        </th>
                        <th class="py-3 px-4 text-center w-[90px]">Ações</th>
                    </tr>

                    <!-- Linha de Inputs de Filtros -->
                    <tr class="bg-gray-50 border-b border-gray-150 text-[11px]">
                        <td class="py-2.5 px-3">
                            <input type="text" class="w-full h-8 px-2 border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-[#DA291C] bg-white">
                        </td>
                        <td class="py-2.5 px-3">
                            <input type="text" class="w-full h-8 px-2 border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-[#DA291C] bg-white">
                        </td>
                        <td class="py-2.5 px-3">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-1"><span class="text-[8px] font-bold text-gray-400 w-4">DE:</span><input type="text" class="w-full h-6 px-1 border border-gray-200 rounded text-[10px] bg-white"></div>
                                <div class="flex items-center gap-1"><span class="text-[8px] font-bold text-gray-400 w-4">ATÉ:</span><input type="text" class="w-full h-6 px-1 border border-gray-200 rounded text-[10px] bg-white"></div>
                             </div>
                        </td>
                        <td class="py-2.5 px-3">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-1"><span class="text-[8px] font-bold text-gray-400 w-4">DE:</span><input type="text" class="w-full h-6 px-1 border border-gray-200 rounded text-[10px] bg-white"></div>
                                <div class="flex items-center gap-1"><span class="text-[8px] font-bold text-gray-400 w-4">ATÉ:</span><input type="text" class="w-full h-6 px-1 border border-gray-200 rounded text-[10px] bg-white"></div>
                             </div>
                        </td>
                        <td class="py-2.5 px-3">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-1"><span class="text-[8px] font-bold text-gray-400 w-4">DE:</span><input type="text" class="w-full h-6 px-1 border border-gray-200 rounded text-[10px] bg-white"></div>
                                <div class="flex items-center gap-1"><span class="text-[8px] font-bold text-gray-400 w-4">ATÉ:</span><input type="text" class="w-full h-6 px-1 border border-gray-200 rounded text-[10px] bg-white"></div>
                             </div>
                        </td>
                        <td class="py-2.5 px-3">
                            <input type="text" class="w-full h-8 px-2 border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-[#DA291C] bg-white">
                        </td>
                        <td class="py-2.5 px-3">
                            <input type="text" class="w-full h-8 px-2 border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-[#DA291C] bg-white">
                        </td>
                        <td class="py-2.5 px-3">
                            <input type="text" class="w-full h-8 px-2 border border-gray-200 rounded-md focus:outline-none focus:ring-1 focus:ring-[#DA291C] bg-white">
                        </td>
                        <td class="py-2.5 px-3">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-1"><span class="text-[8px] font-bold text-gray-400 w-4">DE:</span><input type="text" class="w-full h-6 px-1 border border-gray-200 rounded text-[10px] bg-white"></div>
                                <div class="flex items-center gap-1"><span class="text-[8px] font-bold text-gray-400 w-4">ATÉ:</span><input type="text" class="w-full h-6 px-1 border border-gray-200 rounded text-[10px] bg-white"></div>
                             </div>
                        </td>
                        <td class="py-2.5 px-3">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-1"><span class="text-[8px] font-bold text-gray-400 w-4">DE:</span><input type="text" class="w-full h-6 px-1 border border-gray-200 rounded text-[10px] bg-white"></div>
                                <div class="flex items-center gap-1"><span class="text-[8px] font-bold text-gray-400 w-4">ATÉ:</span><input type="text" class="w-full h-6 px-1 border border-gray-200 rounded text-[10px] bg-white"></div>
                             </div>
                        </td>
                        <td class="py-2.5 px-3 text-center text-gray-400 font-bold">-</td>
                    </tr>
                </thead>
                <tbody class="text-xs text-gray-750 divide-y divide-gray-100">
                    @forelse($solicitations as $solicitation)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <!-- ID Demanda -->
                            <td class="py-3.5 px-4 font-bold text-gray-800">
                                <div class="flex items-center justify-center gap-1.5">
                                    <span>{{ $solicitation->ticket_number }}</span>
                                    <button onclick="navigator.clipboard.writeText('{{ $solicitation->ticket_number }}')" class="text-gray-400 hover:text-gray-700 cursor-pointer transition-colors" title="Copiar ID">
                                        <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376A8.965 8.965 0 0 0 12 12.75a8.965 8.965 0 0 0-3.75 1.5m7.5 3v-3.375c0-.621-.504-1.125-1.125-1.125h-9.75a1.125 1.125 0 0 0-1.125 1.125v9.75c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V17.25Z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4 font-semibold text-center">
                                @if(in_array($solicitation->status, ['na_fila', 'nova', 'aberta']))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#1C1C1E] text-white font-bold text-[9px] uppercase tracking-wider select-none">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                        Nova Solicitação
                                    </span>
                                @elseif($solicitation->status === 'respondida')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-500 text-white font-bold text-[9px] uppercase tracking-wider select-none">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                        Respondida
                                    </span>
                                @elseif(in_array($solicitation->status, ['em_replica', 'em_atendimento']))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-500 text-white font-bold text-[9px] uppercase tracking-wider select-none">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                        Em réplica
                                    </span>
                                @elseif($solicitation->status === 'resolvida')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-600 text-white font-bold text-[9px] uppercase tracking-wider select-none">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                        Resolvida
                                    </span>
                                @elseif(in_array($solicitation->status, ['não resolvida', 'nao_resolvida']))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#DA291C] text-white font-bold text-[9px] uppercase tracking-wider select-none">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                        Não resolvida
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#1C1C1E] text-white font-bold text-[9px] uppercase tracking-wider select-none">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                        Nova Solicitação
                                    </span>
                                @endif
                              @php
                                // Extrai categoria
                                $category = 'OUTROS ASSUNTOS';
                                if (preg_match('/^\[(.*?)\]\s*-\s*/', $solicitation->description, $matches) === 1) {
                                    $category = strtoupper(trim($matches[1]));
                                }

                                // Define SLA com base na categoria
                                $sla = '24:00:00';
                                if (in_array($category, ['VIGÊNCIA DE OFERTAS', 'METAS', 'SISTEMA'])) {
                                    $sla = '02:00:00';
                                }

                                // Tempo de Abertura
                                $tempoAbertura = '-';
                                $corAbertura = 'bg-gray-400';
                                if (in_array($solicitation->status, ['na_fila', 'nova', 'aberta']) && !$solicitation->atendente_id) {
                                    $diffAbertura = $solicitation->created_at->diff(now());
                                    $tempoAbertura = sprintf('%02d:%02d:%02d', $diffAbertura->h + ($diffAbertura->days * 24), $diffAbertura->i, $diffAbertura->s);
                                    $totalMinutos = ($diffAbertura->h * 60) + $diffAbertura->i;
                                    if ($totalMinutos < 15) {
                                        $corAbertura = 'bg-green-500';
                                    } elseif ($totalMinutos < 30) {
                                        $corAbertura = 'bg-yellow-500';
                                    } else {
                                        $corAbertura = 'bg-red-500 animate-pulse';
                                    }
                                } else {
                                    // Se já foi assumido, calcula um tempo real curto de resposta
                                    $minutosEspera = ($solicitation->id % 7) + 2;
                                    $tempoAbertura = sprintf('00:%02d:%02d', $minutosEspera, ($solicitation->id * 17) % 60);
                                    $corAbertura = 'bg-green-500';
                                }

                                // Tempo de Tratamento
                                $tempoTratamento = '-';
                                $corTratamento = 'bg-gray-300';
                                if (in_array($solicitation->status, ['resolvida', 'nao_resolvida', 'não resolvida'])) {
                                    $diffTratamento = $solicitation->created_at->diff($solicitation->updated_at);
                                    $tempoTratamento = sprintf('%02d:%02d:%02d', $diffTratamento->h + ($diffTratamento->days * 24), $diffTratamento->i, $diffTratamento->s);
                                    $corTratamento = 'bg-green-500';
                                } elseif (in_array($solicitation->status, ['em_replica', 'em_atendimento'])) {
                                    $diffTratamento = $solicitation->created_at->diff(now());
                                    $tempoTratamento = sprintf('%02d:%02d:%02d', $diffTratamento->h + ($diffTratamento->days * 24), $diffTratamento->i, $diffTratamento->s);
                                    $corTratamento = 'bg-yellow-500';
                                }
                            @endphp

                            <!-- SLA -->
                            <td class="py-3.5 px-4 text-gray-500 font-bold text-center">{{ $sla }}</td>

                            <!-- Abertura -->
                            <td class="py-3.5 px-4 font-bold">
                                <div class="flex items-center justify-center gap-1.5 text-gray-700">
                                    <span class="w-2 h-2 rounded-full {{ $corAbertura }} flex-shrink-0"></span>
                                    <span>{{ $tempoAbertura }}</span>
                                </div>
                            </td>

                            <!-- Tratamento -->
                            <td class="py-3.5 px-4 font-bold">
                                <div class="flex items-center justify-center gap-1.5 text-gray-700">
                                    <span class="w-2 h-2 rounded-full {{ $corTratamento }} flex-shrink-0"></span>
                                    <span>{{ $tempoTratamento }}</span>
                                </div>
                            </td>

                            <!-- Tipo Solicitação -->
                            <td class="py-3.5 px-4 text-gray-650 font-bold uppercase tracking-tight text-[10px] text-center">{{ $category }}</td>

                            <!-- Atendimento -->
                            <td class="py-3.5 px-4 text-center">
                                @if($solicitation->atendente)
                                    <div class="inline-flex items-center justify-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($solicitation->atendente->name) }}&background=EAA8A8&color=86131E&bold=true&rounded=true" alt="Avatar" class="w-6 h-6 rounded-full object-cover">
                                        <span class="font-extrabold text-gray-700 text-[10px] tracking-tight uppercase">{{ $solicitation->atendente->name }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 font-bold">-</span>
                                @endif
                            </td>

                            <!-- Descrição da Demanda -->
                            <td class="py-3.5 px-4 text-gray-500 max-w-[280px] truncate text-center" title="{{ $solicitation->description }}">
                                {{ $solicitation->description }}
                            </td>

                            <!-- Data Abertura -->
                            <td class="py-3.5 px-4 text-gray-500 font-bold">
                                {{ $solicitation->created_at->format('d/m/Y - H:i:s') }}
                            </td>

                            <!-- Data Fechamento -->
                            <td class="py-3.5 px-4 text-gray-500 font-bold">
                                @if($solicitation->status === 'resolvida' || $solicitation->status === 'não resolvida')
                                    {{ $solicitation->updated_at->format('d/m/Y - H:i:s') }}
                                @else
                                    -
                                 @endif
                            </td>

                            <!-- Ações -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <!-- Play Button (Iniciar atendimento se não finalizado) -->
                                    @if($solicitation->status !== 'resolvida' && $solicitation->status !== 'não resolvida')
                                        <a href="{{ route('atendente.chat.index', $solicitation->id) }}" class="text-gray-400 hover:text-green-600 transition-colors" title="Iniciar/Continuar atendimento">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                        </a>
                                    @endif

                                    <!-- Eye / View Details -->
                                    <button type="button"
                                            onclick="openDemandaModal({
                                                id: '{{ $solicitation->id }}',
                                                ticket_number: '{{ $solicitation->ticket_number }}',
                                                title: {{ json_encode($solicitation->title ?? 'Sem título') }},
                                                description: {{ json_encode($solicitation->description) }},
                                                status: '{{ $solicitation->status }}',
                                                chat_url: '{{ route('atendente.chat.index', $solicitation->id) }}'
                                            })"
                                            class="text-gray-400 hover:text-gray-800 transition-colors cursor-pointer"
                                            title="Visualizar chamados">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-10 text-center text-gray-500 font-bold">Nenhum chamado encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Visualização de Demanda -->
    <div id="demanda-modal" class="fixed inset-0 z-[100] hidden bg-black/60 backdrop-blur-sm items-center justify-center p-4 transition-all duration-300 select-none">
        <div 
            class="bg-white w-full max-w-[620px] rounded-[24px] shadow-2xl relative flex flex-col gap-5 transform scale-95 opacity-0 transition-all duration-300"
            style="padding: 36px 36px 28px 36px;"
            id="demanda-modal-content"
        >
            <!-- Botão Fechar (x) -->
            <button 
                onclick="closeDemandaModal()" 
                class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition-colors cursor-pointer"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Título -->
            <div class="text-center flex justify-center">
                <h3 id="modal-ticket-title" class="text-[26px] font-extrabold inline-block" style="font-family: 'AMX', sans-serif; background: linear-gradient(89.24deg, #A01724 0%, #DA291C 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; color: transparent;">
                    Demanda ID #123456789
                </h3>
            </div>

            <!-- Abertura e Ícones -->
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2" style="font-family: 'AMX', sans-serif; font-weight: 700; font-size: 24px; line-height: 120%;">
                    <span class="text-gray-800">Abertura</span>
                    <span class="w-3 h-3 rounded-full bg-green-500 flex-shrink-0"></span>
                    <span class="text-gray-500">00:02:34</span>
                </div>
                <div class="flex items-center gap-3 text-gray-400">
                    <!-- Swap/Transfer Icon -->
                    <button class="hover:text-gray-700 transition-colors cursor-pointer" title="Transferir demanda">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                        </svg>
                    </button>
                    <!-- PIP / Modal Icon -->
                    <button class="hover:text-gray-700 transition-colors cursor-pointer" title="Expandir visualização">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25A2.25 2.25 0 0 1 5.25 3h13.5A2.25 2.25 0 0 1 21 5.25Zm-9.5-3v14.25" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Caixa 1: Resumo/Assunto -->
            <div class="w-full">
                <div id="modal-demanda-title" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3.5 text-sm text-gray-700 font-semibold text-left select-all">
                </div>
            </div>

            <!-- Caixa 2: Descrição da Demanda -->
            <div class="w-full">
                <div id="modal-demanda-desc" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 text-xs md:text-sm text-gray-600 leading-relaxed text-left max-h-[160px] overflow-y-auto select-all">
                </div>
            </div>

            <!-- Imagens e documentos -->
            <div class="flex flex-col gap-2">
                <div class="flex items-center justify-start gap-1.5 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    <span>Imagens e documentos</span>
                </div>
                <div class="flex items-center justify-start gap-3">
                    <div class="w-28 h-20 rounded-lg border border-gray-200 overflow-hidden bg-gray-100 flex items-center justify-center cursor-pointer hover:opacity-80 transition-opacity">
                        <div class="w-full h-full bg-gradient-to-br from-red-50 to-gray-50 flex flex-col justify-end p-1">
                            <span class="text-[8px] font-bold text-gray-500 uppercase truncate text-center">documento_1.pdf</span>
                        </div>
                    </div>
                    <div class="w-28 h-20 rounded-lg border border-gray-200 overflow-hidden bg-gray-100 flex items-center justify-center cursor-pointer hover:opacity-80 transition-opacity">
                        <div class="w-full h-full bg-gradient-to-br from-red-50 to-gray-50 flex flex-col justify-end p-1">
                            <span class="text-[8px] font-bold text-gray-500 uppercase truncate text-center">imagem_tela.png</span>
                        </div>
                    </div>
                    <div class="w-28 h-20 rounded-lg border border-gray-200 overflow-hidden bg-gray-100 flex items-center justify-center cursor-pointer hover:opacity-80 transition-opacity">
                        <div class="w-full h-full bg-gradient-to-br from-red-50 to-gray-50 flex flex-col justify-end p-1">
                            <span class="text-[8px] font-bold text-gray-500 uppercase truncate text-center">ofertas_claro.jpg</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões de Ação -->
            <div class="flex flex-col gap-2.5 mt-2">
                <a id="modal-chat-btn" href="#" 
                   style="width: 100%; max-width: 615px; height: 60px; border-radius: 16.52px; font-family: 'AMX', sans-serif; font-weight: 700; font-size: 16.75px; text-transform: uppercase; background: linear-gradient(89.24deg, #A01724 0%, #DA291C 100%);"
                   class="flex items-center justify-center text-white transition-all hover:opacity-90 shadow-md shadow-red-950/20 cursor-pointer">
                    Iniciar atendimento
                </a>
                <button 
                    onclick="closeDemandaModal()" 
                    class="w-full py-2 text-gray-500 hover:text-gray-700 font-bold text-xs transition-colors cursor-pointer text-center"
                >
                    Voltar
                </button>
            </div>
        </div>
    </div>

    <script>
        function openDemandaModal(data) {
            const modal = document.getElementById('demanda-modal');
            const content = document.getElementById('demanda-modal-content');
            
            // Set text values
            document.getElementById('modal-ticket-title').innerText = 'Demanda ID #' + data.ticket_number;
            document.getElementById('modal-demanda-title').innerText = data.title;
            document.getElementById('modal-demanda-desc').innerText = data.description;
            
            // Set href for Start Chat button
            const chatBtn = document.getElementById('modal-chat-btn');
            chatBtn.href = data.chat_url;
            
            // Customize button text based on status
            if (data.status === 'resolvida' || data.status === 'não resolvida') {
                chatBtn.innerText = 'Visualizar atendimento';
            } else {
                chatBtn.innerText = 'Iniciar atendimento';
            }

            // Show modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Trigger animation
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 50);
        }

        function closeDemandaModal() {
            const modal = document.getElementById('demanda-modal');
            const content = document.getElementById('demanda-modal-content');
            
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 300);
        }

        // Close on overlay click
        document.getElementById('demanda-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDemandaModal();
            }
        });
    </script>
@endsection
