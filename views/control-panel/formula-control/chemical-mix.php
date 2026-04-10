<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard" class="text-decoration-none" style="color:var(--epc-accent);">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel" class="text-decoration-none" style="color:var(--epc-accent);">Control Panel</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel/formulae" class="text-decoration-none" style="color:var(--epc-accent);">Formulae</a></li>
        <li class="breadcrumb-item active text-light">Chemical Engineering Mix</li>
    </ol>
</nav>

<div class="page-header">
    <h1><i class="fas fa-flask me-2" style="color:#198754;"></i>Chemical Engineering Mix Control Sheet</h1>
    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-success" onclick="exportCSV()"><i class="fas fa-file-csv me-1"></i>Export CSV</button>
        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()"><i class="fas fa-print me-1"></i>Print</button>
        <button class="btn btn-sm btn-outline-info" onclick="resetAll()"><i class="fas fa-undo me-1"></i>Reset</button>
    </div>
</div>

<!-- Section 1: Multi-Component Mass Balance -->
<div class="card border-0 mb-4" style="background:var(--epc-card-bg);border-top:3px solid #198754 !important;">
    <div class="card-header border-bottom border-secondary" style="background:var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="fas fa-balance-scale me-2" style="color:#198754;"></i>1. Multi-Component Mass Balance &amp; Mixing</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-secondary" style="background:#1a1d23;border-color:rgba(255,255,255,.08);color:#9ca3af;font-size:.85rem;">
            <strong style="color:#198754;">Formula:</strong> Σm<sub>in</sub> = Σm<sub>out</sub> &nbsp;|&nbsp; x<sub>i</sub> = m<sub>i</sub>/Σm &nbsp;|&nbsp; y<sub>i</sub> (mol) = (x<sub>i</sub>/MW<sub>i</sub>) / Σ(x<sub>j</sub>/MW<sub>j</sub>) &nbsp;|&nbsp; MW<sub>avg</sub> = Σ(y<sub>i</sub>·MW<sub>i</sub>)
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label text-light">Total Feed Flow (kg/h)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="mb-total" value="10000" step="1" oninput="calcMassBalance()">
            </div>
            <div class="col-md-6">
                <label class="form-label text-light">Operating Pressure (bar)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="mb-pressure" value="10" step="0.1" oninput="calcMassBalance()">
            </div>
        </div>
        <table class="table table-dark table-sm" style="background:transparent;">
            <thead>
                <tr class="text-secondary small text-uppercase">
                    <th>Component</th>
                    <th>Mass Frac (x)</th>
                    <th>MW (g/mol)</th>
                    <th>Mass Flow (kg/h)</th>
                    <th>Mol Flow (kmol/h)</th>
                    <th>Mol Frac (y)</th>
                </tr>
            </thead>
            <tbody id="mbTable">
                <tr>
                    <td><input type="text" class="form-control form-control-sm bg-dark text-light border-secondary" value="Methane (CH₄)"></td>
                    <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary mb-x" step="0.01" value="0.55" oninput="calcMassBalance()"></td>
                    <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary mb-mw" step="0.01" value="16.04" oninput="calcMassBalance()"></td>
                    <td class="mb-mass text-info">-</td>
                    <td class="mb-mol text-warning">-</td>
                    <td class="mb-molFrac text-success">-</td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control form-control-sm bg-dark text-light border-secondary" value="Ethane (C₂H₆)"></td>
                    <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary mb-x" step="0.01" value="0.20" oninput="calcMassBalance()"></td>
                    <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary mb-mw" step="0.01" value="30.07" oninput="calcMassBalance()"></td>
                    <td class="mb-mass text-info">-</td>
                    <td class="mb-mol text-warning">-</td>
                    <td class="mb-molFrac text-success">-</td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control form-control-sm bg-dark text-light border-secondary" value="Propane (C₃H₈)"></td>
                    <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary mb-x" step="0.01" value="0.12" oninput="calcMassBalance()"></td>
                    <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary mb-mw" step="0.01" value="44.10" oninput="calcMassBalance()"></td>
                    <td class="mb-mass text-info">-</td>
                    <td class="mb-mol text-warning">-</td>
                    <td class="mb-molFrac text-success">-</td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control form-control-sm bg-dark text-light border-secondary" value="n-Butane (C₄H₁₀)"></td>
                    <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary mb-x" step="0.01" value="0.08" oninput="calcMassBalance()"></td>
                    <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary mb-mw" step="0.01" value="58.12" oninput="calcMassBalance()"></td>
                    <td class="mb-mass text-info">-</td>
                    <td class="mb-mol text-warning">-</td>
                    <td class="mb-molFrac text-success">-</td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control form-control-sm bg-dark text-light border-secondary" value="CO₂"></td>
                    <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary mb-x" step="0.01" value="0.03" oninput="calcMassBalance()"></td>
                    <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary mb-mw" step="0.01" value="44.01" oninput="calcMassBalance()"></td>
                    <td class="mb-mass text-info">-</td>
                    <td class="mb-mol text-warning">-</td>
                    <td class="mb-molFrac text-success">-</td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control form-control-sm bg-dark text-light border-secondary" value="N₂"></td>
                    <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary mb-x" step="0.01" value="0.02" oninput="calcMassBalance()"></td>
                    <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary mb-mw" step="0.01" value="28.01" oninput="calcMassBalance()"></td>
                    <td class="mb-mass text-info">-</td>
                    <td class="mb-mol text-warning">-</td>
                    <td class="mb-molFrac text-success">-</td>
                </tr>
            </tbody>
            <tfoot>
                <tr style="background:rgba(25,135,84,.05);font-weight:700;">
                    <td class="text-light">TOTAL</td>
                    <td id="mb-totalX" class="text-light">-</td>
                    <td id="mb-avgMW" class="text-success">-</td>
                    <td id="mb-totalMass" class="text-info">-</td>
                    <td id="mb-totalMol" class="text-warning">-</td>
                    <td class="text-light">1.000</td>
                </tr>
            </tfoot>
        </table>
        <div id="mbWarning" style="display:none;" class="alert alert-warning py-2 small"></div>
    </div>
