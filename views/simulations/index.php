<?php $simulations = $simulations ?? []; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Simulations</li>
    </ol>
</nav>

<div class="page-header">
    <h1><i class="fas fa-play-circle me-2" style="color:var(--epc-accent);"></i>Simulations</h1>
    <a href="/enpharchem/simulations/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Simulation</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (!empty($simulations)): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Module</th>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th style="width:120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($simulations as $sim): ?>
                    <tr>
                        <td>
                            <a href="/enpharchem/simulations/<?= (int)$sim['id'] ?>" class="text-decoration-none" style="color:var(--epc-accent);font-weight:500;">
                                <?= htmlspecialchars($sim['name']) ?>
                            </a>
                        </td>
                        <td style="font-size:.85rem;"><?= htmlspecialchars($sim['module_name'] ?? 'N/A') ?></td>
                        <td style="font-size:.85rem;">
                            <?php if (!empty($sim['project_name'])): ?>
                            <a href="/enpharchem/projects/<?= (int)($sim['project_id'] ?? 0) ?>" class="text-decoration-none" style="color:#adb5bd;"><?= htmlspecialchars($sim['project_name']) ?></a>
                            <?php else: ?>
                            <span class="text-muted">--</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge badge-status-<?= htmlspecialchars($sim['status'] ?? 'draft') ?>"><?= htmlspecialchars(ucfirst($sim['status'] ?? 'draft')) ?></span></td>
                        <td style="font-size:.85rem;color:#6c757d;"><?= htmlspecialchars($sim['created_at'] ?? '') ?></td>
                        <td>
                            <a href="/enpharchem/simulations/<?= (int)$sim['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="View"><i class="fas fa-eye"></i></a>
                            <?php if (($sim['status'] ?? '') === 'completed'): ?>
                            <a href="/enpharchem/simulations/<?= (int)$sim['id'] ?>/results" class="btn btn-sm btn-outline-success" title="Results"><i class="fas fa-chart-bar"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-flask fa-3x mb-3" style="color:#6c757d;opacity:.4;"></i>
            <p class="text-muted mb-3">No simulations found.</p>
            <a href="/enpharchem/simulations/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Create Simulation</a>
        </div>
        <?php endif; ?>
    </div>
</div>
