<?php $projects = $projects ?? []; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Projects</li>
    </ol>
</nav>

<div class="page-header">
    <h1><i class="fas fa-project-diagram me-2" style="color:var(--epc-accent);"></i>My Projects</h1>
    <a href="/enpharchem/projects/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Project</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (!empty($projects)): ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th style="width:140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                    <tr>
                        <td>
                            <a href="/enpharchem/projects/<?= (int)$project['id'] ?>" class="text-decoration-none" style="color:var(--epc-accent);font-weight:500;">
                                <?= htmlspecialchars($project['name']) ?>
                            </a>
                        </td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars(ucfirst($project['category'] ?? 'general')) ?></span></td>
                        <td><span class="badge badge-status-<?= htmlspecialchars($project['status'] ?? 'active') ?>"><?= htmlspecialchars(ucfirst($project['status'] ?? 'active')) ?></span></td>
                        <td style="font-size:.85rem;color:#6c757d;"><?= htmlspecialchars($project['created_at'] ?? '') ?></td>
                        <td style="font-size:.85rem;color:#6c757d;"><?= htmlspecialchars($project['updated_at'] ?? '') ?></td>
                        <td>
                            <a href="/enpharchem/projects/<?= (int)$project['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="View"><i class="fas fa-eye"></i></a>
                            <a href="/enpharchem/projects/<?= (int)$project['id'] ?>/edit" class="btn btn-sm btn-outline-secondary me-1" title="Edit"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="/enpharchem/projects/<?= (int)$project['id'] ?>/delete" class="d-inline" onsubmit="return confirm('Delete this project?');">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-folder-open fa-3x mb-3" style="color:#6c757d;opacity:.4;"></i>
            <p class="text-muted mb-3">You don't have any projects yet.</p>
            <a href="/enpharchem/projects/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Create Your First Project</a>
        </div>
        <?php endif; ?>
    </div>
</div>
