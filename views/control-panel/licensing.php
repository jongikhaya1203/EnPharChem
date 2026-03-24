<?php $msg = $_GET['msg'] ?? null; ?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel">Control Panel</a></li>
        <li class="breadcrumb-item active" aria-current="page">Licensing Portal</li>
    </ol>
</nav>

<div class="page-header">
    <h1><i class="fas fa-key me-2" style="color: var(--epc-accent);"></i>Licensing Portal</h1>
    <div>
        <a href="/enpharchem/control-panel/licensing/report" class="btn btn-outline-info btn-sm me-2">
            <i class="fas fa-chart-pie me-1"></i>Report
        </a>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#issueLicenseModal">
            <i class="fas fa-plus me-1"></i>Issue New License
        </button>
    </div>
</div>

<?php if ($msg === 'issued'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>License issued successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php elseif ($msg === 'updated'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>Action completed successfully.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background: rgba(13,110,253,.15); color: #0d6efd;">
                    <i class="fas fa-id-card"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Licenses</div>
                    <div class="fs-4 fw-bold"><?= $totalLicenses ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background: rgba(25,135,84,.15); color: #198754;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="text-muted small">Active</div>
                    <div class="fs-4 fw-bold text-success"><?= $activeLicenses ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background: rgba(255,193,7,.15); color: #ffc107;">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <div>
                    <div class="text-muted small">Suspended</div>
                    <div class="fs-4 fw-bold text-warning"><?= $suspendedLicenses ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background: rgba(220,53,69,.15); color: #dc3545;">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <div class="text-muted small">Expired</div>
                    <div class="fs-4 fw-bold text-danger"><?= $expiredLicenses ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon" style="background: rgba(13,202,240,.15); color: #0dcaf0;">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div class="text-muted small">Pending Requests</div>
                    <div class="fs-4 fw-bold" style="color: #0dcaf0;"><?= $pendingRequests ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item">
        <a class="nav-link <?= empty($showRequestsTab) ? 'active' : '' ?>" data-bs-toggle="tab" href="#licensesTab" role="tab">
            <i class="fas fa-id-card me-1"></i>Licenses
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= !empty($showRequestsTab) ? 'active' : '' ?>" data-bs-toggle="tab" href="#requestsTab" role="tab">
            <i class="fas fa-inbox me-1"></i>Pending Requests
            <?php if ($pendingRequests > 0): ?>
                <span class="badge bg-danger ms-1"><?= $pendingRequests ?></span>
            <?php endif; ?>
        </a>
    </li>
</ul>

<div class="tab-content">
    <!-- Licenses Tab -->
    <div class="tab-pane fade <?= empty($showRequestsTab) ? 'show active' : '' ?>" id="licensesTab" role="tabpanel">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>License Key</th>
                                <th>User</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Issued</th>
                                <th>Expires</th>
                                <th>Modules</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($licenses)): ?>
                                <tr><td colspan="8" class="text-center text-muted py-4">No licenses found. Issue a new license to get started.</td></tr>
                            <?php else: ?>
                                <?php foreach ($licenses as $lic): ?>
                                <tr>
                                    <td>
                                        <code style="color: #0dcaf0; font-size: 0.9rem; font-family: 'Courier New', monospace;"><?= htmlspecialchars($lic['license_key']) ?></code>
                                    </td>
                                    <td>
                                        <?php if ($lic['username']): ?>
                                            <span class="fw-semibold"><?= htmlspecialchars($lic['username']) ?></span>
                                            <br><small class="text-muted"><?= htmlspecialchars($lic['email'] ?? '') ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">Unassigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $typeBadges = [
                                            'trial' => 'bg-secondary',
                                            'standard' => 'bg-primary',
                                            'professional' => 'bg-purple',
                                            'enterprise' => 'bg-warning text-dark',
                                        ];
                                        $badgeClass = $typeBadges[$lic['license_type']] ?? 'bg-secondary';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"
                                              <?php if ($lic['license_type'] === 'professional'): ?>style="background: #6f42c1;"<?php endif; ?>>
                                            <?= ucfirst($lic['license_type']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $statusBadges = [
                                            'active' => 'bg-success',
                                            'suspended' => 'bg-warning text-dark',
                                            'expired' => 'bg-danger',
                                            'revoked' => 'bg-dark',
                                        ];
                                        $sBadge = $statusBadges[$lic['status']] ?? 'bg-secondary';
                                        ?>
                                        <span class="badge <?= $sBadge ?>"><?= ucfirst($lic['status']) ?></span>
                                    </td>
                                    <td><small><?= $lic['issued_date'] ?></small></td>
                                    <td>
                                        <?php if ($lic['expiry_date']): ?>
                                            <small <?php if (strtotime($lic['expiry_date']) < time()): ?>class="text-danger"<?php endif; ?>>
                                                <?= $lic['expiry_date'] ?>
                                            </small>
                                        <?php else: ?>
                                            <small class="text-muted">Perpetual</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-success"><?= $lic['granted_modules'] ?></span>
                                        <span class="text-muted">/</span>
                                        <span class="text-muted small"><?= $lic['total_modules'] ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="/enpharchem/control-panel/licensing/manage?id=<?= $lic['id'] ?>" class="btn btn-sm btn-outline-info" title="Manage">
                                                <i class="fas fa-cog"></i>
                                            </a>
                                            <?php if ($lic['status'] === 'active'): ?>
                                                <form method="POST" action="/enpharchem/control-panel/licensing/manage?id=<?= $lic['id'] ?>" class="d-inline">
                                                    <input type="hidden" name="action" value="suspend_license">
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Suspend" onclick="return confirm('Suspend this license?')">
                                                        <i class="fas fa-pause"></i>
                                                    </button>
                                                </form>
                                            <?php elseif ($lic['status'] === 'suspended'): ?>
                                                <form method="POST" action="/enpharchem/control-panel/licensing/manage?id=<?= $lic['id'] ?>" class="d-inline">
                                                    <input type="hidden" name="action" value="activate_license">
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Activate">
                                                        <i class="fas fa-play"></i>
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

    <!-- Requests Tab -->
    <div class="tab-pane fade <?= !empty($showRequestsTab) ? 'show active' : '' ?>" id="requestsTab" role="tabpanel">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Module</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Justification</th>
                                <th>Requested</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($requests)): ?>
                                <tr><td colspan="8" class="text-center text-muted py-4">No pending requests.</td></tr>
                            <?php else: ?>
                                <?php foreach ($requests as $req): ?>
                                <tr>
                                    <td><?= $req['id'] ?></td>
                                    <td>
                                        <span class="fw-semibold"><?= htmlspecialchars($req['username'] ?? '') ?></span>
                                        <br><small class="text-muted"><?= htmlspecialchars($req['email'] ?? '') ?></small>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($req['module_name'] ?? '') ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($req['category_name'] ?? '') ?></small>
                                    </td>
                                    <td><span class="badge bg-info"><?= ucfirst(str_replace('_', ' ', $req['request_type'])) ?></span></td>
                                    <td>
                                        <?php
                                        $reqBadges = ['pending' => 'bg-warning text-dark', 'approved' => 'bg-success', 'denied' => 'bg-danger', 'cancelled' => 'bg-secondary'];
                                        ?>
                                        <span class="badge <?= $reqBadges[$req['status']] ?? 'bg-secondary' ?>"><?= ucfirst($req['status']) ?></span>
                                    </td>
                                    <td><small><?= htmlspecialchars(mb_strimwidth($req['justification'] ?? '', 0, 80, '...')) ?></small></td>
                                    <td><small><?= date('M j, Y', strtotime($req['created_at'])) ?></small></td>
                                    <td>
                                        <?php if ($req['status'] === 'pending'): ?>
                                        <div class="d-flex gap-1">
                                            <form method="POST" action="/enpharchem/control-panel/licensing/requests" class="d-inline">
                                                <input type="hidden" name="action" value="approve">
                                                <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                                                <input type="hidden" name="review_notes" value="">
                                                <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="/enpharchem/control-panel/licensing/requests" class="d-inline"
                                                  onsubmit="var n=prompt('Deny reason:'); if(!n){return false;} this.querySelector('[name=review_notes]').value=n;">
                                                <input type="hidden" name="action" value="deny">
                                                <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                                                <input type="hidden" name="review_notes" value="">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Deny">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <?php elseif ($req['status'] === 'approved'): ?>
                                            <small class="text-success"><i class="fas fa-check me-1"></i>Approved</small>
                                        <?php elseif ($req['status'] === 'denied'): ?>
                                            <small class="text-danger"><i class="fas fa-times me-1"></i>Denied</small>
                                        <?php endif; ?>
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

