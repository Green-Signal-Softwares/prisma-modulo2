@extends('layouts.app')

@section('title', 'Log de Atividades — Claro Prisma')

@section('content')
<!-- Breadcrumbs -->
<div class="mb-2 select-none">
    @php
        $homeRoute = auth()->check() && auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard');
    @endphp
    <nav aria-label="breadcrumb" class="flex items-center gap-1.5">
        <a href="{{ $homeRoute }}" class="breadcrumb breadcrumb-link">Claro Prisma</a>
        <span class="breadcrumb breadcrumb-separator">&gt;</span>
        <span class="breadcrumb breadcrumb-current">Log de Atividades</span>
    </nav>
</div>

<!-- Header -->
<div class="flex items-center justify-between flex-wrap gap-4 mb-4 select-none">
    <div>
        <div class="flex items-center gap-4">
            <h1 class="text-2xl font-bold text-[#DA291C]">Log de Atividades</h1>
            <a href="{{ request()->fullUrlWithQuery(['download_csv' => 1]) }}" 
                class="flex items-center gap-2 bg-[#86131E] hover:bg-[#A01724] text-white text-xs font-bold px-4 py-2 rounded-lg shadow-sm transition-colors active:scale-[0.98]">
                <span>Download CSV</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
            </a>
        </div>
        <p class="text-xs text-gray-500 mt-1">Texto descritivo</p>
    </div>
</div>

