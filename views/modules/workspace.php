<?php
$module = $module ?? ['name' => 'Module', 'slug' => '', 'description' => '', 'version' => '1.0', 'category_slug' => '', 'category_name' => ''];
$category = $category ?? ['name' => $module['category_name'] ?? 'Category', 'slug' => $module['category_slug'] ?? ''];
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/<?= htmlspecialchars($category['slug']) ?>"><?= htmlspecialchars($category['name']) ?></a></li>
        <li class="breadcrumb-item active"><?= htmlspecialchars($module['name']) ?></li>
    </ol>
</nav>

<div class="page-header">
    <div>
        <h1 class="mb-1"><?= htmlspecialchars($module['name']) ?></h1>
        <p style="color:#6c757d;font-size:.9rem;margin:0;"><?= htmlspecialchars($module['description'] ?? '') ?></p>
    </div>
    <div>
        <span class="badge bg-secondary me-1">v<?= htmlspecialchars($module['version'] ?? '1.0') ?></span>
        <?php if (!empty($module['license_required'])): ?>
        <span class="badge bg-warning text-dark">License Required</span>
        <?php endif; ?>
    </div>
</div>

<!-- Tabbed Interface -->
<ul class="nav nav-tabs mb-4" id="workspaceTabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-overview" role="tab">Overview</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-simulation" role="tab">Simulation</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-config" role="tab">Configuration</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-results" role="tab">Results</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-docs" role="tab">Documentation</a></li>
</ul>