<!-- Issue License Modal -->
<div class="modal fade" id="issueLicenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background: var(--epc-card-bg); border-color: rgba(255,255,255,.1);">
            <form method="POST" action="/enpharchem/control-panel/licensing/issue">
                <div class="modal-header border-bottom" style="border-color: rgba(255,255,255,.08) !important;">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2" style="color: var(--epc-accent);"></i>Issue New License</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Assign to User</label>
                        <select name="user_id" class="form-select">
                            <option value="">-- Unassigned --</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['username']) ?> (<?= htmlspecialchars($u['email']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">License Type</label>
                        <select name="license_type" class="form-select" required>
                            <option value="trial">Trial</option>
                            <option value="standard">Standard</option>
                            <option value="professional">Professional</option>
                            <option value="enterprise">Enterprise</option>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Expiry Date</label>
                            <input type="date" name="expiry_date" class="form-control">
                            <small class="text-muted">Leave blank for perpetual</small>
                        </div>
                        <div class="col-3">
                            <label class="form-label">Max Modules</label>
                            <input type="number" name="max_modules" class="form-control" value="5" min="1">
                        </div>
                        <div class="col-3">
                            <label class="form-label">Max Users</label>
                            <input type="number" name="max_users" class="form-control" value="1" min="1">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Internal notes about this license..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top" style="border-color: rgba(255,255,255,.08) !important;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-key me-1"></i>Issue License</button>
                </div>
            </form>
        </div>
    </div>
</div>
