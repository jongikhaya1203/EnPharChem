<?php
$simulation = $simulation ?? ['id' => 0, 'name' => '', 'status' => 'completed'];
$results = $results ?? [];
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/simulations">Simulations</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/simulations/<?= (int)$simulation['id'] ?>"><?= htmlspecialchars($simulation['name']) ?></a></li>
        <li class="breadcrumb-item active">Results</li>
    </ol>
</nav>

<div class="page-header">
    <h1><i class="fas fa-chart-bar me-2" style="color:var(--epc-accent);"></i>Simulation Results</h1>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-sm" onclick="alert('CSV export coming soon');"><i class="fas fa-file-csv me-1"></i>Export CSV</button>
        <button class="btn btn-outline-secondary btn-sm" onclick="alert('PDF export coming soon');"><i class="fas fa-file-pdf me-1"></i>Export PDF</button>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(25,135,84,.15);color:#198754;"><i class="fas fa-check-circle"></i></div>
                <div>
                    <div style="font-size:1.3rem;font-weight:700;color:#fff;">Converged</div>
                    <div style="font-size:.8rem;color:#6c757d;">Solution Status</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(13,110,253,.15);color:#0d6efd;"><i class="fas fa-thermometer-half"></i></div>
                <div>
                    <div style="font-size:1.3rem;font-weight:700;color:#fff;">342.8 K</div>
                    <div style="font-size:.8rem;color:#6c757d;">Avg. Temperature</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(13,202,240,.15);color:#0dcaf0;"><i class="fas fa-tachometer-alt"></i></div>
                <div>
                    <div style="font-size:1.3rem;font-weight:700;color:#fff;">98.7 kPa</div>
                    <div style="font-size:.8rem;color:#6c757d;">Outlet Pressure</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(255,193,7,.15);color:#ffc107;"><i class="fas fa-percentage"></i></div>
                <div>
                    <div style="font-size:1.3rem;font-weight:700;color:#fff;">94.2%</div>
                    <div style="font-size:.8rem;color:#6c757d;">Conversion Rate</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header py-3">Temperature Profile</div>
            <div class="card-body"><canvas id="resultTempChart" height="280"></canvas></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header py-3">Pressure Drop Across Stages</div>
            <div class="card-body"><canvas id="resultPressureChart" height="280"></canvas></div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3">Component Composition (Feed vs Product)</div>
            <div class="card-body"><canvas id="resultCompChart" height="180"></canvas></div>
        </div>
    </div>
</div>

<!-- Data Tables -->
<div class="card mb-4">
    <div class="card-header py-3"><i class="fas fa-table me-2"></i>Stage-by-Stage Results</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Stage</th>
                        <th>Temperature (K)</th>
                        <th>Pressure (kPa)</th>
                        <th>Vapor Fraction</th>
                        <th>Liquid Flow (kg/h)</th>
                        <th>Vapor Flow (kg/h)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stageData = [
                        [1,370.0,101.3,0.42,580,420],
                        [2,365.2,100.8,0.39,610,390],
                        [3,358.1,100.2,0.35,650,350],
                        [4,350.4,99.5,0.31,690,310],
                        [5,342.8,98.9,0.27,730,270],
                        [6,335.5,98.2,0.23,770,230],
                        [7,330.1,97.6,0.20,800,200],
                        [8,326.0,97.0,0.17,830,170],
                        [9,322.3,96.5,0.14,860,140],
                        [10,318.9,96.0,0.12,880,120],
                    ];
                    foreach ($stageData as $row):
                    ?>
                    <tr>
                        <td><?= $row[0] ?></td>
                        <td><?= number_format($row[1], 1) ?></td>
                        <td><?= number_format($row[2], 1) ?></td>
                        <td><?= number_format($row[3], 2) ?></td>
                        <td><?= number_format($row[4], 0) ?></td>
                        <td><?= number_format($row[5], 0) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Component Balance Table -->
<div class="card">
    <div class="card-header py-3"><i class="fas fa-balance-scale me-2"></i>Component Material Balance</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Component</th>
                        <th>Feed (mol%)</th>
                        <th>Distillate (mol%)</th>
                        <th>Bottoms (mol%)</th>
                        <th>Recovery (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Methane</td><td>40.0</td><td>55.2</td><td>12.1</td><td>96.8</td></tr>
                    <tr><td>Ethane</td><td>20.0</td><td>25.1</td><td>8.3</td><td>92.4</td></tr>
                    <tr><td>Propane</td><td>15.0</td><td>12.3</td><td>22.5</td><td>78.6</td></tr>
                    <tr><td>Butane</td><td>10.0</td><td>4.8</td><td>25.7</td><td>62.3</td></tr>
                    <tr><td>Water</td><td>10.0</td><td>2.1</td><td>21.8</td><td>45.1</td></tr>
                    <tr><td>CO2</td><td>5.0</td><td>0.5</td><td>9.6</td><td>88.9</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    Chart.defaults.color = '#adb5bd';
    Chart.defaults.borderColor = 'rgba(255,255,255,0.06)';
    const stages = Array.from({length:10}, (_,i) => 'Stage '+(i+1));

    new Chart(document.getElementById('resultTempChart'), {
        type: 'line',
        data: {
            labels: stages,
            datasets: [{
                label: 'Temperature (K)',
                data: [370,365.2,358.1,350.4,342.8,335.5,330.1,326,322.3,318.9],
                borderColor: '#dc3545', backgroundColor: 'rgba(220,53,69,0.1)', fill: true, tension: 0.4
            }]
        },
        options: { responsive: true }
    });

    new Chart(document.getElementById('resultPressureChart'), {
        type: 'line',
        data: {
            labels: stages,
            datasets: [{
                label: 'Pressure (kPa)',
                data: [101.3,100.8,100.2,99.5,98.9,98.2,97.6,97.0,96.5,96.0],
                borderColor: '#0dcaf0', backgroundColor: 'rgba(13,202,240,0.1)', fill: true, tension: 0.3
            }]
        },
        options: { responsive: true }
    });

    new Chart(document.getElementById('resultCompChart'), {
        type: 'bar',
        data: {
            labels: ['Methane','Ethane','Propane','Butane','Water','CO2'],
            datasets: [
                { label: 'Feed (mol%)', data: [40,20,15,10,10,5], backgroundColor: 'rgba(13,110,253,0.7)' },
                { label: 'Distillate (mol%)', data: [55.2,25.1,12.3,4.8,2.1,0.5], backgroundColor: 'rgba(25,135,84,0.7)' },
                { label: 'Bottoms (mol%)', data: [12.1,8.3,22.5,25.7,21.8,9.6], backgroundColor: 'rgba(255,193,7,0.7)' }
            ]
        },
        options: { responsive: true }
    });
});
</script>
