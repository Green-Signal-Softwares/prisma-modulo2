@extends('layouts.app')

@section('title', 'Central do usuário — Claro Prisma')

@section('content')
<!-- Breadcrumbs -->
<div class="mb-2 select-none">
    @php
        $homeRoute = auth()->check() && auth()->user()->role === 'admin'
            ? route('admin.dashboard')
            : (auth()->check() && auth()->user()->role === 'atendente' ? route('atendente.dashboard') : route('dashboard'));
    @endphp
    <nav aria-label="breadcrumb" class="flex items-center gap-1.5">
        <a href="{{ $homeRoute }}" class="breadcrumb breadcrumb-link">Claro Prisma</a>
        <span class="breadcrumb breadcrumb-separator">&gt;</span>
        <span class="breadcrumb breadcrumb-current">Central do usuário</span>
    </nav>
</div>

<!-- Header -->
<h1 class="text-4xl font-black text-[#DA291C] tracking-tight mb-2">Central do usuário</h1>
<p class="text-sm text-gray-500 font-medium mb-8">Gerencie suas informações, configurações de acesso e recursos da sua conta em um só lugar.</p>

<!-- Session Alerts -->
@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 text-sm font-semibold rounded-2xl flex items-center gap-3">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 text-sm font-semibold rounded-2xl flex flex-col gap-1">
        @foreach($errors->all() as $error)
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                <span>{{ $error }}</span>
            </div>
        @endforeach
    </div>
@endif

