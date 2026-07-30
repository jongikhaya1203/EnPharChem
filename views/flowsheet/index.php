<?php
/**
 * EnPharChem - Flowsheet Process Simulator (interactive workspace)
 * Flagship process-simulation tool. Build a flowsheet on the canvas,
 * configure unit operations, and solve real mass/energy balances via the
 * server-side FlowsheetEngine.
 */
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Flowsheet Simulator</li>
    </ol>
</nav>

<div class="page-header">
    <div>
        <h1 class="mb-1"><i class="fas fa-project-diagram me-2" style="color:var(--epc-accent)"></i>Flowsheet Simulator</h1>
        <p style="color:#6c757d;font-size:.9rem;margin:0;">
            Steady-state sequential-modular process simulation &mdash; ideal VLE flash, reactors, shortcut distillation.
        </p>
    </div>
    <div>
        <span class="badge bg-secondary me-1">Engine v1.0</span>
        <span class="badge" style="background:var(--epc-primary)">Server compute</span>
    </div>
</div>

<?php
$ls = $licenseState ?? ['allowed' => true, 'reason' => 'unconfigured'];
$reason = $ls['reason'] ?? '';
?>
<?php if (!($ls['allowed'] ?? true)): ?>
<div class="alert alert-warning d-flex flex-wrap align-items-center gap-2" id="licenseBanner">
    <i class="fas fa-lock"></i>
    <span>
        <strong>License required.</strong>
        Running the Flowsheet Simulator needs a
        <strong><?= htmlspecialchars($ls['module']['license_required'] ?? 'professional') ?></strong>
        license for this module. You can build and edit flowsheets, but <strong>Run</strong> is disabled until access is granted.
    </span>
    <button class="btn btn-sm btn-warning ms-auto" id="btnRequestLicense">
        <i class="fas fa-paper-plane me-1"></i>Request access</button>
</div>
<?php elseif (in_array($reason, ['licensed', 'waived', 'admin-bypass', 'offline-license', 'enforcement-disabled'], true)): ?>
<div class="small mb-2" style="color:#6c757d;">
    <i class="fas fa-shield-alt me-1" style="color:#20c997;"></i>
    <?php if ($reason === 'licensed'): ?>Licensed &mdash; module access verified.
    <?php elseif ($reason === 'waived'): ?>License waived for this module (Module License Manager).
    <?php elseif ($reason === 'offline-license'): ?>Offline license verified<?= !empty($ls['license']['customer']) ? ' &mdash; ' . htmlspecialchars($ls['license']['customer']) : '' ?>.
    <?php elseif ($reason === 'enforcement-disabled'): ?>License enforcement disabled for this build.
    <?php else: ?>Administrator access &mdash; license check bypassed.<?php endif; ?>
</div>
<?php endif; ?>

<!-- Toolbar -->
<div class="card mb-3">
    <div class="card-body py-2 d-flex flex-wrap align-items-center gap-2">
        <button class="btn btn-primary btn-sm" id="btnRun"><i class="fas fa-play me-1"></i>Run</button>
        <button class="btn btn-outline-secondary btn-sm" id="btnClear"><i class="fas fa-trash me-1"></i>Clear</button>
        <div class="dropdown">
            <button class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-folder-open me-1"></i>Examples</button>
            <ul class="dropdown-menu" id="exampleMenu"><li><span class="dropdown-item text-muted">Loading…</span></li></ul>
        </div>
        <span class="vr mx-1"></span>
        <label class="small text-muted me-1"><i class="fas fa-atom me-1"></i>Property package</label>
        <select class="form-select form-select-sm" id="thermoModel" style="width:auto;display:inline-block;">
            <option value="peng-robinson" selected>Peng-Robinson (EOS)</option>
            <option value="ideal">Ideal / Raoult</option>
        </select>
        <span class="vr mx-1"></span>
        <button class="btn btn-outline-secondary btn-sm" id="btnSave"><i class="fas fa-save me-1"></i>Save</button>
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-list me-1"></i>Load</button>
            <ul class="dropdown-menu" id="loadMenu"></ul>
        </div>
        <span class="ms-auto small" id="hint" style="color:#6c757d;">
            Add a unit, drag to place, click an <span style="color:#0dcaf0">output ●</span> then an
            <span style="color:#ffc107">input ●</span> to connect.
        </span>
    </div>
</div>