</div>

<!-- Section 2: Reactor Design (Arrhenius) -->
<div class="card border-0 mb-4" style="background:var(--epc-card-bg);border-top:3px solid #0d6efd !important;">
    <div class="card-header border-bottom border-secondary" style="background:var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="fas fa-atom me-2" style="color:#0d6efd;"></i>2. Reaction Kinetics &amp; Reactor Sizing</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-secondary" style="background:#1a1d23;border-color:rgba(255,255,255,.08);color:#9ca3af;font-size:.85rem;">
            <strong style="color:#0d6efd;">Arrhenius:</strong> k = A·exp(-Ea/RT) &nbsp;|&nbsp; <strong>CSTR:</strong> V = F<sub>A0</sub>·X / (-r<sub>A</sub>) &nbsp;|&nbsp; <strong>PFR:</strong> V = F<sub>A0</sub>·∫(dX/(-r<sub>A</sub>))
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label text-light">Pre-exp Factor A (1/s)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="kin-A" value="1.5e10" step="any" oninput="calcKinetics()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">Activation Energy Ea (kJ/mol)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="kin-Ea" value="85" step="0.1" oninput="calcKinetics()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">Temperature (°C)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="kin-T" value="250" step="1" oninput="calcKinetics()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">Initial Concentration C<sub>A0</sub> (mol/L)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="kin-CA0" value="2.0" step="0.01" oninput="calcKinetics()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">Conversion X (%)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="kin-X" value="85" step="1" min="0" max="99" oninput="calcKinetics()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">Feed Rate F<sub>A0</sub> (mol/s)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="kin-FA0" value="50" step="0.1" oninput="calcKinetics()">
            </div>
        </div>
        <hr style="border-color:rgba(255,255,255,.08);">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Rate Constant k</div>
                    <div class="text-info fs-6 fw-bold" id="kin-k">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Rate (-r<sub>A</sub>)</div>
                    <div class="text-warning fs-6 fw-bold" id="kin-rate">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">CSTR Volume</div>
                    <div class="text-success fs-6 fw-bold" id="kin-vcstr">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">PFR Volume (1st order)</div>
                    <div class="text-success fs-6 fw-bold" id="kin-vpfr">-</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section 3: Heat Transfer -->
