<?php
$simulation = $simulation ?? ['id' => 0, 'name' => '', 'description' => '', 'status' => 'draft', 'simulation_type' => '', 'input_data' => '{}', 'output_data' => '{}', 'created_at' => '', 'updated_at' => '', 'module_name' => '', 'project_name' => ''];
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/simulations">Simulations</a></li>
        <li class="breadcrumb-item active"><?= htmlspecialchars($simulation['name']) ?></li>
    </ol>
</nav>

<div class="page-header">
    <div class="d-flex align-items-center gap-3">
        <h1 class="mb-0"><?= htmlspecialchars($simulation['name']) ?></h1>
        <span class="badge badge-status-<?= htmlspecialchars($simulation['status']) ?>" style="font-size:.85rem;">
            <?= htmlspecialchars(ucfirst($simulation['status'])) ?>
        </span>
    </div>
    <div class="d-flex gap-2">
        <?php if ($simulation['status'] === 'draft'): ?>
        <form method="POST" action="/enpharchem/simulations/<?= (int)$simulation['id'] ?>/run" class="d-inline">
            <button type="submit" class="btn btn-success"><i class="fas fa-play me-1"></i>Run Simulation</button>
        </form>
        <?php endif; ?>
        <?php if ($simulation['status'] === 'completed'): ?>
        <a href="/enpharchem/simulations/<?= (int)$simulation['id'] ?>/results" class="btn btn-primary"><i class="fas fa-chart-bar me-1"></i>View Results</a>
        <?php endif; ?>
    </div>
</div>

<!-- Simulation Info -->
<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header py-3"><i class="fas fa-info-circle me-2"></i>Details</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><td style="color:#6c757d;">Module</td><td><?= htmlspecialchars($simulation['module_name'] ?? 'N/A') ?></td></tr>
                    <tr><td style="color:#6c757d;">Project</td><td><?= htmlspecialchars($simulation['project_name'] ?? 'N/A') ?></td></tr>
                    <tr><td style="color:#6c757d;">Type</td><td><?= htmlspecialchars(ucfirst($simulation['simulation_type'] ?? 'N/A')) ?></td></tr>
                    <tr><td style="color:#6c757d;">Created</td><td><?= htmlspecialchars($simulation['created_at'] ?? '') ?></td></tr>
                    <tr><td style="color:#6c757d;">Updated</td><td><?= htmlspecialchars($simulation['updated_at'] ?? '') ?></td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header py-3"><i class="fas fa-align-left me-2"></i>Description</div>
            <div class="card-body">
                <?php if (!empty($simulation['description'])): ?>
                <p style="color:#adb5bd;margin:0;"><?= nl2br(htmlspecialchars($simulation['description'])) ?></p>
                <?php else: ?>
                <p class="text-muted mb-0">No description provided.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Input / Output Data -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header py-3"><i class="fas fa-sign-in-alt me-2"></i>Input Data</div>
            <div class="card-body">
                <pre class="mb-0 p-3" style="background:rgba(0,0,0,.3);border-radius:8px;color:#0dcaf0;font-size:.85rem;max-height:400px;overflow:auto;"><?php
                    $input = $simulation['input_data'] ?? '{}';
                    $decoded = json_decode($input, true);
                    echo htmlspecialchars($decoded ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $input);
                ?></pre>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header py-3"><i class="fas fa-sign-out-alt me-2"></i>Output Data</div>
            <div class="card-body">
                <?php if ($simulation['status'] === 'completed' && !empty($simulation['output_data'])): ?>
                <pre class="mb-0 p-3" style="background:rgba(0,0,0,.3);border-radius:8px;color:#198754;font-size:.85rem;max-height:400px;overflow:auto;"><?php
                    $output = $simulation['output_data'];
                    $decoded = json_decode($output, true);
                    echo htmlspecialchars($decoded ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $output);
                ?></pre>
                <?php else: ?>
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-hourglass-half fa-2x mb-2 d-block" style="opacity:.4;"></i>
                    Output will appear after simulation completes.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Flowsheet Viewer -->
<div class="card">
    <div class="card-header py-3"><i class="fas fa-project-diagram me-2"></i>Flowsheet Viewer</div>
    <div class="card-body">
        <div style="background:rgba(0,0,0,.2);border-radius:8px;border:1px dashed rgba(255,255,255,.1);padding:2rem;text-align:center;">
            <canvas id="flowsheetCanvas" width="800" height="300" style="max-width:100%;background:rgba(0,0,0,.15);border-radius:8px;"></canvas>
            <p class="text-muted mt-2 mb-0" style="font-size:.85rem;">Interactive flowsheet viewer - drag and drop components to build your process</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const canvas = document.getElementById('flowsheetCanvas');
    if(!canvas) return;
    const ctx = canvas.getContext('2d');
    ctx.fillStyle = '#1a1d23';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // Draw placeholder flowsheet elements
    ctx.strokeStyle = '#0d6efd';
    ctx.lineWidth = 2;
    ctx.fillStyle = '#212529';

    // Feed stream
    ctx.fillRect(40, 120, 100, 60);
    ctx.strokeRect(40, 120, 100, 60);
    ctx.fillStyle = '#0dcaf0'; ctx.font = '12px sans-serif';
    ctx.fillText('Feed', 70, 155);

    // Reactor
    ctx.fillStyle = '#212529';
    ctx.fillRect(220, 100, 120, 100);
    ctx.strokeRect(220, 100, 120, 100);
    ctx.fillStyle = '#0dcaf0';
    ctx.fillText('Reactor', 250, 155);

    // Separator
    ctx.fillStyle = '#212529';
    ctx.fillRect(420, 100, 120, 100);
    ctx.strokeRect(420, 100, 120, 100);
    ctx.fillStyle = '#0dcaf0';
    ctx.fillText('Separator', 445, 155);

    // Product
    ctx.fillStyle = '#212529';
    ctx.fillRect(620, 120, 100, 60);
    ctx.strokeRect(620, 120, 100, 60);
    ctx.fillStyle = '#198754';
    ctx.fillText('Product', 642, 155);

    // Arrows
    ctx.strokeStyle = '#6c757d';
    ctx.beginPath(); ctx.moveTo(140, 150); ctx.lineTo(220, 150); ctx.stroke();
    ctx.beginPath(); ctx.moveTo(340, 150); ctx.lineTo(420, 150); ctx.stroke();
    ctx.beginPath(); ctx.moveTo(540, 150); ctx.lineTo(620, 150); ctx.stroke();

    // Arrow heads
    [210, 410, 610].forEach(function(x){
        ctx.beginPath(); ctx.moveTo(x, 145); ctx.lineTo(x+10, 150); ctx.lineTo(x, 155); ctx.stroke();
    });
});
</script>
