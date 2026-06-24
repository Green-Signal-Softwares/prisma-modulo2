<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'PRISMA Claro')</title>

        <!-- Google Fonts Fallback -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

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

            .bg-claro-prisma {
                background-image: 
                    linear-gradient(135deg, rgba(160, 23, 36, 0.94) 0%, rgba(218, 41, 28, 0.94) 100%), 
                    url('/img/corporativa.png');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
            }

            .figma-label {
                font-family: 'AMX', 'Instrument Sans', sans-serif;
                font-size: 20px;
                line-height: 100%;
                letter-spacing: 0%;
                font-weight: 500;
                color: #404040;
            }
        </style>
    </head>
    <body class="bg-[#A01724] bg-claro-prisma min-h-screen flex flex-col justify-between antialiased">
        
        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center p-4">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-[#333333] py-4 px-6 text-white/70 text-xs md:text-sm flex flex-col md:flex-row items-center justify-between gap-3 border-t border-white/5">
            <div>
                © 2026 - CLARO PRISMA - SISTEMA DE GESTÃO INTEGRADA
            </div>
            <div class="flex items-center gap-6">
                <a href="#" class="hover:text-white transition-colors">TERMOS DE USO</a>
                <a href="#" class="hover:text-white transition-colors">POLÍTICA DE PRIVACIDADE</a>
            </div>
        </footer>

        @yield('scripts')
    </body>
</html>