<div class="row g-3">
    <!-- Palette -->
    <div class="col-lg-2 col-md-3">
        <div class="card">
            <div class="card-header py-2"><i class="fas fa-shapes me-2"></i>Unit Operations</div>
            <div class="card-body p-2" id="palette"></div>
        </div>
    </div>

    <!-- Canvas -->
    <div class="col-lg-7 col-md-9">
        <div class="card">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <span><i class="fas fa-drafting-compass me-2"></i>Flowsheet Canvas</span>
                <span class="small" id="canvasStatus" style="color:#6c757d;">Empty</span>
            </div>
            <div class="card-body p-0" style="overflow:auto;background:#12151d;border-radius:0 0 12px 12px;">
                <svg id="canvas" width="1100" height="560" style="display:block;min-width:100%;"></svg>
            </div>
        </div>
    </div>

    <!-- Properties -->
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header py-2"><i class="fas fa-sliders-h me-2"></i>Properties</div>
            <div class="card-body" id="props" style="min-height:200px;">
                <p class="text-muted small mb-0">Select a unit on the canvas to edit its parameters.</p>
            </div>
        </div>
    </div>
</div>

<!-- Results -->
<div id="results" class="mt-3" style="display:none;">
    <div class="row g-3">
        <div class="col-md-3">
            <div class="card h-100"><div class="card-body">
                <div class="small text-muted">Mass balance closure</div>
                <div class="h3 mb-0" id="mbClosure">—</div>
                <div class="small text-muted" id="mbDetail"></div>
            </div></div>
        </div>
        <div class="col-md-9">
            <div class="card h-100"><div class="card-body py-2">
                <div id="warnings"></div>
                <div class="small text-muted" id="engineInfo"></div>
            </div></div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header py-2"><i class="fas fa-stream me-2"></i>Stream Table</div>
        <div class="card-body table-responsive p-2">
            <table class="table table-sm table-hover mb-0" id="streamTable"></table>
        </div>
    </div>

    <div class="row g-3 mt-0">
        <div class="col-lg-7">
            <div class="card"><div class="card-header py-2"><i class="fas fa-vials me-2"></i>Stream Compositions (mole fraction)</div>
                <div class="card-body table-responsive p-2">
                    <table class="table table-sm mb-0" id="compTable"></table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card"><div class="card-header py-2"><i class="fas fa-bolt me-2"></i>Unit Energy Summary</div>
                <div class="card-body table-responsive p-2">
                    <table class="table table-sm mb-0" id="unitTable"></table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-0">
        <div class="col-lg-6"><div class="card"><div class="card-header py-2">Stream Molar Flows</div>
            <div class="card-body"><canvas id="flowChart" height="220"></canvas></div></div></div>
        <div class="col-lg-6"><div class="card"><div class="card-header py-2">Unit Duty / Work (kW)</div>
            <div class="card-body"><canvas id="energyChart" height="220"></canvas></div></div></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){
"use strict";
const BASE = "/enpharchem";
// index.php rejects any POST without a valid CSRF token; the solver and the
// licence-request call both post, so they send it in the X-CSRF-Token header.
const CSRF = (document.querySelector('meta[name="csrf-token"]') || {}).content || "";
const svg = document.getElementById("canvas");
const SVGNS = "http://www.w3.org/2000/svg";

// ---- unit-operation catalog -----------------------------------------
const CATALOG = {
    feed:       {label:"Feed",        icon:"fa-sign-in-alt", color:"#20c997", nin:0, nout:1},
    mixer:      {label:"Mixer",       icon:"fa-code-branch", color:"#6f42c1", nin:3, nout:1},
    heater:     {label:"Heater",      icon:"fa-fire",        color:"#fd7e14", nin:1, nout:1},
    cooler:     {label:"Cooler",      icon:"fa-snowflake",   color:"#0dcaf0", nin:1, nout:1},
    pump:       {label:"Pump",        icon:"fa-tint",        color:"#0d6efd", nin:1, nout:1},
    compressor: {label:"Compressor",  icon:"fa-compress-arrows-alt", color:"#6610f2", nin:1, nout:1},
    reactor:    {label:"Reactor",     icon:"fa-atom",        color:"#dc3545", nin:1, nout:1},
    flash:      {label:"Flash Drum",  icon:"fa-wine-bottle", color:"#198754", nin:1, nout:2},
    splitter:   {label:"Splitter",    icon:"fa-share-alt",   color:"#adb5bd", nin:1, nout:2},
    column:     {label:"Distillation",icon:"fa-grip-lines",  color:"#e83e8c", nin:1, nout:2},
    product:    {label:"Product",     icon:"fa-sign-out-alt",color:"#6c757d", nin:1, nout:0},
};
const BOX_W = 132, BOX_H = 60;

// ---- state ----------------------------------------------------------
let model = {units:[], streams:[]};
let seq = {u:0, s:0};
let selectedUnit = null;
let pending = null;   // {unit, port} awaiting target input
let COMPONENTS = [];  // from server

function defaultParams(type){
    switch(type){
        case "feed": return {components:{"Propane":50,"n-Butane":50}, Tc:25, P:1013};
        case "heater": return {mode:"outletT", Tc:120, dP:0};
        case "cooler": return {mode:"outletT", Tc:25, dP:0};
        case "pump": return {P:2000, eff:0.75};
        case "compressor": return {P:2000, eff:0.75, gamma:1.3};
        case "reactor": return {mode:"isothermal", Tc:250, P:1013,
            reactions:[{key:"", conversion:0.8, stoich:{}, Hrxn:0}]};
        case "flash": return {Tc:40, P:1013};
        case "splitter": return {split:0.5};
        case "column": return {lightKey:"", heavyKey:"", recLK:0.98, recHK:0.98, P:1013};
        default: return {};
    }
}

// ---- palette --------------------------------------------------------
function buildPalette(){
    const pal = document.getElementById("palette");
    pal.innerHTML = "";
    Object.entries(CATALOG).forEach(([type,c])=>{
        const b = document.createElement("button");
        b.className = "btn btn-sm w-100 mb-1 text-start d-flex align-items-center";
        b.style.cssText = "background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);color:#dee2e6;font-size:.8rem;";
        b.innerHTML = `<span style="display:inline-flex;width:26px;height:26px;border-radius:6px;align-items:center;justify-content:center;background:${c.color}22;color:${c.color};margin-right:.5rem;"><i class="fas ${c.icon}"></i></span>${c.label}`;
        b.onclick = ()=> addUnit(type);
        pal.appendChild(b);
    });
}

function addUnit(type){
    const n = model.units.length;
    const u = {id:"U"+(++seq.u), type,
        x: 60 + (n%4)*180, y: 60 + Math.floor(n/4)*110,
        params: defaultParams(type)};
    model.units.push(u);
    render(); selectUnit(u.id);
}

// ---- geometry -------------------------------------------------------
function portPos(u, side, i){
    const c = CATALOG[u.type];
    const n = side==="in" ? c.nin : c.nout;
    const x = side==="in" ? u.x : u.x + BOX_W;
    const y = u.y + BOX_H*(i+1)/(n+1);
    return {x, y};
}
function findUnit(id){ return model.units.find(u=>u.id===id); }

// ---- render ---------------------------------------------------------
function render(){
    while(svg.firstChild) svg.removeChild(svg.firstChild);

    // streams first (under boxes)
    model.streams.forEach(s=>{
        const uf = findUnit(s.from.unit), ut = findUnit(s.to.unit);
        if(!uf||!ut) return;
        const a = portPos(uf,"out",s.from.port), b = portPos(ut,"in",s.to.port);
        const midx = (a.x+b.x)/2;
        const path = el("path",{d:`M ${a.x} ${a.y} C ${midx} ${a.y}, ${midx} ${b.y}, ${b.x} ${b.y}`,
            fill:"none", stroke:"#0dcaf0", "stroke-width":2, "marker-end":"url(#arrow)", class:"epc-stream"});
        path.style.cursor="pointer";
        path.onclick = (e)=>{ e.stopPropagation(); if(confirm("Delete stream "+(s.label||s.id)+"?")) delStream(s.id); };
        svg.appendChild(path);
        const lbl = el("text",{x:midx, y:(a.y+b.y)/2 - 6, fill:"#8ab", "font-size":11, "text-anchor":"middle"});
        lbl.textContent = s.label||s.id;
        svg.appendChild(lbl);
    });

    defs();

    // boxes
    model.units.forEach(u=>{
        const c = CATALOG[u.type];
        const g = el("g",{transform:`translate(${u.x},${u.y})`, class:"epc-unit"});
        g.style.cursor="move";
        const rect = el("rect",{width:BOX_W, height:BOX_H, rx:10,
            fill:"#212836", stroke: u.id===selectedUnit? c.color : "rgba(255,255,255,.14)",
            "stroke-width": u.id===selectedUnit?2.5:1.5});
        g.appendChild(rect);
        const ic = el("text",{x:14, y:26, fill:c.color, "font-size":16, "font-family":"'Font Awesome 6 Free'", "font-weight":900});
        // fallback: use a colored dot instead of FA glyph inside SVG
        const dot = el("circle",{cx:18, cy:20, r:7, fill:c.color});
        g.appendChild(dot);
        const t1 = el("text",{x:34, y:24, fill:"#fff", "font-size":12.5, "font-weight":600});
        t1.textContent = c.label; g.appendChild(t1);
        const t2 = el("text",{x:34, y:42, fill:"#8b98a8", "font-size":10.5});
        t2.textContent = u.id + summarise(u); g.appendChild(t2);

        // drag
        g.addEventListener("mousedown", (e)=> startDrag(e,u));
        g.addEventListener("click", (e)=>{ e.stopPropagation(); if(!dragMoved) selectUnit(u.id); });
        svg.appendChild(g);

        // ports
        for(let i=0;i<c.nin;i++){ portCircle(u,"in",i,"#ffc107"); }
        for(let i=0;i<c.nout;i++){ portCircle(u,"out",i,"#0dcaf0"); }
    });

    document.getElementById("canvasStatus").textContent =
        model.units.length ? `${model.units.length} units · ${model.streams.length} streams` : "Empty";
}

function summarise(u){
    const p=u.params||{};
    if(u.type==="feed"){ const t=Object.keys(p.components||{}).length; return ` · ${t} comp @ ${p.Tc}°C`; }
    if(u.type==="heater"||u.type==="cooler") return p.mode==="duty"?` · ${p.duty||0} kW`:` · ${p.Tc}°C`;
    if(u.type==="flash") return ` · ${p.Tc}°C / ${p.P}kPa`;
    if(u.type==="column") return ` · ${p.lightKey||"LK"}/${p.heavyKey||"HK"}`;
    if(u.type==="reactor") return ` · X=${(p.reactions&&p.reactions[0]?p.reactions[0].conversion:0)}`;
    if(u.type==="splitter") return ` · ${Math.round((p.split||0)*100)}%`;
    if(u.type==="pump"||u.type==="compressor") return ` · ${p.P} kPa`;
    return "";
}

function portCircle(u, side, i, color){
    const pos = portPos(u,side,i);
    const c = el("circle",{cx:pos.x, cy:pos.y, r:6, fill:color, stroke:"#12151d", "stroke-width":2, class:"epc-port"});
    c.style.cursor="crosshair";
    if(pending && side==="in"){ c.setAttribute("r",8); }
    c.addEventListener("click",(e)=>{
        e.stopPropagation();
        if(side==="out"){ pending={unit:u.id, port:i}; setHint(`Connecting from ${u.id}… click a yellow input port.`); render(); }
        else if(pending){ makeStream(pending, {unit:u.id, port:i}); pending=null; }
    });
    svg.appendChild(c);
}

function el(tag, attrs){
    const e = document.createElementNS(SVGNS, tag);
    for(const k in attrs) e.setAttribute(k, attrs[k]);
    return e;
}
function defs(){
    const d = el("defs",{});
    const m = el("marker",{id:"arrow", viewBox:"0 0 10 10", refX:9, refY:5, markerWidth:7, markerHeight:7, orient:"auto-start-reverse"});
    const pth = el("path",{d:"M 0 0 L 10 5 L 0 10 z", fill:"#0dcaf0"});
    m.appendChild(pth); d.appendChild(m); svg.appendChild(d);
}

// ---- drag -----------------------------------------------------------
let dragU=null, dragOff=null, dragMoved=false;
function startDrag(e,u){
    if(e.target.classList.contains("epc-port")) return;
    dragU=u; dragMoved=false;
    const pt=svgPoint(e);
    dragOff={x:pt.x-u.x, y:pt.y-u.y};
    window.addEventListener("mousemove", onDrag);
    window.addEventListener("mouseup", endDrag);
}
function onDrag(e){
    if(!dragU) return;
    const pt=svgPoint(e);
    dragU.x = Math.max(0, pt.x-dragOff.x);
    dragU.y = Math.max(0, pt.y-dragOff.y);
    dragMoved=true; render();
}
function endDrag(){ dragU=null; window.removeEventListener("mousemove",onDrag); window.removeEventListener("mouseup",endDrag); }
function svgPoint(e){
    const r=svg.getBoundingClientRect();
    return {x:(e.clientX-r.left)*(svg.width.baseVal.value/r.width),
            y:(e.clientY-r.top)*(svg.height.baseVal.value/r.height)};
}
svg.addEventListener("click", ()=>{ if(pending){pending=null;setHint("");render();} });

// ---- streams --------------------------------------------------------
function makeStream(from, to){
    if(from.unit===to.unit){ setHint("Cannot connect a unit to itself."); return; }
    // one stream per input port
    model.streams = model.streams.filter(s=> !(s.to.unit===to.unit && s.to.port===to.port));
    const s = {id:"S"+(++seq.s), label:"S"+seq.s, from, to};
    model.streams.push(s); setHint(""); render();
}
function delStream(id){ model.streams=model.streams.filter(s=>s.id!==id); render(); }

// ---- selection & properties ----------------------------------------
function selectUnit(id){ selectedUnit=id; render(); renderProps(); }

function renderProps(){
    const box = document.getElementById("props");
    const u = findUnit(selectedUnit);
    if(!u){ box.innerHTML='<p class="text-muted small mb-0">Select a unit on the canvas to edit its parameters.</p>'; return; }
    const c = CATALOG[u.type];
    let h = `<div class="d-flex justify-content-between align-items-center mb-2">
        <strong style="color:${c.color}"><i class="fas ${c.icon} me-1"></i>${c.label}</strong>
        <button class="btn btn-sm btn-outline-danger py-0 px-1" id="delUnit"><i class="fas fa-trash"></i></button></div>
        <div class="small text-muted mb-2">${u.id}</div>`;
    const p=u.params;

    if(u.type==="feed") h += feedForm(p);
    else if(u.type==="heater"||u.type==="cooler") h += `
        ${sel("mode",p.mode,{outletT:"Specify outlet T",duty:"Specify duty"})}
        ${p.mode==="duty"? num("duty","Duty (kW)",p.duty||0) : num("Tc","Outlet T (°C)",p.Tc)}
        ${num("dP","Pressure drop (kPa)",p.dP||0)}`;
    else if(u.type==="pump") h += num("P","Discharge P (kPa)",p.P)+num("eff","Efficiency",p.eff);
    else if(u.type==="compressor") h += num("P","Discharge P (kPa)",p.P)+num("eff","Isentropic eff.",p.eff)+num("gamma","γ (Cp/Cv)",p.gamma);
    else if(u.type==="flash") h += num("Tc","Flash T (°C)",p.Tc)+num("P","Flash P (kPa)",p.P);
    else if(u.type==="splitter") h += num("split","Split fraction to 2nd outlet",p.split);
    else if(u.type==="column") h += columnForm(p);
    else if(u.type==="reactor") h += reactorForm(p);
    else h += `<p class="small text-muted">No parameters.</p>`;

    box.innerHTML = h;
    box.querySelector("#delUnit").onclick=()=>{ delUnit(u.id); };
    bindInputs(u);
    if(u.type==="feed") bindFeed(u);
    if(u.type==="reactor") bindReactor(u);
}

function feedForm(p){
    let rows="";
    Object.entries(p.components||{}).forEach(([name,flow],i)=>{
        rows += `<div class="input-group input-group-sm mb-1 feed-row">
            <select class="form-select feed-comp">${compOptions(name)}</select>
            <input type="number" class="form-control feed-flow" value="${flow}" step="any" style="max-width:80px">
            <button class="btn btn-outline-danger feed-del" type="button">&times;</button></div>`;
    });
    return `<label class="form-label small">Components (kmol/h)</label>${rows}
        <button class="btn btn-sm btn-outline-primary w-100 mb-2" id="feedAdd"><i class="fas fa-plus"></i> Add component</button>
        ${num("Tc","Temperature (°C)",p.Tc)}${num("P","Pressure (kPa)",p.P)}`;
}
function columnForm(p){
    return `${selComp("lightKey","Light key",p.lightKey)}
        ${selComp("heavyKey","Heavy key",p.heavyKey)}
        ${num("recLK","LK recovery to distillate",p.recLK)}
        ${num("recHK","HK recovery to bottoms",p.recHK)}
        ${num("P","Pressure (kPa)",p.P)}`;
}
function reactorForm(p){
    const rx = (p.reactions&&p.reactions[0]) || {key:"",conversion:0.8,stoich:{},Hrxn:0};
    let srows="";
    Object.entries(rx.stoich||{}).forEach(([name,coeff])=>{
        srows += `<div class="input-group input-group-sm mb-1 stoich-row">
            <select class="form-select stoich-comp">${compOptions(name)}</select>
            <input type="number" class="form-control stoich-coeff" value="${coeff}" step="any" style="max-width:80px" placeholder="±coeff">
            <button class="btn btn-outline-danger stoich-del" type="button">&times;</button></div>`;
    });
    return `${sel("mode",p.mode,{isothermal:"Isothermal",adiabatic:"Adiabatic"})}
        ${p.mode==="adiabatic"?"":num("Tc","Reactor T (°C)",p.Tc)}
        ${num("P","Pressure (kPa)",p.P)}
        <hr style="border-color:rgba(255,255,255,.1)">
        <label class="form-label small">Reaction stoichiometry <span class="text-muted">(− reactant, + product)</span></label>
        ${srows}
        <button class="btn btn-sm btn-outline-primary w-100 mb-2" id="stoichAdd"><i class="fas fa-plus"></i> Add species</button>
        ${selComp2("rxKey","Key (limiting) component",rx.key)}
        ${num("rxConv","Conversion of key (0–1)",rx.conversion)}
        ${num("rxHrxn","Heat of reaction (kJ/kmol, − exo)",rx.Hrxn)}`;
}

// small field helpers
function num(k,label,v){ return `<div class="mb-2"><label class="form-label small mb-0">${label}</label>
    <input type="number" class="form-control form-control-sm" data-k="${k}" value="${v}" step="any"></div>`; }
function sel(k,v,opts){ let o=""; for(const key in opts) o+=`<option value="${key}" ${key===v?"selected":""}>${opts[key]}</option>`;
    return `<div class="mb-2"><select class="form-select form-select-sm" data-k="${k}">${o}</select></div>`; }
function selComp(k,label,v){ return `<div class="mb-2"><label class="form-label small mb-0">${label}</label>
    <select class="form-select form-select-sm" data-k="${k}"><option value="">— select —</option>${compOptions(v)}</select></div>`; }
function selComp2(k,label,v){ return `<div class="mb-2"><label class="form-label small mb-0">${label}</label>
    <select class="form-select form-select-sm" id="${k}"><option value="">— select —</option>${compOptions(v)}</select></div>`; }
function compOptions(sel){ return COMPONENTS.map(c=>`<option value="${c.name}" ${c.name===sel?"selected":""}>${c.name}</option>`).join(""); }

function bindInputs(u){
    document.querySelectorAll('#props [data-k]').forEach(inp=>{
        inp.addEventListener("change",()=>{
            const k=inp.dataset.k;
            let val = inp.type==="number"? parseFloat(inp.value) : inp.value;
            u.params[k]=val;
            if(k==="mode") renderProps();
            render();
        });
    });
}
function bindFeed(u){
    const add=document.getElementById("feedAdd");
    if(add) add.onclick=()=>{ const c=COMPONENTS.find(x=>!(x.name in u.params.components)); u.params.components[c?c.name:"Water"]=10; renderProps(); render(); };
    document.querySelectorAll("#props .feed-row").forEach((row,idx)=>{
        const names=Object.keys(u.params.components);
        const name=names[idx];
        row.querySelector(".feed-comp").addEventListener("change",e=>{
            const flow=u.params.components[name]; delete u.params.components[name];
            u.params.components[e.target.value]=flow; renderProps(); render();
        });
        row.querySelector(".feed-flow").addEventListener("change",e=>{ u.params.components[name]=parseFloat(e.target.value)||0; render(); });
        row.querySelector(".feed-del").addEventListener("click",()=>{ delete u.params.components[name]; renderProps(); render(); });
    });
}
function bindReactor(u){
    const rx=u.params.reactions[0];
    const add=document.getElementById("stoichAdd");
    if(add) add.onclick=()=>{ const c=COMPONENTS.find(x=>!(x.name in rx.stoich)); rx.stoich[c?c.name:"Water"]=1; renderProps(); render(); };
    document.querySelectorAll("#props .stoich-row").forEach((row,idx)=>{
        const names=Object.keys(rx.stoich); const name=names[idx];
        row.querySelector(".stoich-comp").addEventListener("change",e=>{
            const co=rx.stoich[name]; delete rx.stoich[name]; rx.stoich[e.target.value]=co; renderProps(); render();
        });
        row.querySelector(".stoich-coeff").addEventListener("change",e=>{ rx.stoich[name]=parseFloat(e.target.value)||0; render(); });
        row.querySelector(".stoich-del").addEventListener("click",()=>{ delete rx.stoich[name]; renderProps(); render(); });
    });
    const key=document.getElementById("rxKey"); if(key) key.onchange=e=>{ rx.key=e.target.value; };
    const cv=document.querySelector('[data-k="rxConv"]'); // fall through—handled below
    const conv=document.querySelector('#props input[data-k="rxConv"]');
    if(conv) conv.onchange=e=>{ rx.conversion=parseFloat(e.target.value)||0; render(); };
    const hr=document.querySelector('#props input[data-k="rxHrxn"]');
    if(hr) hr.onchange=e=>{ rx.Hrxn=parseFloat(e.target.value)||0; };
}

function delUnit(id){
    model.units=model.units.filter(u=>u.id!==id);
    model.streams=model.streams.filter(s=>s.from.unit!==id && s.to.unit!==id);
    selectedUnit=null; render(); renderProps();
}

function setHint(t){ document.getElementById("hint").textContent = t || "Add a unit, drag to place, connect output → input."; }

// ---- run ------------------------------------------------------------
let flowChart, energyChart;
async function run(){
    if(!model.units.length){ alert("Add at least one unit."); return; }
    const btn=document.getElementById("btnRun");
    btn.disabled=true; btn.innerHTML='<span class="spinner-border spinner-border-sm me-1"></span>Solving…';
    try{
        const payload=Object.assign({}, model, {model:document.getElementById("thermoModel").value});
        const res=await fetch(BASE+"/flowsheet/run",{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-Token":CSRF},body:JSON.stringify(payload)});
        const data=await res.json();
        if(res.status===403 && data.needs_license){ showLicenseBlock(data); return; }
        if(!data.ok){ alert("Solver: "+(data.error||"unknown error")); return; }
        renderResults(data);
    }catch(err){ alert("Request failed: "+err.message); }
    finally{ btn.disabled=false; btn.innerHTML='<i class="fas fa-play me-1"></i>Run'; }
}

async function requestLicense(btn){
    if(btn){ btn.disabled=true; btn.innerHTML='<span class="spinner-border spinner-border-sm me-1"></span>Requesting…'; }
    try{
        const r=await fetch(BASE+"/flowsheet/request-license",{method:"POST",headers:{"X-CSRF-Token":CSRF}});
        const d=await r.json();
        if(btn){ btn.disabled=false; }
        if(d.ok){ if(btn){ btn.innerHTML='<i class="fas fa-check me-1"></i>Requested'; btn.classList.replace('btn-warning','btn-success'); } alert(d.message||"Request submitted."); }
        else{ if(btn){ btn.innerHTML='<i class="fas fa-paper-plane me-1"></i>Request access'; } alert(d.error||"Could not submit request."); }
    }catch(e){ if(btn){ btn.disabled=false; } alert("Request failed: "+e.message); }
}

function showLicenseBlock(d){
    document.getElementById("results").style.display="none";
    let bn=document.getElementById("licenseBanner");
    if(!bn){
        bn=document.createElement("div");
        bn.id="licenseBanner";
        bn.className="alert alert-warning d-flex flex-wrap align-items-center gap-2";
        const tb=document.querySelector(".card.mb-3");
        tb.parentNode.insertBefore(bn, tb);
    }
    const tier=(d.module&&d.module.tier)||"professional";
    bn.innerHTML='<i class="fas fa-lock"></i><span><strong>License required.</strong> '
        +'A <strong>'+tier+'</strong> license for this module is needed to run the simulator. '
        +'Build and edit stay available; compute is blocked server-side.</span>'
        +'<button class="btn btn-sm btn-warning ms-auto" id="btnRequestLicense2"><i class="fas fa-paper-plane me-1"></i>Request access</button>';
    bn.scrollIntoView({behavior:"smooth"});
    const b=document.getElementById("btnRequestLicense2");
    if(b) b.onclick=()=>requestLicense(b);
}

function renderResults(d){
    document.getElementById("results").style.display="block";
    document.getElementById("engineInfo").innerHTML =
        '<i class="fas fa-atom me-1" style="color:var(--epc-accent)"></i><strong>'+(d.property_package||"")+'</strong> · '
        +(d.engine||"")+" · solved "+(d.solved_at||"");
    // balance
    document.getElementById("mbClosure").textContent = (d.balance.closure||0).toFixed(1)+"%";
    document.getElementById("mbDetail").textContent = `${d.balance.mass_in} → ${d.balance.mass_out} kg/h`;
    // warnings
    const w=document.getElementById("warnings");
    w.innerHTML = (d.warnings&&d.warnings.length)
        ? d.warnings.map(x=>`<div class="alert alert-warning py-1 px-2 small mb-1"><i class="fas fa-exclamation-triangle me-1"></i>${x}</div>`).join("")
        : `<div class="text-success small mb-1"><i class="fas fa-check-circle me-1"></i>Solved with no warnings.</div>`;

    const streams=d.streams.filter(s=>s.computed);
    // stream table
    let st=`<thead><tr><th>Stream</th><th>Phase</th><th>V frac</th><th>T (°C)</th><th>P (kPa)</th><th>kmol/h</th><th>kg/h</th></tr></thead><tbody>`;
    streams.forEach(s=>{
        const ph = s.phase==="vapor"?"#0dcaf0":s.phase==="liquid"?"#20c997":"#ffc107";
        st+=`<tr><td><strong>${s.label}</strong></td><td><span style="color:${ph}">${s.phase}</span></td>
            <td>${s.vfrac.toFixed(3)}</td><td>${s.Tc.toFixed(1)}</td><td>${s.P.toFixed(0)}</td>
            <td>${s.molar.toFixed(2)}</td><td>${s.mass.toFixed(1)}</td></tr>`;
    });
    document.getElementById("streamTable").innerHTML=st+"</tbody>";

    // composition matrix
    const comps=[...new Set(streams.flatMap(s=>Object.keys(s.frac)))];
    let ct=`<thead><tr><th>Component</th>${streams.map(s=>`<th>${s.label}</th>`).join("")}</tr></thead><tbody>`;
    comps.forEach(cn=>{ ct+=`<tr><td>${cn}</td>${streams.map(s=>`<td>${(s.frac[cn]||0).toFixed(4)}</td>`).join("")}</tr>`; });
    document.getElementById("compTable").innerHTML=ct+"</tbody>";

    // unit energy
    let ut=`<thead><tr><th>Unit</th><th>Type</th><th>Duty/Work</th><th>Detail</th></tr></thead><tbody>`;
    const eLabels=[], eData=[];
    Object.entries(d.units).forEach(([id,r])=>{
        let e="", det="";
        if("duty_kW" in r){ e=r.duty_kW+" kW"; eLabels.push(id); eData.push(r.duty_kW); }
        else if("work_kW" in r){ e=r.work_kW+" kW"; eLabels.push(id); eData.push(r.work_kW); }
        else if("condenser_kW" in r){ e=`C ${r.condenser_kW} / R ${r.reboiler_kW} kW`; eLabels.push(id); eData.push(r.reboiler_kW); }
        if("T_out" in r) det=`T=${r.T_out}°? `; if("vfrac" in r) det+=`vf=${r.vfrac}`;
        ut+=`<tr><td><strong>${id}</strong></td><td>${r.type}</td><td>${e||"—"}</td><td class="small text-muted">${det}</td></tr>`;
    });
    document.getElementById("unitTable").innerHTML=ut+"</tbody>";

    // charts
    if(flowChart) flowChart.destroy();
    flowChart=new Chart(document.getElementById("flowChart"),{type:"bar",
        data:{labels:streams.map(s=>s.label),datasets:[{label:"kmol/h",data:streams.map(s=>s.molar),backgroundColor:"rgba(13,202,240,.6)"}]},
        options:{plugins:{legend:{display:false}}}});
    if(energyChart) energyChart.destroy();
    energyChart=new Chart(document.getElementById("energyChart"),{type:"bar",
        data:{labels:eLabels,datasets:[{label:"kW",data:eData,
            backgroundColor:eData.map(v=>v>=0?"rgba(253,126,20,.7)":"rgba(13,110,253,.7)")}]},
        options:{plugins:{legend:{display:false}}}});

    document.getElementById("results").scrollIntoView({behavior:"smooth"});
}

// ---- save / load (localStorage) ------------------------------------
const LS="epc_flowsheets";
function saveFlowsheet(){
    const name=prompt("Save flowsheet as:","flowsheet-"+(model.units.length)+"u"); if(!name) return;
    const all=JSON.parse(localStorage.getItem(LS)||"{}"); all[name]={model,seq}; localStorage.setItem(LS,JSON.stringify(all));
    buildLoadMenu(); setHint('Saved "'+name+'".');
}
function buildLoadMenu(){
    const menu=document.getElementById("loadMenu"); const all=JSON.parse(localStorage.getItem(LS)||"{}");
    const names=Object.keys(all);
    menu.innerHTML = names.length? names.map(n=>`<li><a class="dropdown-item d-flex justify-content-between" href="#" data-load="${n}">${n}<i class="fas fa-times text-danger ms-2" data-del="${n}"></i></a></li>`).join("")
        : '<li><span class="dropdown-item text-muted">No saved flowsheets</span></li>';
    menu.querySelectorAll("[data-load]").forEach(a=>a.onclick=(e)=>{
        e.preventDefault();
        if(e.target.dataset.del){ delete all[e.target.dataset.del]; localStorage.setItem(LS,JSON.stringify(all)); buildLoadMenu(); return; }
        const s=all[a.dataset.load]; model=s.model; seq=s.seq||seq; selectedUnit=null; render(); renderProps();
    });
}

// ---- examples -------------------------------------------------------
async function loadExamples(){
    try{
        const r=await fetch(BASE+"/flowsheet/examples"); const d=await r.json();
        const menu=document.getElementById("exampleMenu"); menu.innerHTML="";
        Object.entries(d.examples).forEach(([key,ex])=>{
            const li=document.createElement("li");
            li.innerHTML=`<a class="dropdown-item" href="#">${ex.name}</a>`;
            li.querySelector("a").onclick=(e)=>{ e.preventDefault(); loadExample(ex); };
            menu.appendChild(li);
        });
    }catch(e){}
}
function loadExample(ex){
    model={units:ex.units.map(u=>({...u})), streams:ex.streams.map(s=>({...s}))};
    // reset sequence counters beyond loaded ids
    seq.u=model.units.length+10; seq.s=model.streams.length+10;
    selectedUnit=null; pending=null; render(); renderProps(); setHint('Loaded "'+ex.name+'". Click Run to solve.');
}

// ---- init -----------------------------------------------------------
async function init(){
    buildPalette();
    try{ const r=await fetch(BASE+"/flowsheet/components"); const d=await r.json(); COMPONENTS=d.components||[]; }catch(e){}
    render();
    loadExamples(); buildLoadMenu();
    document.getElementById("btnRun").onclick=run;
    document.getElementById("btnClear").onclick=()=>{ if(confirm("Clear the flowsheet?")){ model={units:[],streams:[]}; selectedUnit=null; render(); renderProps(); document.getElementById("results").style.display="none"; } };
    document.getElementById("btnSave").onclick=saveFlowsheet;
    const rl=document.getElementById("btnRequestLicense");
    if(rl) rl.onclick=()=>requestLicense(rl);
}
init();
})();
</script>
