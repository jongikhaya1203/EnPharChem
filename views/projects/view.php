<?php
$project = $project ?? ['id' => 0, 'name' => '', 'description' => '', 'category' => '', 'status' => 'active', 'created_at' => '', 'updated_at' => ''];
$simulations = $simulations ?? [];
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/projects">Projects</a></li>
        <li class="breadcrumb-item active"><?= htmlspecialchars($project['name']) ?></li>
    </ol>
</nav>

<div class="page-header">
    <div class="d-flex align-items-center gap-3">
        <h1 class="mb-0"><?= htmlspecialchars($project['name']) ?></h1>
        <span class="badge bg-secondary"><?= htmlspecialchars(ucfirst($project['category'] ?? 'general')) ?></span>
        <span class="badge badge-status-<?= htmlspecialchars($project['status'] ?? 'active') ?>"><?= htmlspecialchars(ucfirst($project['status'] ?? 'active')) ?></span>
    </div>
    <div class="d-flex gap-2">
        <a href="/enpharchem/simulations/create?project_id=<?= (int)$project['id'] ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>New Simulation</a>
        <a href="/enpharchem/projects/<?= (int)$project['id'] ?>/edit" class="btn btn-outline-secondary btn-sm"><i class="fas fa-edit me-1"></i>Edit Project</a>
    </div>
</div>

<!-- Project Info -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header py-3"><i class="fas fa-info-circle me-2"></i>Description</div>
            <div class="card-body">
                <?php if (!empty($project['description'])): ?>
                <p style="color:#adb5bd;margin:0;"><?= nl2br(htmlspecialchars($project['description'])) ?></p>
                <?php else: ?>
                <p class="text-muted mb-0">No description provided.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header py-3"><i class="fas fa-calendar me-2"></i>Details</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><td style="color:#6c757d;">Created</td><td><?= htmlspecialchars($project['created_at'] ?? 'N/A') ?></td></tr>
                    <tr><td style="color:#6c757d;">Updated</td><td><?= htmlspecialchars($project['updated_at'] ?? 'N/A') ?></td></tr>
                    <tr><td style="color:#6c757d;">Simulations</td><td><?= count($simulations) ?></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Simulations -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <span><i class="fas fa-play-circle me-2"></i>Project Simulations</span>
        <a href="/enpharchem/simulations/create?project_id=<?= (int)$project['id'] ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Add Simulation</a>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($simulations)): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Module</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($simulations as $sim): ?>
                    <tr>
                        <td><a href="/enpharchem/simulations/<?= (int)$sim['id'] ?>" class="text-decoration-none" style="color:var(--epc-accent);"><?= htmlspecialchars($sim['name']) ?></a></td>
                        <td style="font-size:.85rem;"><?= htmlspecialchars($sim['module_name'] ?? '') ?></td>
                        <td><span class="badge badge-status-<?= htmlspecialchars($sim['status'] ?? 'draft') ?>"><?= htmlspecialchars(ucfirst($sim['status'] ?? 'draft')) ?></span></td>
                        <td style="font-size:.85rem;color:#6c757d;"><?= htmlspecialchars($sim['created_at'] ?? '') ?></td>
                        <td>
                            <a href="/enpharchem/simulations/<?= (int)$sim['id'] ?>" class="btn btn-sm btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-4">
            <p class="text-muted mb-0">No simulations in this project yet.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
