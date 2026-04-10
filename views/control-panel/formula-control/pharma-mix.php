<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard" class="text-decoration-none" style="color:var(--epc-accent);">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel" class="text-decoration-none" style="color:var(--epc-accent);">Control Panel</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel/formulae" class="text-decoration-none" style="color:var(--epc-accent);">Formulae</a></li>
        <li class="breadcrumb-item active text-light">Pharmaceutical Mix</li>
    </ol>
</nav>

<div class="page-header">
    <h1><i class="fas fa-pills me-2" style="color:#0dcaf0;"></i>Pharmaceutical Chemical Mix Control Sheet</h1>
    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-info" onclick="exportCSV()"><i class="fas fa-file-csv me-1"></i>Export CSV</button>
        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()"><i class="fas fa-print me-1"></i>Print</button>
        <button class="btn btn-sm btn-outline-warning" onclick="resetAll()"><i class="fas fa-undo me-1"></i>Reset</button>
    </div>
</div>

<!-- Section 1: Dilution Calculator (C1V1 = C2V2) -->
<div class="card border-0 mb-4" style="background:var(--epc-card-bg);border-top:3px solid #0dcaf0 !important;">
    <div class="card-header border-bottom border-secondary" style="background:var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="fas fa-tint me-2" style="color:#0dcaf0;"></i>1. Dilution &amp; Concentration (C₁V₁ = C₂V₂)</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-secondary" style="background:#1a1d23;border-color:rgba(255,255,255,.08);color:#9ca3af;font-size:.85rem;">
            <strong style="color:#0dcaf0;">Formula:</strong> C₁V₁ = C₂V₂ &nbsp;|&nbsp; Molarity M = n/V = (mass × 1000)/(MW × V<sub>mL</sub>) &nbsp;|&nbsp; %w/v = (g / 100mL) × 100
        </div>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label text-light">Stock Concentration (C₁)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="dil-c1" value="1000" step="0.1" oninput="calcDilution()">
                <small class="text-secondary">mg/mL or mol/L</small>
            </div>
            <div class="col-md-3">
                <label class="form-label text-light">Final Concentration (C₂)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="dil-c2" value="50" step="0.1" oninput="calcDilution()">
                <small class="text-secondary">same unit as C₁</small>
            </div>
            <div class="col-md-3">
                <label class="form-label text-light">Final Volume (V₂)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="dil-v2" value="500" step="0.1" oninput="calcDilution()">
                <small class="text-secondary">mL</small>
            </div>
            <div class="col-md-3">
                <label class="form-label text-light">Molecular Weight</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="dil-mw" value="180.16" step="0.01" oninput="calcDilution()">
                <small class="text-secondary">g/mol (e.g., Glucose = 180.16)</small>
            </div>
        </div>
        <hr style="border-color:rgba(255,255,255,.08);">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-3">
                    <div class="text-secondary small">Stock Volume (V₁)</div>
                    <div class="text-info fs-4 fw-bold" id="dil-v1">-</div>
                    <small class="text-secondary">mL required from stock</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-3">
                    <div class="text-secondary small">Diluent Volume</div>
                    <div class="text-warning fs-4 fw-bold" id="dil-vdil">-</div>
                    <small class="text-secondary">mL solvent to add</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-3">
                    <div class="text-secondary small">Dilution Factor</div>
                    <div class="text-success fs-4 fw-bold" id="dil-df">-</div>
                    <small class="text-secondary">C₁/C₂</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-3">
                    <div class="text-secondary small">Molarity</div>
                    <div class="text-light fs-4 fw-bold" id="dil-molarity">-</div>
                    <small class="text-secondary">mol/L (if C₂ in mg/mL)</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section 2: API Dosage Calculator -->