<!-- Main Table Card -->
<div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex-1 flex flex-col min-h-[500px]">
    
    <form id="filters-form" method="GET" action="{{ route('admin.log-atividades') }}" class="m-0 flex-1 flex flex-col justify-between">
        <!-- Hidden Inputs for State Management -->
        <input type="hidden" name="per_page" id="per_page_hidden" value="{{ request('per_page', 20) }}">
        <input type="hidden" name="page" id="page_hidden" value="{{ request('page', 1) }}">

        <div>
            <!-- Filters & Pagination Top Bar -->
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 pb-5 mb-5 select-none">
                <!-- Pagination Control -->
                <div class="flex flex-wrap items-center gap-4 text-xs text-gray-600">
                    <!-- Page Number Direct Input -->
                    <div class="flex items-center gap-2">
                        <span>Página</span>
                        <input type="number" id="page-input" value="{{ $logs->currentPage() }}" min="1" max="{{ $logs->lastPage() }}"
                            class="w-12 text-center border border-gray-200 rounded-lg py-1 px-1.5 focus:outline-none focus:border-[#DA291C]"
                            onchange="goToPage(this.value)">
                    </div>

                    <!-- Custom Pagination Buttons -->
                    <div class="flex items-center gap-1">
                        <!-- Previous Page Button -->
                        @if ($logs->onFirstPage())
                            <span class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                </svg>
                            </span>
                        @else
                            <a href="#" onclick="goToPage({{ $logs->currentPage() - 1 }}); return false;" 
                                class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                </svg>
                            </a>
                        @endif

                        <!-- Page List -->
                        @php
                            $currentPage = $logs->currentPage();
                            $lastPage = $logs->lastPage();
                            $startPage = max(1, $currentPage - 1);
                            $endPage = min($lastPage, $currentPage + 1);
                        @endphp

                        @if ($startPage > 1)
                            <a href="#" onclick="goToPage(1); return false;" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-700 font-semibold transition-colors">1</a>
                            @if ($startPage > 2)
                                <span class="text-gray-400 px-0.5">...</span>
                            @endif
                        @endif

                        @for ($p = $startPage; $p <= $endPage; $p++)
                            @if ($p == $currentPage)
                                <span class="w-7 h-7 flex items-center justify-center rounded-lg bg-[#86131E] text-white font-bold">{{ $p }}</span>
                            @else
                                <a href="#" onclick="goToPage({{ $p }}); return false;" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-700 font-semibold transition-colors">{{ $p }}</a>
                            @endif
                        @endfor

                        @if ($endPage < $lastPage)
                            @if ($endPage < $lastPage - 1)
                                <span class="text-gray-400 px-0.5">...</span>
                            @endif
                            <a href="#" onclick="goToPage({{ $lastPage }}); return false;" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-700 font-semibold transition-colors">{{ $lastPage }}</a>
                        @endif

                        <!-- Next Page Button -->
                        @if ($logs->hasMorePages())
                            <a href="#" onclick="goToPage({{ $logs->currentPage() + 1 }}); return false;" 
                                class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </a>
                        @else
                            <span class="w-7 h-7 flex items-center justify-center rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </span>
                        @endif
                    </div>

                    <!-- Items Per Page Dropdown -->
                    <div>
                        <select id="per-page-select" onchange="updatePerPage(this.value)"
                            class="bg-white border border-gray-200 text-gray-700 text-xs font-semibold rounded-lg px-2 py-1 focus:outline-none focus:border-[#DA291C] cursor-pointer">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / Pág</option>
                            <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20 / Pág</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / Pág</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / Pág</option>
                        </select>
                    </div>

                    <!-- Count Range -->
                    <div class="text-gray-500 font-medium">
                        {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} de {{ $logs->total() }}
                    </div>
                </div>

                <!-- Filters Action Buttons -->
                <div class="flex items-center gap-4">
                    <a href="#" onclick="clearFilters(); return false;" class="text-xs font-bold text-gray-500 hover:text-gray-700 transition-colors">Cancelar</a>
                    <button type="button" onclick="clearFilters()" class="bg-[#DFA6A9] hover:bg-[#EAA8A8] text-white text-xs font-bold px-4 py-2 rounded-full transition-colors active:scale-[0.98]">Limpar filtros</button>
                    <button type="button" onclick="submitFilters()" class="bg-[#86131E] hover:bg-[#A01724] text-white text-xs font-bold px-6 py-2 rounded-full shadow-md transition-colors active:scale-[0.98]">Filtrar</button>
                </div>
            </div>

            <!-- Table and Filters Grid -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-gray-800 text-[13px] font-bold border-b border-gray-100 select-none">
                            <th class="pb-3 pr-4 font-bold text-gray-800 tracking-tight" style="width: 18%;">Data/Hora</th>
                            <th class="pb-3 px-4 font-bold text-gray-800 tracking-tight text-center" style="width: 15%;">Atividade</th>
                            <th class="pb-3 px-4 font-bold text-gray-800 tracking-tight" style="width: 15%;">Tipo</th>
                            <th class="pb-3 px-4 font-bold text-gray-800 tracking-tight" style="width: 25%;">Nome</th>
                            <th class="pb-3 px-4 font-bold text-gray-800 tracking-tight" style="width: 15%;">PDV</th>
                            <th class="pb-3 pl-4 font-bold text-gray-800 tracking-tight text-center" style="width: 12%;">Ação</th>
                        </tr>
                        <!-- Filter Fields Row -->
                        <tr class="bg-white">
                            <!-- Data/Hora Datepickers (De/Até) -->
                            <td class="py-3 pr-4 align-middle">
                                <div class="flex flex-col gap-1 text-[9px] font-bold text-gray-400 max-w-[150px]">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-6 uppercase">DE:</span>
                                        <input type="date" name="data_de" value="{{ request('data_de') }}"
                                            class="w-full bg-white border border-gray-200 rounded-lg px-2 py-0.5 text-xs text-gray-700 focus:outline-none focus:border-[#DA291C]">
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-6 uppercase">ATÉ:</span>
                                        <input type="date" name="data_ate" value="{{ request('data_ate') }}"
                                            class="w-full bg-white border border-gray-200 rounded-lg px-2 py-0.5 text-xs text-gray-700 focus:outline-none focus:border-[#DA291C]">
                                    </div>
                                </div>
                            </td>
                            <!-- Atividade Select Dropdown -->
                            <td class="py-3 px-4 align-middle">
                                <select name="atividade" class="w-full bg-white border border-gray-200 text-gray-700 text-xs rounded-lg px-2 py-1.5 focus:outline-none focus:border-[#DA291C] cursor-pointer">
                                    <option value="">Selecione...</option>
                                    @foreach ($activities as $act)
                                        <option value="{{ $act }}" {{ request('atividade') == $act ? 'selected' : '' }}>{{ $act }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <!-- Tipo Select Dropdown -->
                            <td class="py-3 px-4 align-middle">
                                <select name="tipo" class="w-full bg-white border border-gray-200 text-gray-700 text-xs rounded-lg px-2 py-1.5 focus:outline-none focus:border-[#DA291C] cursor-pointer">
                                    <option value="">Selecione...</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type }}" {{ request('tipo') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <!-- Nome Input Field -->
                            <td class="py-3 px-4 align-middle">
                                <input type="text" name="nome" value="{{ request('nome') }}" placeholder="Nome..."
                                    class="w-full bg-white border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-700 placeholder-gray-300 focus:outline-none focus:border-[#DA291C]">
                            </td>
                            <!-- PDV Input Field -->
                            <td class="py-3 px-4 align-middle">
                                <input type="text" name="pdv" value="{{ request('pdv') }}" placeholder="PDV..."
                                    class="w-full bg-white border border-gray-200 rounded-lg px-3 py-1.5 text-xs text-gray-700 placeholder-gray-300 focus:outline-none focus:border-[#DA291C]">
                            </td>
                            <!-- Empty Ação Filter -->
                            <td class="py-3 pl-4 align-middle text-center text-gray-400 font-bold text-xs select-none">
                                -
                            </td>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 border-t border-gray-100">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-gray-50/50 transition-colors text-xs font-semibold text-gray-600">
                                <!-- Timestamp -->
                                <td class="py-3.5 pr-4 text-gray-500 whitespace-nowrap">
                                    {{ $log->created_at->format('d/m/Y \à\s H\hi') }}
                                </td>
                                <!-- Activity Badge -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @php
                                        $badgeClass = match ($log->activity) {
                                            'Exclusão' => 'bg-red-50 text-red-600 border border-red-100',
                                            'Importação' => 'bg-orange-50 text-orange-600 border border-orange-100',
                                            'Atualização' => 'bg-blue-50 text-blue-600 border border-blue-100',
                                            'Criação' => 'bg-green-50 text-green-600 border border-green-100',
                                            default => 'bg-gray-50 text-gray-600 border border-gray-100'
                                        };
                                    @endphp
                                    <span class="px-4 py-1 text-[11px] font-bold rounded-full {{ $badgeClass }}">
                                        {{ $log->activity }}
                                    </span>
                                </td>
                                <!-- Type -->
                                <td class="py-3.5 px-4 text-gray-500 uppercase tracking-wide whitespace-nowrap">
                                    {{ $log->type }}
                                </td>
                                <!-- User Name -->
                                <td class="py-3.5 px-4 text-gray-800 font-bold whitespace-nowrap">
                                    {{ $log->user_name ?? ($log->user ? $log->user->name : 'N/A') }}
                                </td>
                                <!-- PDV -->
                                <td class="py-3.5 px-4 text-gray-500 whitespace-nowrap">
                                    {{ $log->pdv ?? '-' }}
                                </td>
                                <!-- Action Button Link -->
                                <td class="py-3.5 pl-4 text-center whitespace-nowrap">
                                    <button type="button" onclick="showDetailsModal({{ json_encode($log) }})"
                                        class="text-gray-800 hover:text-[#DA291C] underline font-bold transition-colors cursor-pointer focus:outline-none">
                                        Ver detalhes
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-400 font-medium select-none">
                                    Nenhum log de atividade encontrado para os filtros informados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

