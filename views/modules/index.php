<?php $categories = $categories ?? []; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Modules</li>
    </ol>
</nav>

<div class="page-header">
    <h1><i class="fas fa-cubes me-2" style="color:var(--epc-accent);"></i>Module Categories</h1>
    <p class="text-muted mb-0">Browse all EnPharChem platform modules across <?= count($categories) ?> categories</p>
</div>

<div class="row g-4">
    <?php foreach ($categories as $cat): ?>
    <div class="col-md-6 col-lg-4">
        <a href="/enpharchem/modules/category?slug=<?= htmlspecialchars($cat['slug']) ?>" class="text-decoration-none">
            <div class="card h-100" style="transition:all .2s;cursor:pointer;" onmouseover="this.style.borderColor='var(--epc-primary)';this.style.transform='translateY(-3px)'" onmouseout="this.style.borderColor='rgba(255,255,255,.08)';this.style.transform='none'">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.2rem;">
                            <i class="fas <?= htmlspecialchars($cat['icon'] ?? 'fa-cube') ?>"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="font-size:1rem;color:#fff;"><?= htmlspecialchars($cat['name']) ?></h5>
                            <span class="badge bg-primary" style="font-size:.7rem;"><?= (int)($cat['module_count'] ?? 0) ?> modules</span>
                        </div>
                    </div>
                    <p style="font-size:.85rem;color:#9ca3af;margin:0;line-height:1.6;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        <?= htmlspecialchars($cat['description'] ?? 'Explore modules in this category') ?>
                    </p>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center" style="background:transparent;border-color:rgba(255,255,255,.06);">
                    <span style="font-size:.8rem;color:#0dcaf0;">Browse Modules</span>
                    <i class="fas fa-arrow-right" style="color:#0dcaf0;font-size:.8rem;"></i>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<?php if (empty($categories)): ?>
<div class="card">
    <div class="card-body text-center py-5">
        <i class="fas fa-cubes fa-3x mb-3" style="color:#3d4248;"></i>
        <h5 class="text-muted">No Module Categories Found</h5>
        <p class="text-muted">Module categories will appear here once configured.</p>
    </div>
</div>
<?php endif; ?>
