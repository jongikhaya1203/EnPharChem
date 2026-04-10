<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard" class="text-decoration-none" style="color:var(--epc-accent);">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel" class="text-decoration-none" style="color:var(--epc-accent);">Control Panel</a></li>
        <li class="breadcrumb-item active text-light">Module License Manager</li>
    </ol>
</nav>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Module licenses updated successfully.</div>
<?php endif; ?>

<div class="page-header">
    <h1><i class="fas fa-shield-alt me-2" style="color:var(--epc-accent);"></i>Module License Manager</h1>
    <p class="text-muted mb-0">Grant or revoke licenses per module category. When granted, "License Required" badge is hidden.</p>
</div>

<!-- Stats Bar -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 p-3" style="background:var(--epc-card-bg);">
            <div class="d-flex align-items-center gap-3">
                <div style="width:42px;height:42px;border-radius:10px;background:rgba(13,110,253,.15);color:#0d6efd;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                    <i class="fas fa-cubes"></i>
                </div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:#fff;"><?= $stats['total_modules'] ?></div>
                    <div style="font-size:.75rem;color:#6c757d;">Total Modules</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 p-3" style="background:var(--epc-card-bg);">
            <div class="d-flex align-items-center gap-3">
                <div style="width:42px;height:42px;border-radius:10px;background:rgba(25,135,84,.15);color:#198754;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                    <i class="fas fa-unlock"></i>
                </div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:#198754;"><?= $stats['granted'] ?></div>
                    <div style="font-size:.75rem;color:#6c757d;">License Granted</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 p-3" style="background:var(--epc-card-bg);">
            <div class="d-flex align-items-center gap-3">
                <div style="width:42px;height:42px;border-radius:10px;background:rgba(255,193,7,.15);color:#ffc107;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                    <i class="fas fa-lock"></i>
                </div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:#ffc107;"><?= $stats['required'] ?></div>
                    <div style="font-size:.75rem;color:#6c757d;">License Required</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 p-3" style="background:var(--epc-card-bg);">
            <div class="d-flex align-items-center gap-3">
                <div style="width:42px;height:42px;border-radius:10px;background:rgba(13,202,240,.15);color:#0dcaf0;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:#0dcaf0;"><?= $stats['categories'] ?></div>
                    <div style="font-size:.75rem;color:#6c757d;">Categories</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions -->
<div class="card border-0 mb-4" style="background:var(--epc-card-bg);border-left:4px solid var(--epc-accent) !important;">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h6 class="text-light mb-1"><i class="fas fa-bolt me-2 text-warning"></i>Bulk Actions</h6>
                <small class="text-secondary">Apply license changes to all modules at once</small>
            </div>
            <div class="d-flex gap-2">
                <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="grant_all">
                    <button type="submit" class="btn btn-success" onclick="return confirm('Grant license for ALL modules? This will hide the License Required badge on every module.')">
                        <i class="fas fa-unlock me-1"></i>Grant All Licenses
                    </button>
                </form>
                <form method="POST" class="d-inline">
                    <input type="hidden" name="action" value="revoke_all">
                    <button type="submit" class="btn btn-warning" onclick="return confirm('Revoke license for ALL modules? This will show License Required badge on every module.')">
                        <i class="fas fa-lock me-1"></i>Revoke All Licenses
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Categories Accordion -->
<?php foreach ($categories as $cat):
    $catModules = $modulesByCategory[$cat['id']] ?? [];
    $waived = (int)($cat['waived_count'] ?? 0);
    $totalInCat = (int)($cat['total_modules'] ?? 0);
    $pct = $totalInCat > 0 ? round(($waived / $totalInCat) * 100) : 0;
