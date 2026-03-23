<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/projects">Projects</a></li>
        <li class="breadcrumb-item active">New Project</li>
    </ol>
</nav>

<div class="page-header">
    <h1><i class="fas fa-plus-circle me-2" style="color:var(--epc-accent);"></i>Create New Project</h1>
</div>

<div class="card" style="max-width:720px;">
    <div class="card-body">
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/enpharchem/projects/create">
            <div class="mb-3">
                <label for="name" class="form-label">Project Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name"
                       value="<?= htmlspecialchars($name ?? '') ?>" placeholder="e.g. Refinery Process Optimization" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"
                          placeholder="Describe the project objectives and scope..."><?= htmlspecialchars($description ?? '') ?></textarea>
            </div>

            <div class="mb-4">
                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                <select class="form-select" id="category" name="category" required>
                    <option value="">-- Select Category --</option>
                    <?php
                    $cats = ['energy' => 'Energy', 'chemicals' => 'Chemicals', 'pharma' => 'Pharmaceutical', 'subsurface' => 'Subsurface', 'grid' => 'Grid Management', 'general' => 'General'];
                    foreach ($cats as $val => $label):
                    ?>
                    <option value="<?= $val ?>" <?= (isset($category) && $category === $val) ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Create Project</button>
                <a href="/enpharchem/projects" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
