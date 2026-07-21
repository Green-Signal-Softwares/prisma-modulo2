@extends('layouts.app')

@section('title', 'Presets Globais — Claro Prisma')

@section('content')
<!-- Breadcrumbs -->
<div class="mb-2 select-none">
    @php
        $homeRoute = auth()->check() && auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard');
    @endphp
    <nav aria-label="breadcrumb" class="flex items-center gap-1.5">
        <a href="{{ $homeRoute }}" class="breadcrumb breadcrumb-link">Claro Prisma</a>
        <span class="breadcrumb breadcrumb-separator">&gt;</span>
        <span class="breadcrumb breadcrumb-current">Presets Globais</span>
    </nav>
</div>

<!-- Header -->
<h1 class="text-4xl font-black text-[#DA291C] tracking-tight mb-8">Presets Globais</h1>

<!-- Session Alerts -->
@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 text-sm font-semibold rounded-2xl flex items-center gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

<!-- Section 1: Respostas Rápidas -->
<div class="mb-10">
    <div class="flex items-center gap-3 mb-6">
        <h2 class="text-xl font-bold text-gray-800">Respostas rápidas</h2>
        <button onclick="openAddPresetModal()" class="bg-[#2D3139] hover:bg-[#1A1D22] text-white text-xs font-bold px-3 py-1.5 rounded-full transition-all cursor-pointer flex items-center gap-1 select-none">
            Adicionar nova <span class="text-sm font-bold">+</span>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($presets as $preset)
            <div class="bg-white rounded-[24px] border border-gray-200 p-6 flex justify-between items-start gap-4 shadow-sm hover:shadow-md transition-all">
                <div class="flex-1">
                    <h3 class="text-xl font-extrabold text-[#DA291C] mb-3">{{ $preset->shortcut }}</h3>
                    <div class="text-sm font-semibold text-gray-700 leading-relaxed space-y-2 whitespace-pre-line">{{ $preset->text }}</div>
                </div>
                <div class="flex items-center gap-2 select-none self-center">
                    <!-- Edit Button -->
                    <button onclick="openEditPresetModal({{ $preset->toJson() }})" class="w-8 h-8 rounded-full bg-[#E27D18] hover:bg-[#C96910] flex items-center justify-center text-white transition-all shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <!-- Delete Button -->
                    <form action="{{ route('admin.presets.destroy', $preset->id) }}" method="POST" onsubmit="return confirm('Deseja realmente remover esta resposta rápida?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-red-600 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Section 2: Tags -->
