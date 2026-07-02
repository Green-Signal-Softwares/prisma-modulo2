<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'PRISMA Claro')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..900;1,400..900&display=swap"
        rel="stylesheet">

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css'])

    <style>
        /* Custom AMX Font definition */
        @font-face {
            font-family: 'AMX';
            src: local('AMX'), local('AMX-Medium'), local('AMX Medium'), local('AMX-Regular');
            font-weight: 500;
            font-style: normal;
        }

        @font-face {
            font-family: 'AMX';
            src: local('AMX-Bold'), local('AMX Bold');
            font-weight: 700;
            font-style: normal;
        }

        body {
            font-family: 'AMX', 'Instrument Sans', ui-sans-serif, system-ui, -apple-system, sans-serif;
        }

        /* Sidebar Smooth Transition */
        #sidebar {
            background: linear-gradient(89.24deg, #A01724 0%, #DA291C 100%);
            width: 264px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 50;
            transform: translateX(0);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .content-wrapper {
            padding-left: 264px;
            transition: padding-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Collapsed State */
        body.sidebar-collapsed #sidebar {
            transform: translateX(-264px);
        }

        body.sidebar-collapsed .content-wrapper {
            padding-left: 0;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .content-wrapper {
                padding-left: 0 !important;
            }

            body.sidebar-collapsed #sidebar {
                transform: translateX(-264px);
            }

            body:not(.sidebar-collapsed) #sidebar {
                transform: translateX(0);
            }
        }
    </style>
</head>

