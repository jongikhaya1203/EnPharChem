<?php $users = $users ?? []; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/admin">Admin</a></li>
        <li class="breadcrumb-item active">Users</li>
    </ol>
</nav>

<div class="page-header">
    <h1><i class="fas fa-users-cog me-2" style="color:var(--epc-accent);"></i>User Management</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="fas fa-user-plus me-1"></i>Add User</button>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (!empty($users)): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>License</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th style="width:140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td style="font-weight:500;"><?= htmlspecialchars($user['username']) ?></td>
                        <td style="font-size:.85rem;"><?= htmlspecialchars($user['email'] ?? '') ?></td>
                        <td>
                            <?php
                            $roleColors = ['admin' => 'danger', 'engineer' => 'primary', 'viewer' => 'secondary', 'user' => 'info'];
                            $role = $user['role'] ?? 'user';
                            $rColor = $roleColors[$role] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $rColor ?>"><?= htmlspecialchars(ucfirst($role)) ?></span>
                        </td>
                        <td>
                            <?php if (!empty($user['license_type'])): ?>
                            <span class="badge bg-success"><?= htmlspecialchars(ucfirst($user['license_type'])) ?></span>
                            <?php else: ?>
                            <span class="badge bg-secondary">None</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php $active = ($user['is_active'] ?? 1); ?>
                            <span class="badge bg-<?= $active ? 'success' : 'danger' ?>"><?= $active ? 'Active' : 'Inactive' ?></span>
                        </td>
                        <td style="font-size:.85rem;color:#6c757d;"><?= htmlspecialchars($user['last_login'] ?? 'Never') ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-outline-warning me-1" title="Toggle Status"><i class="fas fa-power-off"></i></button>
                            <button class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this user?');"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-5 text-muted">No users found.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:#212529;border:1px solid rgba(255,255,255,.08);">
            <div class="modal-header" style="border-color:rgba(255,255,255,.08);">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add New User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/enpharchem/admin/users/create">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role">
                            <option value="user">User</option>
                            <option value="engineer">Engineer</option>
                            <option value="admin">Admin</option>
                            <option value="viewer">Viewer</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="border-color:rgba(255,255,255,.08);">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>
