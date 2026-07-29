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

<?php /* The Add User modal was removed: it posted to /admin/users/create, which
   has no route or handler, and offered an invalid 'user' role. A proper
   admin create-user flow (password hashing, uniqueness check, role validated
   against the schema enum) is a good follow-up. Existing users can still be
   enabled/disabled from the table above. */ ?>