<div class="card border-0 mb-4" style="background:var(--epc-card-bg);border-top:3px solid #6f42c1 !important;">
    <div class="card-header border-bottom border-secondary" style="background:var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="fas fa-prescription-bottle me-2" style="color:#6f42c1;"></i>2. API Dosage &amp; Potency Calculator</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-secondary" style="background:#1a1d23;border-color:rgba(255,255,255,.08);color:#9ca3af;font-size:.85rem;">
            <strong style="color:#6f42c1;">Formula:</strong> Dose<sub>actual</sub> = Dose<sub>label</sub> × (100 / Potency%) &nbsp;|&nbsp; Tablets = (Dose × Patient Weight) / mg per tablet &nbsp;|&nbsp; BSA (Mosteller) = √(H × W / 3600)
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label text-light">Label Dose per kg (mg)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="api-dose" value="15" step="0.1" oninput="calcAPI()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">Patient Weight (kg)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="api-weight" value="70" step="0.1" oninput="calcAPI()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">Patient Height (cm)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="api-height" value="175" step="0.1" oninput="calcAPI()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">API Potency (%)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="api-potency" value="98.5" step="0.1" oninput="calcAPI()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">mg per Tablet</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="api-tablet" value="250" step="1" oninput="calcAPI()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">Frequency (per day)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="api-freq" value="3" step="1" oninput="calcAPI()">
            </div>
        </div>
        <hr style="border-color:rgba(255,255,255,.08);">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Total Daily Dose</div>
                    <div class="text-info fs-5 fw-bold" id="api-totalDose">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Actual Dose (Potency Adj)</div>
                    <div class="text-warning fs-5 fw-bold" id="api-actualDose">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Tablets per Dose</div>
                    <div class="text-success fs-5 fw-bold" id="api-tabPerDose">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">BSA (Mosteller)</div>
                    <div class="text-light fs-5 fw-bold" id="api-bsa">-</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section 3: Batch Formulation -->
