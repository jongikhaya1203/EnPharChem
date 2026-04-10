<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard" class="text-decoration-none" style="color:var(--epc-accent);">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel" class="text-decoration-none" style="color:var(--epc-accent);">Control Panel</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel/formulae" class="text-decoration-none" style="color:var(--epc-accent);">Formulae</a></li>
        <li class="breadcrumb-item active text-light">Energy Chemical Mix</li>
    </ol>
</nav>

<div class="page-header">
    <h1><i class="fas fa-fire me-2" style="color:#fd7e14;"></i>Energy Chemical Mix Control Sheet</h1>
    <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-warning" onclick="exportCSV()"><i class="fas fa-file-csv me-1"></i>Export CSV</button>
        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()"><i class="fas fa-print me-1"></i>Print</button>
        <button class="btn btn-sm btn-outline-info" onclick="resetAll()"><i class="fas fa-undo me-1"></i>Reset</button>
    </div>
</div>

<!-- Section 1: Fuel Blend Calculator -->
<div class="card border-0 mb-4" style="background:var(--epc-card-bg);border-top:3px solid #fd7e14 !important;">
    <div class="card-header border-bottom border-secondary" style="background:var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="fas fa-gas-pump me-2" style="color:#fd7e14;"></i>1. Fuel Blend &amp; Heating Value Calculator</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-secondary" style="background:#1a1d23;border-color:rgba(255,255,255,.08);color:#9ca3af;font-size:.85rem;">
            <strong style="color:#fd7e14;">Formula:</strong> LHV<sub>blend</sub> = Σ(x<sub>i</sub> × LHV<sub>i</sub>) &nbsp;|&nbsp; HHV = LHV + (2.442 × 8.937 × H%) &nbsp;|&nbsp; Density<sub>blend</sub> = Σ(x<sub>i</sub> × ρ<sub>i</sub>)
        </div>
        <div class="row g-3">
            <div class="col-md-7">
                <table class="table table-dark table-sm" style="background:transparent;">
                    <thead>
                        <tr class="text-secondary small text-uppercase">
                            <th>Fuel Component</th>
                            <th>Mass Fraction</th>
                            <th>LHV (MJ/kg)</th>
                            <th>Density (kg/m³)</th>
                            <th>H content (%)</th>
                        </tr>
                    </thead>
                    <tbody id="fuelTable">
                        <tr>
                            <td><input type="text" class="form-control form-control-sm bg-dark text-light border-secondary" value="Natural Gas"></td>
                            <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary fuel-x" step="0.01" value="0.40" oninput="calcFuel()"></td>
                            <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary fuel-lhv" step="0.1" value="49.1" oninput="calcFuel()"></td>
                            <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary fuel-rho" step="0.01" value="0.72" oninput="calcFuel()"></td>
                            <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary fuel-h" step="0.1" value="25.0" oninput="calcFuel()"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control form-control-sm bg-dark text-light border-secondary" value="Diesel #2"></td>
                            <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary fuel-x" step="0.01" value="0.35" oninput="calcFuel()"></td>
                            <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary fuel-lhv" step="0.1" value="42.8" oninput="calcFuel()"></td>
                            <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary fuel-rho" step="0.01" value="832" oninput="calcFuel()"></td>
                            <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary fuel-h" step="0.1" value="13.2" oninput="calcFuel()"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control form-control-sm bg-dark text-light border-secondary" value="Heavy Fuel Oil"></td>
                            <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary fuel-x" step="0.01" value="0.15" oninput="calcFuel()"></td>
                            <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary fuel-lhv" step="0.1" value="40.5" oninput="calcFuel()"></td>
                            <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary fuel-rho" step="0.01" value="980" oninput="calcFuel()"></td>
                            <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary fuel-h" step="0.1" value="11.5" oninput="calcFuel()"></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control form-control-sm bg-dark text-light border-secondary" value="Biodiesel B20"></td>
                            <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary fuel-x" step="0.01" value="0.10" oninput="calcFuel()"></td>
                            <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary fuel-lhv" step="0.1" value="39.0" oninput="calcFuel()"></td>
                            <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary fuel-rho" step="0.01" value="860" oninput="calcFuel()"></td>
                            <td><input type="number" class="form-control form-control-sm bg-dark text-light border-secondary fuel-h" step="0.1" value="12.0" oninput="calcFuel()"></td>
                        </tr>
                    </tbody>
                </table>
                <div id="fuelWarning" style="display:none;" class="alert alert-warning py-2 small"></div>
            </div>
            <div class="col-md-5">
                <div class="card bg-dark border-secondary">
                    <div class="card-header" style="background:rgba(253,126,20,.1);border-color:rgba(253,126,20,.2);">
                        <h6 class="text-warning mb-0"><i class="fas fa-chart-line me-1"></i>Calculated Results</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-dark table-sm mb-0">
                            <tbody>
                                <tr><td style="color:#9ca3af;">Total Mass Fraction</td><td class="text-end" id="r-totalX">1.00</td></tr>
                                <tr><td style="color:#9ca3af;">Blend LHV</td><td class="text-end text-warning" id="r-blendLHV">-</td></tr>
                                <tr><td style="color:#9ca3af;">Blend HHV</td><td class="text-end text-warning" id="r-blendHHV">-</td></tr>
                                <tr><td style="color:#9ca3af;">Blend Density</td><td class="text-end" id="r-blendRho">-</td></tr>
                                <tr><td style="color:#9ca3af;">Avg H Content</td><td class="text-end" id="r-avgH">-</td></tr>
                                <tr><td style="color:#9ca3af;">Energy per m³</td><td class="text-end text-info" id="r-energyVol">-</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section 2: Combustion Stoichiometry -->