<div class="tab-content">
    <!-- Overview Tab -->
    <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header py-3"><i class="fas fa-info-circle me-2"></i>Module Overview</div>
                    <div class="card-body">
                        <h5 style="color:#fff;">Features</h5>
                        <ul style="color:#adb5bd;">
                            <li>Advanced process modeling and simulation capabilities</li>
                            <li>Comprehensive thermodynamic property calculations</li>
                            <li>Integration with industry-standard data sources</li>
                            <li>Real-time monitoring and optimization tools</li>
                            <li>Export results in multiple formats (CSV, PDF, JSON)</li>
                        </ul>

                        <h5 style="color:#fff;" class="mt-4">Getting Started</h5>
                        <ol style="color:#adb5bd;">
                            <li>Navigate to the <strong>Simulation</strong> tab to create a new simulation run.</li>
                            <li>Configure your process parameters and input conditions.</li>
                            <li>Run the simulation and monitor progress in real-time.</li>
                            <li>Review results in the <strong>Results</strong> tab with charts and data tables.</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header py-3"><i class="fas fa-tag me-2"></i>Version Info</div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tr><td style="color:#6c757d;">Version</td><td><?= htmlspecialchars($module['version'] ?? '1.0') ?></td></tr>
                            <tr><td style="color:#6c757d;">Category</td><td><?= htmlspecialchars($category['name']) ?></td></tr>
                            <tr><td style="color:#6c757d;">License</td><td><?= !empty($module['license_required']) ? 'Required' : 'Free' ?></td></tr>
                            <tr><td style="color:#6c757d;">Status</td><td><span class="badge bg-success">Active</span></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simulation Tab -->
    <div class="tab-pane fade" id="tab-simulation" role="tabpanel">
        <div class="card">
            <div class="card-header py-3"><i class="fas fa-play-circle me-2"></i>Create / Configure Simulation</div>
            <div class="card-body">
                <form method="POST" action="/enpharchem/simulations/create">
                    <input type="hidden" name="module_slug" value="<?= htmlspecialchars($module['slug'] ?? '') ?>">
                    <input type="hidden" name="category_slug" value="<?= htmlspecialchars($category['slug']) ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Simulation Name</label>
                            <input type="text" class="form-control" name="name" placeholder="e.g. Distillation Column Run 1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Project</label>
                            <select class="form-select" name="project_id">
                                <option value="">-- Select Project --</option>
                                <?php if (!empty($projects)): ?>
                                <?php foreach ($projects as $p): ?>
                                <option value="<?= (int)$p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Simulation description..."></textarea>
                    </div>

                    <?php
                    $isProcessSim = in_array($category['slug'] ?? '', ['process-sim-energy', 'process-sim-chemicals']);
                    if ($isProcessSim): ?>
                    <!-- Process Simulation specific fields -->
                    <hr style="border-color:rgba(255,255,255,.08);">
                    <h6 style="color:#fff;" class="mb-3"><i class="fas fa-flask me-2"></i>Process Parameters</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Components Selection</label>
                            <input type="text" class="form-control" name="components" placeholder="e.g. Methane, Ethane, Propane, Water">
                            <small class="text-muted">Comma-separated list of chemical components</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Thermodynamic Model</label>
                            <select class="form-select" name="thermo_model">
                                <option value="peng-robinson">Peng-Robinson</option>
                                <option value="srk">Soave-Redlich-Kwong (SRK)</option>
                                <option value="nrtl">NRTL</option>
                                <option value="uniquac">UNIQUAC</option>
                                <option value="wilson">Wilson</option>
                                <option value="ideal">Ideal Gas/Liquid</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Temperature (K)</label>
                            <input type="number" class="form-control" name="temperature" step="0.1" placeholder="298.15">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pressure (kPa)</label>
                            <input type="number" class="form-control" name="pressure" step="0.1" placeholder="101.325">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Flow Rate (kg/h)</label>
                            <input type="number" class="form-control" name="flow_rate" step="0.1" placeholder="1000">
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Generic parameters -->
                    <hr style="border-color:rgba(255,255,255,.08);">
                    <h6 style="color:#fff;" class="mb-3"><i class="fas fa-cog me-2"></i>Simulation Parameters</h6>
                    <div class="mb-3">
                        <label class="form-label">Parameters (JSON)</label>
                        <textarea class="form-control font-monospace" name="parameters" rows="6" placeholder='{"key": "value"}'></textarea>
                        <small class="text-muted">Enter simulation parameters in JSON format</small>
                    </div>
                    <?php endif; ?>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-play me-1"></i>Create &amp; Run Simulation</button>
                        <button type="button" class="btn btn-outline-secondary ms-2" onclick="this.form.reset()">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Configuration Tab -->
    <div class="tab-pane fade" id="tab-config" role="tabpanel">
        <div class="card">
            <div class="card-header py-3"><i class="fas fa-cog me-2"></i>Module Configuration</div>
            <div class="card-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Default Unit System</label>
                            <select class="form-select">
                                <option value="si">SI (Metric)</option>
                                <option value="imperial">Imperial</option>
                                <option value="cgs">CGS</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Convergence Tolerance</label>
                            <input type="number" class="form-control" value="0.0001" step="0.00001">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Max Iterations</label>
                            <input type="number" class="form-control" value="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Output Format</label>
                            <select class="form-select">
                                <option value="json">JSON</option>
                                <option value="csv">CSV</option>
                                <option value="xml">XML</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="autoSave" checked
                               style="background-color:var(--epc-primary);border-color:var(--epc-primary);">
                        <label class="form-check-label" for="autoSave">Auto-save simulation state</label>
                    </div>
                    <div class="mt-4">
                        <button type="button" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Configuration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Results Tab -->
    <div class="tab-pane fade" id="tab-results" role="tabpanel">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header py-3">Temperature Profile</div>
                    <div class="card-body">
                        <canvas id="tempChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header py-3">Pressure Drop</div>
                    <div class="card-body">
                        <canvas id="pressureChart" height="250"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header py-3">Composition Analysis</div>
                    <div class="card-body">
                        <canvas id="compositionChart" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Documentation Tab -->
    <div class="tab-pane fade" id="tab-docs" role="tabpanel">
        <div class="card">
            <div class="card-header py-3"><i class="fas fa-book me-2"></i>Module Documentation</div>
            <div class="card-body" style="color:#adb5bd;">
                <h5 style="color:#fff;">Introduction</h5>
                <p>This module provides comprehensive tools for <?= htmlspecialchars(strtolower($module['name'] ?? 'engineering simulation')) ?>. It integrates advanced algorithms with industry-standard methodologies to deliver accurate and reliable results.</p>

                <h5 style="color:#fff;" class="mt-4">Input Requirements</h5>
                <ul>
                    <li><strong>Process Stream Data:</strong> Flow rates, compositions, temperature, and pressure</li>
                    <li><strong>Equipment Specifications:</strong> Dimensions, material properties, design parameters</li>
                    <li><strong>Operating Conditions:</strong> Set points, constraints, and boundary conditions</li>
                </ul>

                <h5 style="color:#fff;" class="mt-4">Output Description</h5>
                <ul>
                    <li><strong>Material Balance:</strong> Complete mass and energy balance for the process</li>
                    <li><strong>Performance Metrics:</strong> Efficiency, yield, conversion, and utility consumption</li>
                    <li><strong>Graphical Results:</strong> Temperature profiles, pressure drops, composition charts</li>
                </ul>

                <h5 style="color:#fff;" class="mt-4">Methodology</h5>
                <p>The module employs rigorous thermodynamic models and numerical methods including Newton-Raphson convergence, sequential modular simulation, and equation-oriented approaches where applicable.</p>

                <h5 style="color:#fff;" class="mt-4">References</h5>
                <ol>
                    <li>Perry's Chemical Engineers' Handbook, 9th Edition</li>
                    <li>Smith, Van Ness, Abbott - Introduction to Chemical Engineering Thermodynamics</li>
                    <li>Seider, Seader, Lewin - Product and Process Design Principles</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const chartDefaults = {
        color: '#adb5bd',
        borderColor: 'rgba(255,255,255,0.06)',
    };
    Chart.defaults.color = chartDefaults.color;
    Chart.defaults.borderColor = chartDefaults.borderColor;

    // Temperature Profile
    const stages = Array.from({length:10}, (_,i) => 'Stage '+(i+1));
    new Chart(document.getElementById('tempChart'), {
        type: 'line',
        data: {
            labels: stages,
            datasets: [{
                label: 'Temperature (K)',
                data: [370, 365, 358, 350, 342, 335, 330, 326, 322, 318],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: { responsive: true, plugins: { legend: { display: true } } }
    });

    // Pressure Drop
    new Chart(document.getElementById('pressureChart'), {
        type: 'bar',
        data: {
            labels: stages,
            datasets: [{
                label: 'Pressure (kPa)',
                data: [101.3, 100.8, 100.2, 99.5, 98.9, 98.2, 97.6, 97.0, 96.5, 96.0],
                backgroundColor: 'rgba(13,202,240,0.6)',
                borderColor: '#0dcaf0',
                borderWidth: 1
            }]
        },
        options: { responsive: true, plugins: { legend: { display: true } } }
    });

    // Composition
    new Chart(document.getElementById('compositionChart'), {
        type: 'bar',
        data: {
            labels: ['Methane', 'Ethane', 'Propane', 'Butane', 'Water', 'CO2'],
            datasets: [
                { label: 'Feed', data: [0.40, 0.20, 0.15, 0.10, 0.10, 0.05], backgroundColor: 'rgba(13,110,253,0.7)' },
                { label: 'Product', data: [0.55, 0.25, 0.12, 0.05, 0.02, 0.01], backgroundColor: 'rgba(13,202,240,0.7)' }
            ]
        },
        options: { responsive: true, plugins: { legend: { display: true } } }
    });
});
</script>
