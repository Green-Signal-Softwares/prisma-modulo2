@extends('layouts.guest')

@section('title', 'Login - PRISMA Claro')

@section('content')
<div class="bg-white rounded-[32px] w-full max-w-[500px] p-8 md:p-10 shadow-2xl transition-all duration-300 hover:shadow-red-950/20">
    
    <!-- Logo Header -->
    <div class="flex items-center justify-center gap-6 mb-8">
        <img src="/img/Logo Prisma.png" alt="Logo Prisma" class="h-10 object-contain">
        <div class="w-px h-8 bg-gray-200"></div>
        <img src="/img/Logo Claro.png" alt="Logo Claro" class="h-10 object-contain">
    </div>

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-[#DA291C] rounded-[14px] text-sm">
            @foreach ($errors->all() as $error)
                <p class="font-medium">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Login Form -->
    <form action="{{ route('login') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- E-mail -->
        <div class="space-y-2">
            <label for="email" class="block figma-label">E-mail</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}"
                required 
                placeholder="Digite seu e-mail" 
                class="w-full px-4 py-3.5 border border-gray-300 rounded-[14px] text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#DA291C] focus:ring-1 focus:ring-[#DA291C] transition-all text-sm"
            >
        </div>

        <!-- Senha -->
        <div class="space-y-2">
            <label for="password" class="block figma-label">Senha</label>
            <div class="relative">
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    placeholder="Digite sua senha" 
                    class="w-full px-4 py-3.5 pr-12 border border-gray-300 rounded-[14px] text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#DA291C] focus:ring-1 focus:ring-[#DA291C] transition-all text-sm"
                >
                <!-- Show/Hide Password Icons -->
                <button 
                    type="button" 
                    onclick="togglePasswordVisibility()" 
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
                >
                    <!-- Eye Icon -->
                    <svg id="eye-show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <!-- Eye Crossed Icon (Hidden by default) -->
                    <svg id="eye-hide" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5 hidden">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.822 7.822 3 3m-3-3-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Lembrar-me & Esqueci a senha -->
        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2 cursor-pointer text-[#404040] select-none">
                <input 
                    type="checkbox" 
                    name="remember" 
                    class="rounded border-gray-300 text-[#DA291C] focus:ring-[#DA291C] h-4 w-4"
                >
                <span>Lembrar-me</span>
            </label>
            <a href="#" class="text-[#DA291C] font-semibold hover:underline">Esqueci a senha</a>
        </div>

        <!-- Confirm Button -->
        <button 
            type="submit" 
            class="w-full bg-[#DA291C] hover:bg-[#B31D14] text-white py-3.5 px-4 rounded-[12px] font-semibold text-base transition-all flex items-center justify-center gap-2 active:scale-[0.99] cursor-pointer shadow-lg shadow-red-600/10"
        >
            <span>Confirmar</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
            </svg>
        </button>
    </form>

    <!-- Register link -->
    <div class="text-center mt-6 text-sm text-gray-500">
        Ainda não possui conta? <a href="#" class="text-[#DA291C] font-semibold hover:underline">Cadastre-se</a>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // Toggle password visibility
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const eyeShow = document.getElementById('eye-show');
        const eyeHide = document.getElementById('eye-hide');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeShow.classList.add('hidden');
            eyeHide.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            eyeShow.classList.remove('hidden');
            eyeHide.classList.add('hidden');
        }
    }
</script>
@endsection
