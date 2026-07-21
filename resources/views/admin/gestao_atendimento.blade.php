@extends('layouts.app')

@section('title', 'Gestão de Atendimento — Claro Prisma')

@section('content')
<style>
    /* ── Canvas styling ── */
    .canvas-viewport {
        background-color: #fafafa;
        background-image: radial-gradient(#e2e8f0 1.5px, transparent 1.5px);
        background-size: 24px 24px;
        position: relative;
        overflow: hidden;
        transition: height 0.3s ease;
    }
    .canvas-viewport.fullscreen {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 9999 !important;
        border-radius: 0 !important;
        margin: 0 !important;
        border: none !important;
    }
    .fullscreen-close-btn {
        display: none;
    }
    .canvas-viewport.fullscreen .fullscreen-close-btn {
        display: flex !important;
    }
    #canvas-container {
        cursor: grab;
    }
    #canvas-container:active {
        cursor: grabbing;
    }

    /* ── Flow Node card ── */
    .flow-node {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 20px;
        width: 190px;
        text-align: center;
        position: relative;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 10;
    }
    .flow-node:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }
    .flow-node-label {
        font-size: 10px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: .05em;
        line-height: 1.2;
        margin-bottom: 6px;
    }
    .flow-node-title {
        font-size: 18px;
        font-weight: 800;
        color: #1e293b;
        line-height: 1.2;
    }
    .flow-node-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 10px;
    }
    .flow-node-add, .flow-node-edit {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: none;
        transition: all 0.15s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.06);
    }
    .flow-node-add {
        background: #1e293b;
        color: #ffffff;
    }
    .flow-node-add:hover {
        background: #0f172a;
        transform: scale(1.15);
    }
    .flow-node-edit {
        background: #ea580c;
        color: #ffffff;
    }
    .flow-node-edit:hover {
        background: #c2410c;
        transform: scale(1.15);
    }

    /* ── Level badge ── */
    .level-badge {
        display: inline-flex; align-items: center; gap: 4px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        cursor: pointer;
        transition: all .15s ease;
    }
    .level-badge:hover { 
        background: #f1f5f9; 
        border-color: #cbd5e1;
    }
</style>

{{-- Breadcrumbs --}}
<div class="mb-2 select-none">
    @php
        $homeRoute = auth()->check() && auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard');
    @endphp
    <nav aria-label="breadcrumb" class="flex items-center gap-1.5">
        <a href="{{ $homeRoute }}" class="breadcrumb breadcrumb-link">Claro Prisma</a>
        <span class="breadcrumb breadcrumb-separator">&gt;</span>
        <span class="breadcrumb breadcrumb-current">Gestão de Atendimento</span>
    </nav>
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

