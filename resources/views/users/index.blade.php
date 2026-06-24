@extends('layouts.app')

@section('title', 'Gestão de Usuários - PRISMA Claro')

@section('content')
<!-- Breadcrumbs & Heading -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <div class="text-xs text-gray-500 mb-1">
            <span>Claro Prisma</span> &gt; <span class="font-medium text-gray-700">Gestão de Usuários</span>
        </div>
        <h1 class="text-3xl font-bold text-[#A01724]">Gestão de Usuários</h1>
    </div>
    <div>
        <a href="{{ route('users.create') }}" class="flex items-center gap-2 px-5 py-2.5 bg-[#DA291C] hover:bg-[#B31D14] text-white font-semibold rounded-xl text-sm transition-all shadow-md shadow-red-600/10 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>Novo Usuário</span>
        </a>
    </div>
</div>

<!-- Alert messages -->
@if (session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl text-sm font-medium flex items-center gap-2 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if (session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-[#DA291C] rounded-2xl text-sm font-medium flex items-center gap-2 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
            <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        <span>{{ session('error') }}</span>
    </div>
@endif

<!-- Users Card & Table -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/75 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <th class="py-4 px-6">Nome</th>
                    <th class="py-4 px-6">E-mail</th>
                    <th class="py-4 px-6">Criado Em</th>
                    <th class="py-4 px-6 text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm text-gray-700">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <!-- Nome com Avatar -->
                        <td class="py-4 px-6 font-semibold text-gray-950 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 text-[#DA291C] font-bold text-xs flex items-center justify-center border border-gray-200">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <span>{{ $user->name }}</span>
                            @if($user->id === auth()->id())
                                <span class="bg-gray-100 text-gray-600 text-[9px] font-bold px-1.5 py-0.5 rounded uppercase">Você</span>
                            @endif
                        </td>
                        <!-- E-mail -->
                        <td class="py-4 px-6 font-medium text-gray-600">
                            {{ $user->email }}
                        </td>
                        <!-- Criado Em -->
                        <td class="py-4 px-6 text-gray-500">
                            {{ $user->created_at->format('d/m/Y H:i') }}
                        </td>
                        <!-- Ações -->
                        <td class="py-4 px-6 text-right">
                            @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')" class="inline-block m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-semibold text-xs flex items-center gap-1 ml-auto cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        <span>Excluir</span>
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400 font-medium italic">Sem ações</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-gray-500 font-medium">
                            Nenhum usuário encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
