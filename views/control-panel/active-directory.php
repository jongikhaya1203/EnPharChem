<!-- Active Directory Management -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard" class="text-decoration-none" style="color: var(--epc-accent);">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel" class="text-decoration-none" style="color: var(--epc-accent);">Control Panel</a></li>
        <li class="breadcrumb-item active text-light" aria-current="page">Active Directory</li>
    </ol>
</nav>

<h2 class="text-light mb-4"><i class="bi bi-shield-lock-fill me-2" style="color: var(--epc-accent);"></i>Active Directory Management</h2>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <i class="bi bi-collection-fill text-info fs-4"></i>
                <div class="text-light fs-3 fw-bold"><?= number_format($adStats['total_groups'] ?? 0) ?></div>
                <div class="text-secondary small">Total Groups</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <i class="bi bi-people-fill text-primary fs-4"></i>
                <div class="text-light fs-3 fw-bold"><?= number_format($adStats['total_users'] ?? 0) ?></div>
                <div class="text-secondary small">Total Users</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                <div class="text-light fs-3 fw-bold"><?= htmlspecialchars($adStats['active_pct'] ?? '0') ?>%</div>
                <div class="text-secondary small">Active Users</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <i class="bi bi-arrow-repeat text-warning fs-4"></i>
                <div class="text-light fs-6 fw-bold"><?= htmlspecialchars($adStats['last_sync'] ?? 'Never') ?></div>
                <div class="text-secondary small">Last Sync</div>
            </div>
        </div>
    </div>
</div>

<!-- Groups Section -->
<div class="card border-0 mb-4" style="background: var(--epc-card-bg);">
    <div class="card-header d-flex justify-content-between align-items-center border-bottom border-secondary" style="background: var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="bi bi-collection-fill me-2 text-info"></i>Groups</h5>
        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#addGroupModal">
            <i class="bi bi-plus-lg me-1"></i>Add Group
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0" style="background: transparent;">
                <thead>
                    <tr class="text-secondary small text-uppercase">
                        <th>Name</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Members</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($groups)): ?>
                        <?php foreach ($groups as $group): ?>
                            <tr>
                                <td class="text-light fw-semibold"><?= htmlspecialchars($group['name'] ?? '') ?></td>
                                <td class="text-secondary"><?= htmlspecialchars($group['description'] ?? '') ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars(ucfirst($group['group_type'] ?? '')) ?></span></td>
                                <td class="text-light"><?= number_format($group['member_count'] ?? 0) ?></td>
                                <td>
                                    <?php
                                    $gStatus = $group['status'] ?? 'active';
                                    $gBadge = $gStatus === 'active' ? 'bg-success' : 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $gBadge ?>"><?= ucfirst($gStatus) ?></span>
                                </td>
                                <td class="text-secondary small"><?= htmlspecialchars($group['created_at'] ?? '') ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="delete_group">
                                        <input type="hidden" name="group_id" value="<?= htmlspecialchars($group['id'] ?? '') ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this group?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-secondary py-4">No groups found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Group Modal -->
<div class="modal fade" id="addGroupModal" tabindex="-1" aria-labelledby="addGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background: var(--epc-card-bg); border: 1px solid rgba(255,255,255,0.1);">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title text-light" id="addGroupModalLabel"><i class="bi bi-plus-circle me-2 text-info"></i>Add Group</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="create_group">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="groupName" class="form-label text-light">Group Name</label>
                        <input type="text" class="form-control bg-dark text-light border-secondary" id="groupName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="groupDesc" class="form-label text-light">Description</label>
                        <input type="text" class="form-control bg-dark text-light border-secondary" id="groupDesc" name="description">
                    </div>
                    <div class="mb-3">
                        <label for="groupType" class="form-label text-light">Group Type</label>
                        <select class="form-select bg-dark text-light border-secondary" id="groupType" name="group_type" required>
                            <option value="security">Security</option>
                            <option value="distribution">Distribution</option>
                            <option value="organizational">Organizational</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Create Group</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Users Section -->
<div class="card border-0 mb-4" style="background: var(--epc-card-bg);">
    <div class="card-header d-flex justify-content-between align-items-center border-bottom border-secondary" style="background: var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="bi bi-people-fill me-2 text-primary"></i>Users</h5>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-plus-lg me-1"></i>Add User
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0" style="background: transparent;">
                <thead>
                    <tr class="text-secondary small text-uppercase">
                        <th>Display Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Title</th>
                        <th>Group</th>
                        <th>Status</th>
                        <th>Last Logon</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($adUsers)): ?>
                        <?php foreach ($adUsers as $user): ?>
                            <tr>
                                <td class="text-light fw-semibold"><?= htmlspecialchars($user['display_name'] ?? '') ?></td>
                                <td class="text-secondary"><?= htmlspecialchars($user['email'] ?? '') ?></td>
                                <td class="text-light"><?= htmlspecialchars($user['department'] ?? '') ?></td>
                                <td class="text-secondary"><?= htmlspecialchars($user['title'] ?? '') ?></td>
                                <td class="text-light"><?= htmlspecialchars($user['group_name'] ?? '') ?></td>
                                <td>
                                    <?php
                                    $uStatus = $user['account_status'] ?? $user['status'] ?? 'active';
                                    $statusColors = [
                                        'active' => 'bg-success',
                                        'disabled' => 'bg-danger',
                                        'locked' => 'bg-warning text-dark',
                                        'expired' => 'bg-secondary',
                                    ];
                                    $uBadge = $statusColors[$uStatus] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $uBadge ?>"><?= ucfirst($uStatus) ?></span>
                                </td>
                                <td class="text-secondary small"><?= htmlspecialchars($user['last_logon'] ?? 'Never') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-secondary py-4">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: var(--epc-card-bg); border: 1px solid rgba(255,255,255,0.1);">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title text-light" id="addUserModalLabel"><i class="bi bi-person-plus-fill me-2 text-primary"></i>Add User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="create_user">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="userName" class="form-label text-light">Username</label>
                            <input type="text" class="form-control bg-dark text-light border-secondary" id="userName" name="username" required>
                        </div>
                        <div class="col-md-6">
                            <label for="userDisplayName" class="form-label text-light">Display Name</label>
                            <input type="text" class="form-control bg-dark text-light border-secondary" id="userDisplayName" name="display_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="userEmail" class="form-label text-light">Email</label>
                            <input type="email" class="form-control bg-dark text-light border-secondary" id="userEmail" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label for="userDepartment" class="form-label text-light">Department</label>
                            <input type="text" class="form-control bg-dark text-light border-secondary" id="userDepartment" name="department">
                        </div>
                        <div class="col-md-6">
                            <label for="userTitle" class="form-label text-light">Title</label>
                            <input type="text" class="form-control bg-dark text-light border-secondary" id="userTitle" name="title">
                        </div>
                        <div class="col-md-6">
                            <label for="userGroup" class="form-label text-light">Group</label>
                            <select class="form-select bg-dark text-light border-secondary" id="userGroup" name="group_id">
                                <option value="">-- Select Group --</option>
                                <?php foreach ($groups as $group): ?>
                                    <option value="<?= htmlspecialchars($group['id'] ?? '') ?>"><?= htmlspecialchars($group['name'] ?? '') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="userPhone" class="form-label text-light">Phone</label>
                            <input type="text" class="form-control bg-dark text-light border-secondary" id="userPhone" name="phone">
                        </div>
                        <div class="col-md-6">
                            <label for="userLocation" class="form-label text-light">Location</label>
                            <input type="text" class="form-control bg-dark text-light border-secondary" id="userLocation" name="location">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>
