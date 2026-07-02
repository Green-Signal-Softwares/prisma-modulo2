@extends('layouts.app')

@section('title', 'Gestão de Atendimento — Claro Prisma')

@section('content')
<!-- Breadcrumbs -->
<div class="flex items-center gap-2 text-xs text-gray-500 mb-2 select-none">
    <span>Claro Prisma</span>
    <span>&gt;</span>
    <span class="text-gray-800 font-medium">Gestão de Atendimento</span>
</div>

<!-- Header -->
<h1 class="text-2xl font-bold text-[#DA291C] mb-6">Gestão de Atendimento</h1>

<!-- Performance Statistics Grid -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 select-none">
    <!-- Card 1 -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between min-h-[120px]">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Atendentes Ativos</span>
        <div class="flex items-baseline justify-between mt-2">
            <span class="text-3xl font-extrabold text-gray-900">12</span>
            <span class="text-xs text-green-500 font-bold bg-green-50 px-2 py-0.5 rounded-full">+2 hoje</span>
        </div>
    </div>
    <!-- Card 2 -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between min-h-[120px]">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Em Espera na Fila</span>
        <div class="flex items-baseline justify-between mt-2">
            <span class="text-3xl font-extrabold text-amber-500">4</span>
            <span class="text-xs text-gray-400 font-normal">tempo méd. 2m</span>
        </div>
    </div>
    <!-- Card 3 -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between min-h-[120px]">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Média de Resposta</span>
        <div class="flex items-baseline justify-between mt-2">
            <span class="text-3xl font-extrabold text-gray-900">4m 15s</span>
            <span class="text-xs text-green-500 font-bold bg-green-50 px-2 py-0.5 rounded-full">-30s</span>
        </div>
    </div>
    <!-- Card 4 -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between min-h-[120px]">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Avaliação Média</span>
        <div class="flex items-baseline justify-between mt-2">
            <span class="text-3xl font-extrabold text-[#DA291C]">4.8 / 5.0</span>
            <span class="text-xs text-amber-500 font-bold">★★★★★</span>
        </div>
    </div>
</div>

<!-- Main Section: List of Attendants and Status -->
<div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold text-gray-800">Status dos Attendants</h2>
        <button class="bg-[#DA291C] hover:bg-[#B31F15] text-white font-bold py-2 px-4 rounded-xl text-sm transition-all select-none cursor-pointer">
            Adicionar Attendant
        </button>
    </div>

    <div class="overflow-x-auto select-none">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-100 text-xs font-bold text-gray-400 uppercase tracking-wider">
                    <th class="pb-3 pl-3">Attendant</th>
                    <th class="pb-3">Setor/Fila</th>
                    <th class="pb-3">Status</th>
                    <th class="pb-3">Chamados Ativos</th>
                    <th class="pb-3 text-right pr-3">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                <!-- Attendant 1 -->
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="py-4 pl-3 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center font-bold text-red-800 text-xs">
                            LR
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900 block">Lucas R.</span>
                            <span class="text-xs text-gray-500 font-light">lucas.r@claro.com.br</span>
                        </div>
                    </td>
                    <td class="py-4 font-medium text-gray-700">Nível 1 (Geral)</td>
                    <td class="py-4">
                        <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            Disponível
                        </span>
                    </td>
                    <td class="py-4 font-bold text-gray-800">2 chamados</td>
                    <td class="py-4 text-right pr-3">
                        <button class="text-gray-400 hover:text-gray-600 transition-colors font-medium mr-3 cursor-pointer">Monitorar</button>
                        <button class="text-[#DA291C] hover:text-[#B31F15] transition-colors font-semibold cursor-pointer">Pausar</button>
                    </td>
                </tr>

                <!-- Attendant 2 -->
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="py-4 pl-3 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center font-bold text-amber-800 text-xs">
                            AS
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900 block">Ana Silva</span>
                            <span class="text-xs text-gray-500 font-light">ana.silva@claro.com.br</span>
                        </div>
                    </td>
                    <td class="py-4 font-medium text-gray-700">Nível 2 (Vendas)</td>
                    <td class="py-4">
                        <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 text-xs font-bold px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Em Atendimento
                        </span>
                    </td>
                    <td class="py-4 font-bold text-gray-800">1 chamado</td>
                    <td class="py-4 text-right pr-3">
                        <button class="text-gray-400 hover:text-gray-600 transition-colors font-medium mr-3 cursor-pointer">Monitorar</button>
                        <button class="text-[#DA291C] hover:text-[#B31F15] transition-colors font-semibold cursor-pointer">Pausar</button>
                    </td>
                </tr>

                <!-- Attendant 3 -->
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="py-4 pl-3 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center font-bold text-gray-800 text-xs">
                            FB
                        </div>
                        <div>
                            <span class="font-semibold text-gray-900 block">Felipe Bueno</span>
                            <span class="text-xs text-gray-500 font-light">felipe.bueno@claro.com.br</span>
                        </div>
                    </td>
                    <td class="py-4 font-medium text-gray-700">Nível 2 (Técnico)</td>
                    <td class="py-4">
                        <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-600 text-xs font-bold px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Pausado
                        </span>
                    </td>
                    <td class="py-4 font-bold text-gray-800">0 chamados</td>
                    <td class="py-4 text-right pr-3">
                        <button class="text-gray-400 hover:text-gray-600 transition-colors font-medium mr-3 cursor-pointer">Monitorar</button>
                        <button class="text-green-600 hover:text-green-700 transition-colors font-semibold cursor-pointer">Ativar</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
