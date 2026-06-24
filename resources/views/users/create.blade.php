@extends('layouts.app')

@section('title', 'Novo Usuário - PRISMA Claro')

@section('content')
<!-- Breadcrumbs & Heading -->
<div class="mb-6">
    <div class="text-xs text-gray-500 mb-1">
        <span>Claro Prisma</span> &gt; <span>Gestão de Usuários</span> &gt; <span class="font-medium text-gray-700">Novo Usuário</span>
    </div>
    <h1 class="text-3xl font-bold text-[#A01724]">Cadastrar Novo Usuário</h1>
</div>

<!-- Back button -->
<div class="mb-6">
    <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-600 hover:text-[#DA291C] transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
        </svg>
        <span>Voltar para a listagem</span>
    </a>
</div>

<!-- Create Form Card -->
<div class="bg-white p-6 md:p-8 rounded-2xl border border-gray-100 shadow-sm max-w-xl">
    
    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-[#DA291C] rounded-[14px] text-sm">
            <div class="font-bold mb-1">Por favor, corrija os erros abaixo:</div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Nome Completo -->
        <div class="space-y-2">
            <label for="name" class="block text-sm font-bold text-gray-700">Nome Completo</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name') }}"
                required 
                placeholder="Ex: Mauro Filho" 
                class="w-full px-4 py-3 border border-gray-300 rounded-[14px] text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#DA291C] focus:ring-1 focus:ring-[#DA291C] transition-all text-sm"
            >
        </div>

        <!-- E-mail -->
        <div class="space-y-2">
            <label for="email" class="block text-sm font-bold text-gray-700">Endereço de E-mail</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}"
                required 
                placeholder="Ex: mauro@claro.com.br" 
                class="w-full px-4 py-3 border border-gray-300 rounded-[14px] text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#DA291C] focus:ring-1 focus:ring-[#DA291C] transition-all text-sm"
            >
        </div>

        <!-- Senha -->
        <div class="space-y-2">
            <label for="password" class="block text-sm font-bold text-gray-700">Senha</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                required 
                placeholder="Digite a senha do usuário" 
                class="w-full px-4 py-3 border border-gray-300 rounded-[14px] text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#DA291C] focus:ring-1 focus:ring-[#DA291C] transition-all text-sm"
            >
        </div>

        <!-- Confirmação de Senha -->
        <div class="space-y-2">
            <label for="password_confirmation" class="block text-sm font-bold text-gray-700">Confirmar Senha</label>
            <input 
                type="password" 
                id="password_confirmation" 
                name="password_confirmation" 
                required 
                placeholder="Repita a senha digitada" 
                class="w-full px-4 py-3 border border-gray-300 rounded-[14px] text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#DA291C] focus:ring-1 focus:ring-[#DA291C] transition-all text-sm"
            >
        </div>

        <!-- Submit Button -->
        <button 
            type="submit" 
            class="w-full bg-[#DA291C] hover:bg-[#B31D14] text-white py-3.5 px-4 rounded-[12px] font-semibold text-base transition-all flex items-center justify-center gap-2 active:scale-[0.99] cursor-pointer shadow-lg shadow-red-600/10"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
            </svg>
            <span>Salvar Usuário</span>
        </button>
    </form>

</div>
@endsection
