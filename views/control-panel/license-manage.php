<?php $msg = $_GET['msg'] ?? null; ?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel">Control Panel</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel/licensing">Licensing</a></li>
        <li class="breadcrumb-item active" aria-current="page">Manage License</li>
    </ol>
</nav>

<?php if ($msg === 'updated' || $msg === 'bulk_updated'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>Action completed successfully.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- License Header Card -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-3 mb-3 mb-lg-0">
                    <div class="stat-icon" style="background: rgba(13,202,240,.15); color: #0dcaf0; width: 56px; height: 56px; font-size: 1.4rem;">
                        <i class="fas fa-key"></i>
                    </div>
                    <div>
                        <h4 class="mb-1" style="font-family: 'Courier New', monospace; color: #0dcaf0; letter-spacing: 1px;">
                            <?= htmlspecialchars($license['license_key']) ?>
                        </h4>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <?php
                            $typeBadges = ['trial' => 'bg-secondary', 'standard' => 'bg-primary', 'professional' => '', 'enterprise' => 'bg-warning text-dark'];
                            $tBadge = $typeBadges[$license['license_type']] ?? 'bg-secondary';
                            ?>
                            <span class="badge <?= $tBadge ?>"
                                  <?php if ($license['license_type'] === 'professional'): ?>style="background: #6f42c1;"<?php endif; ?>>
                                <?= ucfirst($license['license_type']) ?>
                            </span>
                            <?php
                            $statusBadges = ['active' => 'bg-success', 'suspended' => 'bg-warning text-dark', 'expired' => 'bg-danger', 'revoked' => 'bg-dark'];
                            $sBadge = $statusBadges[$license['status']] ?? 'bg-secondary';
                            ?>
                            <span class="badge <?= $sBadge ?>"><?= ucfirst($license['status']) ?></span>
                            <?php if ($license['username']): ?>
                                <span class="text-muted">|</span>
                                <span><i class="fas fa-user me-1 text-muted"></i><?= htmlspecialchars($license['username']) ?></span>
                                <small class="text-muted">(<?= htmlspecialchars($license['email'] ?? '') ?>)</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row text-center">
                    <div class="col-6">
                        <small class="text-muted d-block">Issued</small>
                        <span class="fw-semibold"><?= $license['issued_date'] ?></span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Expires</small>
                        <span class="fw-semibold <?= ($license['expiry_date'] && strtotime($license['expiry_date']) < time()) ? 'text-danger' : '' ?>">
                            <?= $license['expiry_date'] ?: 'Perpetual' ?>
                        </span>
                    </div>
                </div>
                <div class="row text-center mt-2">
                    <div class="col-6">
                        <small class="text-muted d-block">Max Modules</small>
                        <span class="fw-semibold"><?= $license['max_modules'] ?></span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Max Users</small>
                        <span class="fw-semibold"><?= $license['max_users'] ?></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="text-center mb-2">
                    <small class="text-muted d-block">Module Coverage</small>
                    <span class="fs-4 fw-bold text-success"><?= $grantedCount ?></span>
                    <span class="text-muted">/</span>
                    <span class="text-muted"><?= $totalModules ?></span>
                    <?php $pct = $totalModules > 0 ? round($grantedCount / $totalModules * 100) : 0; ?>
                    <div class="progress mt-1" style="height: 6px; background: rgba(255,255,255,.08);">
                        <div class="progress-bar bg-success" style="width: <?= $pct ?>%"></div>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center small">
                    <span class="badge bg-success"><?= $grantedCount ?> granted</span>
                    <span class="badge bg-danger"><?= $deniedCount ?> denied</span>
                    <span class="badge bg-warning text-dark"><?= $pendingCount ?> pending</span>
                    <span class="badge bg-secondary"><?= $revokedCount ?> revoked</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="fas fa-bolt me-2 text-warning"></i>Quick Actions</span>
    </div>
    <div class="card-body">
        <div class="d-flex gap-2 flex-wrap">
            <?php if ($license['status'] === 'active'): ?>
                <form method="POST" action="/enpharchem/control-panel/licensing/manage?id=<?= $license['id'] ?>" class="d-inline">
                    <input type="hidden" name="action" value="suspend_license">
                    <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Suspend this license?')">
                        <i class="fas fa-pause me-1"></i>Suspend License
                    </button>
                </form>
            <?php elseif ($license['status'] === 'suspended'): ?>
                <form method="POST" action="/enpharchem/control-panel/licensing/manage?id=<?= $license['id'] ?>" class="d-inline">
                    <input type="hidden" name="action" value="activate_license">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-play me-1"></i>Activate License
                    </button>
                </form>
            <?php endif; ?>

            <?php if ($license['status'] !== 'revoked'): ?>
                <form method="POST" action="/enpharchem/control-panel/licensing/manage?id=<?= $license['id'] ?>" class="d-inline">
                    <input type="hidden" name="action" value="revoke_license">
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Revoke this license? This cannot be easily undone.')">
                        <i class="fas fa-ban me-1"></i>Revoke License
                    </button>
                </form>
            <?php endif; ?>

            <div class="vr mx-1"></div>

            <form method="POST" action="/enpharchem/control-panel/licensing/bulk" class="d-inline">
                <input type="hidden" name="license_id" value="<?= $license['id'] ?>">
                <input type="hidden" name="action" value="grant_by_tier">
                <button type="submit" class="btn btn-outline-info btn-sm" onclick="return confirm('This will revoke existing grants and re-grant based on the license tier (<?= $license['license_type'] ?>). Continue?')">
                    <i class="fas fa-layer-group me-1"></i>Grant by Tier (<?= ucfirst($license['license_type']) ?>)
                </button>
            </form>

            <form method="POST" action="/enpharchem/control-panel/licensing/bulk" class="d-inline">
                <input type="hidden" name="license_id" value="<?= $license['id'] ?>">
                <input type="hidden" name="action" value="grant_all">
                <button type="submit" class="btn btn-outline-success btn-sm" onclick="return confirm('Grant ALL modules to this license?')">
                    <i class="fas fa-check-double me-1"></i>Grant All Modules
                </button>
            </form>

            <form method="POST" action="/enpharchem/control-panel/licensing/bulk" class="d-inline">
                <input type="hidden" name="license_id" value="<?= $license['id'] ?>">
                <input type="hidden" name="action" value="revoke_all">
                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Revoke ALL modules from this license?')">
                    <i class="fas fa-times-circle me-1"></i>Revoke All Modules
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Module Management Section -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-cubes me-2" style="color: var(--epc-accent);"></i>Module Management
        <small class="text-muted ms-2">(<?= $totalModules ?> modules across <?= count($categories) ?> categories)</small>
    </div>
    <div class="card-body p-0">
        <div class="accordion" id="moduleCategoriesAccordion">
            <?php foreach ($categories as $idx => $cat): ?>
                <?php
                $catModules = $modulesByCategory[$cat['id']] ?? [];
                $catGranted = 0;
                $catTotal = count($catModules);
                foreach ($catModules as $cm) {
                    if (isset($moduleGrants[$cm['id']]) && $moduleGrants[$cm['id']]['status'] === 'granted') {
                        $catGranted++;
                    }
                }
                ?>
                <div class="accordion-item" style="background: transparent; border-color: rgba(255,255,255,.06);">
                    <h2 class="accordion-header">
                        <button class="accordion-button <?= $idx > 0 ? 'collapsed' : '' ?>" type="button"
                                data-bs-toggle="collapse" data-bs-target="#cat<?= $cat['id'] ?>"
                                style="background: rgba(255,255,255,.03); color: #dee2e6; box-shadow: none;">
                            <span class="d-flex align-items-center gap-2 w-100">
                                <i class="fas <?= htmlspecialchars($cat['icon'] ?? 'fa-puzzle-piece') ?>" style="color: var(--epc-accent); width: 20px; text-align: center;"></i>
                                <strong><?= htmlspecialchars($cat['name']) ?></strong>
                                <span class="badge bg-success ms-2"><?= $catGranted ?></span>
                                <span class="text-muted small">/ <?= $catTotal ?> modules</span>
                                <?php if ($catGranted === $catTotal && $catTotal > 0): ?>
                                    <span class="badge bg-success ms-auto me-3"><i class="fas fa-check"></i> All Granted</span>
                                <?php elseif ($catGranted === 0): ?>
                                    <span class="badge bg-secondary ms-auto me-3">None Granted</span>
                                <?php else: ?>
                                    <span class="badge bg-info ms-auto me-3"><?= round($catGranted / $catTotal * 100) ?>%</span>
                                <?php endif; ?>
                            </span>
                        </button>
                    </h2>
                    <div id="cat<?= $cat['id'] ?>" class="accordion-collapse collapse <?= $idx === 0 ? 'show' : '' ?>" data-bs-parent="#moduleCategoriesAccordion">
                        <div class="accordion-body p-0">
                            <!-- Category-level actions -->
                            <div class="d-flex gap-2 p-3 border-bottom" style="border-color: rgba(255,255,255,.06) !important; background: rgba(255,255,255,.02);">
                                <form method="POST" action="/enpharchem/control-panel/licensing/manage?id=<?= $license['id'] ?>" class="d-inline">
                                    <input type="hidden" name="action" value="grant_all_category">
                                    <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Grant all modules in <?= htmlspecialchars($cat['name']) ?>?')">
                                        <i class="fas fa-check-double me-1"></i>Grant All in Category
                                    </button>
                                </form>
                                <form method="POST" action="/enpharchem/control-panel/licensing/manage?id=<?= $license['id'] ?>" class="d-inline"
                                      onsubmit="var r=prompt('Deny reason for all modules in this category:','Category-level denial'); if(!r){return false;} this.querySelector('[name=deny_reason]').value=r;">
                                    <input type="hidden" name="action" value="deny_all_category">
                                    <input type="hidden" name="category_id" value="<?= $cat['id'] ?>">
                                    <input type="hidden" name="deny_reason" value="">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-times-circle me-1"></i>Deny All in Category
                                    </button>
                                </form>
                            </div>

                            <!-- Modules Table -->
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 small">
                                    <thead>
                                        <tr>
                                            <th style="width: 30%;">Module</th>
                                            <th style="width: 30%;">Description</th>
                                            <th style="width: 15%;">Status</th>
                                            <th style="width: 25%;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($catModules)): ?>
                                            <tr><td colspan="4" class="text-center text-muted py-3">No modules in this category.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($catModules as $mod): ?>
                                                <?php
                                                $grant = $moduleGrants[$mod['id']] ?? null;
                                                $mStatus = $grant ? $grant['status'] : 'not_assigned';
                                                ?>
                                                <tr>
                                                    <td>
                                                        <span class="fw-semibold"><?= htmlspecialchars($mod['name']) ?></span>
                                                        <?php if ($grant && $grant['granted_by_name'] ?? false): ?>
                                                            <br><small class="text-muted">by <?= htmlspecialchars($grant['granted_by_name']) ?> on <?= $grant['granted_date'] ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted"><?= htmlspecialchars(mb_strimwidth($mod['description'] ?? '', 0, 100, '...')) ?></small>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $msBadges = [
                                                            'granted' => '<span class="badge bg-success">Granted</span>',
                                                            'denied' => '<span class="badge bg-danger">Denied</span>',
                                                            'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
                                                            'revoked' => '<span class="badge bg-secondary">Revoked</span>',
                                                            'not_assigned' => '<span class="badge" style="border: 1px solid rgba(255,255,255,.2); color: #6c757d;">Not Assigned</span>',
                                                        ];
                                                        echo $msBadges[$mStatus] ?? $msBadges['not_assigned'];
                                                        ?>
                                                        <?php if ($mStatus === 'denied' && ($grant['denied_reason'] ?? '')): ?>
                                                            <br><small class="text-danger"><i class="fas fa-info-circle me-1"></i><?= htmlspecialchars(mb_strimwidth($grant['denied_reason'], 0, 60, '...')) ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-1 flex-wrap">
                                                            <?php if ($mStatus === 'not_assigned' || $mStatus === 'pending' || $mStatus === 'denied' || $mStatus === 'revoked'): ?>
                                                                <!-- Grant button -->
                                                                <form method="POST" action="/enpharchem/control-panel/licensing/manage?id=<?= $license['id'] ?>" class="d-inline">
                                                                    <input type="hidden" name="action" value="grant_module">
                                                                    <input type="hidden" name="module_id" value="<?= $mod['id'] ?>">
                                                                    <button type="submit" class="btn btn-sm btn-success" title="Grant this module">
                                                                        <i class="fas fa-check me-1"></i><?= ($mStatus === 'revoked') ? 'Re-grant' : (($mStatus === 'denied') ? 'Override' : 'Grant') ?>
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>

                                                            <?php if ($mStatus === 'not_assigned' || $mStatus === 'pending'): ?>
                                                                <!-- Deny button -->
                                                                <form method="POST" action="/enpharchem/control-panel/licensing/manage?id=<?= $license['id'] ?>" class="d-inline"
                                                                      onsubmit="var r=prompt('Reason for denying <?= htmlspecialchars(addslashes($mod['name'])) ?>:'); if(!r){return false;} this.querySelector('[name=deny_reason]').value=r;">
                                                                    <input type="hidden" name="action" value="deny_module">
                                                                    <input type="hidden" name="module_id" value="<?= $mod['id'] ?>">
                                                                    <input type="hidden" name="deny_reason" value="">
                                                                    <button type="submit" class="btn btn-sm btn-danger" title="Deny this module">
                                                                        <i class="fas fa-times me-1"></i>Deny
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>

                                                            <?php if ($mStatus === 'granted'): ?>
                                                                <!-- Revoke button -->
                                                                <form method="POST" action="/enpharchem/control-panel/licensing/manage?id=<?= $license['id'] ?>" class="d-inline">
                                                                    <input type="hidden" name="action" value="revoke_module">
                                                                    <input type="hidden" name="module_id" value="<?= $mod['id'] ?>">
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Revoke this module" onclick="return confirm('Revoke access to <?= htmlspecialchars(addslashes($mod['name'])) ?>?')">
                                                                        <i class="fas fa-minus-circle me-1"></i>Revoke
                                                                    </button>
                                                                </form>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- License Notes -->