<div class="card border-0 mb-4" style="background:var(--epc-card-bg);border-top:3px solid #dc3545 !important;">
    <div class="card-header border-bottom border-secondary" style="background:var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="fas fa-atom me-2" style="color:#dc3545;"></i>2. Combustion Stoichiometry (CxHy + O2)</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-secondary" style="background:#1a1d23;border-color:rgba(255,255,255,.08);color:#9ca3af;font-size:.85rem;">
            <strong style="color:#dc3545;">Formula:</strong> C<sub>x</sub>H<sub>y</sub> + (x + y/4)O<sub>2</sub> → xCO<sub>2</sub> + (y/2)H<sub>2</sub>O &nbsp;|&nbsp; AFR<sub>stoich</sub> = 4.76 × (x + y/4) × 28.97 / (12x + y)
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label text-light">Carbon atoms (x)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="comb-x" step="1" value="8" min="1" oninput="calcComb()">
                <small class="text-secondary">e.g., Octane = 8, Methane = 1, Propane = 3</small>
            </div>
            <div class="col-md-6">
                <label class="form-label text-light">Hydrogen atoms (y)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="comb-y" step="1" value="18" min="1" oninput="calcComb()">
                <small class="text-secondary">e.g., Octane = 18, Methane = 4, Propane = 8</small>
            </div>
            <div class="col-md-6">
                <label class="form-label text-light">Excess Air (%)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="comb-excess" step="1" value="15" oninput="calcComb()">
            </div>
            <div class="col-md-6">
                <label class="form-label text-light">Fuel Mass Flow (kg/h)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="comb-mass" step="1" value="1000" oninput="calcComb()">
            </div>
        </div>
        <hr style="border-color:rgba(255,255,255,.08);">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">MW Fuel (g/mol)</div>
                    <div class="text-light fs-5 fw-bold" id="rc-mw">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Stoich AFR</div>
                    <div class="text-warning fs-5 fw-bold" id="rc-afr">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Actual AFR</div>
                    <div class="text-info fs-5 fw-bold" id="rc-afractual">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Air Flow (kg/h)</div>
                    <div class="text-success fs-5 fw-bold" id="rc-air">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">CO₂ Produced</div>
                    <div class="text-warning fs-5 fw-bold" id="rc-co2">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">H₂O Produced</div>
                    <div class="text-info fs-5 fw-bold" id="rc-h2o">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">O₂ Consumed</div>
                    <div class="text-danger fs-5 fw-bold" id="rc-o2">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Flue Gas Total</div>
                    <div class="text-light fs-5 fw-bold" id="rc-flue">-</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section 3: Energy Balance & Thermal Efficiency -->
