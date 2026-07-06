@extends('layouts.app')

@section('title', 'Gestão de Atendimento — Claro Prisma')

@section('content')
<style>
    /* ── Tree connector lines ── */
    .tree-root { display: flex; flex-direction: column; align-items: center; }
    .tree-children { display: flex; flex-direction: row; align-items: flex-start; justify-content: center; gap: 16px; position: relative; padding-top: 32px; }
    .tree-children::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 2px; height: 32px;
        background: #d1d5db;
    }
    .tree-branch { display: flex; flex-direction: column; align-items: center; position: relative; }
    .tree-branch::before {
        content: '';
        position: absolute;
        top: 0; left: 50%;
        transform: translateX(-50%);
        width: 2px; height: 32px;
        background: #d1d5db;
    }
    /* Horizontal connector across siblings */
    .tree-siblings-wrap { position: relative; display: flex; gap: 16px; align-items: flex-start; }
    .tree-siblings-wrap::before {
        content: '';
        position: absolute;
        top: 0;
        left: calc(50% - 50%);
        width: 100%;
        height: 2px;
        background: #d1d5db;
    }

    /* ── Flow Node card ── */
    .flow-node {
        background: #fff;
        border: 1.5px solid #e5e7eb;
        border-radius: 14px;
        padding: 10px 16px 12px;
        min-width: 140px;
        max-width: 180px;
        text-align: center;
        position: relative;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        transition: box-shadow .15s;
    }
    .flow-node:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.1); }
    .flow-node-label {
        font-size: 10px;
        font-weight: 700;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: .08em;
        line-height: 1;
        margin-bottom: 4px;
    }
    .flow-node-title {
        font-size: 18px;
        font-weight: 800;
        color: #1f2937;
        line-height: 1.1;
    }
    .flow-node-actions { display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 8px; }
    .flow-node-add {
        width: 22px; height: 22px;
        background: #f97316;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; border: none;
        transition: background .15s;
    }
    .flow-node-add:hover { background: #ea6d0e; }
    .flow-node-edit {
        width: 22px; height: 22px;
        background: #f97316;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; border: none;
        transition: background .15s;
    }
    .flow-node-edit:hover { background: #ea6d0e; }

    /* ── Vertical connector from node down to children ── */
    .v-line {
        width: 2px; height: 32px;
        background: #d1d5db;
        margin: 0 auto;
    }
    /* ── Horizontal bar spanning children ── */
    .h-bar-wrap { position: relative; display: flex; justify-content: center; }
    .h-bar {
        height: 2px;
        background: #d1d5db;
        position: absolute;
        top: 0;
    }

    /* ── Level badge ── */
    .level-badge {
        display: inline-flex; align-items: center; gap: 4px;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 999px;
        padding: 3px 10px;
        font-size: 11px;
        font-weight: 700;
        color: #374151;
        cursor: pointer;
        transition: background .15s;
    }
    .level-badge:hover { background: #e5e7eb; }
</style>

{{-- Breadcrumbs --}}
<div class="flex items-center gap-2 text-xs text-gray-500 mb-2 select-none">
    <span>Claro Prisma</span>
    <span>&gt;</span>
    <span class="text-gray-800 font-medium">Gestão de Atendimento</span>
</div>

<h1 class="text-2xl font-bold text-[#DA291C] mb-6">Gestão de Atendimento</h1>

{{-- Triage Section header --}}
<div class="flex items-center gap-3 mb-4 select-none">
    <span class="text-base font-bold text-gray-800">Triagem</span>
    <button onclick="openAddNodeModal(null,'root')"
        class="inline-flex items-center gap-1.5 bg-[#1f2937] hover:bg-[#111827] text-white text-xs font-bold px-3 py-1.5 rounded-full transition-all cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
            <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
        </svg>
        Adicionar novo
    </button>
</div>

{{-- Flow Canvas --}}
<div class="bg-white rounded-[20px] border border-gray-200 shadow-sm p-8 overflow-x-auto select-none">

    {{-- Top bar: flow selector + add + expand --}}
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-3">
            <span class="text-sm font-bold text-gray-800">Fluxo de Triagem</span>
            <button id="flow-selector-btn" onclick="toggleFlowDropdown()"
                class="level-badge">
                <span id="flow-selector-label">Fluxo Promoções</span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5 text-gray-500">
                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                </svg>
            </button>
            {{-- Dropdown (items rendered dynamically by JS) --}}
            <div id="flow-dropdown" class="hidden absolute z-50 bg-white border border-gray-200 rounded-xl shadow-lg py-1 w-52" style="margin-top:2.5rem">
                {{-- Populated by renderDropdown() --}}
            </div>
            <button onclick="openAddNodeModal(null,'root')"
                class="inline-flex items-center gap-1.5 bg-[#1f2937] hover:bg-[#111827] text-white text-xs font-bold px-3 py-1.5 rounded-full transition-all cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                    <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                </svg>
                Adicionar novo
            </button>
        </div>
        <button class="text-[#DA291C] hover:text-[#a01724] transition-colors" title="Expandir">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
            </svg>
        </button>
    </div>

    {{-- Tree Render --}}
    <div id="flow-tree" class="flex flex-col items-center min-w-max mx-auto">
        {{-- Rendered by JS --}}
    </div>
</div>

{{-- ── Modal: Add/Edit Node ── --}}
<div id="node-modal" class="hidden fixed inset-0 z-[100] bg-black/55 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[430px] flex flex-col" style="padding: 26px 28px 22px;">

        {{-- Header --}}
        <div class="flex items-start justify-between mb-5">
            <h3 id="node-modal-title" class="text-[22px] font-extrabold leading-tight" style="color:#DA291C;">Adicionar item</h3>
            <button onclick="closeNodeModal()" class="text-gray-400 hover:text-gray-700 transition-colors mt-0.5 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Root-only field: flow name --}}
        <div id="modal-flow-name-wrap" class="hidden mb-3">
            <input id="node-flow-name-input" type="text"
                placeholder="Nome do fluxo (disponível somente para administradores)"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#DA291C] focus:ring-1 focus:ring-[#DA291C] transition-all">
        </div>

        {{-- Root-only field: setor name --}}
        <div id="modal-setor-name-wrap" class="hidden mb-3">
            <input id="node-setor-name-input" type="text"
                placeholder="Nome do setor (departamento)"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#DA291C] focus:ring-1 focus:ring-[#DA291C] transition-all">
        </div>

        {{-- Child-only: type dropdown --}}
        <div id="modal-type-wrap" class="hidden mb-3">
            <label class="text-xs font-bold text-gray-600 block mb-1">Tipo</label>
            <select id="node-type-input"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 bg-white focus:outline-none focus:border-[#DA291C] focus:ring-1 focus:ring-[#DA291C] transition-all appearance-none">
                <option value="fila">Fila</option>
                <option value="subitem1">Fila - subitem 1</option>
                <option value="subitem2">Fila - subitem 2</option>
            </select>
        </div>

        {{-- Child-only: item name --}}
        <div id="modal-item-name-wrap" class="hidden mb-3">
            <input id="node-name-input" type="text"
                placeholder="Nome do item"
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#DA291C] focus:ring-1 focus:ring-[#DA291C] transition-all">
        </div>

        {{-- Nível de acesso (radio) --}}
        <div class="mb-5">
            <span class="text-sm font-semibold text-gray-700 mr-3">Nível de acesso</span>
            <label class="inline-flex items-center gap-1.5 mr-4 cursor-pointer">
                <input type="radio" name="node-nivel" value="n1" class="accent-[#DA291C]">
                <span class="text-sm text-gray-600">Nível 1</span>
            </label>
            <label class="inline-flex items-center gap-1.5 mr-4 cursor-pointer">
                <input type="radio" name="node-nivel" value="n2" class="accent-[#DA291C]">
                <span class="text-sm text-gray-600">Nível 2</span>
            </label>
            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                <input type="radio" name="node-nivel" value="ambos" checked class="accent-[#DA291C]">
                <span class="text-sm text-gray-600">Ambos</span>
            </label>
        </div>

        {{-- CTA --}}
        <button onclick="saveNode()"
            class="w-full h-12 rounded-xl font-extrabold text-white text-base mb-3 transition-all"
            style="background: linear-gradient(90deg,#a01724 0%,#da291c 100%)">
            Adicionar item
        </button>

        <button onclick="closeNodeModal()"
            class="w-full text-center text-sm font-semibold text-gray-500 hover:text-gray-800 transition-colors cursor-pointer">
            Cancelar
        </button>
    </div>
</div>

<script>
// ── Data model: array of top-level flows ──
let allFlows = @json($triageFlows ?? []);
if (!Array.isArray(allFlows) || allFlows.length === 0) {
    allFlows = [
        { id: 1, name: 'Novo Fluxo', type: 'setor', n1: true, n2: true, children: [] }
    ];
}

let activeFlowId = allFlows[0].id;
let nextId = 1;
let editingNodeId = null;
let addingParentId = null;

function showToast(message) {
    alert(message);
}

function getMaxNodeId(nodes) {
    let max = 0;
    (nodes || []).forEach(node => {
        max = Math.max(max, Number(node.id) || 0);
        if (node.children && node.children.length) {
            max = Math.max(max, getMaxNodeId(node.children));
        }
    });
    return max;
}

async function persistTriageFlows() {
    const response = await fetch("{{ route('admin.gestao-atendimento.save') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ flows: allFlows })
    });

    const data = await response.json().catch(() => ({}));
    if (!response.ok || !data.success) {
        throw new Error(data.message || 'Nao foi possivel salvar o fluxo de triagem.');
    }
}

nextId = getMaxNodeId(allFlows) + 1;

function getActiveFlow() {
    return allFlows.find(f => f.id === activeFlowId) || allFlows[0];
}

// ── Type labels ──
function typeLabel(node, depth) {
    const d = depth || 0;
    const nivel = (node.n1 && node.n2) ? 'N1/N2' : (node.n1 ? 'N1' : 'N2');
    if (d === 0) return `Setor (departamento) · ${nivel}`;
    if (d === 1) return `Fila · ${nivel}`;
    if (d === 2) return `Fila - Subitem 1 · ${nivel}`;
    return `Fila - Subitem 2 · ${nivel}`;
}

// ── Render tree ──
function renderTree() {
    const wrap = document.getElementById('flow-tree');
    const flow = getActiveFlow();
    wrap.innerHTML = renderNodeHTML(flow, 0);
}

function renderNodeHTML(node, depth) {
    const label = typeLabel(node, depth);
    const safeNodeName = String(node.name || '').replace(/'/g, "\\'");
    const childrenHTML = node.children && node.children.length
        ? renderChildren(node.children, depth + 1)
        : '';

    return `
        <div class="flex flex-col items-center">
            <div class="flow-node" id="node-${node.id}">
                <div class="flow-node-label">${label}</div>
                <div class="flow-node-title">${node.name}</div>
                <div class="flow-node-actions">
                    <button class="flow-node-add" onclick="openAddNodeModal(${node.id},'child','${safeNodeName}')" title="Adicionar filho">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="white" class="w-3 h-3">
                            <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/>
                        </svg>
                    </button>
                    <button class="flow-node-edit" onclick="openEditNodeModal(${node.id})" title="Editar">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="white" class="w-3 h-3">
                            <path d="m5.433 13.917 1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z"/>
                        </svg>
                    </button>
                </div>
            </div>
            ${childrenHTML}
        </div>`;
}

function renderChildren(children, depth) {
    if (!children || children.length === 0) return '';

    // Vertical connector
    let html = '<div class="v-line"></div>';

    if (children.length === 1) {
        html += `<div class="flex flex-col items-center">${renderNodeHTML(children[0], depth)}</div>`;
        return html;
    }

    // Horizontal line spanning the group
    const nodeWidth = 180;
    const gap = 16;
    const totalWidth = children.length * nodeWidth + (children.length - 1) * gap;

    html += `<div style="position:relative; width:${totalWidth}px; height:2px; background:#d1d5db; margin-bottom:0;"></div>`;
    html += `<div style="display:flex; gap:${gap}px; align-items:flex-start;">`;

    children.forEach((child, i) => {
        html += `<div class="flex flex-col items-center" style="width:${nodeWidth}px;">`;
        html += `<div class="v-line"></div>`;
        html += renderNodeHTML(child, depth);
        html += `</div>`;
    });

    html += `</div>`;
    return html;
}

// ── Modal helpers ──
function getNivelValue() {
    return document.querySelector('input[name="node-nivel"]:checked')?.value || 'ambos';
}
function setNivelValue(n1, n2) {
    let val = 'ambos';
    if (n1 && !n2) val = 'n1';
    if (!n1 && n2) val = 'n2';
    const radio = document.querySelector(`input[name="node-nivel"][value="${val}"]`);
    if (radio) radio.checked = true;
}

function setModalContext(isRoot) {
    // Root: show flow name + setor name; hide type + item name
    document.getElementById('modal-flow-name-wrap').classList.toggle('hidden', !isRoot);
    document.getElementById('modal-setor-name-wrap').classList.toggle('hidden', !isRoot);
    document.getElementById('modal-type-wrap').classList.toggle('hidden', isRoot);
    document.getElementById('modal-item-name-wrap').classList.toggle('hidden', isRoot);

    document.getElementById('node-modal-title').textContent =
        isRoot ? 'Adicionar item - Triagem' : 'Adicionar item - Fluxo de Triagem';
}

function getStructuralTypeByParentId(parentId) {
    if (parentId === null || parentId === undefined) return 'fila';
    const activeFlow = getActiveFlow();
    const depth = nodeDepth(activeFlow, parentId);
    if (depth <= 0) return 'fila';
    if (depth === 1) return 'subitem1';
    return 'subitem2';
}

function findParentNode(node, targetId, parent) {
    if (node.id === targetId) return parent || null;
    for (const child of (node.children || [])) {
        const found = findParentNode(child, targetId, node);
        if (found) return found;
    }
    return null;
}

function getParentNodeFromAllFlows(targetId) {
    for (const flow of allFlows) {
        const parent = findParentNode(flow, targetId, null);
        if (parent) return parent;
    }
    return null;
}

function configureTypeInputForParent(parentId, parentNameHint) {
    const typeEl = document.getElementById('node-type-input');
    if (!typeEl) return;

    const parentNode = parentId !== null ? findInAllFlows(parentId) : null;
    const inheritedName = (parentNameHint && String(parentNameHint).trim())
        || (parentNode ? parentNode.name : '');

    typeEl.innerHTML = `<option value="__inherited__">${inheritedName}</option>`;
    typeEl.value = '__inherited__';
    typeEl.disabled = true;
}

function configureTypeInputForEdit(nodeId) {
    const typeEl = document.getElementById('node-type-input');
    if (!typeEl) return;

    const parentNode = getParentNodeFromAllFlows(nodeId);
    const inheritedName = parentNode ? parentNode.name : '';

    typeEl.innerHTML = `<option value="__inherited__">${inheritedName}</option>`;
    typeEl.value = '__inherited__';
    typeEl.disabled = true;
}

function openAddNodeModal(parentId, mode, parentNameHint) {
    editingNodeId = null;
    addingParentId = parentId;
    const isRoot = (mode === 'root');
    setModalContext(isRoot);

    // Reset fields
    if (document.getElementById('node-flow-name-input')) document.getElementById('node-flow-name-input').value = '';
    if (document.getElementById('node-setor-name-input')) document.getElementById('node-setor-name-input').value = '';
    if (document.getElementById('node-name-input')) document.getElementById('node-name-input').value = '';

    const typeEl = document.getElementById('node-type-input');
    if (typeEl) typeEl.disabled = false;

    // Pre-fill type based on parent depth
    if (!isRoot && parentId !== null) {
        configureTypeInputForParent(parentId, parentNameHint);
    }

    setNivelValue(true, true);
    document.getElementById('node-modal').classList.remove('hidden');
}

function openEditNodeModal(nodeId) {
    const node = findInAllFlows(nodeId);
    if (!node) return;
    editingNodeId = nodeId;
    addingParentId = null;
    const activeFlow = getActiveFlow();
    const isRoot = (nodeId === activeFlow.id);
    setModalContext(isRoot);

    if (isRoot) {
        if (document.getElementById('node-setor-name-input')) document.getElementById('node-setor-name-input').value = node.name;
    } else {
        if (document.getElementById('node-name-input')) document.getElementById('node-name-input').value = node.name;
        configureTypeInputForEdit(nodeId);
    }
    setNivelValue(node.n1, node.n2);
    document.getElementById('node-modal').classList.remove('hidden');
}

function closeNodeModal() {
    document.getElementById('node-modal').classList.add('hidden');
}

async function saveNode() {
    const nivel = getNivelValue();
    const n1 = nivel === 'n1' || nivel === 'ambos';
    const n2 = nivel === 'n2' || nivel === 'ambos';
    const isRoot = document.getElementById('modal-flow-name-wrap') &&
        !document.getElementById('modal-flow-name-wrap').classList.contains('hidden');

    let name = '';
    if (isRoot) {
        name = document.getElementById('node-setor-name-input')?.value.trim() || '';
    } else {
        name = document.getElementById('node-name-input')?.value.trim() || '';
    }
    if (!name) { alert('Informe o nome do item.'); return; }

    const inheritedFrom = isRoot
        ? null
        : (addingParentId !== null
            ? (findInAllFlows(addingParentId)?.name || null)
            : (getParentNodeFromAllFlows(editingNodeId)?.name || null));
    const type = isRoot ? 'setor' : (editingNodeId !== null
        ? (findInAllFlows(editingNodeId)?.type || 'fila')
        : getStructuralTypeByParentId(addingParentId));

    const snapshot = {
        flows: JSON.parse(JSON.stringify(allFlows)),
        activeFlowId,
        nextId,
    };

    if (editingNodeId !== null) {
        // Search across all flows
        let found = null;
        for (const f of allFlows) {
            found = findNode(f, editingNodeId);
            if (found) break;
        }
        if (found) {
            found.name = name;
            found.type = type;
            found.parent_name = inheritedFrom;
            found.n1 = n1;
            found.n2 = n2;
        }
    } else if (isRoot) {
        // Create a brand-new top-level flow (Triagem)
        const flowName = document.getElementById('node-flow-name-input')?.value.trim() || name;
        const newFlow = { id: nextId++, name, flowLabel: flowName, type: 'setor', n1, n2, children: [] };
        allFlows.push(newFlow);
        activeFlowId = newFlow.id;
        document.getElementById('flow-selector-label').textContent = flowName || name;
        renderDropdown();
    } else {
        const newNode = { id: nextId++, name, type, parent_name: inheritedFrom, n1, n2, children: [] };
        if (addingParentId === null) {
            getActiveFlow().children.push(newNode);
        } else {
            const parent = findInAllFlows(addingParentId);
            if (parent) parent.children.push(newNode);
        }
    }

    try {
        await persistTriageFlows();
        closeNodeModal();
        renderTree();
    } catch (error) {
        allFlows = snapshot.flows;
        activeFlowId = snapshot.activeFlowId;
        nextId = snapshot.nextId;
        document.getElementById('flow-selector-label').textContent = (getActiveFlow().flowLabel || getActiveFlow().name || 'Fluxo de Triagem');
        renderDropdown();
        renderTree();
        showToast(error.message || 'Falha ao salvar no banco.');
    }
}

function findInAllFlows(id) {
    for (const f of allFlows) {
        const found = findNode(f, id);
        if (found) return found;
    }
    return null;
}

function nodeDepth(root, targetId, depth) {
    depth = depth || 0;
    if (root.id === targetId) return depth;
    for (const child of (root.children || [])) {
        const d = nodeDepth(child, targetId, depth + 1);
        if (d !== -1) return d;
    }
    return -1;
}

function findNode(node, id) {
    if (node.id === id) return node;
    for (const child of (node.children || [])) {
        const found = findNode(child, id);
        if (found) return found;
    }
    return null;
}

// ── Flow selector ──
function renderDropdown() {
    const dd = document.getElementById('flow-dropdown');
    dd.innerHTML = allFlows.map(f => {
        const label = f.flowLabel || f.name;
        const isActive = f.id === activeFlowId;
        return `<button onclick="selectFlow(${f.id})" class="w-full text-left px-4 py-2 text-sm font-medium transition-colors ${
            isActive ? 'text-[#DA291C] bg-red-50' : 'text-gray-700 hover:bg-gray-50'
        }">${label}</button>`;
    }).join('');
}

function toggleFlowDropdown() {
    const dd = document.getElementById('flow-dropdown');
    renderDropdown();
    dd.classList.toggle('hidden');
}

function selectFlow(flowId) {
    activeFlowId = flowId;
    const flow = getActiveFlow();
    document.getElementById('flow-selector-label').textContent = flow.flowLabel || flow.name;
    document.getElementById('flow-dropdown').classList.add('hidden');
    renderTree();
}

document.addEventListener('click', e => {
    const dd = document.getElementById('flow-dropdown');
    if (!dd.classList.contains('hidden') && !e.target.closest('#flow-selector-btn') && !e.target.closest('#flow-dropdown')) {
        dd.classList.add('hidden');
    }
});

// ── Init ──
document.getElementById('flow-selector-label').textContent = (getActiveFlow().flowLabel || getActiveFlow().name || 'Fluxo de Triagem');
renderDropdown();
renderTree();
</script>
@endsection