<div class="card border-0 mb-4" style="background:var(--epc-card-bg);border-top:3px solid #dc3545 !important;">
    <div class="card-header border-bottom border-secondary" style="background:var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="fas fa-temperature-high me-2" style="color:#dc3545;"></i>3. Heat Exchanger LMTD &amp; Sizing</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-secondary" style="background:#1a1d23;border-color:rgba(255,255,255,.08);color:#9ca3af;font-size:.85rem;">
            <strong style="color:#dc3545;">Formula:</strong> Q = m&#775;·C<sub>p</sub>·ΔT &nbsp;|&nbsp; LMTD = (ΔT₁ - ΔT₂) / ln(ΔT₁/ΔT₂) &nbsp;|&nbsp; A = Q / (U·LMTD·F)
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label text-light">Hot In (°C)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="ht-Thi" value="180" oninput="calcHeat()">
            </div>
            <div class="col-md-3">
                <label class="form-label text-light">Hot Out (°C)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="ht-Tho" value="90" oninput="calcHeat()">
            </div>
            <div class="col-md-3">
                <label class="form-label text-light">Cold In (°C)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="ht-Tci" value="25" oninput="calcHeat()">
            </div>
            <div class="col-md-3">
                <label class="form-label text-light">Cold Out (°C)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="ht-Tco" value="75" oninput="calcHeat()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">Hot Fluid Flow (kg/s)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="ht-mh" value="5.0" step="0.1" oninput="calcHeat()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">Hot Fluid Cp (kJ/kg·K)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="ht-cp" value="2.1" step="0.01" oninput="calcHeat()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">Overall U (W/m²·K)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="ht-u" value="500" step="1" oninput="calcHeat()">
            </div>
        </div>
        <hr style="border-color:rgba(255,255,255,.08);">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Heat Duty Q</div>
                    <div class="text-danger fs-5 fw-bold" id="ht-Q">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">LMTD Counter</div>
                    <div class="text-warning fs-5 fw-bold" id="ht-lmtd">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Required Area</div>
                    <div class="text-success fs-5 fw-bold" id="ht-area">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Temp Approach</div>
                    <div class="text-info fs-5 fw-bold" id="ht-approach">-</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section 4: Raoult's Law Vapor-Liquid Equilibrium -->