<!-- Details Modal Component -->
<div id="details-modal" class="fixed inset-0 z-[100] hidden bg-black/60 backdrop-blur-sm items-center justify-center p-4 transition-all duration-300 select-none">
    <div class="bg-white w-full max-w-[500px] rounded-[24px] shadow-2xl relative flex flex-col p-6 gap-4 transform scale-95 opacity-0 transition-all duration-300" id="details-modal-content">
        <!-- Close button (x) -->
        <button onclick="closeDetailsModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors focus:outline-none cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Modal Header -->
        <div>
            <h3 class="text-lg font-bold text-[#DA291C]" id="modal-title-activity">Detalhes do Log</h3>
            <p class="text-xs text-gray-400 font-semibold mt-1" id="modal-timestamp"></p>
        </div>

        <!-- Separator -->
        <div class="border-b border-gray-100 my-1"></div>

        <!-- Modal Body Content -->
        <div class="flex flex-col gap-3 text-sm">
            <div class="grid grid-cols-3 gap-2 py-0.5">
                <span class="text-gray-400 font-bold text-xs uppercase tracking-wider">Atividade:</span>
                <span class="col-span-2 font-bold text-gray-800" id="modal-activity"></span>
            </div>
            <div class="grid grid-cols-3 gap-2 py-0.5">
                <span class="text-gray-400 font-bold text-xs uppercase tracking-wider">Tipo:</span>
                <span class="col-span-2 font-semibold text-gray-700" id="modal-type"></span>
            </div>
            <div class="grid grid-cols-3 gap-2 py-0.5">
                <span class="text-gray-400 font-bold text-xs uppercase tracking-wider">Nome:</span>
                <span class="col-span-2 font-bold text-gray-800" id="modal-name"></span>
            </div>
            <div class="grid grid-cols-3 gap-2 py-0.5">
                <span class="text-gray-400 font-bold text-xs uppercase tracking-wider">PDV:</span>
                <span class="col-span-2 font-semibold text-gray-700" id="modal-pdv"></span>
            </div>
            <div class="flex flex-col gap-1.5 mt-2 bg-gray-50 p-4 rounded-2xl border border-gray-100 text-left">
                <span class="text-gray-400 font-bold text-[10px] uppercase tracking-wider">Descrição Detalhada:</span>
                <p class="text-gray-600 text-xs leading-relaxed font-normal whitespace-pre-line" id="modal-details"></p>
            </div>
        </div>

        <!-- Modal Footer Button -->
        <div class="flex justify-end mt-2">
            <button onclick="closeDetailsModal()" class="bg-[#86131E] hover:bg-[#A01724] text-white text-xs font-bold px-6 py-2.5 rounded-full transition-all focus:outline-none cursor-pointer">
                Fechar
            </button>
        </div>
    </div>
