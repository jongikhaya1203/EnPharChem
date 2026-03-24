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
    <span class="text-muted"><?= count($modules ?? []) ?> modules across <?= count($categories ?? []) ?> categories</span>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Module updated successfully.</div>
<?php endif; ?>

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
                        <?php foreach ($catModules as $mod):
                            $isActive = $mod['is_active'] ?? 1;
                            $modId = $mod['id'] ?? 0;
                        ?>
                        <tr style="<?= !$isActive ? 'opacity:.6;' : '' ?>">
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
                                <span class="badge bg-<?= ($mod['license_required'] ?? 'standard') === 'enterprise' ? 'warning text-dark' : (($mod['license_required'] ?? 'standard') === 'professional' ? 'info' : 'success') ?>">
                                    <?= ucfirst($mod['license_required'] ?? 'standard') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($isActive): ?>
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><i class="fas fa-ban me-1"></i>Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <!-- Edit Button -->
                                    <button class="btn btn-sm btn-outline-primary" title="Edit Module"
                                            onclick="openEditModal(<?= $modId ?>, '<?= htmlspecialchars(addslashes($mod['name']), ENT_QUOTES) ?>', '<?= htmlspecialchars($mod['version'] ?? '1.0.0') ?>', '<?= htmlspecialchars($mod['icon'] ?? 'fa-cube') ?>', '<?= htmlspecialchars($mod['license_required'] ?? 'standard') ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <!-- Toggle Active/Inactive -->
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="module_id" value="<?= $modId ?>">
                                        <?php if ($isActive): ?>
                                            <input type="hidden" name="action" value="deactivate">
                                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Deactivate Module" onclick="return confirm('Deactivate <?= htmlspecialchars(addslashes($mod['name']), ENT_QUOTES) ?>?')">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                        <?php else: ?>
                                            <input type="hidden" name="action" value="activate">
                                            <button type="submit" class="btn btn-sm btn-success" title="Activate Module">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                    <!-- View Module -->
                                    <a href="/enpharchem/modules/view?slug=<?= htmlspecialchars($mod['slug'] ?? '') ?>" class="btn btn-sm btn-outline-info" title="View Module">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
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

<!-- Edit Module Modal -->
<div class="modal fade" id="editModuleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:var(--epc-card-bg, #212529);border:1px solid rgba(255,255,255,.08);">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title text-light"><i class="fas fa-edit me-2" style="color:var(--epc-accent, #0dcaf0);"></i>Edit Module</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="update_module">
                <input type="hidden" name="module_id" id="editModuleId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-light">Module Name</label>
                        <input type="text" class="form-control bg-dark text-light border-secondary" name="module_name" id="editModuleName">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Version</label>
                        <input type="text" class="form-control bg-dark text-light border-secondary" name="module_version" id="editModuleVersion">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">Icon (Font Awesome)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-dark border-secondary text-light" id="editIconPreview"><i class="fas fa-cube"></i></span>
                            <input type="text" class="form-control bg-dark text-light border-secondary" name="module_icon" id="editModuleIcon" oninput="document.getElementById('editIconPreview').innerHTML='<i class=\'fas '+this.value+'\'></i>'">
                        </div>
                        <small class="text-secondary">e.g., fa-bolt, fa-flask, fa-cogs</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-light">License Required</label>
                        <select class="form-select bg-dark text-light border-secondary" name="license_required" id="editModuleLicense">
                            <option value="standard">Standard</option>
                            <option value="professional">Professional</option>
                            <option value="enterprise">Enterprise</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditModal(id, name, version, icon, license) {
    document.getElementById('editModuleId').value = id;
    document.getElementById('editModuleName').value = name;
    document.getElementById('editModuleVersion').value = version;
    document.getElementById('editModuleIcon').value = icon;
    document.getElementById('editModuleLicense').value = license;
    document.getElementById('editIconPreview').innerHTML = '<i class="fas ' + icon + '"></i>';
    new bootstrap.Modal(document.getElementById('editModuleModal')).show();
}
</script>