<div class="card border-0 mb-4" style="background:var(--epc-card-bg);border-top:3px solid #6f42c1 !important;">
    <div class="card-header border-bottom border-secondary" style="background:var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="fas fa-wind me-2" style="color:#6f42c1;"></i>4. Raoult's Law (VLE) - Bubble Point</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-secondary" style="background:#1a1d23;border-color:rgba(255,255,255,.08);color:#9ca3af;font-size:.85rem;">
            <strong style="color:#6f42c1;">Formula:</strong> P<sub>total</sub> = Σ(x<sub>i</sub> · P<sub>i</sub><sup>sat</sup>) &nbsp;|&nbsp; y<sub>i</sub> = (x<sub>i</sub> · P<sub>i</sub><sup>sat</sup>) / P<sub>total</sub> &nbsp;|&nbsp; Antoine: log P<sup>sat</sup> = A - B/(T+C)
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label text-light">Temperature (°C)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="vle-T" value="80" step="1" oninput="calcVLE()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">x₁ - Benzene (mol frac)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="vle-x1" value="0.4" step="0.01" min="0" max="1" oninput="calcVLE()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">x₂ - Toluene (mol frac)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="vle-x2" value="0.6" step="0.01" min="0" max="1" oninput="calcVLE()">
            </div>
        </div>
        <div class="row g-3 mt-2">
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">P₁<sup>sat</sup> Benzene</div>
                    <div class="text-info fs-6 fw-bold" id="vle-p1">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">P₂<sup>sat</sup> Toluene</div>
                    <div class="text-info fs-6 fw-bold" id="vle-p2">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Total Pressure</div>
                    <div class="text-warning fs-6 fw-bold" id="vle-ptot">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Relative Volatility α</div>
                    <div class="text-success fs-6 fw-bold" id="vle-alpha">-</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">y₁ - Vapor Benzene</div>
                    <div class="text-danger fs-5 fw-bold" id="vle-y1">-</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">y₂ - Vapor Toluene</div>
                    <div class="text-danger fs-5 fw-bold" id="vle-y2">-</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calcMassBalance() {
    var total = parseFloat(document.getElementById('mb-total').value) || 0;
    var xs = document.querySelectorAll('.mb-x');
    var mws = document.querySelectorAll('.mb-mw');
    var rows = document.querySelectorAll('#mbTable tr');

    var totalX = 0, totalMol = 0, avgMW = 0;
    var data = [];

    for (var i = 0; i < xs.length; i++) {
        var x = parseFloat(xs[i].value) || 0;
        var mw = parseFloat(mws[i].value) || 1;
        var mass = x * total;
        var mol = mass / mw;
        totalX += x;
        totalMol += mol;
        data.push({x:x, mw:mw, mass:mass, mol:mol});
    }

    data.forEach(function(d, i) {
        var molFrac = totalMol > 0 ? d.mol / totalMol : 0;
        avgMW += molFrac * d.mw;
        var row = rows[i];
        row.querySelector('.mb-mass').textContent = d.mass.toFixed(1) + ' kg/h';
        row.querySelector('.mb-mol').textContent = d.mol.toFixed(2) + ' kmol/h';
        row.querySelector('.mb-molFrac').textContent = molFrac.toFixed(4);
    });

    document.getElementById('mb-totalX').textContent = totalX.toFixed(3);
    document.getElementById('mb-avgMW').textContent = avgMW.toFixed(2);
    document.getElementById('mb-totalMass').textContent = total.toFixed(0) + ' kg/h';
    document.getElementById('mb-totalMol').textContent = totalMol.toFixed(2) + ' kmol/h';

    var warn = document.getElementById('mbWarning');
    if (Math.abs(totalX - 1.0) > 0.001) {
        warn.style.display = 'block';
        warn.textContent = '⚠ Mass fractions sum to ' + totalX.toFixed(3) + ' (should equal 1.000)';
    } else { warn.style.display = 'none'; }
}

function calcKinetics() {
    var A = parseFloat(document.getElementById('kin-A').value) || 0;
    var Ea = parseFloat(document.getElementById('kin-Ea').value) || 0;
    var Tc = parseFloat(document.getElementById('kin-T').value) || 0;
    var CA0 = parseFloat(document.getElementById('kin-CA0').value) || 0;
    var X = parseFloat(document.getElementById('kin-X').value) / 100 || 0;
    var FA0 = parseFloat(document.getElementById('kin-FA0').value) || 0;

    var T = Tc + 273.15;
    var R = 8.314; // J/(mol·K)
    var k = A * Math.exp(-(Ea * 1000) / (R * T));
    var CA = CA0 * (1 - X);
    var rate = k * CA; // 1st order
    var Vcstr = rate > 0 ? (FA0 * X / rate) : 0;
    // PFR 1st order: V = (FA0 / (k*CA0)) * (-ln(1-X))
    var Vpfr = (k > 0 && CA0 > 0 && X < 1) ? (FA0 / (k * CA0)) * (-Math.log(1 - X)) : 0;

    document.getElementById('kin-k').textContent = k.toExponential(3) + ' 1/s';
    document.getElementById('kin-rate').textContent = rate.toExponential(3) + ' mol/L·s';
    document.getElementById('kin-vcstr').textContent = Vcstr.toFixed(2) + ' L';
    document.getElementById('kin-vpfr').textContent = Vpfr.toFixed(2) + ' L';
}

