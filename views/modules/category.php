<?php
$category = $category ?? ['name' => 'Category', 'slug' => '', 'icon' => 'fa-cube', 'description' => ''];
$modules = $modules ?? [];
$catIcons = [
    'process-sim-energy' => 'fa-bolt', 'process-sim-chemicals' => 'fa-flask',
    'exchanger-design' => 'fa-exchange-alt', 'concurrent-feed' => 'fa-drafting-compass',
    'subsurface-engineering' => 'fa-mountain', 'energy-utilities-optimization' => 'fa-leaf',
    'operations-support' => 'fa-desktop', 'advanced-process-control' => 'fa-sliders-h',
    'dynamic-optimization' => 'fa-chart-line', 'manufacturing-execution' => 'fa-industry',
    'petroleum-supply-chain' => 'fa-oil-can', 'supply-chain-management' => 'fa-truck',
    'asset-performance' => 'fa-heartbeat', 'industrial-data-fabric' => 'fa-database',
    'digital-grid-management' => 'fa-plug',
];
$icon = $catIcons[$category['slug'] ?? ''] ?? ($category['icon'] ?? 'fa-cube');
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/modules">Modules</a></li>
        <li class="breadcrumb-item active"><?= htmlspecialchars($category['name']) ?></li>
    </ol>
</nav>

<div class="page-header">
    <div class="d-flex align-items-center gap-3">
        <div class="module-icon" style="background:rgba(13,110,253,.15);color:#0d6efd;width:56px;height:56px;font-size:1.4rem;">
            <i class="fas <?= $icon ?>"></i>
        </div>
        <div>
            <h1 class="mb-0"><?= htmlspecialchars($category['name']) ?></h1>
            <?php if (!empty($category['description'])): ?>
            <p class="mb-0 mt-1" style="color:#6c757d;font-size:.9rem;"><?= htmlspecialchars($category['description']) ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($modules)): ?>
<div class="row g-3">
    <?php foreach ($modules as $mod): ?>
    <div class="col-md-6 col-lg-4">
        <a href="/enpharchem/<?= htmlspecialchars($category['slug']) ?>/module?slug=<?= htmlspecialchars($mod['slug'] ?? '') ?>" class="text-decoration-none">
            <div class="card module-card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="module-icon" style="background:rgba(13,202,240,.12);color:#0dcaf0;">
                            <i class="fas <?= htmlspecialchars($mod['icon'] ?? $icon) ?>"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="mb-0" style="color:#fff;font-weight:600;"><?= htmlspecialchars($mod['name']) ?></h6>
                                <?php if (!empty($mod['license_required'])): ?>
                                <span class="badge bg-warning text-dark" style="font-size:.65rem;">License Required</span>
                                <?php else: ?>
                                <span class="badge bg-success" style="font-size:.65rem;">Free</span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($mod['version'])): ?>
                            <small style="color:#6c757d;">v<?= htmlspecialchars($mod['version']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p style="font-size:.85rem;color:#adb5bd;margin-bottom:0;"><?= htmlspecialchars($mod['description'] ?? 'No description available.') ?></p>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="card">
    <div class="card-body text-center py-5">
        <i class="fas fa-cube fa-3x mb-3" style="color:#6c757d;opacity:.4;"></i>
        <p class="text-muted mb-0">No modules available in this category yet.</p>
    </div>
</div>
<?php endif; ?>