<div class="card border-0 mb-4" style="background:var(--epc-card-bg);border-top:3px solid #d63384 !important;">
    <div class="card-header border-bottom border-secondary" style="background:var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="fas fa-flask me-2" style="color:#d63384;"></i>3. Batch Formulation &amp; Scaling</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-secondary" style="background:#1a1d23;border-color:rgba(255,255,255,.08);color:#9ca3af;font-size:.85rem;">
            <strong style="color:#d63384;">Formula:</strong> Ingredient<sub>scaled</sub> = (Target Batch / Reference Batch) × Ingredient<sub>ref</sub> &nbsp;|&nbsp; Scale Factor = Target / Reference
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label text-light">Reference Batch Size (kg)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="bat-ref" value="100" step="0.1" oninput="calcBatch()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">Target Batch Size (kg)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="bat-target" value="500" step="0.1" oninput="calcBatch()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">Expected Yield (%)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="bat-yield" value="95" step="0.1" oninput="calcBatch()">
            </div>
        </div>
        <table class="table table-dark table-sm" style="background:transparent;">
            <thead>
                <tr class="text-secondary small text-uppercase">
                    <th>Ingredient</th>
                    <th>Role</th>
                    <th>Reference (%)</th>
                    <th>Reference (kg)</th>
                    <th>Scaled (kg)</th>
                </tr>
            </thead>
            <tbody id="batchTable">
                <tr>
                    <td><input type="text" class="form-control form-control-sm bg-dark text-light border-secondary" value="Active API"></td>
                    <td><span class="badge bg-primary">API</span></td>
                    <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary bat-pct" step="0.1" value="25.0" oninput="calcBatch()"></td>
                    <td class="bat-ref-kg">-</td>
                    <td class="bat-scaled text-info fw-bold">-</td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control form-control-sm bg-dark text-light border-secondary" value="Microcrystalline Cellulose"></td>
                    <td><span class="badge bg-success">Diluent</span></td>
                    <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary bat-pct" step="0.1" value="45.0" oninput="calcBatch()"></td>
                    <td class="bat-ref-kg">-</td>
                    <td class="bat-scaled text-info fw-bold">-</td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control form-control-sm bg-dark text-light border-secondary" value="Lactose Monohydrate"></td>
                    <td><span class="badge bg-success">Diluent</span></td>
                    <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary bat-pct" step="0.1" value="20.0" oninput="calcBatch()"></td>
                    <td class="bat-ref-kg">-</td>
                    <td class="bat-scaled text-info fw-bold">-</td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control form-control-sm bg-dark text-light border-secondary" value="Croscarmellose Sodium"></td>
                    <td><span class="badge bg-info">Disintegrant</span></td>
                    <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary bat-pct" step="0.1" value="5.0" oninput="calcBatch()"></td>
                    <td class="bat-ref-kg">-</td>
                    <td class="bat-scaled text-info fw-bold">-</td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control form-control-sm bg-dark text-light border-secondary" value="Magnesium Stearate"></td>
                    <td><span class="badge bg-warning text-dark">Lubricant</span></td>
                    <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary bat-pct" step="0.1" value="2.0" oninput="calcBatch()"></td>
                    <td class="bat-ref-kg">-</td>
                    <td class="bat-scaled text-info fw-bold">-</td>
                </tr>
                <tr>
                    <td><input type="text" class="form-control form-control-sm bg-dark text-light border-secondary" value="Colloidal Silicon Dioxide"></td>
                    <td><span class="badge bg-warning text-dark">Glidant</span></td>
                    <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary bat-pct" step="0.1" value="3.0" oninput="calcBatch()"></td>
                    <td class="bat-ref-kg">-</td>
                    <td class="bat-scaled text-info fw-bold">-</td>
                </tr>
            </tbody>
            <tfoot>
                <tr style="background:rgba(214,51,132,.05);font-weight:700;">
                    <td colspan="2" class="text-light">TOTAL</td>
                    <td id="bat-totalPct" class="text-light">-</td>
                    <td id="bat-totalRef" class="text-light">-</td>
                    <td id="bat-totalScaled" class="text-info">-</td>
                </tr>
            </tfoot>
        </table>
        <div class="row g-3 mt-2">
            <div class="col-md-4">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Scale Factor</div>
                    <div class="text-warning fs-5 fw-bold" id="bat-sf">-</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Expected Output</div>
                    <div class="text-success fs-5 fw-bold" id="bat-output">-</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Estimated Waste</div>
                    <div class="text-danger fs-5 fw-bold" id="bat-waste">-</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calcDilution() {
    var c1 = parseFloat(document.getElementById('dil-c1').value) || 0;
    var c2 = parseFloat(document.getElementById('dil-c2').value) || 0;
    var v2 = parseFloat(document.getElementById('dil-v2').value) || 0;
    var mw = parseFloat(document.getElementById('dil-mw').value) || 0;

    var v1 = c1 > 0 ? (c2 * v2) / c1 : 0;
    var vdil = v2 - v1;
    var df = c2 > 0 ? c1 / c2 : 0;
    // Molarity from mg/mL: (c2 / mw) = mol/mL × 1000 = mol/L
    var molarity = mw > 0 ? (c2 / mw) : 0;

    document.getElementById('dil-v1').textContent = v1.toFixed(2) + ' mL';
    document.getElementById('dil-vdil').textContent = vdil.toFixed(2) + ' mL';
    document.getElementById('dil-df').textContent = df.toFixed(1) + ' x';
    document.getElementById('dil-molarity').textContent = molarity.toFixed(4) + ' M';
}