?>
<div class="card border-0 mb-3" style="background:var(--epc-card-bg);">
    <div class="card-header border-bottom border-secondary d-flex align-items-center justify-content-between flex-wrap gap-2" style="background:var(--epc-card-bg);cursor:pointer;" onclick="toggleCategory('cat-<?= $cat['id'] ?>')">
        <div class="d-flex align-items-center gap-3 flex-grow-1">
            <i class="fas fa-chevron-right cat-chevron" id="chev-cat-<?= $cat['id'] ?>" style="transition:transform .2s;color:var(--epc-accent);"></i>
            <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);display:flex;align-items:center;justify-content:center;color:#fff;">
                <i class="fas <?= htmlspecialchars($cat['icon'] ?? 'fa-cube') ?>"></i>
            </div>
            <div>
                <h6 class="text-light mb-0"><?= htmlspecialchars($cat['name']) ?></h6>
                <small class="text-secondary"><?= $totalInCat ?> modules &bull; <?= $waived ?> granted &bull; <?= $totalInCat - $waived ?> require license</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2" onclick="event.stopPropagation()">
            <!-- Progress -->
            <div style="width:120px;">
                <div style="height:8px;background:rgba(255,255,255,.1);border-radius:4px;overflow:hidden;">
                    <div style="width:<?= $pct ?>%;height:100%;background:linear-gradient(90deg,#198754,#0dcaf0);border-radius:4px;"></div>
                </div>
                <small style="font-size:10px;color:#6c757d;"><?= $pct ?>% granted</small>
            </div>
            <!-- Category Actions -->
            <form method="POST" class="d-inline">
                <input type="hidden" name="action" value="grant_category">
                <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                <button type="submit" class="btn btn-sm btn-success" title="Grant all in category" onclick="return confirm('Grant license for all modules in <?= htmlspecialchars(addslashes($cat['name']), ENT_QUOTES) ?>?')">
                    <i class="fas fa-unlock me-1"></i>Grant All
                </button>
            </form>
            <form method="POST" class="d-inline">
                <input type="hidden" name="action" value="revoke_category">
                <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-warning" title="Revoke all in category" onclick="return confirm('Revoke license for all modules in <?= htmlspecialchars(addslashes($cat['name']), ENT_QUOTES) ?>?')">
                    <i class="fas fa-lock me-1"></i>Revoke All
                </button>
            </form>
        </div>
    </div>
    <div class="card-body p-0" id="cat-<?= $cat['id'] ?>" style="display:none;">
        <?php if (!empty($catModules)): ?>
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0" style="background:transparent;">
                <thead>
                    <tr class="text-secondary small text-uppercase">
                        <th>Module</th>
                        <th>Description</th>
                        <th style="width:120px;">License Tier</th>
                        <th style="width:140px;">Status</th>
                        <th style="width:130px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($catModules as $mod):
                        $waived = !empty($mod['license_waived']);
                    ?>
                    <tr style="<?= $waived ? 'background:rgba(25,135,84,.04);' : '' ?>">
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:28px;height:28px;border-radius:6px;background:<?= $waived ? 'rgba(25,135,84,.2)' : 'rgba(255,193,7,.15)' ?>;display:flex;align-items:center;justify-content:center;color:<?= $waived ? '#198754' : '#ffc107' ?>;font-size:.75rem;">
                                    <i class="fas <?= htmlspecialchars($mod['icon'] ?? 'fa-cube') ?>"></i>
                                </div>
                                <div style="font-weight:600;color:#fff;"><?= htmlspecialchars($mod['name']) ?></div>
                            </div>
                        </td>
                        <td style="font-size:.8rem;color:#9ca3af;max-width:400px;">
                            <?= htmlspecialchars(mb_substr($mod['description'] ?? '', 0, 100)) ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= ($mod['license_required'] ?? 'standard') === 'enterprise' ? 'warning text-dark' : (($mod['license_required'] ?? 'standard') === 'professional' ? 'info' : 'secondary') ?>">
                                <?= ucfirst($mod['license_required'] ?? 'standard') ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($waived): ?>
                                <span class="badge bg-success"><i class="fas fa-unlock me-1"></i>Granted</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark"><i class="fas fa-lock me-1"></i>Required</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="module_id" value="<?= $mod['id'] ?>">
                                <?php if ($waived): ?>
                                    <input type="hidden" name="action" value="revoke_module">
                                    <button type="submit" class="btn btn-sm btn-outline-warning w-100">
                                        <i class="fas fa-lock me-1"></i>Revoke
                                    </button>
                                <?php else: ?>
                                    <input type="hidden" name="action" value="grant_module">
                                    <button type="submit" class="btn btn-sm btn-success w-100">
                                        <i class="fas fa-unlock me-1"></i>Grant
                                    </button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-3 text-muted">No modules in this category.</div>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>

<script>
function toggleCategory(id) {
    var el = document.getElementById(id);
    var chev = document.getElementById('chev-' + id);
    if (el.style.display === 'none') {
        el.style.display = 'block';
        if (chev) chev.style.transform = 'rotate(90deg)';
    } else {
        el.style.display = 'none';
        if (chev) chev.style.transform = 'rotate(0deg)';
    }
}
</script>