function calcHeat() {
    var Thi = parseFloat(document.getElementById('ht-Thi').value) || 0;
    var Tho = parseFloat(document.getElementById('ht-Tho').value) || 0;
    var Tci = parseFloat(document.getElementById('ht-Tci').value) || 0;
    var Tco = parseFloat(document.getElementById('ht-Tco').value) || 0;
    var mh = parseFloat(document.getElementById('ht-mh').value) || 0;
    var cp = parseFloat(document.getElementById('ht-cp').value) || 0;
    var U = parseFloat(document.getElementById('ht-u').value) || 0;

    var Q = mh * cp * (Thi - Tho); // kW
    var dT1 = Thi - Tco;
    var dT2 = Tho - Tci;
    var lmtd = 0;
    if (dT1 > 0 && dT2 > 0 && dT1 !== dT2) {
        lmtd = (dT1 - dT2) / Math.log(dT1 / dT2);
    } else if (dT1 === dT2) {
        lmtd = dT1;
    }
    var area = (U > 0 && lmtd > 0) ? (Q * 1000 / (U * lmtd)) : 0;
    var approach = Math.min(dT1, dT2);

    document.getElementById('ht-Q').textContent = Q.toFixed(1) + ' kW';
    document.getElementById('ht-lmtd').textContent = lmtd.toFixed(2) + ' °C';
    document.getElementById('ht-area').textContent = area.toFixed(2) + ' m²';
    document.getElementById('ht-approach').textContent = approach.toFixed(1) + ' °C';
}

function calcVLE() {
    var T = parseFloat(document.getElementById('vle-T').value) || 0;
    var x1 = parseFloat(document.getElementById('vle-x1').value) || 0;
    var x2 = parseFloat(document.getElementById('vle-x2').value) || 0;

    // Antoine coefficients (mmHg, °C)
    // Benzene: A=6.90565, B=1211.033, C=220.79
    // Toluene: A=6.95464, B=1344.800, C=219.482
    var logP1 = 6.90565 - (1211.033 / (T + 220.79));
    var logP2 = 6.95464 - (1344.800 / (T + 219.482));
    var P1sat = Math.pow(10, logP1); // mmHg
    var P2sat = Math.pow(10, logP2);

    // Convert to bar (760 mmHg = 1.01325 bar)
    var P1bar = P1sat / 750.062;
    var P2bar = P2sat / 750.062;

    var Ptotal = x1 * P1bar + x2 * P2bar;
    var y1 = Ptotal > 0 ? (x1 * P1bar) / Ptotal : 0;
    var y2 = Ptotal > 0 ? (x2 * P2bar) / Ptotal : 0;
    var alpha = P2bar > 0 ? P1bar / P2bar : 0;

    document.getElementById('vle-p1').textContent = P1bar.toFixed(3) + ' bar';
    document.getElementById('vle-p2').textContent = P2bar.toFixed(3) + ' bar';
    document.getElementById('vle-ptot').textContent = Ptotal.toFixed(3) + ' bar';
    document.getElementById('vle-alpha').textContent = alpha.toFixed(2);
    document.getElementById('vle-y1').textContent = y1.toFixed(4);
    document.getElementById('vle-y2').textContent = y2.toFixed(4);
}

function exportCSV() {
    var csv = 'EnPharChem - Chemical Engineering Mix Control Sheet\n\n';
    csv += 'Section,Parameter,Value\n';
    csv += 'Mass Balance,Average MW,' + document.getElementById('mb-avgMW').textContent + '\n';
    csv += 'Mass Balance,Total Mol Flow,' + document.getElementById('mb-totalMol').textContent + '\n';
    csv += 'Kinetics,Rate Constant k,' + document.getElementById('kin-k').textContent + '\n';
    csv += 'Kinetics,CSTR Volume,' + document.getElementById('kin-vcstr').textContent + '\n';
    csv += 'Kinetics,PFR Volume,' + document.getElementById('kin-vpfr').textContent + '\n';
    csv += 'Heat Transfer,Heat Duty Q,' + document.getElementById('ht-Q').textContent + '\n';
    csv += 'Heat Transfer,LMTD,' + document.getElementById('ht-lmtd').textContent + '\n';
    csv += 'Heat Transfer,Required Area,' + document.getElementById('ht-area').textContent + '\n';
    csv += 'VLE,Total Pressure,' + document.getElementById('vle-ptot').textContent + '\n';
    csv += 'VLE,Relative Volatility,' + document.getElementById('vle-alpha').textContent + '\n';

    var blob = new Blob([csv], {type: 'text/csv'});
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'chemical-mix-' + Date.now() + '.csv';
    a.click();
}

function resetAll() {
    if (confirm('Reset all values to defaults?')) location.reload();
}

document.addEventListener('DOMContentLoaded', function() {
    calcMassBalance();
    calcKinetics();
    calcHeat();
    calcVLE();
});
</script>