function calcAPI() {
    var dose = parseFloat(document.getElementById('api-dose').value) || 0;
    var weight = parseFloat(document.getElementById('api-weight').value) || 0;
    var height = parseFloat(document.getElementById('api-height').value) || 0;
    var potency = parseFloat(document.getElementById('api-potency').value) || 100;
    var tabletMg = parseFloat(document.getElementById('api-tablet').value) || 0;
    var freq = parseFloat(document.getElementById('api-freq').value) || 1;

    var totalDaily = dose * weight;
    var perDose = totalDaily / freq;
    var actualDose = perDose * (100 / potency);
    var tabsPerDose = tabletMg > 0 ? actualDose / tabletMg : 0;
    var bsa = Math.sqrt((height * weight) / 3600);

    document.getElementById('api-totalDose').textContent = totalDaily.toFixed(1) + ' mg/day';
    document.getElementById('api-actualDose').textContent = actualDose.toFixed(1) + ' mg';
    document.getElementById('api-tabPerDose').textContent = tabsPerDose.toFixed(2);
    document.getElementById('api-bsa').textContent = bsa.toFixed(2) + ' m²';
}

function calcBatch() {
    var ref = parseFloat(document.getElementById('bat-ref').value) || 0;
    var target = parseFloat(document.getElementById('bat-target').value) || 0;
    var yield_ = parseFloat(document.getElementById('bat-yield').value) || 0;
    var sf = ref > 0 ? target / ref : 0;

    var rows = document.querySelectorAll('#batchTable tr');
    var totalPct = 0, totalRef = 0, totalScaled = 0;

    rows.forEach(function(row) {
        var pctInput = row.querySelector('.bat-pct');
        if (!pctInput) return;
        var pct = parseFloat(pctInput.value) || 0;
        var refKg = (pct / 100) * ref;
        var scaledKg = (pct / 100) * target;

        row.querySelector('.bat-ref-kg').textContent = refKg.toFixed(3) + ' kg';
        row.querySelector('.bat-scaled').textContent = scaledKg.toFixed(3) + ' kg';

        totalPct += pct;
        totalRef += refKg;
        totalScaled += scaledKg;
    });

    document.getElementById('bat-totalPct').textContent = totalPct.toFixed(1) + ' %';
    document.getElementById('bat-totalRef').textContent = totalRef.toFixed(2) + ' kg';
    document.getElementById('bat-totalScaled').textContent = totalScaled.toFixed(2) + ' kg';
    document.getElementById('bat-sf').textContent = sf.toFixed(2) + ' x';
    document.getElementById('bat-output').textContent = (target * yield_ / 100).toFixed(2) + ' kg';
    document.getElementById('bat-waste').textContent = (target * (100 - yield_) / 100).toFixed(2) + ' kg';
}

function exportCSV() {
    var csv = 'EnPharChem - Pharmaceutical Chemical Mix Control Sheet\n\n';
    csv += 'Section,Parameter,Value\n';
    csv += 'Dilution,Stock Volume V1,' + document.getElementById('dil-v1').textContent + '\n';
    csv += 'Dilution,Diluent Volume,' + document.getElementById('dil-vdil').textContent + '\n';
    csv += 'Dilution,Dilution Factor,' + document.getElementById('dil-df').textContent + '\n';
    csv += 'Dilution,Molarity,' + document.getElementById('dil-molarity').textContent + '\n';
    csv += 'API,Total Daily Dose,' + document.getElementById('api-totalDose').textContent + '\n';
    csv += 'API,Actual Dose,' + document.getElementById('api-actualDose').textContent + '\n';
    csv += 'API,BSA,' + document.getElementById('api-bsa').textContent + '\n';
    csv += 'Batch,Scale Factor,' + document.getElementById('bat-sf').textContent + '\n';
    csv += 'Batch,Expected Output,' + document.getElementById('bat-output').textContent + '\n';

    var blob = new Blob([csv], {type: 'text/csv'});
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'pharma-mix-' + Date.now() + '.csv';
    a.click();
}

function resetAll() {
    if (confirm('Reset all values to defaults?')) location.reload();
}

document.addEventListener('DOMContentLoaded', function() {
    calcDilution();
    calcAPI();
    calcBatch();
});
</script>