{{-- Flow Canvas Container --}}
<div class="bg-white rounded-[20px] border border-gray-200 shadow-sm p-6 select-none flex flex-col">

    {{-- Top bar: flow selector + add + expand --}}
    <div class="relative flex items-center justify-center mb-6 pb-4 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <span class="text-lg font-bold text-gray-800">Fluxo de Triagem</span>
            <div class="relative">
                <button id="flow-selector-btn" onclick="toggleFlowDropdown()" class="level-badge">
                    <span id="flow-selector-label">Fluxo Promoções</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5 text-gray-500">
                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                </button>
                {{-- Dropdown --}}
                <div id="flow-dropdown" class="hidden absolute left-0 z-50 bg-white border border-gray-200 rounded-xl shadow-lg py-1 w-52 mt-1">
                    {{-- Populated by renderDropdown() --}}
                </div>
            </div>
            <button onclick="openAddNodeModal(null,'root')"
                class="inline-flex items-center gap-1.5 bg-[#1f2937] hover:bg-[#111827] text-white text-xs font-bold px-3 py-1.5 rounded-full transition-all cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                    <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                </svg>
                Adicionar novo
            </button>
        </div>
        <button onclick="toggleFullscreen()" class="absolute right-0 text-[#DA291C] hover:text-[#a01724] transition-colors p-1.5 hover:bg-red-50 rounded-lg cursor-pointer" title="Expandir/Recolher">
            <svg id="fullscreen-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
            </svg>
        </button>
    </div>

    {{-- Canvas Viewport --}}
    <div id="canvas-viewport" class="canvas-viewport w-full h-[580px] rounded-xl border border-gray-100 select-none">
        
        {{-- Close Fullscreen Button (only visible in fullscreen) --}}
        <button onclick="toggleFullscreen()" class="fullscreen-close-btn absolute top-4 right-4 z-30 text-[#DA291C] hover:text-[#a01724] bg-white hover:bg-red-50 border border-gray-200 transition-all p-2 rounded-xl shadow-md cursor-pointer items-center justify-center" title="Recolher (Esc)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
        
        {{-- Floating Toolbar --}}
        <div class="absolute bottom-4 right-4 z-20 flex items-center gap-1 bg-white/90 backdrop-blur-md border border-gray-200 rounded-xl shadow-md p-1.5 transition-all">
            <button onclick="zoomOut()" class="p-2 hover:bg-gray-100 active:scale-95 rounded-lg text-gray-500 transition-all cursor-pointer" title="Zoom Out">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                </svg>
            </button>
            <span id="zoom-indicator" class="text-xs font-bold text-gray-600 px-1 min-w-[42px] text-center select-none">100%</span>
            <button onclick="zoomIn()" class="p-2 hover:bg-gray-100 active:scale-95 rounded-lg text-gray-500 transition-all cursor-pointer" title="Zoom In">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </button>
            <div class="w-px h-4 bg-gray-200 mx-1"></div>
            <button onclick="centerCanvas()" class="p-2 hover:bg-gray-100 active:scale-95 rounded-lg text-gray-500 transition-all cursor-pointer" title="Centralizar">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5M15 15l5.25 5.25" />
                </svg>
            </button>
        </div>

        {{-- Canvas Container --}}
        <div id="canvas-container" class="absolute transform-gpu" style="transform-origin: 0 0; padding: 60px;">
            {{-- SVG lines container --}}
            <svg id="tree-connections-svg" class="absolute top-0 left-0 w-full h-full pointer-events-none" style="z-index: 0;"></svg>

            {{-- HTML Tree nodes --}}
            <div id="flow-tree" class="relative z-10 flex flex-col items-center select-none" style="width: max-content;">
                {{-- Rendered by JS --}}
            </div>
        </div>
    </div>
</div>

{{-- ── Modal: Add/Edit Node ── --}}
<div id="node-modal" class="hidden fixed inset-0 z-[100] bg-black/55 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[430px] flex flex-col" style="padding: 26px 28px 22px;">

        {{-- Header --}}
        <div class="flex items-start justify-between mb-5">
            <h3 id="node-modal-title" class="text-[22px] font-extrabold leading-tight" style="color:#DA291C;">Adicionar item</h3>
            <button onclick="closeNodeModal()" class="text-gray-400 hover:text-gray-700 transition-colors mt-0.5 focus:outline-none cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Root-only field: flow name --}}
        <div id="modal-flow-name-wrap" class="hidden mb-3">
            <input id="node-flow-name-input" type="text"
                placeholder="Nome do fluxo"
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
            class="w-full h-12 rounded-xl font-extrabold text-white text-base mb-3 transition-all cursor-pointer"
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

// Zoom and pan state
let scale = 1.0;
let translateX = 0;
let translateY = 0;
let isDragging = false;
let startX = 0;
let startY = 0;
let isInitialLoad = true;

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

function typeLabel(node, depth) {
    const d = depth || 0;
    const nivel = (node.n1 && node.n2) ? 'N1/N2' : (node.n1 ? 'N1' : 'N2');
    if (d === 0) return `Setor (departamento) · ${nivel}`;
    if (d === 1) return `Fila · ${nivel}`;
    if (d === 2) return `Fila - Subitem 1 · ${nivel}`;
    return `Fila - Subitem 2 · ${nivel}`;
}

