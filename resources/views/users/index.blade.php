@extends('layouts.app')

@section('title', 'Gestão de Usuários — Claro Prisma')

@section('content')
<!-- Breadcrumbs -->
<div class="flex items-center gap-1.5 text-xs text-gray-500 mb-1 select-none font-medium">
    <span class="text-gray-400">Claro Prisma</span>
    <span class="text-gray-300 font-normal">&gt;</span>
    <span class="text-gray-400">Gestão de Usuários</span>
</div>

<!-- Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-[#A01724]">Gestão de Usuários</h1>
    <p class="text-xs text-gray-400 mt-0.5">Texto descritivo</p>
</div>

<!-- Perfis de Acesso Section -->
<div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 mb-6">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
        <div class="flex items-center gap-4">
            <h2 class="text-lg font-bold text-[#A01724]">Perfis de acesso</h2>
            <div class="flex items-center gap-1.5 flex-wrap">
                <button onclick="openProfileModal()" class="bg-[#404040] hover:bg-[#505050] text-white font-semibold py-1.5 px-3.5 rounded-full text-xs transition-all flex items-center gap-1 cursor-pointer">
                    Adicionar novo <span class="text-sm font-bold leading-none">+</span>
                </button>
                <button id="toggleEditBtn" onclick="toggleEditMode()" class="bg-[#A0A0A0] hover:bg-[#909090] text-white font-semibold py-1.5 px-3.5 rounded-full text-xs transition-all flex items-center gap-1 cursor-pointer">
                    Habilitar edição <span class="text-[10px] leading-none">🔒</span>
                </button>
                <button onclick="downloadProfilesCSV()" class="bg-[#A01724] hover:bg-[#86131E] text-white font-bold py-1.5 px-3.5 rounded-full text-xs transition-all flex items-center gap-1 cursor-pointer">
                    Download CSV <span class="text-xs leading-none">📥</span>
                </button>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto border border-gray-200 rounded-2xl shadow-sm">
        <table class="w-full text-left border-collapse" id="profilesTable">
            <thead>
                <tr class="text-xs font-bold text-white uppercase tracking-wider">
                    <th class="py-2.5 px-3 bg-[#86131E] border-r border-white/20 border-b border-gray-200 text-left">PERFIL ↑</th>
                    <th class="py-2.5 px-3 bg-[#86131E] border-r border-white/20 border-b border-gray-200 text-center">NÍVEL N1 ↑</th>
                    <th class="py-2.5 px-3 bg-[#86131E] border-r border-white/20 border-b border-gray-200 text-center">NÍVEL N2 ↑</th>
                    <th class="py-2.5 px-3 bg-[#86131E] border-r border-white/20 border-b border-gray-200 text-center">● FILA ↑</th>
                    <th class="py-2.5 px-3 bg-[#E87722] border-r border-white/20 border-b border-gray-200 text-center">PROMOÇÕES ↑</th>
                    <th class="py-2.5 px-3 bg-[#E87722] border-r border-white/20 border-b border-gray-200 text-center">SISTEMAS E ACESSO ↑</th>
                    <th class="py-2.5 px-3 bg-[#E87722] border-r border-white/20 border-b border-gray-200 text-center">VIGÊNCIA DE OFERTA ↑</th>
                    <th class="py-2.5 px-3 bg-[#86131E] border-b border-gray-200 text-center edit-actions hidden">Ações</th>
                </tr>
            </thead>
            <tbody class="text-xs">
                @forelse($profiles as $profile)
                <tr class="hover:bg-gray-50/50 transition-colors border-b border-gray-200" data-profile-id="{{ $profile->id }}">
                    <td class="py-3 px-3 font-bold text-gray-700 border-r border-gray-200 bg-white">{{ strtoupper($profile->name) }}</td>
                    <td class="py-3 px-3 text-center border-r border-gray-200 bg-white">
                        <label class="relative inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" onchange="toggleProfilePermission({{ $profile->id }}, 'nivel_n1', this.checked)" class="sr-only peer" {{ $profile->nivel_n1 ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                        </label>
                    </td>
                    <td class="py-3 px-3 text-center border-r border-gray-200 bg-white">
                        <label class="relative inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" onchange="toggleProfilePermission({{ $profile->id }}, 'nivel_n2', this.checked)" class="sr-only peer" {{ $profile->nivel_n2 ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                        </label>
                    </td>
                    <td class="py-3 px-3 text-center border-r border-gray-200 bg-white">
                        @if(strtoupper($profile->name) === 'ATENDENTE N1')
                            <span class="text-xs font-semibold text-gray-500 uppercase">Personalizado</span>
                        @else
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" onchange="toggleProfilePermission({{ $profile->id }}, 'fila', this.checked)" class="sr-only peer" {{ $profile->fila ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                        @endif
                    </td>
                    <td class="py-3 px-3 text-center border-r border-gray-200 bg-white">
                        <label class="relative inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" onchange="toggleFilaPermissionLocal({{ $profile->id }}, 'promocoes', this.checked)" class="sr-only peer" id="checkbox_{{ $profile->id }}_promocoes">
                            <div class="w-5 h-5 bg-white border border-gray-300 rounded transition-all peer-checked:bg-[#E87722] peer-checked:border-[#E87722] flex items-center justify-center after:content-['✓'] after:text-white after:text-xs after:font-bold after:hidden peer-checked:after:block"></div>
                        </label>
                    </td>
                    <td class="py-3 px-3 text-center border-r border-gray-200 bg-white">
                        <label class="relative inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" onchange="toggleFilaPermissionLocal({{ $profile->id }}, 'sistemas', this.checked)" class="sr-only peer" id="checkbox_{{ $profile->id }}_sistemas">
                            <div class="w-5 h-5 bg-white border border-gray-300 rounded transition-all peer-checked:bg-[#E87722] peer-checked:border-[#E87722] flex items-center justify-center after:content-['✓'] after:text-white after:text-xs after:font-bold after:hidden peer-checked:after:block"></div>
                        </label>
                    </td>
                    <td class="py-3 px-3 text-center border-r border-gray-200 bg-white">
                        <label class="relative inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" onchange="toggleFilaPermissionLocal({{ $profile->id }}, 'vigencia', this.checked)" class="sr-only peer" id="checkbox_{{ $profile->id }}_vigencia">
                            <div class="w-5 h-5 bg-white border border-gray-300 rounded transition-all peer-checked:bg-[#E87722] peer-checked:border-[#E87722] flex items-center justify-center after:content-['✓'] after:text-white after:text-xs after:font-bold after:hidden peer-checked:after:block"></div>
                        </label>
                    </td>
                    <td class="py-3 px-3 text-center edit-actions hidden bg-white">
                        <div class="inline-flex items-center gap-1.5 justify-center">
                            <button onclick="editProfile({{ json_encode($profile) }})" class="p-1 text-orange-500 hover:text-orange-700 cursor-pointer" title="Editar Perfil">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            <button onclick="deleteProfile({{ $profile->id }})" class="p-1 text-gray-400 hover:text-red-600 cursor-pointer" title="Excluir Perfil">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-8 text-center text-gray-400 text-xs">Nenhum perfil de acesso cadastrado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Usuários Section -->
<div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
        <div class="flex items-center gap-4">
            <h2 class="text-lg font-bold text-[#A01724]">Usuários</h2>
            <div class="flex items-center gap-1.5 flex-wrap">
                <button onclick="openUserModal()" class="bg-[#404040] hover:bg-[#505050] text-white font-semibold py-1.5 px-3.5 rounded-full text-xs transition-all flex items-center gap-1 cursor-pointer">
                    Adicionar novo <span class="text-sm font-bold leading-none">+</span>
                </button>
                <button type="button" class="bg-[#E87722] hover:bg-[#d66b1e] text-white font-semibold py-1.5 px-3.5 rounded-full text-xs transition-all flex items-center gap-1 cursor-pointer">
                    Personalizar <span class="text-xs leading-none">↗</span>
                </button>
                <button type="button" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold py-1.5 px-3.5 rounded-full text-xs transition-all flex items-center gap-1 cursor-pointer border border-gray-200">
                    Favoritos <span class="text-xs leading-none">☆</span>
                </button>
                <button onclick="downloadUsersCSV()" class="bg-[#A01724] hover:bg-[#86131E] text-white font-bold py-1.5 px-3.5 rounded-full text-xs transition-all flex items-center gap-1 cursor-pointer">
                    Download CSV <span class="text-xs leading-none">📥</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden Filter Form for Submitting -->
    <form id="usersFilterForm" method="GET" action="{{ route('users.index') }}" class="hidden"></form>

    <!-- Pagination & Action Bar -->
    <div class="flex items-center justify-between gap-4 mb-4 bg-gray-50/50 p-3 rounded-2xl border border-gray-150 flex-wrap text-xs text-gray-500">
        <div class="flex items-center gap-2 flex-wrap">
            <span>Página</span>
            <input type="text" class="w-8 text-center border border-gray-200 rounded px-1 py-0.5 text-xs focus:outline-none" value="3">
            <button type="button" class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 rounded text-gray-500 font-bold transition-colors">&lt;</button>
            <button type="button" class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 rounded text-gray-600 font-semibold transition-colors">1</button>
            <button type="button" class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 rounded text-gray-600 font-semibold transition-colors">2</button>
            <button type="button" class="px-2 py-0.5 bg-[#A01724] text-white rounded font-bold">3</button>
            <button type="button" class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 rounded text-gray-600 font-semibold transition-colors">4</button>
            <span class="text-gray-400">..</span>
            <button type="button" class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 rounded text-gray-600 font-semibold transition-colors">12</button>
            <button type="button" class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 rounded text-gray-500 font-bold transition-colors">&gt;</button>
            <select class="border border-gray-200 rounded px-1.5 py-0.5 text-xs bg-transparent focus:outline-none text-gray-600">
                <option>20/Pág</option>
            </select>
            <span class="text-gray-400 ml-2 font-medium">1 - 20 de 200</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-gray-600 font-semibold px-2 py-1 transition-colors">Cancelar</a>
            <button type="button" onclick="clearFilters()" class="bg-[#DAA5A7]/30 text-[#A01724] hover:bg-[#DAA5A7]/50 font-bold py-1.5 px-4 rounded-full text-xs transition-all cursor-pointer">Limpar filtros</button>
            <button type="submit" form="usersFilterForm" class="bg-[#A01724] hover:bg-[#86131E] text-white font-bold py-1.5 px-5 rounded-full text-xs transition-all cursor-pointer">Filtrar</button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="overflow-x-auto border border-gray-200 rounded-2xl shadow-sm">
        <table class="w-full text-left border-collapse" id="usersTable">
            <thead>
                <!-- Row 1: Titles -->
                <tr class="text-xs font-bold text-white uppercase tracking-wider">
                    <th class="py-2.5 px-3 bg-[#86131E] border-r border-white/20 border-b border-gray-200 text-left">NOME =</th>
                    <th class="py-2.5 px-3 bg-[#86131E] border-r border-white/20 border-b border-gray-200 text-left">TELEFONE =</th>
                    <th class="py-2.5 px-3 bg-[#86131E] border-r border-white/20 border-b border-gray-200 text-left">EMAIL =</th>
                    <th class="py-2.5 px-3 bg-[#86131E] border-r border-white/20 border-b border-gray-200 text-left">LOGIN =</th>
                    <th class="py-2.5 px-3 bg-[#86131E] border-r border-white/20 border-b border-gray-200 text-left">PERFIL DE ACESSO =</th>
                    <th class="py-2.5 px-3 bg-[#86131E] border-r border-white/20 border-b border-gray-200 text-center">STATUS =</th>
                    <th class="py-2.5 px-3 bg-[#86131E] border-r border-white/20 border-b border-gray-200 text-center">AÇÕES</th>
                    <th class="py-2.5 px-3 bg-[#86131E] border-b border-gray-200 text-left">LOG =</th>
                </tr>
                <!-- Row 2: Inline Filters -->
                <tr class="bg-gray-50/50">
                    <td class="p-1.5 border-r border-gray-200 border-b border-gray-200">
                        <input type="text" name="nome" value="{{ request('nome') }}" form="usersFilterForm" class="w-full bg-white border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:border-[#A01724] text-gray-700 font-medium">
                    </td>
                    <td class="p-1.5 border-r border-gray-200 border-b border-gray-200">
                        <input type="text" name="telefone" value="{{ request('telefone') }}" form="usersFilterForm" class="w-full bg-white border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:border-[#A01724] text-gray-700 font-medium">
                    </td>
                    <td class="p-1.5 border-r border-gray-200 border-b border-gray-200">
                        <input type="text" name="email" value="{{ request('email') }}" form="usersFilterForm" class="w-full bg-white border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:border-[#A01724] text-gray-700 font-medium">
                    </td>
                    <td class="p-1.5 border-r border-gray-200 border-b border-gray-200">
                        <input type="text" name="login" value="{{ request('login') }}" form="usersFilterForm" class="w-full bg-white border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:border-[#A01724] text-gray-700 font-medium">
                    </td>
                    <td class="p-1.5 border-r border-gray-200 border-b border-gray-200">
                        <select name="perfil_id" form="usersFilterForm" class="w-full bg-white border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:border-[#A01724] text-gray-700 font-semibold">
                            <option value="">TODOS</option>
                            @foreach($profiles as $p)
                            <option value="{{ $p->id }}" {{ request('perfil_id') == $p->id ? 'selected' : '' }}>{{ strtoupper($p->name) }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="p-1.5 border-r border-gray-200 border-b border-gray-200">
                        <select name="status" form="usersFilterForm" class="w-full bg-white border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:border-[#A01724] text-gray-700 font-semibold">
                            <option value="">TODOS</option>
                            <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>ATIVO</option>
                            <option value="ausente" {{ request('status') == 'ausente' ? 'selected' : '' }}>AUSENTE</option>
                            <option value="inativo" {{ request('status') == 'inativo' ? 'selected' : '' }}>INATIVO</option>
                            <option value="bloqueado" {{ request('status') == 'bloqueado' ? 'selected' : '' }}>BLOQUEADO</option>
                        </select>
                    </td>
                    <td class="p-1.5 border-r border-gray-200 border-b border-gray-200 text-center text-gray-400 font-bold">-</td>
                    <td class="p-1.5 border-b border-gray-200">
                        <div class="flex items-center gap-1 justify-between">
                            <div class="flex items-center gap-0.5">
                                <span class="text-[9px] text-gray-400 font-bold">DE:</span>
                                <input type="text" name="log_de" value="{{ request('log_de') }}" form="usersFilterForm" placeholder="AAAA-MM-DD" class="w-16 bg-white border border-gray-300 rounded px-1 py-0.5 text-[9px] focus:outline-none focus:border-[#A01724] text-gray-700 font-medium">
                            </div>
                            <div class="flex items-center gap-0.5">
                                <span class="text-[9px] text-gray-400 font-bold">ATÉ:</span>
                                <input type="text" name="log_ate" value="{{ request('log_ate') }}" form="usersFilterForm" placeholder="AAAA-MM-DD" class="w-16 bg-white border border-gray-300 rounded px-1 py-0.5 text-[9px] focus:outline-none focus:border-[#A01724] text-gray-700 font-medium">
                            </div>
                        </div>
                    </td>
                </tr>
            </thead>
            <tbody class="text-xs">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50/50 transition-colors border-b border-gray-200" data-user-id="{{ $user->id }}">
                    <td class="py-3 px-3 font-bold text-gray-700 border-r border-gray-200 bg-white uppercase">{{ $user->name }}</td>
                    <td class="py-3 px-3 text-gray-500 font-medium border-r border-gray-200 bg-white">{{ $user->phone ?? '-' }}</td>
                    <td class="py-3 px-3 text-gray-500 border-r border-gray-200 bg-white uppercase">{{ $user->email }}</td>
                    <td class="py-3 px-3 text-gray-500 font-mono border-r border-gray-200 bg-white uppercase">{{ $user->login ?? '-' }}</td>
                    <td class="py-3 px-3 text-gray-700 font-semibold border-r border-gray-200 bg-white uppercase">
                        {{ $user->accessProfile ? $user->accessProfile->name : 'SEM PERFIL' }}
                    </td>
                    <td class="py-3 px-3 text-center font-bold text-xs border-r border-gray-200 bg-white uppercase">
                        @if($user->status === 'ativo')
                            <span class="text-green-600">ATIVO</span>
                        @elseif($user->status === 'ausente')
                            <span class="text-amber-500">AUSENTE</span>
                        @elseif($user->status === 'inativo')
                            <span class="text-red-500">INATIVO</span>
                        @else
                            <span class="text-gray-500">BLOQUEADO</span>
                        @endif
                    </td>
                    <td class="py-3 px-3 text-center border-r border-gray-200 bg-white">
                        <div class="inline-flex items-center gap-1.5 justify-center">
                            <button onclick="editUser({{ json_encode($user) }})" class="p-1 text-[#E87722] hover:text-[#d66b1e] cursor-pointer" title="Editar Usuário">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            <button onclick="deleteUser({{ $user->id }})" class="p-1 text-[#404040] hover:text-red-600 cursor-pointer" title="Excluir Usuário">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                    <td class="py-3 px-3 text-gray-400 font-semibold text-[10px] bg-white pr-4 uppercase">
                        ALTERADO - {{ $user->updated_at ? $user->updated_at->format('d/m \À\S H\hi') : '31/03 ÀS 9H22' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-8 text-center text-gray-400 text-xs">Nenhum usuário correspondente aos filtros.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- PROFILE MODAL -->
<div id="profileModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden select-none transition-all">
    <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-xl border border-gray-100 mx-4">
        <h3 id="profileModalTitle" class="text-lg font-bold text-gray-900 mb-4">Adicionar Perfil de Acesso</h3>
        
        <form id="profileForm" onsubmit="saveProfileForm(event)">
            <input type="hidden" id="profile_id" name="profile_id">
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Nome do Perfil</label>
                <input type="text" id="profile_name" required placeholder="Ex: ATENDENTE N1 - SUPORTE" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#A01724] text-gray-800">
            </div>

            <div class="grid grid-cols-3 gap-3 mb-6">
                <div class="bg-gray-50 p-3 rounded-xl text-center">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Nível N1</label>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="profile_n1" class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                    </label>
                </div>
                <div class="bg-gray-50 p-3 rounded-xl text-center">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Nível N2</label>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="profile_n2" class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                    </label>
                </div>
                <div class="bg-gray-50 p-3 rounded-xl text-center">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2">Fila</label>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="profile_fila" class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeProfileModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold py-2 px-4 rounded-xl text-xs transition-colors cursor-pointer">Cancelar</button>
                <button type="submit" class="bg-[#A01724] hover:bg-[#86131E] text-white font-bold py-2 px-5 rounded-xl text-xs transition-colors cursor-pointer">Salvar</button>
            </div>
        </form>
    </div>
</div>

<!-- USER MODAL -->
<div id="userModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden select-none transition-all">
    <div class="bg-white rounded-3xl w-full max-w-lg p-6 shadow-xl border border-gray-100 mx-4">
        <h3 id="userModalTitle" class="text-lg font-bold text-gray-900 mb-4">Adicionar Usuário</h3>
        
        <form id="userForm" onsubmit="saveUserForm(event)">
            <input type="hidden" id="user_id" name="user_id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Nome Completo</label>
                    <input type="text" id="user_name" required placeholder="Ex: JOSÉ DA SILVA" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#A01724] text-gray-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Telefone</label>
                    <input type="text" id="user_phone" placeholder="Ex: (31) 12345-6789" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#A01724] text-gray-800">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Email</label>
                    <input type="email" id="user_email" required placeholder="Ex: jose@claro.com.br" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#A01724] text-gray-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Login / Fícula</label>
                    <input type="text" id="user_login" placeholder="Ex: F123456" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#A01724] text-gray-800">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Perfil de Acesso</label>
                    <select id="user_profile_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#A01724] text-gray-800">
                        <option value="">Sem Perfil</option>
                        @foreach($profiles as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Status</label>
                    <select id="user_status" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#A01724] text-gray-800">
                        <option value="ativo">ATIVO</option>
                        <option value="ausente">AUSENTE</option>
                        <option value="inativo">INATIVO</option>
                        <option value="bloqueado">BLOQUEADO</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Senha</label>
                <input type="password" id="user_password" placeholder="Mínimo 6 caracteres" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#A01724] text-gray-800">
                <p id="password_hint" class="text-[10px] text-gray-400 mt-1 hidden">Deixe em branco para manter a senha atual.</p>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeUserModal()" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold py-2 px-4 rounded-xl text-xs transition-colors cursor-pointer">Cancelar</button>
                <button type="submit" class="bg-[#A01724] hover:bg-[#86131E] text-white font-bold py-2 px-5 rounded-xl text-xs transition-colors cursor-pointer">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
    // LOCAL FILA PERMISSIONS (MOCK STORAGE)
    function getFilaPermission(profileName, profileId, queueName) {
        const key = `profile_${profileId}_queue_${queueName}`;
        const val = localStorage.getItem(key);
        if (val === null) {
            const upperName = profileName.toUpperCase();
            if (upperName.includes('ATENDENTE N1') && !upperName.includes('+')) {
                return true;
            }
            if (upperName.includes('ATENDENTE N2') && !upperName.includes('+')) {
                return true;
            }
            if (upperName.includes('PROMOÇÕES')) {
                return true;
            }
            return false;
        }
        return val === 'true';
    }

    function toggleFilaPermissionLocal(profileId, queueName, checked) {
        const key = `profile_${profileId}_queue_${queueName}`;
        localStorage.setItem(key, checked);
    }

    // Toggle Actions Column (Edit/Delete Profile)
    function toggleEditMode() {
        const btn = document.getElementById('toggleEditBtn');
        const actions = document.querySelectorAll('.edit-actions');
        const isHidden = actions[0] ? actions[0].classList.contains('hidden') : true;
        
        actions.forEach(el => {
            if (isHidden) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        });
        
        if (isHidden) {
            btn.innerHTML = 'Desabilitar edição 🔓';
            btn.classList.remove('bg-[#A0A0A0]', 'hover:bg-[#909090]');
            btn.classList.add('bg-[#A01724]', 'hover:bg-[#86131E]');
        } else {
            btn.innerHTML = 'Habilitar edição 🔒';
            btn.classList.remove('bg-[#A01724]', 'hover:bg-[#86131E]');
            btn.classList.add('bg-[#A0A0A0]', 'hover:bg-[#909090]');
        }
    }

    // Clear Filters
    function clearFilters() {
        const form = document.getElementById('usersFilterForm');
        if (form) {
            form.querySelectorAll('input, select').forEach(el => el.value = '');
        }
        window.location.href = '{{ route("users.index") }}';
    }

    // Initialize Mock Checked States on Load
    document.addEventListener('DOMContentLoaded', () => {
        const profiles = @json($profiles);
        profiles.forEach(profile => {
            ['promocoes', 'sistemas', 'vigencia'].forEach(queue => {
                const el = document.getElementById(`checkbox_${profile.id}_${queue}`);
                if (el) {
                    el.checked = getFilaPermission(profile.name, profile.id, queue);
                }
            });
        });
    });

    // PROFILE SCRIPTS
    function openProfileModal() {
        document.getElementById('profileModalTitle').innerText = 'Adicionar Perfil de Acesso';
        document.getElementById('profileForm').reset();
        document.getElementById('profile_id').value = '';
        document.getElementById('profileModal').classList.remove('hidden');
    }

    function closeProfileModal() {
        document.getElementById('profileModal').classList.add('hidden');
    }

    function editProfile(profile) {
        document.getElementById('profileModalTitle').innerText = 'Editar Perfil de Acesso';
        document.getElementById('profile_id').value = profile.id;
        document.getElementById('profile_name').value = profile.name;
        document.getElementById('profile_n1').checked = !!profile.nivel_n1;
        document.getElementById('profile_n2').checked = !!profile.nivel_n2;
        document.getElementById('profile_fila').checked = !!profile.fila;
        document.getElementById('profileModal').classList.remove('hidden');
    }

    async function saveProfileForm(e) {
        e.preventDefault();
        const id = document.getElementById('profile_id').value;
        const name = document.getElementById('profile_name').value;
        const nivel_n1 = document.getElementById('profile_n1').checked;
        const nivel_n2 = document.getElementById('profile_n2').checked;
        const fila = document.getElementById('profile_fila').checked;

        const url = id ? `/admin/access-profiles/${id}` : '/admin/access-profiles';
        const method = id ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name, nivel_n1, nivel_n2, fila })
            });

            const result = await response.json();
            if (result.success) {
                location.reload();
            } else {
                alert('Erro ao salvar o perfil de acesso.');
            }
        } catch (error) {
            console.error(error);
            alert('Ocorreu um erro ao processar a requisição.');
        }
    }

    async function toggleProfilePermission(profileId, field, value) {
        try {
            const response = await fetch(`/admin/access-profiles/${profileId}/toggle`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ field, value })
            });
            const res = await response.json();
            if (!res.success) {
                alert('Erro ao atualizar permissão.');
            }
        } catch (error) {
            console.error(error);
        }
    }

    async function deleteProfile(id) {
        if (!confirm('Deseja realmente excluir este perfil de acesso? Usuários associados a ele ficarão sem perfil.')) return;

        try {
            const response = await fetch(`/admin/access-profiles/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const res = await response.json();
            if (res.success) {
                location.reload();
            } else {
                alert('Erro ao excluir o perfil.');
            }
        } catch (error) {
            console.error(error);
        }
    }

    function downloadProfilesCSV() {
        let csv = [];
        const rows = document.querySelectorAll('#profilesTable tr');
        
        for (let i = 0; i < rows.length; i++) {
            const row = [], cols = rows[i].querySelectorAll('td, th');
            const skipColIndex = cols.length - 1; // Actions col
            
            for (let j = 0; j < cols.length; j++) {
                if (cols[j].classList.contains('edit-actions')) continue;
                let text = '';
                const checkbox = cols[j].querySelector('input[type="checkbox"]');
                if (checkbox) {
                    text = checkbox.checked ? 'SIM' : 'NÃO';
                } else {
                    text = cols[j].innerText.trim();
                }
                row.push('"' + text.replace(/"/g, '""') + '"');
            }
            csv.push(row.join(','));
        }
        
        const csvContent = 'data:text/csv;charset=utf-8,\uFEFF' + csv.join('\n');
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', 'perfis_de_acesso.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // USER SCRIPTS
    function openUserModal() {
        document.getElementById('userModalTitle').innerText = 'Adicionar Usuário';
        document.getElementById('userForm').reset();
        document.getElementById('user_id').value = '';
        document.getElementById('user_password').required = true;
        document.getElementById('password_hint').classList.add('hidden');
        document.getElementById('userModal').classList.remove('hidden');
    }

    function closeUserModal() {
        document.getElementById('userModal').classList.add('hidden');
    }

    function editUser(user) {
        document.getElementById('userModalTitle').innerText = 'Editar Usuário';
        document.getElementById('user_id').value = user.id;
        document.getElementById('user_name').value = user.name;
        document.getElementById('user_phone').value = user.phone || '';
        document.getElementById('user_email').value = user.email;
        document.getElementById('user_login').value = user.login || '';
        document.getElementById('user_profile_id').value = user.access_profile_id || '';
        document.getElementById('user_status').value = user.status || 'ativo';
        document.getElementById('user_password').required = false;
        document.getElementById('user_password').value = '';
        document.getElementById('password_hint').classList.remove('hidden');
        document.getElementById('userModal').classList.remove('hidden');
    }

    async function saveUserForm(e) {
        e.preventDefault();
        const id = document.getElementById('user_id').value;
        const name = document.getElementById('user_name').value;
        const phone = document.getElementById('user_phone').value;
        const email = document.getElementById('user_email').value;
        const login = document.getElementById('user_login').value;
        const access_profile_id = document.getElementById('user_profile_id').value;
        const status = document.getElementById('user_status').value;
        const password = document.getElementById('user_password').value;

        const url = id ? `/admin/users/${id}` : '/admin/users';
        const method = id ? 'PUT' : 'POST';

        const bodyData = { name, phone, email, login, access_profile_id, status };
        if (password) {
            bodyData.password = password;
        }

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(bodyData)
            });

            const result = await response.json();
            if (result.success) {
                location.reload();
            } else {
                alert(result.message || 'Erro ao salvar o usuário.');
            }
        } catch (error) {
            console.error(error);
            alert('Ocorreu um erro ao processar a requisição.');
        }
    }

    async function deleteUser(id) {
        if (!confirm('Deseja realmente excluir este usuário?')) return;

        try {
            const response = await fetch(`/admin/users/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const res = await response.json();
            if (res.success) {
                location.reload();
            } else {
                alert('Erro ao excluir o usuário.');
            }
        } catch (error) {
            console.error(error);
        }
    }

    function downloadUsersCSV() {
        let csv = [];
        const rows = document.querySelectorAll('#usersTable tr');
        
        for (let i = 0; i < rows.length; i++) {
            const row = [], cols = rows[i].querySelectorAll('td, th');
            
            for (let j = 0; j < cols.length; j++) {
                if (j === 6) continue; // exclude actions column
                let text = cols[j].innerText.trim();
                text = text.replace(/\s+/g, ' ');
                row.push('"' + text.replace(/'/g, "'").replace(/"/g, '""') + '"');
            }
            csv.push(row.join(','));
        }
        
        const csvContent = 'data:text/csv;charset=utf-8,\uFEFF' + csv.join('\n');
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', 'usuarios.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endsection
