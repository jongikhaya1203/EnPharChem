<?php
$categories = $categories ?? [];
$modules = $modules ?? [];
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/admin">Admin</a></li>
        <li class="breadcrumb-item active">Modules</li>
    </ol>
</nav>

<div class="page-header">
    <h1><i class="fas fa-cubes me-2" style="color:var(--epc-accent);"></i>Module Management</h1>
</div>

<?php if (!empty($categories)): ?>
    <?php foreach ($categories as $cat): ?>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
            <span>
                <i class="fas <?= htmlspecialchars($cat['icon'] ?? 'fa-cube') ?> me-2" style="color:var(--epc-accent);"></i>
                <?= htmlspecialchars($cat['name']) ?>
            </span>
            <span class="badge bg-primary"><?= (int)($cat['module_count'] ?? 0) ?> modules</span>
        </div>
        <div class="card-body p-0">
            <?php
            $catModules = array_filter($modules, function($m) use ($cat) {
                return ($m['category_name'] ?? '') === $cat['name'] || ($m['category_id'] ?? '') == ($cat['id'] ?? '');
            });
            ?>
            <?php if (!empty($catModules)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Module</th>
                            <th>Slug</th>
                            <th>Version</th>
                            <th>License</th>
                            <th>Status</th>
                            <th style="width:100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($catModules as $mod): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:32px;height:32px;border-radius:6px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.75rem;">
                                        <i class="fas <?= htmlspecialchars($mod['icon'] ?? 'fa-cube') ?>"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight:600;"><?= htmlspecialchars($mod['name']) ?></div>
                                        <div style="font-size:.75rem;color:#6c757d;"><?= htmlspecialchars(mb_substr($mod['description'] ?? '', 0, 80)) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><code style="color:var(--epc-accent);font-size:.8rem;"><?= htmlspecialchars($mod['slug'] ?? '') ?></code></td>
                            <td><?= htmlspecialchars($mod['version'] ?? '1.0.0') ?></td>
                            <td>
                                <?php if (!empty($mod['license_required'])): ?>
                                <span class="badge bg-warning text-dark">Required</span>
                                <?php else: ?>
                                <span class="badge bg-success">Free</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($mod['is_active'] ?? true): ?>
                                <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-outline-secondary" title="Toggle"><i class="fas fa-power-off"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-3 text-muted" style="font-size:.9rem;">No modules in this category.</div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
<?php else: ?>
<div class="card">
    <div class="card-body text-center py-5 text-muted">
        <i class="fas fa-cubes fa-3x mb-3" style="opacity:.4;"></i>
        <p>No module categories configured.</p>
    </div>
</div>
<?php endif; ?>
