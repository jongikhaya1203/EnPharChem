<?php
$stats = $stats ?? ['total_users' => 0, 'total_projects' => 0, 'total_simulations' => 0, 'total_modules' => 0];
$recentActivity = $recentActivity ?? [];
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Admin</li>
    </ol>
</nav>

<div class="page-header">
    <h1><i class="fas fa-shield-alt me-2" style="color:var(--epc-accent);"></i>Admin Dashboard</h1>
</div>

<!-- System Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(13,110,253,.15);color:#0d6efd;"><i class="fas fa-users"></i></div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:#fff;"><?= (int)$stats['total_users'] ?></div>
                    <div style="font-size:.8rem;color:#6c757d;">Total Users</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(13,202,240,.15);color:#0dcaf0;"><i class="fas fa-project-diagram"></i></div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:#fff;"><?= (int)$stats['total_projects'] ?></div>
                    <div style="font-size:.8rem;color:#6c757d;">Total Projects</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(25,135,84,.15);color:#198754;"><i class="fas fa-play-circle"></i></div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:#fff;"><?= (int)$stats['total_simulations'] ?></div>
                    <div style="font-size:.8rem;color:#6c757d;">Total Simulations</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(255,193,7,.15);color:#ffc107;"><i class="fas fa-cubes"></i></div>
                <div>
                    <div style="font-size:1.5rem;font-weight:700;color:#fff;"><?= (int)$stats['total_modules'] ?></div>
                    <div style="font-size:.8rem;color:#6c757d;">Total Modules</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Activity -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header py-3"><i class="fas fa-history me-2"></i>Recent Activity</div>
            <div class="card-body p-0">
                <?php if (!empty($recentActivity)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Details</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentActivity as $act): ?>
                            <tr>
                                <td><?= htmlspecialchars($act['username'] ?? '') ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($act['action'] ?? '') ?></span></td>
                                <td style="font-size:.85rem;"><?= htmlspecialchars($act['details'] ?? '') ?></td>
                                <td style="font-size:.85rem;color:#6c757d;"><?= htmlspecialchars($act['created_at'] ?? '') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-4 text-muted">No recent activity.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Admin Links -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header py-3"><i class="fas fa-link me-2"></i>Quick Links</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/enpharchem/admin/users" class="btn btn-outline-primary text-start"><i class="fas fa-users-cog me-2"></i>Manage Users</a>
                    <a href="/enpharchem/admin/modules" class="btn btn-outline-primary text-start"><i class="fas fa-cubes me-2"></i>Manage Modules</a>
                    <a href="/enpharchem/admin/settings" class="btn btn-outline-primary text-start"><i class="fas fa-wrench me-2"></i>System Settings</a>
                    <a href="/enpharchem/admin/logs" class="btn btn-outline-secondary text-start"><i class="fas fa-file-alt me-2"></i>View Logs</a>
                    <a href="/enpharchem/admin/backup" class="btn btn-outline-secondary text-start"><i class="fas fa-download me-2"></i>Database Backup</a>
                </div>
            </div>
        </div>
    </div>
</div>