<?php if (!empty($license['notes'])): ?>
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-sticky-note me-2 text-warning"></i>Notes
    </div>
    <div class="card-body">
        <p class="mb-0"><?= nl2br(htmlspecialchars($license['notes'])) ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Audit Log -->
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="fas fa-history me-2 text-info"></i>Audit Log</span>
        <small class="text-muted">Last 50 actions</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 small">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Action</th>
                        <th>Performed By</th>
                        <th>Details</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($auditLog)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-3">No audit log entries yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($auditLog as $log): ?>
                        <tr>
                            <td><small><?= date('M j, Y H:i', strtotime($log['created_at'])) ?></small></td>
                            <td>
                                <?php
                                $actionColors = [
                                    'license_issued' => 'text-info',
                                    'module_granted' => 'text-success',
                                    'module_denied' => 'text-danger',
                                    'module_revoked' => 'text-warning',
                                    'category_granted' => 'text-success',
                                    'category_denied' => 'text-danger',
                                    'license_suspended' => 'text-warning',
                                    'license_activated' => 'text-success',
                                    'license_revoked' => 'text-danger',
                                    'bulk_grant_all' => 'text-success',
                                    'bulk_revoke_all' => 'text-danger',
                                    'bulk_grant_by_tier' => 'text-info',
                                    'request_approved' => 'text-success',
                                    'request_denied' => 'text-danger',
                                ];
                                $aColor = $actionColors[$log['action']] ?? 'text-muted';
                                ?>
                                <span class="<?= $aColor ?> fw-semibold"><?= htmlspecialchars(str_replace('_', ' ', ucfirst($log['action']))) ?></span>
                            </td>
                            <td><?= htmlspecialchars($log['performed_by_name'] ?? 'System') ?></td>
                            <td><small><?= htmlspecialchars(mb_strimwidth($log['details'] ?? '', 0, 120, '...')) ?></small></td>
                            <td><small class="text-muted"><?= htmlspecialchars($log['ip_address'] ?? '') ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mb-4">
    <a href="/enpharchem/control-panel/licensing" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Licensing Portal
    </a>
</div>

<style>
    .accordion-button::after {
        filter: invert(1) grayscale(100%) brightness(200%);
    }
    .accordion-button:not(.collapsed) {
        background: rgba(13, 202, 240, .08) !important;
        color: #0dcaf0 !important;
    }
</style>
