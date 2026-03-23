<?php
$module = $module ?? [];
$projects = $projects ?? [];
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/modules">Modules</a></li>
        <li class="breadcrumb-item active">Select Project</li>
    </ol>
</nav>

<div class="card mb-4">
    <div class="card-body text-center py-4">
        <div style="width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:1.5rem;margin-bottom:12px;">
            <i class="fas <?= htmlspecialchars($module['icon'] ?? 'fa-cube') ?>"></i>
        </div>
        <h3><?= htmlspecialchars($module['name'] ?? '') ?></h3>
        <p class="text-muted">Select a project to launch this module, or create a new project.</p>
    </div>
</div>

<?php if (!empty($projects)): ?>
<div class="row g-3">
    <?php foreach ($projects as $p): ?>
    <div class="col-md-6 col-lg-4">
        <a href="/enpharchem/modules/launch?slug=<?= htmlspecialchars($module['slug'] ?? '') ?>&project_id=<?= $p['id'] ?>" class="text-decoration-none">
            <div class="card h-100" style="cursor:pointer;transition:all .2s;" onmouseover="this.style.borderColor='var(--epc-primary)'" onmouseout="this.style.borderColor='rgba(255,255,255,.08)'">
                <div class="card-body">
                    <h5 style="font-size:1rem;color:#fff;margin-bottom:6px;"><?= htmlspecialchars($p['name']) ?></h5>
                    <span class="badge bg-<?= $p['status'] === 'active' ? 'success' : ($p['status'] === 'draft' ? 'warning' : 'secondary') ?> mb-2"><?= htmlspecialchars(ucfirst($p['status'])) ?></span>
                    <p style="font-size:.8rem;color:#6c757d;margin:0;"><?= htmlspecialchars($p['description'] ?? 'No description') ?></p>
                </div>
                <div class="card-footer" style="background:transparent;border-color:rgba(255,255,255,.06);">
                    <small style="color:#0dcaf0;"><i class="fas fa-rocket me-1"></i>Launch with this project</small>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="card">
    <div class="card-body text-center py-5">
        <i class="fas fa-folder-open fa-3x mb-3" style="color:#3d4248;"></i>
        <h5 class="text-muted">No Projects Yet</h5>
        <p class="text-muted">Create a project first to launch this module.</p>
        <a href="/enpharchem/projects/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Create Project</a>
    </div>
</div>
<?php endif; ?>