<div class="mb-10">
    <div class="flex items-center gap-3 mb-6">
        <h2 class="text-xl font-bold text-gray-800">Tags</h2>
        <button onclick="openAddTagModal()" class="bg-[#2D3139] hover:bg-[#1A1D22] text-white text-xs font-bold px-3 py-1.5 rounded-full transition-all cursor-pointer flex items-center gap-1 select-none">
            Adicionar nova <span class="text-sm font-bold">+</span>
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($tags as $tag)
            <div class="bg-white rounded-[18px] border border-gray-200 py-3.5 px-5 flex justify-between items-center gap-3 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <span class="w-3.5 h-3.5 rounded-full flex-shrink-0" style="background-color: {{ $tag->color }}"></span>
                    <span class="text-lg font-bold text-[#DA291C]">{{ $tag->name }}</span>
                </div>
                <div class="flex items-center gap-2 select-none">
                    <!-- Edit Button -->
                    <button onclick="openEditTagModal({{ $tag->toJson() }})" class="w-7 h-7 rounded-full bg-[#E27D18] hover:bg-[#C96910] flex items-center justify-center text-white transition-all shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <!-- Delete Button -->
                    <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST" onsubmit="return confirm('Deseja realmente remover esta tag?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-red-600 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Add/Edit Preset Modal -->
<div id="preset-modal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-white w-full max-w-[640px] rounded-[24px] shadow-2xl p-6 relative flex flex-col gap-4 transform scale-95 opacity-0 transition-all duration-300">
        <button onclick="closePresetModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>

        <h3 id="preset-modal-title" class="text-3xl font-extrabold text-[#DA291C]">Adicionar resposta rápida</h3>

        <form id="preset-form" method="POST" class="flex flex-col gap-4">
            @csrf
            <div id="preset-method-field"></div>

            <div class="flex flex-col gap-1">
                <label for="preset-shortcut" class="text-xs font-bold text-gray-700">Nome da resposta/atalho</label>
                <input id="preset-shortcut" name="shortcut" type="text" placeholder="Ex: /Mensagem inicial" required class="w-full h-12 rounded-lg border border-gray-300 px-3 text-sm text-gray-700 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#DA291C]">
            </div>

            <div class="flex flex-col gap-1">
                <label for="preset-text" class="text-xs font-bold text-gray-700">Mensagem</label>
                <textarea id="preset-text" name="text" placeholder="Digite a resposta rápida aqui..." required class="w-full min-h-[140px] rounded-lg border border-gray-300 px-3 py-3 text-sm text-gray-700 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#DA291C]"></textarea>
            </div>

            <div class="flex gap-3 justify-end mt-2">
                <button type="button" onclick="closePresetModal()" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-bold hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-white font-bold hover:opacity-95 transition-all bg-[#DA291C]">
                    Salvar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Tag Modal -->
<div id="tag-modal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-white w-full max-w-[500px] rounded-[24px] shadow-2xl p-6 relative flex flex-col gap-4 transform scale-95 opacity-0 transition-all duration-300">
        <button onclick="closeTagModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>

        <h3 id="tag-modal-title" class="text-3xl font-extrabold text-[#DA291C]">Adicionar tag</h3>

        <form id="tag-form" method="POST" class="flex flex-col gap-4">
            @csrf
            <div id="tag-method-field"></div>

            <div class="flex flex-col gap-1">
                <label for="tag-name" class="text-xs font-bold text-gray-700">Nome da tag</label>
                <input id="tag-name" name="name" type="text" placeholder="Ex: Tag 1" required class="w-full h-12 rounded-lg border border-gray-300 px-3 text-sm text-gray-700 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#DA291C]">
            </div>

            <div class="flex flex-col gap-1">
                <label for="tag-color" class="text-xs font-bold text-gray-700">Cor</label>
                <div class="flex gap-2 items-center">
                    <input id="tag-color-picker" type="color" oninput="document.getElementById('tag-color').value = this.value" class="w-12 h-12 rounded-lg border border-gray-300 cursor-pointer p-1">
                    <input id="tag-color" name="color" type="text" placeholder="#CCCCCC" required oninput="document.getElementById('tag-color-picker').value = this.value" class="w-full h-12 rounded-lg border border-gray-300 px-3 text-sm text-gray-700 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-[#DA291C]">
                </div>
            </div>

            <div class="flex gap-3 justify-end mt-2">
                <button type="button" onclick="closeTagModal()" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-bold hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-white font-bold hover:opacity-95 transition-all bg-[#DA291C]">
                    Salvar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Preset Modals
    function openAddPresetModal() {
        document.getElementById('preset-modal-title').textContent = 'Adicionar resposta rápida';
        document.getElementById('preset-form').action = "{{ route('admin.presets.store') }}";
        document.getElementById('preset-method-field').innerHTML = '';
        document.getElementById('preset-shortcut').value = '';
        document.getElementById('preset-text').value = '';
        showModal('preset-modal');
    }

    function openEditPresetModal(preset) {
        document.getElementById('preset-modal-title').textContent = 'Editar resposta rápida';
        document.getElementById('preset-form').action = "/admin/presets/" + preset.id;
        document.getElementById('preset-method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('preset-shortcut').value = preset.shortcut;
        document.getElementById('preset-text').value = preset.text;
        showModal('preset-modal');
    }

    function closePresetModal() {
        hideModal('preset-modal');
    }

    // Tag Modals
    function openAddTagModal() {
        document.getElementById('tag-modal-title').textContent = 'Adicionar tag';
        document.getElementById('tag-form').action = "{{ route('admin.tags.store') }}";
        document.getElementById('tag-method-field').innerHTML = '';
        document.getElementById('tag-name').value = '';
        document.getElementById('tag-color').value = '#CCCCCC';
        document.getElementById('tag-color-picker').value = '#CCCCCC';
        showModal('tag-modal');
    }

    function openEditTagModal(tag) {
        document.getElementById('tag-modal-title').textContent = 'Editar tag';
        document.getElementById('tag-form').action = "/admin/tags/" + tag.id;
        document.getElementById('tag-method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('tag-name').value = tag.name;
        document.getElementById('tag-color').value = tag.color;
        document.getElementById('tag-color-picker').value = tag.color;
        showModal('tag-modal');
    }

    function closeTagModal() {
        hideModal('tag-modal');
    }

    // Generic Modal Animation helpers
    function showModal(id) {
        const modal = document.getElementById(id);
        const content = modal.querySelector('div');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function hideModal(id) {
        const modal = document.getElementById(id);
        const content = modal.querySelector('div');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 300);
    }
</script>
@endsection
