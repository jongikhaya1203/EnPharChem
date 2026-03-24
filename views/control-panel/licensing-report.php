<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel">Control Panel</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel/licensing">Licensing</a></li>
        <li class="breadcrumb-item active" aria-current="page">Report</li>
    </ol>
</nav>

<div class="page-header">
    <h1><i class="fas fa-chart-pie me-2" style="color: var(--epc-accent);"></i>Licensing Report</h1>
    <a href="/enpharchem/control-panel/licensing" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Back to Licensing
    </a>
</div>

<!-- Overview Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <div class="fs-3 fw-bold"><?= $totalLicenses ?></div>
                <small class="text-muted">Total Licenses</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <div class="fs-3 fw-bold text-success"><?= $activeLicenses ?></div>
                <small class="text-muted">Active</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <div class="fs-3 fw-bold text-warning"><?= $suspendedLicenses ?></div>
                <small class="text-muted">Suspended</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <div class="fs-3 fw-bold text-danger"><?= $expiredLicenses ?></div>
                <small class="text-muted">Expired</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <div class="fs-3 fw-bold" style="color: #6c757d;"><?= $revokedLicenses ?></div>
                <small class="text-muted">Revoked</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- License Type Distribution -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2 text-info"></i>License Type Distribution
            </div>
            <div class="card-body">
                <?php if (empty($typeDistribution)): ?>
                    <p class="text-muted text-center">No license data available.</p>
                <?php else: ?>
                    <?php
                    $typeColors = ['trial' => '#6c757d', 'standard' => '#0d6efd', 'professional' => '#6f42c1', 'enterprise' => '#ffc107'];
                    $maxCount = max(array_column($typeDistribution, 'cnt'));
                    ?>
                    <?php foreach ($typeDistribution as $td): ?>
                        <?php $pct = $maxCount > 0 ? round($td['cnt'] / $maxCount * 100) : 0; ?>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="width: 100px;" class="fw-semibold text-end"><?= ucfirst($td['license_type']) ?></div>
                            <div class="flex-fill">
                                <div class="progress" style="height: 24px; background: rgba(255,255,255,.06);">
                                    <div class="progress-bar" style="width: <?= $pct ?>%; background: <?= $typeColors[$td['license_type']] ?? '#6c757d' ?>;">
                                        <?= $td['cnt'] ?>
                                    </div>
                                </div>
                            </div>
                            <div style="width: 40px;" class="text-end text-muted"><?= $td['cnt'] ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Expiring Soon -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Expiring Soon (Next 30 Days)</span>
                <span class="badge bg-warning text-dark"><?= count($expiringSoon) ?></span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 small">
                        <thead>
                            <tr>
                                <th>License Key</th>
                                <th>User</th>
                                <th>Expires</th>
                                <th>Days Left</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($expiringSoon)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">No licenses expiring in the next 30 days.</td></tr>
                            <?php else: ?>
                                <?php foreach ($expiringSoon as $exp): ?>
                                    <?php $daysLeft = (int)((strtotime($exp['expiry_date']) - time()) / 86400); ?>
                                    <tr>
                                        <td>
                                            <a href="/enpharchem/control-panel/licensing/manage?id=<?= $exp['id'] ?>" style="color: #0dcaf0; font-family: 'Courier New', monospace; text-decoration: none;">
                                                <?= htmlspecialchars($exp['license_key']) ?>
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($exp['username'] ?? 'Unassigned') ?></td>
                                        <td><?= $exp['expiry_date'] ?></td>
                                        <td>
                                            <span class="badge <?= $daysLeft <= 7 ? 'bg-danger' : ($daysLeft <= 14 ? 'bg-warning text-dark' : 'bg-info') ?>">
                                                <?= $daysLeft ?> days
                                            </span>
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
</div>

<!-- Module Coverage -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-th me-2" style="color: var(--epc-accent);"></i>Module Coverage
        <small class="text-muted ms-2">(Number of licenses with each module granted)</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 small">
                <thead>
                    <tr>
                        <th>Module</th>
                        <th>Category</th>
                        <th>Licenses with Grant</th>
                        <th>Coverage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($moduleCoverage)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-3">No module data available.</td></tr>
                    <?php else: ?>
                        <?php $maxGrants = max(array_column($moduleCoverage, 'grant_count')) ?: 1; ?>
                        <?php foreach ($moduleCoverage as $mc): ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($mc['module_name']) ?></td>
                            <td><small class="text-muted"><?= htmlspecialchars($mc['category_name']) ?></small></td>
                            <td>
                                <span class="badge <?= $mc['grant_count'] > 0 ? 'bg-success' : 'bg-secondary' ?>"><?= $mc['grant_count'] ?></span>
                            </td>
                            <td style="width: 30%;">
                                <?php $barPct = $maxGrants > 0 ? round($mc['grant_count'] / $maxGrants * 100) : 0; ?>
                                <div class="progress" style="height: 8px; background: rgba(255,255,255,.06);">
                                    <div class="progress-bar bg-info" style="width: <?= $barPct ?>%;"></div>
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

<!-- Recent Activity -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-history me-2 text-info"></i>Recent Activity
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 small">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>License</th>
                        <th>Action</th>
                        <th>By</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentActivity)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-3">No recent activity.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentActivity as $act): ?>
                        <tr>
                            <td><small><?= date('M j, Y H:i', strtotime($act['created_at'])) ?></small></td>
                            <td>
                                <?php if ($act['license_key']): ?>
                                    <code style="color: #0dcaf0; font-family: 'Courier New', monospace;"><?= htmlspecialchars($act['license_key']) ?></code>
                                <?php else: ?>
                                    <span class="text-muted">--</span>
                                <?php endif; ?>
                            </td>
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
                                ];
                                $aColor = $actionColors[$act['action']] ?? 'text-muted';
                                ?>
                                <span class="<?= $aColor ?>"><?= htmlspecialchars(str_replace('_', ' ', ucfirst($act['action']))) ?></span>
                            </td>
                            <td><?= htmlspecialchars($act['performed_by_name'] ?? 'System') ?></td>
                            <td><small class="text-muted"><?= htmlspecialchars(mb_strimwidth($act['details'] ?? '', 0, 100, '...')) ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
