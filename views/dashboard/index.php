<?php
$stats = $stats ?? ['total_projects' => 0, 'active_simulations' => 0, 'completed_simulations' => 0, 'available_modules' => 0];
$categories = $categories ?? [];
$recentProjects = $recentProjects ?? [];
$recentSimulations = $recentSimulations ?? [];
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
</nav>

<div class="page-header">
    <h1><i class="fas fa-th-large me-2" style="color:var(--epc-accent);"></i>Dashboard</h1>
    <div>
        <a href="/enpharchem/projects/create" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>New Project</a>
        <a href="/enpharchem/modules" class="btn btn-outline-primary btn-sm ms-1"><i class="fas fa-cubes me-1"></i>Browse Modules</a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(13,110,253,.15);color:#0d6efd;">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:#fff;"><?= (int)$stats['total_projects'] ?></div>
                    <div style="font-size:.8rem;color:#6c757d;">Total Projects</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(13,202,240,.15);color:#0dcaf0;">
                    <i class="fas fa-spinner fa-pulse"></i>
                </div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:#fff;"><?= (int)$stats['active_simulations'] ?></div>
                    <div style="font-size:.8rem;color:#6c757d;">Active Simulations</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(25,135,84,.15);color:#198754;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:#fff;"><?= (int)$stats['completed_simulations'] ?></div>
                    <div style="font-size:.8rem;color:#6c757d;">Completed Simulations</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(255,193,7,.15);color:#ffc107;">
                    <i class="fas fa-cubes"></i>
                </div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:#fff;"><?= (int)$stats['available_modules'] ?></div>
                    <div style="font-size:.8rem;color:#6c757d;">Available Modules</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Module Categories Grid -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <span><i class="fas fa-cubes me-2"></i>Module Categories</span>
        <a href="/enpharchem/modules" class="btn btn-outline-primary btn-sm">View All</a>
    </div>
    <div class="card-body">
        <?php if (!empty($categories)): ?>
        <div class="row g-3">
            <?php
            $catIcons = [
                'process-sim-energy' => 'fa-bolt',
                'process-sim-chemicals' => 'fa-flask',
                'exchanger-design' => 'fa-exchange-alt',
                'concurrent-feed' => 'fa-drafting-compass',
                'subsurface-engineering' => 'fa-mountain',
                'energy-utilities-optimization' => 'fa-leaf',
                'operations-support' => 'fa-desktop',
                'advanced-process-control' => 'fa-sliders-h',
                'dynamic-optimization' => 'fa-chart-line',
                'manufacturing-execution' => 'fa-industry',
                'petroleum-supply-chain' => 'fa-oil-can',
                'supply-chain-management' => 'fa-truck',
                'asset-performance' => 'fa-heartbeat',
                'industrial-data-fabric' => 'fa-database',
                'digital-grid-management' => 'fa-plug',
            ];
            $catColors = ['#0d6efd','#0dcaf0','#198754','#ffc107','#dc3545','#6f42c1','#fd7e14','#20c997','#e83e8c','#6610f2','#0d6efd','#0dcaf0','#198754','#ffc107','#dc3545'];
            $ci = 0;
            foreach ($categories as $cat):
                $icon = $catIcons[$cat['slug'] ?? ''] ?? 'fa-cube';
                $color = $catColors[$ci % count($catColors)];
                $ci++;
            ?>
            <div class="col-md-6 col-lg-4">
                <a href="/enpharchem/<?= htmlspecialchars($cat['slug']) ?>" class="text-decoration-none">
                    <div class="card module-card h-100 p-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="module-icon" style="background:<?= $color ?>20;color:<?= $color ?>;">
                                <i class="fas <?= $icon ?>"></i>
                            </div>
                            <div>
                                <div style="font-weight:600;color:#fff;"><?= htmlspecialchars($cat['name']) ?></div>
                                <div style="font-size:.8rem;color:#6c757d;"><?= (int)($cat['module_count'] ?? 0) ?> modules</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-muted mb-0">No module categories available.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Control Panel & Quick Actions (visible to superuser/admin) -->
