<?php
$module = $module ?? [];
$relatedModules = $relatedModules ?? [];
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/modules">Modules</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/modules/category?slug=<?= htmlspecialchars($module['category_slug'] ?? '') ?>"><?= htmlspecialchars($module['category_name'] ?? 'Category') ?></a></li>
        <li class="breadcrumb-item active"><?= htmlspecialchars($module['name'] ?? '') ?></li>
    </ol>
</nav>

<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div style="width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.5rem;">
                <i class="fas <?= htmlspecialchars($module['icon'] ?? 'fa-cube') ?>"></i>
            </div>
            <div>
                <h2 class="mb-1" style="font-size:1.4rem;"><?= htmlspecialchars($module['name'] ?? '') ?></h2>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge bg-primary"><?= htmlspecialchars($module['category_name'] ?? '') ?></span>
                    <span class="badge bg-secondary">v<?= htmlspecialchars($module['version'] ?? '1.0.0') ?></span>
                    <span class="badge bg-info"><?= htmlspecialchars(ucfirst($module['license_required'] ?? 'standard')) ?> License</span>
                </div>
            </div>
            <div class="ms-auto">
                <a href="/enpharchem/modules/launch?slug=<?= htmlspecialchars($module['slug'] ?? '') ?>" class="btn btn-primary">
                    <i class="fas fa-rocket me-1"></i> Launch Module
                </a>
            </div>
        </div>
        <p style="color:#9ca3af;line-height:1.7;"><?= htmlspecialchars($module['description'] ?? '') ?></p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header py-3"><i class="fas fa-info-circle me-2"></i>Overview</div>
            <div class="card-body">
                <h6 style="color:#0dcaf0;">Key Features</h6>
                <ul style="color:#9ca3af;">
                    <li>Rigorous thermodynamic calculations and property estimation</li>
                    <li>Comprehensive unit operation modeling library</li>
                    <li>Integrated optimization and sensitivity analysis</li>
                    <li>Interactive flowsheet creation and visualization</li>
                    <li>Detailed reporting and documentation generation</li>
                </ul>
                <h6 style="color:#0dcaf0;" class="mt-4">Applications</h6>
                <ul style="color:#9ca3af;">
                    <li>Process design and simulation</li>
                    <li>Equipment sizing and rating</li>
                    <li>Energy optimization and heat integration</li>
                    <li>Safety and relief system analysis</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header py-3"><i class="fas fa-link me-2"></i>Related Modules</div>
            <div class="card-body p-0">
                <?php if (!empty($relatedModules)): ?>
                    <?php foreach ($relatedModules as $rm): ?>
                    <a href="/enpharchem/modules/view?slug=<?= htmlspecialchars($rm['slug']) ?>" class="d-flex align-items-center gap-2 p-3 text-decoration-none" style="border-bottom:1px solid rgba(255,255,255,.06);color:#dee2e6;" onmouseover="this.style.background='rgba(255,255,255,.04)'" onmouseout="this.style.background='transparent'">
                        <div style="width:28px;height:28px;border-radius:6px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.7rem;">
                            <i class="fas <?= htmlspecialchars($rm['icon'] ?? 'fa-cube') ?>"></i>
                        </div>
                        <span style="font-size:.85rem;"><?= htmlspecialchars($rm['name']) ?></span>
                    </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4 text-muted" style="font-size:.85rem;">No related modules</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header py-3"><i class="fas fa-cog me-2"></i>Module Info</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr><td style="color:#6c757d;">Version</td><td><?= htmlspecialchars($module['version'] ?? '1.0.0') ?></td></tr>
                        <tr><td style="color:#6c757d;">Category</td><td><?= htmlspecialchars($module['category_name'] ?? '') ?></td></tr>
                        <tr><td style="color:#6c757d;">License</td><td><?= htmlspecialchars(ucfirst($module['license_required'] ?? 'standard')) ?></td></tr>
                        <tr><td style="color:#6c757d;">Status</td><td><span class="badge bg-success">Active</span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