<body class="bg-[#F8F9FA] min-h-screen antialiased select-none">
    <script>
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }
    </script>

    <!-- Sidebar Component -->
    <aside id="sidebar" class="flex flex-col shadow-xl pt-[34px] pr-[32px] pb-[34px] pl-[20px] gap-[20px] text-white">
        <!-- Top Row: Hamburger & Menu Title -->
        <div class="relative flex items-center justify-between">
            <div class="flex items-center gap-3">
                <!-- Hamburger Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                    stroke="currentColor" class="w-6 h-6 text-white">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <span class="text-xl font-bold text-white tracking-tight">Menu</span>
            </div>
            <!-- Setinha toggle button (Absolute positioned overflowing the border) -->
            <button onclick="toggleSidebar()"
                class="absolute right-[-48px] w-9 h-9 rounded-full bg-white border border-gray-200 shadow-md flex items-center justify-center cursor-pointer hover:scale-105 active:scale-95 transition-all focus:outline-none z-55">
                <img src="/img/Icone Back Sidebar.png" alt="Recolher Sidebar" class="w-3.5 h-3.5 object-contain">
            </button>
        </div>

        <!-- White horizontal separator line -->
        <div class="border-b border-white/20 my-1"></div>

        <!-- Pedir Suporte Online White Pill Button -->
        @if(Auth::check() && Auth::user()->role !== 'atendente')
            <button onclick="openSupportModal()"
                class="w-full bg-white hover:bg-gray-50 text-[#DA291C] text-center font-bold py-3 px-4 rounded-2xl transition-all shadow-sm block text-sm active:scale-[0.98] cursor-pointer mb-2">
                Pedir suporte online
            </button>
        @endif

        <!-- Principal Section -->
        <!-- Principal Section -->
        <div class="flex flex-col gap-1.5 mt-2">
            <span class="text-[11px] font-bold text-white/50 uppercase tracking-wider pl-3 mb-1">Principal</span>

            @if(Auth::check() && Auth::user()->role === 'admin')
                <!-- Home Link -->
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all text-sm font-semibold {{ Route::currentRouteName() === 'admin.dashboard' ? 'bg-white/15 text-white font-bold' : 'hover:bg-white/10 text-white/80' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span>Home</span>
                </a>

                <!-- Dashboard Link -->
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all text-sm font-semibold hover:bg-white/10 text-white/80">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Mensagens Link -->
                <a href="{{ route('admin.chat.index') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all text-sm font-semibold {{ str_contains(Route::currentRouteName(), 'chat') ? 'bg-white/15 text-white font-bold' : 'hover:bg-white/10 text-white/80' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                    </svg>
                    <span>Mensagens</span>
                </a>

                <!-- Tickets Link -->
                <a href="{{ route('admin.tickets') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all text-sm font-semibold {{ Route::currentRouteName() === 'admin.tickets' ? 'bg-white/15 text-white font-bold' : 'hover:bg-white/10 text-white/80' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-3.75-6H4.5m15 0h-3M3.75 6h16.5a1.5 1.5 0 0 1 1.5 1.5v9a1.5 1.5 0 0 1-1.5 1.5H3.75A1.5 1.5 0 0 1 2.25 16.5v-9A1.5 1.5 0 0 1 3.75 6Z" />
                    </svg>
                    <span>Tickets</span>
                </a>

                <!-- Histórico completo Link -->
                <a href="{{ route('admin.historico') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all text-sm font-semibold {{ Route::currentRouteName() === 'admin.historico' ? 'bg-white/15 text-white font-bold' : 'hover:bg-white/10 text-white/80' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-9-9c2.53 0 4.757 1.048 6.34 2.74L21 8.25" />
                    </svg>
                    <span>Histórico</span>
                </a>

                <!-- Gestão de Atendimento Link -->
                <a href="{{ route('admin.gestao-atendimento') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all text-sm font-semibold {{ Route::currentRouteName() === 'admin.gestao-atendimento' ? 'bg-white/15 text-white font-bold' : 'hover:bg-white/10 text-white/80' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                    <span>Gestão de Atendimento</span>
                </a>

                <!-- Presets Globais Link -->
                <a href="{{ route('admin.presets-globais') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all text-sm font-semibold {{ Route::currentRouteName() === 'admin.presets-globais' ? 'bg-white/15 text-white font-bold' : 'hover:bg-white/10 text-white/80' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                    </svg>
                    <span>Presets globais</span>
                </a>
            @elseif(Auth::check() && Auth::user()->role === 'atendente')
                <!-- Home Link -->
                <a href="{{ route('atendente.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all text-sm font-semibold {{ in_array(Route::currentRouteName(), ['dashboard', 'atendente.dashboard']) ? 'bg-white/15 text-white' : 'hover:bg-white/10 text-white/80' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span>Home</span>
                </a>

                <!-- Mensagens Link -->
                <a href="{{ route('atendente.chat.index') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all text-sm font-semibold {{ str_contains(Route::currentRouteName(), 'chat') ? 'bg-white/15 text-white' : 'hover:bg-white/10 text-white/80' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                    </svg>
                    <span>Mensagens</span>
                </a>

                <!-- Tickets Link (Exclusivo atendentes) -->
                <a href="{{ route('atendente.tickets') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all text-sm font-semibold {{ Route::currentRouteName() === 'atendente.tickets' ? 'bg-white/15 text-white' : 'hover:bg-white/10 text-white/80' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-3.75-6H4.5m15 0h-3M3.75 6h16.5a1.5 1.5 0 0 1 1.5 1.5v9a1.5 1.5 0 0 1-1.5 1.5H3.75A1.5 1.5 0 0 1 2.25 16.5v-9A1.5 1.5 0 0 1 3.75 6Z" />
                    </svg>
                    <span>Tickets</span>
                </a>

                <!-- Histórico completo Link (Exclusivo atendentes) -->
                <a href="{{ route('atendente.historico') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all text-sm font-semibold {{ Route::currentRouteName() === 'atendente.historico' ? 'bg-white/15 text-white' : 'hover:bg-white/10 text-white/80' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    <span>Histórico completo</span>
                </a>
            @else
                <!-- Home Link -->
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all text-sm font-semibold {{ in_array(Route::currentRouteName(), ['dashboard', 'atendente.dashboard']) ? 'bg-white/15 text-white' : 'hover:bg-white/10 text-white/80' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span>Home</span>
                </a>

                <!-- Mensagens Link -->
                <a href="{{ route('chat.index') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all text-sm font-semibold {{ str_contains(Route::currentRouteName(), 'chat') ? 'bg-white/15 text-white' : 'hover:bg-white/10 text-white/80' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                    </svg>
                    <span>Mensagens</span>
                </a>
            @endif
        </div>

        <!-- Sistema Section -->
        <div class="flex flex-col gap-1.5 mt-2">
            <span class="text-[11px] font-bold text-white/50 uppercase tracking-wider pl-3 mb-1">Sistema</span>

            @if(Auth::check() && Auth::user()->role === 'admin')
                <!-- Gestão de Usuários (Admin) -->
                <a href="{{ route('users.index') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all text-sm font-semibold {{ Route::currentRouteName() === 'users.index' ? 'bg-white/15 text-white font-bold' : 'hover:bg-white/10 text-white/80' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.386 11.386 0 0 1 8.625 21c-2.39 0-4.608-.737-6.446-2A4.125 4.125 0 0 1 9.75 16.5c.802 0 1.554.23 2.19.625m2.06 1.003a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                    <span>Gestão de Usuários</span>
                </a>

                <!-- Configurações Link -->
                <a href="{{ route('users.index') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all text-sm font-semibold hover:bg-white/10 text-white/80">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.43l-1.003.828c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.43l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 0 1 0-.255c.007-.378-.138-.75-.43-.991l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <span>Configurações</span>
                </a>

                <!-- Log de Atividades (Admin) -->
                <a href="{{ route('admin.log-atividades') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all text-sm font-semibold {{ Route::currentRouteName() === 'admin.log-atividades' ? 'bg-white/15 text-white font-bold' : 'hover:bg-white/10 text-white/80' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                    <span>Log de Atividades</span>
                </a>
            @else
                <!-- Configurações Link -->
                <a href="{{ route('users.index') }}"
                    class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-all text-sm font-semibold {{ str_starts_with(Route::currentRouteName(), 'users') ? 'bg-white/15 text-white' : 'hover:bg-white/10 text-white/80' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.43l-1.003.828c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.43l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 0 1 0-.255c.007-.378-.138-.75-.43-.991l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <span>Configurações</span>
                </a>
            @endif

            <!-- Logout Link -->
            <form action="{{ route('logout') }}" method="POST" class="m-0" id="sidebar-logout-form">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-3 rounded-2xl hover:bg-white/10 text-white/80 transition-all text-sm font-semibold cursor-pointer text-left">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                    </svg>
                    <span>Sair</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Wrapper (Shifted right by Sidebar width) -->
    <div class="content-wrapper">

        <!-- Header / Navbar Principal -->
        <header
            class="h-16 px-6 bg-white border-b border-gray-100 flex items-center justify-between sticky top-0 z-40 select-none shadow-sm">

            <!-- Lado Esquerdo: Sidebar Toggle & Logos -->
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()"
                    class="p-2 text-gray-600 hover:bg-gray-50 rounded-lg focus:outline-none transition-colors cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <div class="flex items-center gap-3">
                    <img src="/img/Logo Prisma.png" alt="Logo Prisma" class="h-7 object-contain">
                    <div class="w-px h-5 bg-gray-200"></div>
                    <img src="/img/Logo Claro.png" alt="Logo Claro" class="h-7 object-contain">
                </div>
            </div>

            <!-- Centro: Barra de Pesquisa e Filtros -->
            <div class="hidden md:flex items-center gap-4 flex-1 max-w-xl mx-8">
                <!-- Campo de Pesquisa -->
                <div class="relative w-full">
                    <input type="text" placeholder="Pesquisar"
                        class="w-full pl-4 pr-10 py-1.5 bg-[#F8F9FA] border border-gray-200 rounded-full text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#DA291C] focus:bg-white focus:ring-1 focus:ring-[#DA291C] transition-all">
                    <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21-21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </span>
                </div>
                <!-- Filtros -->
                <button
                    class="flex items-center gap-1.5 px-4 py-1.5 border border-gray-200 rounded-full text-xs font-semibold text-gray-600 hover:bg-gray-50 focus:outline-none transition-all cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                    </svg>
                    <span>Filtros</span>
                </button>
            </div>

            <!-- Lado Direito: Ações & Perfil -->
            <div class="flex items-center gap-4">
                <!-- Grid Button -->
                <button class="p-2 text-gray-500 hover:bg-gray-50 rounded-full focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                    </svg>
                </button>

                <!-- Notifications -->
                <div class="relative" id="notifications-dropdown-container">
                    <button onclick="toggleNotificationsMenu(event)"
                        class="relative p-2 text-gray-500 hover:bg-gray-50 rounded-full focus:outline-none cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        <span id="notification-badge"
                            class="absolute top-1 right-1 min-w-[16px] h-4 px-1 bg-[#DA291C] text-white text-[8px] font-bold rounded-full hidden items-center justify-center border border-white whitespace-nowrap">0</span>
                    </button>

                    <!-- Dropdown de Notificações -->
                    <div id="notifications-menu"
                        class="absolute right-0 top-12 w-80 bg-white border border-gray-100 rounded-xl shadow-lg py-2 hidden z-50 overflow-hidden flex-col max-h-[400px]">
                        <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-800">Notificações</span>
                            <button onclick="markAllNotificationsAsRead(event)"
                                class="text-[10px] font-semibold text-[#DA291C] hover:underline cursor-pointer">Marcar
                                todas como lidas</button>
                        </div>
                        <div id="notifications-list"
                            class="overflow-y-auto divide-y divide-gray-50 flex-1 max-h-[300px]">
                            <div class="px-4 py-6 text-center text-xs text-gray-400 font-medium">Carregando...</div>
                        </div>
                    </div>
                </div>

                <!-- Perfil Dropdown -->
                <div class="relative flex items-center gap-3 pl-3 border-l border-gray-100"
                    id="profile-dropdown-container">
                    <button onclick="toggleProfileMenu()"
                        class="flex items-center gap-2 focus:outline-none cursor-pointer group">
                        <div
                            class="relative w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center border border-gray-200">
                            @php
                                $userName = Auth::user()->name ?? 'Usuário';
                                if (Auth::check() && Auth::user()->role === 'admin') {
                                    $userBg = '404040';
                                    $userColor = 'ffffff';
                                } elseif (Auth::check() && Auth::user()->role === 'atendente') {
                                    $userBg = 'EAA8A8';
                                    $userColor = '86131E';
                                } else {
                                    $userBg = 'D1E7DD';
                                    $userColor = '0F5132';
                                }
                            @endphp
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($userName) }}&background={{ $userBg }}&color={{ $userColor }}&bold=true&rounded=true"
                                alt="Avatar" class="w-full h-full rounded-full object-cover">
                            <span
                                class="absolute -bottom-1 left-1/2 -translate-x-1/2 bg-[#DA291C] text-[7px] font-bold text-white px-1 rounded uppercase tracking-wider scale-90 border border-white whitespace-nowrap text-center">
                                @if(Auth::check() && Auth::user()->role === 'admin')
                                    Admin
                                @elseif(Auth::check() && Auth::user()->role === 'atendente')
                                    Atendente
                                @else
                                    Cliente
                                @endif
                            </span>
                        </div>

                        <div class="hidden md:flex flex-col text-left">
                            <span
                                class="text-xs font-semibold text-gray-800 leading-tight group-hover:text-[#DA291C] transition-colors">{{ Auth::user()->name ?? 'Usuário' }}</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                            stroke="currentColor" class="w-3 h-3 text-gray-500 transition-transform duration-200"
                            id="profile-chevron">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <div id="profile-menu"
                        class="absolute right-0 top-12 w-48 bg-white border border-gray-100 rounded-xl shadow-lg py-1 hidden z-50">
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-[#DA291C] transition-colors flex items-center gap-2 cursor-pointer font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                                </svg>
                                <span>Sair</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Conteúdo Principal -->
        <main class="flex-1 flex flex-col p-6 md:p-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer
            class="bg-[#A01724] py-4 px-6 text-white text-xs md:text-sm flex flex-col md:flex-row items-center justify-center gap-x-12 gap-y-2 border-t border-[#DA291C]/10 select-none text-center">
            <div class="flex items-center gap-2">
                <span>© 2026 - CLARO PRISMA - SISTEMA DE GESTÃO INTEGRADA</span>
            </div>
            <div class="flex items-center gap-6 font-semibold">
                <a href="#" class="hover:text-gray-200 transition-colors">TERMOS DE USO</a>
                <span class="text-white/20">|</span>
                <a href="#" class="hover:text-gray-200 transition-colors">POLÍTICA DE PRIVACIDADE</a>
            </div>
        </footer>

    </div>

    <!-- Modal de Suporte Online -->
    <div id="support-modal"
        class="fixed inset-0 z-[100] hidden bg-black/60 backdrop-blur-sm items-center justify-center p-4 transition-all duration-300 select-none">
        <div class="bg-white w-full max-w-[666px] rounded-[21px] shadow-2xl relative flex flex-col gap-[21px] transform scale-95 opacity-0 transition-all duration-300"
            style="padding: 42px 25px 24px 25px;" id="support-modal-content">
            <!-- Botão Fechar (x) -->
            <button onclick="closeSupportModal()"
                class="absolute top-4 right-5 text-gray-400 hover:text-gray-600 transition-colors focus:outline-none cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- STEP 1: Seleção de Assunto -->
            <div id="support-step-1" class="flex flex-col gap-[21px]">
                <!-- Cabeçalho -->
                <div class="text-center space-y-2">
                    <h3 class="font-bold tracking-tight select-none inline-block"
                        style="font-family: 'AMX', sans-serif; font-size: 31.95px; line-height: 120%; font-weight: 600; background: linear-gradient(89.24deg, #A01724 0%, #DA291C 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; color: transparent;">
                        Bem vindo ao Claro Prisma
                    </h3>
                    <p class="text-xs md:text-sm text-gray-500 font-semibold max-w-md mx-auto leading-relaxed">
                        Selecione abaixo o assunto da sua mensagem para que possamos te atender melhor:
                    </p>
                </div>

                <!-- Opções (Assuntos) -->
                <div class="flex flex-col gap-3 my-2">
                    @php
                        $supportOptions = [
                            'Promoções',
                            'Sistemas e acesso',
                            'Vigência de ofertas',
                            'Assuntos técnicos',
                            'Outros assuntos'
                        ];
                    @endphp
                    @foreach($supportOptions as $option)
                        <button type="button" onclick="selectSupportOption(this, '{{ $option }}')"
                            class="w-full text-center py-4 px-6 border border-gray-200 hover:border-[#DA291C] hover:bg-red-50/20 rounded-xl transition-all cursor-pointer focus:outline-none support-option-btn active:scale-[0.99] font-bold text-[22px] leading-[1.3] text-[#404040]"
                            style="font-family: 'AMX', sans-serif;">
                            {{ $option }}
                        </button>
                    @endforeach
                </div>

                <!-- Botões de Ação -->
                <div class="flex flex-col gap-3 items-center">
                    <button type="button" id="btn-advance-support" disabled onclick="goToStep(2)"
                        class="w-full py-4 bg-[#EAA8A8] text-white text-base font-bold rounded-xl shadow-md cursor-not-allowed transition-all text-center select-none active:scale-[0.99] focus:outline-none"
                        style="font-family: 'AMX', sans-serif;">
                        Avançar
                    </button>
                    <button type="button" onclick="closeSupportModal()"
                        class="text-sm font-bold text-gray-500 hover:text-gray-800 transition-colors py-1 cursor-pointer focus:outline-none">
                        Cancelar
                    </button>
                </div>
            </div>

            <!-- STEP 2: Informações complementares -->
            <div id="support-step-2" class="hidden flex-col gap-[21px]">
                <!-- Cabeçalho -->
                <div class="text-center space-y-2">
                    <h3 class="font-bold tracking-tight select-none inline-block"
                        style="font-family: 'AMX', sans-serif; font-size: 31.95px; line-height: 120%; font-weight: 700; background: linear-gradient(89.24deg, #A01724 0%, #DA291C 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; color: transparent;">
                        Informações complementares
                    </h3>
                    <p class="text-xs md:text-sm text-gray-500 font-semibold max-w-md mx-auto leading-relaxed">
                        Agora conte um pouco mais sobre a sua dúvida para direcionarmos ao atendente certo.
                    </p>
                </div>

                <!-- Formulário -->
                <form id="support-step-2-form" class="flex flex-col gap-4 text-left m-0"
                    action="{{ route('solicitations.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Categoria Oculta -->
                    <input type="hidden" name="category" id="selected-category-input" value="">

                    <!-- Título da mensagem -->
                    <div>
                        <input type="text" name="title" id="support-title-input" placeholder="Título da mensagem"
                            required oninput="validateStep2Form()"
                            class="w-full px-4 py-3.5 border border-gray-200 rounded-[12px] text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#DA291C] focus:ring-1 focus:ring-[#DA291C] transition-all font-medium"
                            style="font-family: 'AMX', sans-serif;">
                    </div>

                    <!-- Texto da mensagem -->
                    <div>
                        <textarea name="description" id="support-description-input" placeholder="Texto da mensagem"
                            rows="4" required oninput="validateStep2Form()"
                            class="w-full px-4 py-3.5 border border-gray-200 rounded-[12px] text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#DA291C] focus:ring-1 focus:ring-[#DA291C] transition-all font-medium resize-none"
                            style="font-family: 'AMX', sans-serif;"></textarea>
                    </div>

                    <!-- Upload do arquivo -->
                    <div>
                        <label for="support-file-input"
                            class="w-full flex flex-col items-center justify-center py-4 px-6 border border-dashed border-gray-300 hover:border-[#DA291C] rounded-[12px] bg-gray-50 hover:bg-red-50/5 cursor-pointer transition-all select-none"
                            style="border-style: dashed; border-width: 2px;">
                            <span class="text-xs md:text-sm font-semibold text-gray-500" id="file-label-text">
                                Arraste ou selecione um arquivo <span
                                    class="italic font-normal text-gray-400">(opcional)</span>
                            </span>
                            <input type="file" name="files[]" id="support-file-input" class="hidden" multiple
                                onchange="handleSupportFileSelected(this)">
                        </label>
                        <!-- Container para exibir os arquivos anexados -->
                        <div id="attached-files-container" class="hidden"></div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex flex-col gap-3 items-center mt-2">
                        <button type="submit" id="btn-submit-support" disabled
                            class="w-full py-4 bg-[#EAA8A8] text-white text-base font-bold rounded-xl shadow-md cursor-not-allowed transition-all text-center select-none active:scale-[0.99] focus:outline-none"
                            style="font-family: 'AMX', sans-serif;">
                            Começar conversa
                        </button>
                        <button type="button" onclick="goToStep(1)"
                            class="text-sm font-bold text-gray-500 hover:text-gray-800 transition-colors py-1 cursor-pointer focus:outline-none">
                            Voltar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Sidebar Toggle with Persistence
        function toggleSidebar() {
            const collapsed = document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebar-collapsed', collapsed ? 'true' : 'false');
        }

        // Dropdown de perfil
        function toggleProfileMenu() {
            const menu = document.getElementById('profile-menu');
            const chevron = document.getElementById('profile-chevron');

            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                chevron.classList.add('rotate-180');
            } else {
                menu.classList.add('hidden');
                chevron.classList.remove('rotate-180');
            }
        }

        // Fechar menus se clicar fora
        window.addEventListener('click', function (e) {
            // Perfil
            const profileDropdown = document.getElementById('profile-dropdown-container');
            const profileMenu = document.getElementById('profile-menu');
            const profileChevron = document.getElementById('profile-chevron');
            if (profileDropdown && !profileDropdown.contains(e.target)) {
                if (profileMenu && !profileMenu.classList.contains('hidden')) {
                    profileMenu.classList.add('hidden');
                    profileChevron.classList.remove('rotate-180');
                }
            }

            // Notificações
            const notificationsDropdown = document.getElementById('notifications-dropdown-container');
            const notificationsMenu = document.getElementById('notifications-menu');
            if (notificationsDropdown && !notificationsDropdown.contains(e.target)) {
                if (notificationsMenu && !notificationsMenu.classList.contains('hidden')) {
                    notificationsMenu.classList.remove('flex');
                    notificationsMenu.classList.add('hidden');
                }
            }
        });

        // Dropdown de notificações
        function toggleNotificationsMenu(e) {
            if (e) e.stopPropagation();
            const menu = document.getElementById('notifications-menu');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                menu.classList.add('flex');
                loadNotifications();
            } else {
                menu.classList.remove('flex');
                menu.classList.add('hidden');
            }
        }

        function loadNotifications() {
            fetch('{{ route("notifications.index") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateNotificationBadge(data.count);
                        const list = document.getElementById('notifications-list');
                        list.innerHTML = '';

                        if (data.notifications.length === 0) {
                            list.innerHTML = '<div class="px-4 py-8 text-center text-xs text-gray-400 font-medium">Nenhuma notificação nova</div>';
                            return;
                        }

                        data.notifications.forEach(notif => {
                            const item = document.createElement('div');
                            item.className = "px-4 py-3 hover:bg-gray-50 transition-colors cursor-pointer text-left";

                            // Determina o redirecionamento baseado no papel
                            const role = '{{ Auth::check() ? Auth::user()->role : "user" }}';
                            const baseUrl = role === 'atendente' ? '/atendente/messages/' : '/messages/';
                            const redirectUrl = baseUrl + notif.solicitation_id;

                            item.innerHTML = `
                                    <div onclick="readAndRedirect('${notif.id}', '${redirectUrl}', event)">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs font-bold text-gray-800">${notif.title}</span>
                                            <span class="text-[9px] text-gray-400 font-semibold">${notif.time}</span>
                                        </div>
                                        <p class="text-[11px] text-gray-500 font-medium leading-relaxed">${notif.message}</p>
                                    </div>
                                `;
                            list.appendChild(item);
                        });
                    }
                })
                .catch(err => console.error('Erro ao carregar notificações:', err));
        }

        function updateNotificationBadge(count) {
            const badge = document.getElementById('notification-badge');
            if (count > 0) {
                badge.innerText = count;
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
        }

        function readAndRedirect(id, url, e) {
            if (e) e.stopPropagation();

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            })
                .then(() => {
                    window.location.href = url;
                })
                .catch(() => {
                    window.location.href = url;
                });
        }

        function markAllNotificationsAsRead(e) {
            if (e) e.stopPropagation();

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch('{{ route("notifications.readAll") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateNotificationBadge(0);
                        const list = document.getElementById('notifications-list');
                        list.innerHTML = '<div class="px-4 py-8 text-center text-xs text-gray-400 font-medium">Nenhuma notificação nova</div>';
                    }
                })
                .catch(err => console.error(err));
        }

        // Loop para atualizar badge em background a cada 15 segundos
        setInterval(() => {
            fetch('{{ route("notifications.index") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateNotificationBadge(data.count);
                    }
                });
        }, 15000);

        // Carrega ao iniciar
        document.addEventListener('DOMContentLoaded', () => {
            fetch('{{ route("notifications.index") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateNotificationBadge(data.count);
                    }
                });
        });

        // Modal de Suporte Online State Management
        let selectedSupportOption = null;

        function openSupportModal() {
            const modal = document.getElementById('support-modal');
            const content = document.getElementById('support-modal-content');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeSupportModal() {
            const modal = document.getElementById('support-modal');
            const content = document.getElementById('support-modal-content');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                resetSupportModal();
            }, 300);
        }

        function selectSupportOption(element, option) {
            // Limpa seleção anterior
            const buttons = document.querySelectorAll('.support-option-btn');
            buttons.forEach(btn => {
                btn.classList.remove('border-[#DA291C]', 'bg-red-50/10', 'text-[#DA291C]');
                btn.classList.add('border-gray-200', 'text-[#404040]');
            });

            // Destaca a opção selecionada
            element.classList.remove('border-gray-200', 'text-[#404040]');
            element.classList.add('border-[#DA291C]', 'bg-red-50/10', 'text-[#DA291C]');

            selectedSupportOption = option;

            // Ativa o botão avançar
            const btnAdvance = document.getElementById('btn-advance-support');
            btnAdvance.disabled = false;
            btnAdvance.classList.remove('bg-[#EAA8A8]', 'cursor-not-allowed');
            btnAdvance.classList.add('bg-[#DA291C]', 'hover:bg-[#B31D14]', 'cursor-pointer');
        }

        let attachedFiles = [];

        function handleSupportFileSelected(input) {
            if (input.files) {
                for (let i = 0; i < input.files.length; i++) {
                    attachedFiles.push(input.files[i]);
                }
            }
            renderAttachedFiles();
            updateFileInputFiles();
        }

        function removeAttachedFile(index) {
            attachedFiles.splice(index, 1);
            renderAttachedFiles();
            updateFileInputFiles();
        }

        function updateFileInputFiles() {
            const input = document.getElementById('support-file-input');
            const dt = new DataTransfer();
            attachedFiles.forEach(file => {
                dt.items.add(file);
            });
            input.files = dt.files;
        }

        function renderAttachedFiles() {
            const container = document.getElementById('attached-files-container');
            container.innerHTML = '';

            if (attachedFiles.length === 0) {
                container.classList.add('hidden');
                return;
            }

            container.classList.remove('hidden');
            container.className = "flex flex-wrap gap-2 mt-3 justify-start";

            attachedFiles.forEach((file, index) => {
                const tag = document.createElement('div');
                tag.className = "flex items-center gap-1.5 px-3 py-1.5 border border-gray-200 rounded-lg bg-white text-xs font-bold text-gray-750 shadow-sm transition-all";
                tag.style.fontFamily = "'AMX', sans-serif";

                tag.innerHTML = `
                        <svg class="w-3.5 h-3.5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5-3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span class="truncate max-w-[150px] text-gray-650">${file.name}</span>
                        <button type="button" onclick="removeAttachedFile(${index})" class="text-gray-400 hover:text-red-500 transition-colors focus:outline-none ml-1 cursor-pointer">
                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    `;
                container.appendChild(tag);
            });
        }

        function resetSupportModal() {
            selectedSupportOption = null;
            const buttons = document.querySelectorAll('.support-option-btn');
            buttons.forEach(btn => {
                btn.classList.remove('border-[#DA291C]', 'bg-red-50/10', 'text-[#DA291C]');
                btn.classList.add('border-gray-200', 'text-[#404040]');
            });

            const btnAdvance = document.getElementById('btn-advance-support');
            btnAdvance.disabled = true;
            btnAdvance.classList.remove('bg-[#DA291C]', 'hover:bg-[#B31D14]', 'cursor-pointer');
            btnAdvance.classList.add('bg-[#EAA8A8]', 'cursor-not-allowed');

            // Reseta inputs do Step 2
            document.getElementById('support-title-input').value = '';
            document.getElementById('support-description-input').value = '';
            document.getElementById('support-file-input').value = '';
            document.getElementById('selected-category-input').value = '';

            attachedFiles = [];
            renderAttachedFiles();

            // Retorna ao Step 1
            goToStep(1);
        }

        function goToStep(step) {
            const step1 = document.getElementById('support-step-1');
            const step2 = document.getElementById('support-step-2');
            if (step === 1) {
                step2.classList.remove('flex');
                step2.classList.add('hidden');
                step1.classList.remove('hidden');
                step1.classList.add('flex');
            } else if (step === 2) {
                step1.classList.remove('flex');
                step1.classList.add('hidden');
                step2.classList.remove('hidden');
                step2.classList.add('flex');
                // Define a categoria no input hidden
                document.getElementById('selected-category-input').value = selectedSupportOption;
                validateStep2Form();
            }
        }

        function validateStep2Form() {
            const title = document.getElementById('support-title-input').value.trim();
            const desc = document.getElementById('support-description-input').value.trim();
            const btnSubmit = document.getElementById('btn-submit-support');
            if (title && desc) {
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('bg-[#EAA8A8]', 'cursor-not-allowed');
                btnSubmit.classList.add('bg-[#DA291C]', 'hover:bg-[#B31D14]', 'cursor-pointer');
            } else {
                btnSubmit.disabled = true;
                btnSubmit.classList.remove('bg-[#DA291C]', 'hover:bg-[#B31D14]', 'cursor-pointer');
                btnSubmit.classList.add('bg-[#EAA8A8]', 'cursor-not-allowed');
            }
        }
    </script>
    @yield('scripts')
</body>

</html>