<div class="card border-0 mb-4" style="background:var(--epc-card-bg);border-top:3px solid #ffc107 !important;">
    <div class="card-header border-bottom border-secondary" style="background:var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="fas fa-bolt me-2" style="color:#ffc107;"></i>3. Energy Balance &amp; Thermal Efficiency</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-secondary" style="background:#1a1d23;border-color:rgba(255,255,255,.08);color:#9ca3af;font-size:.85rem;">
            <strong style="color:#ffc107;">Formula:</strong> Q<sub>in</sub> = m&#775;<sub>fuel</sub> × LHV &nbsp;|&nbsp; η<sub>thermal</sub> = W<sub>net</sub> / Q<sub>in</sub> &nbsp;|&nbsp; Q<sub>loss</sub> = m&#775;<sub>flue</sub> × C<sub>p</sub> × ΔT
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label text-light">Fuel Flow (kg/h)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="eb-mfuel" value="1000" oninput="calcEB()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">LHV (MJ/kg)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="eb-lhv" value="44.5" step="0.1" oninput="calcEB()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">Net Work Output (kW)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="eb-wnet" value="4500" oninput="calcEB()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">Flue Gas Temp (°C)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="eb-tflue" value="185" oninput="calcEB()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">Ambient Temp (°C)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="eb-tamb" value="25" oninput="calcEB()">
            </div>
            <div class="col-md-4">
                <label class="form-label text-light">Flue Gas Cp (kJ/kg·K)</label>
                <input type="number" class="form-control bg-dark text-light border-secondary" id="eb-cp" value="1.05" step="0.01" oninput="calcEB()">
            </div>
        </div>
        <hr style="border-color:rgba(255,255,255,.08);">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Heat Input</div>
                    <div class="text-warning fs-5 fw-bold" id="eb-qin">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Thermal Efficiency</div>
                    <div class="text-success fs-5 fw-bold" id="eb-eta">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Flue Gas Loss</div>
                    <div class="text-danger fs-5 fw-bold" id="eb-qloss">-</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-center p-2">
                    <div class="text-secondary small">Heat Rate</div>
                    <div class="text-info fs-5 fw-bold" id="eb-hr">-</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calcFuel() {
    var xs = document.querySelectorAll('.fuel-x');
    var lhvs = document.querySelectorAll('.fuel-lhv');
    var rhos = document.querySelectorAll('.fuel-rho');
    var hs = document.querySelectorAll('.fuel-h');

    var totalX = 0, blendLHV = 0, blendRho = 0, avgH = 0;
    for (var i = 0; i < xs.length; i++) {
        var x = parseFloat(xs[i].value) || 0;
        var lhv = parseFloat(lhvs[i].value) || 0;
        var rho = parseFloat(rhos[i].value) || 0;
        var h = parseFloat(hs[i].value) || 0;
        totalX += x;
        blendLHV += x * lhv;
        blendRho += x * rho;
        avgH += x * h;
    }

    document.getElementById('r-totalX').textContent = totalX.toFixed(3);
    document.getElementById('r-blendLHV').textContent = blendLHV.toFixed(2) + ' MJ/kg';
    var hhv = blendLHV + (2.442 * 8.937 * avgH / 100);
    document.getElementById('r-blendHHV').textContent = hhv.toFixed(2) + ' MJ/kg';
    document.getElementById('r-blendRho').textContent = blendRho.toFixed(2) + ' kg/m³';
    document.getElementById('r-avgH').textContent = avgH.toFixed(2) + ' %';
    document.getElementById('r-energyVol').textContent = (blendLHV * blendRho / 1000).toFixed(2) + ' GJ/m³';

    var warn = document.getElementById('fuelWarning');
    if (Math.abs(totalX - 1.0) > 0.001) {
        warn.style.display = 'block';
        warn.textContent = '⚠ Mass fractions sum to ' + totalX.toFixed(3) + ' (should equal 1.000)';
    } else {
        warn.style.display = 'none';
    }
}

