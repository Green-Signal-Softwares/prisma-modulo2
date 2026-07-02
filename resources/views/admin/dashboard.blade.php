@extends('layouts.app')

@section('title', 'Admin Home — Claro Prisma')

@section('content')
<!-- Breadcrumbs -->
<div class="flex items-center gap-2 text-xs text-gray-500 mb-2 select-none">
    <span>Claro Prisma</span>
    <span>&gt;</span>
    <span class="text-gray-800 font-medium">Home</span>
</div>

<!-- Greeting Header -->
<h1 class="text-2xl font-bold text-[#DA291C] mb-6">Bom dia, {{ Auth::user()->name }}!</h1>

<!-- Banner Card -->
<div class="relative rounded-[32px] overflow-hidden min-h-[220px] md:min-h-[260px] bg-gradient-to-r from-black/80 via-black/40 to-transparent flex flex-col justify-center px-8 md:px-12 mb-8 shadow-md select-none">
    <!-- Background Image representing a desk with devices -->
    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1200&q=80" alt="Banner background" class="absolute inset-0 w-full h-full object-cover -z-10 mix-blend-overlay opacity-60">
    <div class="absolute inset-0 bg-gradient-to-r from-[#A01724]/20 to-black/30 -z-20"></div>
    
    <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-6">Seja bem-vindo!</h2>
    <div>
        <a href="{{ route('admin.tickets') }}" class="inline-flex items-center gap-2 bg-[#DA291C] hover:bg-[#B31F15] text-white font-bold py-3 px-6 rounded-full transition-all hover:scale-[1.02] active:scale-[0.98] shadow-lg text-sm select-none cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672 13.684 16.6m0 0-2.51 2.225.569-9.47 5.227 7.917-3.286-.672ZM12 2.25V4.5m5.303.197-1.591 1.591M21.75 12h-2.25m-.197 5.303-1.591-1.591M12 21.75V19.5m-5.303-.197 1.591-1.591M2.25 12h2.25m.197-5.303 1.591 1.591" />
            </svg>
            <span>Acesse as demandas de suporte</span>
        </a>
    </div>
</div>

<!-- Ticket List Section Header -->
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('admin.historico') }}" class="flex items-center gap-2 text-lg font-bold text-gray-800 hover:text-[#DA291C] transition-colors group">
        <span>Ver histórico completo</span>
        <span class="w-6 h-6 rounded-full bg-[#DA291C] text-white flex items-center justify-center text-xs transition-transform group-hover:translate-x-1 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3 h-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
        </span>
    </a>
    
    <a href="{{ route('admin.chat.index') }}" class="inline-flex items-center gap-2 bg-[#DA291C] hover:bg-[#B31F15] text-white font-bold py-2.5 px-5 rounded-2xl transition-all shadow-md text-sm select-none cursor-pointer">
        <span>Central de mensagens</span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
        </svg>
    </a>
</div>

<!-- Ticket cards list -->
<div class="flex flex-col gap-6">
    @forelse($solicitations as $solicitation)
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 flex flex-col gap-4 relative hover:shadow-md transition-shadow">
            
            <!-- Badges and Status -->
            <div class="flex flex-wrap items-center gap-3">
                @if(in_array($solicitation->status, ['resolvida', 'respondida']))
                    <!-- Respondida (Gray) -->
                    <span class="inline-flex items-center gap-1.5 bg-[#8E9093]/15 text-[#4D4E50] text-xs font-bold px-3 py-1.5 rounded-full select-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                        </svg>
                        Respondida
                    </span>
                @elseif(in_array($solicitation->status, ['em_replica', 'em_atendimento']))
                    <!-- Em réplica (Orange) -->
                    <span class="inline-flex items-center gap-1.5 bg-[#E27D18]/15 text-[#E27D18] text-xs font-bold px-3 py-1.5 rounded-full select-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7C4.547 9.547 4.5 10.768 4.5 12s.047 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.092-1.209.138-2.43.138-3.662Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 10.5 12 7.5m0 0 3 3m-3-3v12" />
                        </svg>
                        Em réplica
                    </span>
                @else
                    <!-- Nova / Aberta (Blue) -->
                    <span class="inline-flex items-center gap-1.5 bg-[#007BFF]/15 text-[#007BFF] text-xs font-bold px-3 py-1.5 rounded-full select-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        Nova
                    </span>
                @endif
            </div>

            <!-- Title -->
            <h3 class="text-xl font-bold text-gray-800 pr-12">{{ $solicitation->title }}</h3>

            <!-- Metadata info row -->
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-xs text-gray-500 select-none">
                <!-- Date -->
                <div class="flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-[#DA291C]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                    <span>{{ $solicitation->created_at->format('d/m/Y') }}</span>
                </div>
                <!-- Time -->
                <div class="flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-[#DA291C]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-9-9c2.53 0 4.757 1.048 6.34 2.74L21 8.25" />
                    </svg>
                    <span>{{ $solicitation->created_at->format('H:i:s') }}</span>
                </div>
                <!-- ID / Ticket Number -->
                <div class="flex items-center gap-1.5">
                    <span class="bg-[#DA291C] text-white text-[9px] font-extrabold px-1 py-0.5 rounded uppercase scale-90">ID</span>
                    <span class="font-semibold text-gray-700">{{ $solicitation->ticket_number }}</span>
                </div>
            </div>

            <!-- Description/Snippet -->
            <p class="text-sm text-gray-600 leading-relaxed font-light mt-1">
                {{ Str::limit($solicitation->description, 280) }}
            </p>

            <!-- Bottom Action button -->
            <div class="mt-2 flex">
                <a 
                    href="{{ route('admin.tickets.show', $solicitation->id) }}" 
                    class="inline-flex items-center justify-center bg-white hover:bg-gray-50 text-gray-800 font-bold py-2.5 px-6 rounded-xl border border-gray-200 transition-colors text-sm shadow-sm select-none cursor-pointer"
                >
                    Ver chat
                </a>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-3xl p-12 text-center shadow-sm border border-gray-100 select-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-300 mx-auto mb-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-.621-.504-1.125-1.125-1.125H9.75M8.25 21h8.25" />
            </svg>
            <p class="text-gray-500 font-semibold">Nenhuma demanda encontrada no momento.</p>
        </div>
    @endforelse
</div>
@endsection
