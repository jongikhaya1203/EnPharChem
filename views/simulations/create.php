<?php
$module = $module ?? null;
$project = $project ?? null;
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/simulations">Simulations</a></li>
        <li class="breadcrumb-item active">New Simulation</li>
    </ol>
</nav>

<div class="page-header">
    <h1><i class="fas fa-plus-circle me-2" style="color:var(--epc-accent);"></i>Create Simulation</h1>
</div>

<?php if ($module || $project): ?>
<div class="row g-3 mb-4">
    <?php if ($module): ?>
    <div class="col-auto">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-cube" style="color:var(--epc-accent);"></i>
                <div>
                    <div style="font-size:.75rem;color:#6c757d;">Module</div>
                    <div style="font-weight:600;color:#fff;"><?= htmlspecialchars($module['name'] ?? '') ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($project): ?>
    <div class="col-auto">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-project-diagram" style="color:var(--epc-primary);"></i>
                <div>
                    <div style="font-size:.75rem;color:#6c757d;">Project</div>
                    <div style="font-weight:600;color:#fff;"><?= htmlspecialchars($project['name'] ?? '') ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="card" style="max-width:720px;">
    <div class="card-body">
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/enpharchem/simulations/create">
            <?php if ($module): ?>
            <input type="hidden" name="module_id" value="<?= (int)($module['id'] ?? 0) ?>">
            <?php endif; ?>
            <?php if ($project): ?>
            <input type="hidden" name="project_id" value="<?= (int)($project['id'] ?? 0) ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="name" class="form-label">Simulation Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name"
                       value="<?= htmlspecialchars($name ?? '') ?>" placeholder="e.g. Heat Exchanger Analysis Run 1" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"
                          placeholder="Describe simulation objectives..."><?= htmlspecialchars($description ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label for="simulation_type" class="form-label">Simulation Type</label>
                <select class="form-select" id="simulation_type" name="simulation_type">
                    <option value="steady-state">Steady State</option>
                    <option value="dynamic">Dynamic</option>
                    <option value="optimization">Optimization</option>
                    <option value="sensitivity">Sensitivity Analysis</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="parameters" class="form-label">Parameters (JSON)</label>
                <textarea class="form-control font-monospace" id="parameters" name="parameters" rows="8"
                          placeholder='{
  "temperature": 298.15,
  "pressure": 101.325,
  "flow_rate": 1000,
  "components": ["Methane", "Ethane", "Water"]
}'><?= htmlspecialchars($parameters ?? '') ?></textarea>
                <small class="text-muted">Enter simulation parameters in JSON format</small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-play me-1"></i>Create Simulation</button>
                <a href="/enpharchem/simulations" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