function calcComb() {
    var x = parseFloat(document.getElementById('comb-x').value) || 0;
    var y = parseFloat(document.getElementById('comb-y').value) || 0;
    var excess = parseFloat(document.getElementById('comb-excess').value) || 0;
    var mfuel = parseFloat(document.getElementById('comb-mass').value) || 0;

    var mwFuel = (12.011 * x) + (1.008 * y);
    var o2Mol = x + y/4;
    var mwO2 = 32.0, mwAir = 28.97, mwCO2 = 44.01, mwH2O = 18.015;
    // Stoichiometric AFR: 4.76 mol air per mol O2 × 28.97 / MW_fuel
    var afrStoich = (4.76 * o2Mol * mwAir) / mwFuel;
    var afrActual = afrStoich * (1 + excess/100);

    var mAir = mfuel * afrActual;
    var molFuel = mfuel / mwFuel;
    var mCO2 = molFuel * x * mwCO2;
    var mH2O = molFuel * (y/2) * mwH2O;
    var mO2 = molFuel * o2Mol * mwO2;
    var mFlue = mAir + mfuel; // conservation of mass

    document.getElementById('rc-mw').textContent = mwFuel.toFixed(2);
    document.getElementById('rc-afr').textContent = afrStoich.toFixed(2);
    document.getElementById('rc-afractual').textContent = afrActual.toFixed(2);
    document.getElementById('rc-air').textContent = mAir.toFixed(0);
    document.getElementById('rc-co2').textContent = mCO2.toFixed(0) + ' kg/h';
    document.getElementById('rc-h2o').textContent = mH2O.toFixed(0) + ' kg/h';
    document.getElementById('rc-o2').textContent = mO2.toFixed(0) + ' kg/h';
    document.getElementById('rc-flue').textContent = mFlue.toFixed(0) + ' kg/h';
}

function calcEB() {
    var mfuel = parseFloat(document.getElementById('eb-mfuel').value) || 0;
    var lhv = parseFloat(document.getElementById('eb-lhv').value) || 0;
    var wnet = parseFloat(document.getElementById('eb-wnet').value) || 0;
    var tflue = parseFloat(document.getElementById('eb-tflue').value) || 0;
    var tamb = parseFloat(document.getElementById('eb-tamb').value) || 0;
    var cp = parseFloat(document.getElementById('eb-cp').value) || 0;

    // Q_in = m_fuel * LHV (convert kg/h * MJ/kg → kW)
    var qin = (mfuel * lhv * 1000) / 3600; // kW
    var eta = qin > 0 ? (wnet / qin) * 100 : 0;
    // Flue gas loss (assume 17× fuel flow for typical air)
    var mflue = mfuel * 17;
    var qloss = (mflue * cp * (tflue - tamb)) / 3600; // kW
    var heatRate = wnet > 0 ? (qin * 3600 / wnet) : 0; // kJ/kWh

    document.getElementById('eb-qin').textContent = qin.toFixed(0) + ' kW';
    document.getElementById('eb-eta').textContent = eta.toFixed(1) + ' %';
    document.getElementById('eb-qloss').textContent = qloss.toFixed(0) + ' kW';
    document.getElementById('eb-hr').textContent = heatRate.toFixed(0) + ' kJ/kWh';
}

function exportCSV() {
    var csv = 'EnPharChem - Energy Chemical Mix Control Sheet\n\n';
    csv += 'Section,Parameter,Value\n';
    csv += 'Fuel Blend,Blend LHV,' + document.getElementById('r-blendLHV').textContent + '\n';
    csv += 'Fuel Blend,Blend HHV,' + document.getElementById('r-blendHHV').textContent + '\n';
    csv += 'Fuel Blend,Blend Density,' + document.getElementById('r-blendRho').textContent + '\n';
    csv += 'Combustion,MW Fuel,' + document.getElementById('rc-mw').textContent + '\n';
    csv += 'Combustion,Stoich AFR,' + document.getElementById('rc-afr').textContent + '\n';
    csv += 'Combustion,CO2 Produced,' + document.getElementById('rc-co2').textContent + '\n';
    csv += 'Energy Balance,Heat Input,' + document.getElementById('eb-qin').textContent + '\n';
    csv += 'Energy Balance,Thermal Efficiency,' + document.getElementById('eb-eta').textContent + '\n';

    var blob = new Blob([csv], {type: 'text/csv'});
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'energy-mix-' + Date.now() + '.csv';
    a.click();
}

function resetAll() {
    if (confirm('Reset all values to defaults?')) location.reload();
}

// Initial calculation
document.addEventListener('DOMContentLoaded', function() {
    calcFuel();
    calcComb();
    calcEB();
});
</script>