</div>

<script>
    // State management helper functions
    function updatePerPage(val) {
        document.getElementById('per_page_hidden').value = val;
        document.getElementById('page_hidden').value = 1; // Reset to page 1 on per_page change
        submitFilters();
    }

    function goToPage(page) {
        document.getElementById('page_hidden').value = page;
        submitFilters();
    }

    function clearFilters() {
        const form = document.getElementById('filters-form');
        form.querySelectorAll('input[type="text"], input[type="date"], select:not(#per-page-select)').forEach(input => {
            input.value = '';
        });
        document.getElementById('page_hidden').value = 1;
        submitFilters();
    }

    function submitFilters() {
        document.getElementById('filters-form').submit();
    }

    // Details Modal controllers
    function showDetailsModal(log) {
        document.getElementById('modal-title-activity').innerText = 'Log: ' + log.activity + ' (' + log.type + ')';
        
        // Format datetime correctly in Portuguese locale
        const dateObj = new Date(log.created_at);
        const formattedDate = dateObj.toLocaleDateString('pt-BR') + ' às ' + dateObj.toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'});
        document.getElementById('modal-timestamp').innerText = formattedDate;
        
        document.getElementById('modal-activity').innerText = log.activity;
        document.getElementById('modal-type').innerText = log.type;
        document.getElementById('modal-name').innerText = log.user_name || 'N/A';
        document.getElementById('modal-pdv').innerText = log.pdv || '-';
        document.getElementById('modal-details').innerText = log.details || 'Nenhuma informação detalhada informada.';
        
        const modal = document.getElementById('details-modal');
        const content = document.getElementById('details-modal-content');
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeDetailsModal() {
        const modal = document.getElementById('details-modal');
        const content = document.getElementById('details-modal-content');
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    // Close on click outside Modal Card
    document.getElementById('details-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDetailsModal();
        }
    });
</script>
@endsection