// ── Render tree ──
function renderTree(forceCenter = false) {
    const wrap = document.getElementById('flow-tree');
    const flow = getActiveFlow();
    wrap.innerHTML = renderNodeHTML(flow, 0);
    
    // Wait for the DOM to render and update positions, then draw lines
    requestAnimationFrame(() => {
        const container = document.getElementById('canvas-container');
        const tree = document.getElementById('flow-tree');
        if (container && tree) {
            const padding = 120; // 60px padding on each side
            container.style.width = `${tree.offsetWidth + padding}px`;
            container.style.height = `${tree.offsetHeight + padding}px`;
        }
        
        drawConnections();
        
        if (isInitialLoad || forceCenter) {
            centerCanvas();
            isInitialLoad = false;
        }
    });
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
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                            <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/>
                        </svg>
                    </button>
                    <button class="flow-node-edit" onclick="openEditNodeModal(${node.id})" title="Editar">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
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
    
    // Renders children in a flex row with spacing
    let html = `<div class="tree-children flex gap-12 mt-12 items-start justify-center">`;
    children.forEach(child => {
        html += renderNodeHTML(child, depth);
    });
    html += `</div>`;
    return html;
}

// Helper to compute local unscaled coordinates inside the canvas container
function getLocalCoords(el, canvasEl) {
    const rect = el.getBoundingClientRect();
    const canvasRect = canvasEl.getBoundingClientRect();
    return {
        x: (rect.left - canvasRect.left) / scale,
        y: (rect.top - canvasRect.top) / scale,
        w: rect.width / scale,
        h: rect.height / scale
    };
}

function drawConnections() {
    const svg = document.getElementById('tree-connections-svg');
    const canvas = document.getElementById('canvas-container');
    if (!svg || !canvas) return;
    
    svg.innerHTML = '';
    
    // Resize SVG to match unscaled canvas container
    svg.setAttribute('width', canvas.offsetWidth);
    svg.setAttribute('height', canvas.offsetHeight);
    
    const flow = getActiveFlow();
    if (!flow) return;
    
    function traverseAndDraw(node) {
        if (!node.children || node.children.length === 0) return;
        
        const parentEl = document.getElementById(`node-${node.id}`);
        if (!parentEl) return;
        
        const parentCoords = getLocalCoords(parentEl, canvas);
        
        node.children.forEach(child => {
            const childEl = document.getElementById(`node-${child.id}`);
            if (!childEl) return;
            
            const childCoords = getLocalCoords(childEl, canvas);
            
            // Parent bottom center
            const x1 = parentCoords.x + parentCoords.w / 2;
            const y1 = parentCoords.y + parentCoords.h;
            
            // Child top center
            const x2 = childCoords.x + childCoords.w / 2;
            const y2 = childCoords.y;
            
            // Smooth vertical-horizontal-vertical S-curve
            const dy = y2 - y1;
            const cpY1 = y1 + dy * 0.45;
            const cpY2 = y2 - dy * 0.45;
            
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            const d = `M ${x1} ${y1} C ${x1} ${cpY1}, ${x2} ${cpY2}, ${x2} ${y2}`;
            
            path.setAttribute('d', d);
            path.setAttribute('stroke', '#cbd5e1'); // slate-200
            path.setAttribute('stroke-width', '2.5');
            path.setAttribute('fill', 'none');
            svg.appendChild(path);
            
            traverseAndDraw(child);
        });
    }
    
    traverseAndDraw(flow);
}

// ── Pan and Zoom ──
function updateTransform() {
    const container = document.getElementById('canvas-container');
    if (container) {
        container.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
    }
    const indicator = document.getElementById('zoom-indicator');
    if (indicator) {
        indicator.textContent = `${Math.round(scale * 100)}%`;
    }
}

function zoom(factor, isZoomIn) {
    const viewport = document.getElementById('canvas-viewport');
    if (!viewport) return;
    
    const vw = viewport.clientWidth;
    const vh = viewport.clientHeight;
    
    const vcx = vw / 2;
    const vcy = vh / 2;
    
    const ccx = (vcx - translateX) / scale;
    const ccy = (vcy - translateY) / scale;
    
    let newScale = isZoomIn ? scale + factor : scale - factor;
    newScale = Math.max(0.3, Math.min(2.5, newScale));
    
    translateX = vcx - ccx * newScale;
    translateY = vcy - ccy * newScale;
    scale = newScale;
    
    updateTransform();
}

function zoomIn() { zoom(0.1, true); }
function zoomOut() { zoom(0.1, false); }
function resetZoom() {
    scale = 1.0;
    updateTransform();
}