<!-- Main Card Container -->
<div class="bg-white rounded-[24px] border border-gray-300 shadow-sm p-8 flex flex-col md:flex-row gap-8 min-h-[500px]">
    
    <!-- Left Sidebar Menu -->
    <div class="w-full md:w-64 flex flex-col items-start border-b md:border-b-0 md:border-r border-gray-300 pb-6 md:pb-0 md:pr-8 flex-shrink-0">
        <!-- Avatar + Name Horizontal Row -->
        <div class="w-full flex items-center gap-4 mb-4 select-none">
            <!-- User Avatar -->
            <div class="relative w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 border border-gray-200 flex-shrink-0 group">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=f0f0f0&color=666666&size=128&bold=true&rounded=true" 
                     alt="Avatar" class="w-full h-full rounded-full object-cover">
                <!-- Camera Badge Overlay -->
                <div class="absolute bottom-0 right-0 w-6 h-6 rounded-full bg-[#9c1322] hover:bg-[#b31d14] text-white flex items-center justify-center border-2 border-white shadow cursor-pointer transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                    </svg>
                </div>
            </div>
            <!-- User Name -->
            <span class="text-base font-bold text-gray-800 leading-tight" style="font-family: 'AMX', sans-serif;">{{ $user->name }}</span>
        </div>

        <!-- Alterar Foto Button -->
        <button class="w-full bg-[#9c1322] hover:bg-[#b31d14] text-white font-bold text-xs py-2.5 px-4 rounded-full transition-all mb-5 cursor-pointer">
            Alterar Foto
        </button>

        <!-- Divider line -->
        <hr class="w-full border-gray-150 mb-5" />

        <!-- Navigation Links -->
        <div class="w-full flex flex-col gap-4 text-left pl-1">
            <button onclick="switchTab('meus-dados')" id="tab-btn-meus-dados" 
                class="w-full text-left font-bold text-sm transition-all cursor-pointer text-[#DA291C] hover:text-[#b31d14]">
                Meus dados
            </button>
            <button onclick="switchTab('editar-senha')" id="tab-btn-editar-senha" 
                class="w-full text-left font-bold text-sm transition-all cursor-pointer text-gray-600 hover:text-gray-900">
                Editar senha
            </button>
            <button onclick="switchTab('configuracoes')" id="tab-btn-configuracoes" 
                class="w-full text-left font-bold text-sm transition-all cursor-pointer text-gray-600 hover:text-gray-900">
                Configurações
            </button>
            <form action="{{ route('logout') }}" method="POST" class="m-0 w-full">
                @csrf
                <button type="submit" 
                    class="w-full text-left font-bold text-sm transition-all cursor-pointer text-gray-600 hover:text-gray-900">
                    Sair
                </button>
            </form>
        </div>
    </div>

    <!-- Right Side: Content Area -->
    <div class="flex-1 md:pl-8 pt-6 md:pt-0">
        
        <!-- Tab 1: Meus dados Form -->
        <div id="tab-content-meus-dados" class="block">
            <form action="{{ route('profile.update') }}" method="POST" id="profile-form">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <!-- Nome Completo -->
                    <div class="md:col-span-2 flex flex-col gap-1">
                        <label class="text-xs font-bold text-gray-500">Nome Completo</label>
                        <input type="text" value="{{ $user->name }}" disabled
                            class="w-full h-12 px-4 bg-gray-100 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-500 outline-none">
                    </div>

                    <!-- Como você quer ser chamado -->
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold text-gray-500">Como você quer ser chamado</label>
                        <input type="text" name="name" value="{{ $user->name }}" required disabled
                            class="profile-editable-input w-full h-12 px-4 bg-gray-100 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-500 outline-none focus:outline-none focus:border-[#DA291C] focus:bg-white focus:ring-1 focus:ring-[#DA291C] transition-all">
                    </div>

                    <!-- Cargo -->
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold text-gray-500">Cargo</label>
                        <select name="role" disabled
                            class="w-full h-12 px-4 bg-gray-100 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-500 cursor-not-allowed outline-none">
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrador</option>
                            <option value="atendente" {{ $user->role === 'atendente' ? 'selected' : '' }}>Atendente</option>
                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Cliente</option>
                        </select>
                    </div>

                    <!-- Login -->
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold text-gray-500">Login</label>
                        <input type="text" name="login" value="{{ $user->login ?? 'ADMIN212121' }}" required disabled
                            class="w-full h-12 px-4 bg-gray-100 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-500 outline-none">
                    </div>

                    <!-- CPF -->
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold text-gray-500">CPF</label>
                        <input type="text" value="021.021.021-21" disabled
                            class="w-full h-12 px-4 bg-gray-100 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-500 outline-none">
                    </div>

                    <!-- E-mail -->
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold text-gray-500">E-mail</label>
                        <input type="email" name="email" value="{{ $user->email }}" required disabled
                            class="profile-editable-input w-full h-12 px-4 bg-gray-100 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-500 outline-none focus:outline-none focus:border-[#DA291C] focus:bg-white focus:ring-1 focus:ring-[#DA291C] transition-all">
                    </div>

                    <!-- Celular -->
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold text-gray-500">Celular</label>
                        <input type="text" name="phone" value="{{ $user->phone ?? '(99) 12345-6789' }}" disabled
                            class="profile-editable-input w-full h-12 px-4 bg-gray-100 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-500 outline-none focus:outline-none focus:border-[#DA291C] focus:bg-white focus:ring-1 focus:ring-[#DA291C] transition-all">
                    </div>

                    <!-- Gestor -->
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold text-gray-500">Gestor</label>
                        <input type="text" value="Felipe Gomes Melo" disabled
                            class="w-full h-12 px-4 bg-gray-100 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-500 outline-none">
                    </div>

                    <!-- Canal -->
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold text-gray-500">Canal</label>
                        <input type="text" value="Agente Autorizado" disabled
                            class="w-full h-12 px-4 bg-gray-100 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-500 outline-none">
                    </div>
                </div>

                <!-- Action Button -->
                <div class="mt-8 flex gap-4 justify-start">
                    <!-- Non-Editing State -->
                    <button type="button" onclick="enableEditMode()" id="edit-btn-view"
                        class="px-10 py-2.5 bg-white border border-[#9c1322] hover:bg-red-50 text-[#9c1322] text-sm font-bold rounded-full transition-all cursor-pointer select-none">
                        Editar
                    </button>
                    
                    <!-- Editing State -->
                    <button type="button" onclick="cancelEditMode()" id="edit-btn-cancel" style="display: none;"
                        class="px-10 py-2.5 bg-white border border-[#9c1322] hover:bg-red-50 text-[#9c1322] text-sm font-bold rounded-full transition-all cursor-pointer select-none">
                        Cancelar
                    </button>
                    <button type="submit" id="edit-btn-save" style="display: none;"
                        class="px-10 py-2.5 bg-[#9c1322] hover:bg-[#b31d14] text-white text-sm font-bold rounded-full transition-all cursor-pointer select-none">
                        Salvar
                    </button>
                </div>
            </form>
        </div>

        <!-- Tab 2: Editar senha Form -->
        <div id="tab-content-editar-senha" class="hidden">
            <form action="{{ route('profile.update') }}" method="POST" id="password-form">
                @csrf
                <input type="hidden" name="name" value="{{ $user->name }}">
                
                <div class="max-w-xl flex flex-col gap-5">
                    <!-- Senha Atual -->
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold text-gray-500">Senha atual</label>
                        <div class="relative">
                            <input type="password" name="current_password" id="input-current-password" required
                                class="w-full h-12 pl-4 pr-24 bg-gray-50 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-700 focus:outline-none focus:border-[#DA291C] focus:bg-white focus:ring-1 focus:ring-[#DA291C] transition-all">
                            <!-- Toggle Button container with eye-slash and eye -->
                            <button type="button" onclick="togglePasswordVisibility('input-current-password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-1.5 text-gray-500 hover:text-gray-800 focus:outline-none select-none">
                                <span class="text-gray-400">
                                    <!-- Eye Slash -->
                                    <svg class="w-5 h-5 icon-slash hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                    <!-- Eye -->
                                    <svg class="w-5 h-5 icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Nova Senha -->
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-bold text-gray-500">Nova senha</label>
                        <div class="relative">
                            <input type="password" name="new_password" id="input-new-password" placeholder="Digite sua nova senha" required
                                class="w-full h-12 pl-4 pr-12 bg-gray-50 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#DA291C] focus:bg-white focus:ring-1 focus:ring-[#DA291C] transition-all">
                            <button type="button" onclick="togglePasswordVisibility('input-new-password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center text-gray-500 hover:text-gray-800 focus:outline-none select-none">
                                <svg class="w-5 h-5 icon-slash hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                                <svg class="w-5 h-5 icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Confirmar a senha (no label at the top) -->
                    <div class="flex flex-col gap-1">
                        <div class="relative">
                            <input type="password" name="new_password_confirmation" id="input-confirm-password" placeholder="Confirmar a senha" required
                                class="w-full h-12 pl-4 pr-12 bg-gray-50 border border-gray-200 rounded-[12px] text-sm font-semibold text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#DA291C] focus:bg-white focus:ring-1 focus:ring-[#DA291C] transition-all">
                            <button type="button" onclick="togglePasswordVisibility('input-confirm-password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center text-gray-500 hover:text-gray-800 focus:outline-none select-none">
                                <svg class="w-5 h-5 icon-slash hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                                <svg class="w-5 h-5 icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="mt-8 flex gap-4 justify-start">
                    <button type="button" onclick="switchTab('meus-dados')"
                        class="px-10 py-2.5 bg-white border border-[#9c1322] hover:bg-red-50 text-[#9c1322] text-sm font-bold rounded-full transition-all cursor-pointer select-none">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-10 py-2.5 bg-[#9c1322] hover:bg-[#b31d14] text-white text-sm font-bold rounded-full transition-all cursor-pointer select-none">
                        Confirmar
                    </button>
                </div>
            </form>
        <!-- Tab 2: Editar senha Form Ends -->
        </div>

        <!-- Tab 3: Configurações (Notificações) -->
        <div id="tab-content-configuracoes" class="hidden">
            <h2 class="text-[22px] font-bold text-gray-800 mb-6" style="font-family: 'AMX', sans-serif;">Notificações</h2>
            
            <div class="flex flex-col gap-3.5 max-w-2xl">
                <!-- Item 1: Tela do sistema -->
                <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-150 rounded-[14px] transition-all">
                    <div class="flex items-center gap-3">
                        <span class="p-2 bg-white rounded-lg border border-gray-200 text-[#9c1322] flex items-center justify-center">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                            </svg>
                        </span>
                        <span class="text-sm font-bold text-gray-850" style="font-family: 'AMX', sans-serif;">Tela do sistema</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" id="toggle-tela" class="sr-only peer toggle-preference" onchange="syncNotificationToggles()">
                        <div class="w-12 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#24A148] transition-colors"></div>
                    </label>
                </div>

                <!-- Item 2: Notificação Push -->
                <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-150 rounded-[14px] transition-all">
                    <div class="flex items-center gap-3">
                        <span class="p-2 bg-white rounded-lg border border-gray-200 text-[#9c1322] flex items-center justify-center">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a9.04 9.04 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5M19.186 9.499a6.008 6.008 0 0 0-11.618 0M19.186 9.499c.09.812.26 1.612.508 2.386a12.022 12.022 0 0 0 1.208 2.622L21.2 16.5a1.205 1.205 0 0 1-1.054 1.8H3.855a1.205 1.205 0 0 1-1.054-1.8l.286-.593a12.019 12.019 0 0 0 1.208-2.622C4.54 11.11 4.71 10.31 4.8 9.5M19.186 9.499h-14.37" />
                            </svg>
                        </span>
                        <span class="text-sm font-bold text-gray-855" style="font-family: 'AMX', sans-serif;">Notificação Push</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" id="toggle-push" class="sr-only peer toggle-preference" checked onchange="syncNotificationToggles()">
                        <div class="w-12 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#24A148] transition-colors"></div>
                    </label>
                </div>

                <!-- Item 3: App -->
                <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-150 rounded-[14px] transition-all">
                    <div class="flex items-center gap-3">
                        <span class="p-2 bg-white rounded-lg border border-gray-200 text-[#9c1322] flex items-center justify-center">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                            </svg>
                        </span>
                        <span class="text-sm font-bold text-gray-850" style="font-family: 'AMX', sans-serif;">App</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" id="toggle-app" class="sr-only peer toggle-preference" checked onchange="syncNotificationToggles()">
                        <div class="w-12 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#24A148] transition-colors"></div>
                    </label>
                </div>

                <!-- Item 4: E-mail -->
                <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-150 rounded-[14px] transition-all">
                    <div class="flex items-center gap-3">
                        <span class="p-2 bg-white rounded-lg border border-gray-200 text-[#9c1322] flex items-center justify-center">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H4.5a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5H4.5a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </span>
                        <span class="text-sm font-bold text-gray-850" style="font-family: 'AMX', sans-serif;">E-mail</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" id="toggle-email" class="sr-only peer toggle-preference" checked onchange="syncNotificationToggles()">
                        <div class="w-12 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#24A148] transition-colors"></div>
                    </label>
                </div>

                <!-- Item 5: Todos -->
                <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-150 rounded-[14px] transition-all">
                    <div class="flex items-center gap-3">
                        <span class="p-2 bg-white rounded-lg border border-gray-200 text-[#9c1322] flex items-center justify-center">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                            </svg>
                        </span>
                        <span class="text-sm font-bold text-gray-850" style="font-family: 'AMX', sans-serif;">Todos</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" id="toggle-todos" class="sr-only peer" onchange="toggleAllNotifications(this)">
                        <div class="w-12 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#24A148] transition-colors"></div>
                    </label>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Handle URL parameters to automatically select tab
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab === 'password') {
            switchTab('editar-senha');
        } else if (tab === 'configuracoes') {
            switchTab('configuracoes');
        }
        loadNotificationPreferences();
    });

    // Tab switching logic
    function switchTab(tabName) {
        const meusDadosContent = document.getElementById('tab-content-meus-dados');
        const editarSenhaContent = document.getElementById('tab-content-editar-senha');
        const configuracoesContent = document.getElementById('tab-content-configuracoes');
        
        const meusDadosBtn = document.getElementById('tab-btn-meus-dados');
        const editarSenhaBtn = document.getElementById('tab-btn-editar-senha');
        const configuracoesBtn = document.getElementById('tab-btn-configuracoes');

        // Hide all
        [meusDadosContent, editarSenhaContent, configuracoesContent].forEach(el => {
            el.classList.add('hidden');
            el.classList.remove('block');
        });

        // Reset btn classes
        [meusDadosBtn, editarSenhaBtn, configuracoesBtn].forEach(btn => {
            btn.className = "w-full text-left font-bold text-sm transition-all cursor-pointer text-gray-600 hover:text-gray-900";
        });

        // Show selected
        if (tabName === 'meus-dados') {
            meusDadosContent.classList.remove('hidden');
            meusDadosContent.classList.add('block');
            meusDadosBtn.className = "w-full text-left font-bold text-sm transition-all cursor-pointer text-[#DA291C] hover:text-[#b31d14]";
        } else if (tabName === 'editar-senha') {
            editarSenhaContent.classList.remove('hidden');
            editarSenhaContent.classList.add('block');
            editarSenhaBtn.className = "w-full text-left font-bold text-sm transition-all cursor-pointer text-[#DA291C] hover:text-[#b31d14]";
        } else if (tabName === 'configuracoes') {
            configuracoesContent.classList.remove('hidden');
            configuracoesContent.classList.add('block');
            configuracoesBtn.className = "w-full text-left font-bold text-sm transition-all cursor-pointer text-[#DA291C] hover:text-[#b31d14]";
        }
    }

    // Toggle edit/save mode for Meus Dados
    function enableEditMode() {
        const inputs = document.querySelectorAll('.profile-editable-input');
        inputs.forEach(input => {
            input.removeAttribute('disabled');
            input.classList.remove('bg-gray-100');
            input.classList.add('bg-white');
        });
        
        // Toggle action buttons
        document.getElementById('edit-btn-view').style.display = 'none';
        document.getElementById('edit-btn-cancel').style.display = 'inline-block';
        document.getElementById('edit-btn-save').style.display = 'inline-block';
    }

    function cancelEditMode() {
        window.location.reload();
    }

    // Password show/hide toggle
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const iconSlash = btn.querySelector('.icon-slash');
        const iconEye = btn.querySelector('.icon-eye');

        if (input.type === 'password') {
            input.type = 'text';
            iconSlash.classList.remove('hidden');
            iconEye.classList.add('hidden');
        } else {
            input.type = 'password';
            iconSlash.classList.add('hidden');
            iconEye.classList.remove('hidden');
        }
    }

    // Notification synchronization toggles
    function toggleAllNotifications(todosToggle) {
        const toggles = document.querySelectorAll('.toggle-preference');
        toggles.forEach(toggle => {
            toggle.checked = todosToggle.checked;
        });
        saveNotificationPreferences();
    }

    function syncNotificationToggles() {
        const toggles = document.querySelectorAll('.toggle-preference');
        const todosToggle = document.getElementById('toggle-todos');
        
        let allChecked = true;
        toggles.forEach(toggle => {
            if (!toggle.checked) {
                allChecked = false;
            }
        });
        todosToggle.checked = allChecked;
        saveNotificationPreferences();
    }

    // Save/Load preferences from localStorage to make it persist
    function saveNotificationPreferences() {
        const preferences = {
            tela: document.getElementById('toggle-tela').checked,
            push: document.getElementById('toggle-push').checked,
            app: document.getElementById('toggle-app').checked,
            email: document.getElementById('toggle-email').checked,
        };
        localStorage.setItem('notification_preferences', JSON.stringify(preferences));
    }

    function loadNotificationPreferences() {
        const stored = localStorage.getItem('notification_preferences');
        if (stored) {
            try {
                const preferences = JSON.parse(stored);
                document.getElementById('toggle-tela').checked = !!preferences.tela;
                document.getElementById('toggle-push').checked = !!preferences.push;
                document.getElementById('toggle-app').checked = !!preferences.app;
                document.getElementById('toggle-email').checked = !!preferences.email;
                
                // Sync the "todos" toggle based on initial load
                syncNotificationToggles();
            } catch (e) {
                console.error("Error loading notification preferences", e);
            }
        } else {
            // Default setup from markup
            syncNotificationToggles();
        }
    }
</script>
@endsection