<?php if (isset($user) && in_array($user['role'] ?? '', ['superuser', 'admin'])): ?>
<div class="card mb-4" style="border:1px solid rgba(13,202,240,.2);background:linear-gradient(135deg,rgba(13,110,253,.05),rgba(13,202,240,.05));">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:50px;height:50px;border-radius:12px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.3rem;">
                    <i class="fas fa-cogs"></i>
                </div>
                <div>
                    <h5 class="mb-0" style="color:#fff;">Control Panel</h5>
                    <p class="mb-0" style="font-size:.85rem;color:#9ca3af;">Manage Active Directory, CMS, Marketing, Training & Data</p>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="/enpharchem/control-panel" class="btn btn-primary btn-sm"><i class="fas fa-cogs me-1"></i>Open Control Panel</a>
                <a href="/enpharchem/control-panel/active-directory" class="btn btn-outline-info btn-sm"><i class="fas fa-sitemap me-1"></i>Active Directory</a>
                <a href="/enpharchem/control-panel/cms" class="btn btn-outline-success btn-sm"><i class="fas fa-file-alt me-1"></i>CMS Pages</a>
                <a href="/enpharchem/control-panel/marketing" class="btn btn-outline-warning btn-sm"><i class="fas fa-bullhorn me-1"></i>Marketing</a>
                <a href="/enpharchem/control-panel/training" class="btn btn-outline-light btn-sm"><i class="fas fa-graduation-cap me-1"></i>Training</a>
                <a href="/enpharchem/control-panel/data-management" class="btn btn-outline-danger btn-sm"><i class="fas fa-database me-1"></i>Data Mgmt</a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Recent Projects -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <span><i class="fas fa-project-diagram me-2"></i>Recent Projects</span>
                <a href="/enpharchem/projects" class="btn btn-outline-primary btn-sm">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recentProjects)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentProjects as $proj): ?>
                            <tr>
                                <td><a href="/enpharchem/projects/<?= (int)$proj['id'] ?>" class="text-decoration-none" style="color:var(--epc-accent);"><?= htmlspecialchars($proj['name']) ?></a></td>
                                <td><span class="badge badge-status-<?= htmlspecialchars($proj['status'] ?? 'active') ?>"><?= htmlspecialchars(ucfirst($proj['status'] ?? 'active')) ?></span></td>
                                <td style="font-size:.85rem;color:#6c757d;"><?= htmlspecialchars($proj['updated_at'] ?? '') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-folder-open fa-2x mb-2 d-block" style="opacity:.4;"></i>
                    No projects yet. <a href="/enpharchem/projects/create" style="color:var(--epc-accent);">Create your first project</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Simulations -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <span><i class="fas fa-play-circle me-2"></i>Recent Simulations</span>
                <a href="/enpharchem/simulations" class="btn btn-outline-primary btn-sm">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recentSimulations)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Module</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentSimulations as $sim): ?>
                            <tr>
                                <td><a href="/enpharchem/simulations/<?= (int)$sim['id'] ?>" class="text-decoration-none" style="color:var(--epc-accent);"><?= htmlspecialchars($sim['name']) ?></a></td>
                                <td style="font-size:.85rem;"><?= htmlspecialchars($sim['module_name'] ?? '') ?></td>
                                <td><span class="badge badge-status-<?= htmlspecialchars($sim['status'] ?? 'draft') ?>"><?= htmlspecialchars(ucfirst($sim['status'] ?? 'draft')) ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-flask fa-2x mb-2 d-block" style="opacity:.4;"></i>
                    No simulations yet.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card mt-4">
    <div class="card-header py-3"><i class="fas fa-bolt me-2"></i>Quick Actions</div>
    <div class="card-body d-flex flex-wrap gap-2">
        <a href="/enpharchem/projects/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Project</a>
        <a href="/enpharchem/modules" class="btn btn-outline-primary"><i class="fas fa-cubes me-1"></i>Browse Modules</a>
        <a href="/enpharchem/simulations/create" class="btn btn-outline-info"><i class="fas fa-play me-1"></i>Run Simulation</a>
    </div>
</div>