function centerCanvas() {
    const viewport = document.getElementById('canvas-viewport');
    const container = document.getElementById('canvas-container');
    if (!viewport || !container) return;
    
    // Clear transform temporarily to get accurate measurements
    const prevTransform = container.style.transform;
    container.style.transform = 'none';
    
    const cw = container.offsetWidth;
    const ch = container.offsetHeight;
    
    container.style.transform = prevTransform;
    
    const vw = viewport.clientWidth;
    const vh = viewport.clientHeight;
    
    scale = 1.0;
    translateX = (vw - cw) / 2;
    translateY = (vh - ch) / 2;
    
    updateTransform();
}

function toggleFullscreen() {
    const viewport = document.getElementById('canvas-viewport');
    const btnIcon = document.getElementById('fullscreen-icon');
    if (!viewport) return;
    
    const isFullscreen = viewport.classList.toggle('fullscreen');
    
    if (btnIcon) {
        if (isFullscreen) {
            // Change icon to collapse / minimize
            btnIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 9h3m0 0V6m0 3-3-3m-3 9h3m0 0v3m0-3-3 3m12-9h-3m0 0V6m0 3 3-3m3 9h-3m0 0v3m0-3 3 3" />
            `;
        } else {
            // Change icon to expand
            btnIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
            `;
        }
    }
    
    if (isFullscreen) {
        viewport.style.height = '100vh';
    } else {
        viewport.style.height = '580px';
    }
    
    // Wait for the animation/transition to finish, then center
    setTimeout(() => {
        centerCanvas();
        drawConnections();
    }, 150);
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

// Set Modal Context
function setModalContext(isRoot) {
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

    if (document.getElementById('node-flow-name-input')) document.getElementById('node-flow-name-input').value = '';
    if (document.getElementById('node-setor-name-input')) document.getElementById('node-setor-name-input').value = '';
    if (document.getElementById('node-name-input')) document.getElementById('node-name-input').value = '';

    const typeEl = document.getElementById('node-type-input');
    if (typeEl) typeEl.disabled = false;

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
        return `<button onclick="selectFlow(${f.id})" class="w-full text-left px-4 py-2 text-sm font-medium transition-colors cursor-pointer ${
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
    renderTree(true); // forceCenter = true
}

document.addEventListener('click', e => {
    const dd = document.getElementById('flow-dropdown');
    if (!dd.classList.contains('hidden') && !e.target.closest('#flow-selector-btn') && !e.target.closest('#flow-dropdown')) {
        dd.classList.add('hidden');
    }
});

// ── Setup listeners on load ──
document.addEventListener('DOMContentLoaded', () => {
    const viewport = document.getElementById('canvas-viewport');
    const container = document.getElementById('canvas-container');
    
    if (!viewport || !container) return;
    
    // Mouse drag-to-pan
    viewport.addEventListener('mousedown', (e) => {
        if (e.target.closest('.flow-node') || e.target.closest('button') || e.target.closest('input') || e.target.closest('select')) {
            return;
        }
        isDragging = true;
        viewport.classList.remove('cursor-grab');
        viewport.classList.add('cursor-grabbing');
        startX = e.clientX - translateX;
        startY = e.clientY - translateY;
    });
    
    window.addEventListener('mousemove', (e) => {
        if (!isDragging) return;
        translateX = e.clientX - startX;
        translateY = e.clientY - startY;
        updateTransform();
    });
    
    window.addEventListener('mouseup', () => {
        if (isDragging) {
            isDragging = false;
            viewport.classList.remove('cursor-grabbing');
            viewport.classList.add('cursor-grab');
        }
    });
    
    // Mouse wheel zoom
    viewport.addEventListener('wheel', (e) => {
        e.preventDefault();
        const isZoomIn = e.deltaY < 0;
        zoom(0.05, isZoomIn);
    }, { passive: false });
    
    // Window resize handler
    window.addEventListener('resize', () => {
        centerCanvas();
        drawConnections();
    });

    // Escape key listener to exit fullscreen
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const viewport = document.getElementById('canvas-viewport');
            if (viewport && viewport.classList.contains('fullscreen')) {
                toggleFullscreen();
            }
        }
    });
});

// ── Init ──
document.getElementById('flow-selector-label').textContent = (getActiveFlow().flowLabel || getActiveFlow().name || 'Fluxo de Triagem');
renderDropdown();
renderTree();
</script>
@endsection
