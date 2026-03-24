<!-- Marketing Material Management -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard" class="text-decoration-none" style="color: var(--epc-accent);">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel" class="text-decoration-none" style="color: var(--epc-accent);">Control Panel</a></li>
        <li class="breadcrumb-item active text-light" aria-current="page">Marketing Material</li>
    </ol>
</nav>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'seeded'): ?>
        <div class="alert alert-success d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <div>Successfully loaded <?= (int)($_GET['count'] ?? 0) ?> marketing documents with PDF links.</div>
        </div>
    <?php elseif ($_GET['msg'] === 'deleted'): ?>
        <div class="alert alert-info"><i class="fas fa-trash me-2"></i>Material deleted.</div>
    <?php endif; ?>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-light mb-0"><i class="fas fa-bullhorn me-2" style="color: var(--epc-accent);"></i>Marketing Material</h2>
    <div class="d-flex gap-2">
        <a href="/enpharchem/marketing/seed-materials" class="btn btn-info btn-sm"><i class="fas fa-magic me-1"></i>Load EnPharChem Docs</a>
        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#createMaterialModal">
            <i class="fas fa-plus me-1"></i>Create Material
        </button>
    </div>
</div>

<!-- Material Cards Grid -->
<div class="row g-4">
    <?php if (!empty($materials)): ?>
        <?php
        $typeColors = [
            'brochure' => ['bg' => '#0d6efd', 'icon' => 'bi-file-earmark-richtext'],
            'whitepaper' => ['bg' => '#6f42c1', 'icon' => 'bi-file-earmark-pdf'],
            'case_study' => ['bg' => '#198754', 'icon' => 'bi-journal-bookmark'],
            'datasheet' => ['bg' => '#0dcaf0', 'icon' => 'bi-table'],
            'presentation' => ['bg' => '#fd7e14', 'icon' => 'bi-easel'],
            'video' => ['bg' => '#dc3545', 'icon' => 'bi-camera-video'],
            'infographic' => ['bg' => '#d63384', 'icon' => 'bi-bar-chart-steps'],
        ];
        $statusColors = [
            'draft' => 'bg-secondary',
            'review' => 'bg-warning text-dark',
            'approved' => 'bg-info',
            'published' => 'bg-success',
        ];
        ?>
        <?php foreach ($materials as $material): ?>
            <?php
            $mType = $material['material_type'] ?? 'brochure';
            $typeConfig = $typeColors[$mType] ?? ['bg' => '#6c757d', 'icon' => 'bi-file-earmark'];
            $mStatus = $material['status'] ?? 'draft';
            $mStatusBadge = $statusColors[$mStatus] ?? 'bg-secondary';
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 h-100" style="background: var(--epc-card-bg);">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge rounded-pill" style="background: <?= $typeConfig['bg'] ?>;">
                                <i class="bi <?= $typeConfig['icon'] ?> me-1"></i><?= ucwords(str_replace('_', ' ', $mType)) ?>
                            </span>
                            <span class="badge <?= $mStatusBadge ?>"><?= ucfirst($mStatus) ?></span>
                        </div>
                        <h5 class="text-light mb-2"><?= htmlspecialchars($material['title'] ?? '') ?></h5>
                        <p class="text-secondary small flex-grow-1">
                            <?= htmlspecialchars(mb_strimwidth($material['description'] ?? '', 0, 120, '...')) ?>
                        </p>
                        <?php if (!empty($material['target_audience'])): ?>
                            <div class="mb-2">
                                <small class="text-secondary"><i class="bi bi-people me-1"></i>Audience:</small>
                                <small class="text-light"><?= htmlspecialchars($material['target_audience']) ?></small>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($material['category'])): ?>
                            <div class="mb-2">
                                <small class="text-secondary"><i class="bi bi-tag me-1"></i>Category:</small>
                                <small class="text-light"><?= htmlspecialchars($material['category']) ?></small>
                            </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <small class="text-secondary"><i class="bi bi-download me-1"></i>Downloads:</small>
                            <small class="text-light"><?= number_format($material['download_count'] ?? 0) ?></small>
                        </div>
                        <?php if (!empty($material['creator_name'])): ?>
                            <div class="mb-3">
                                <small class="text-secondary">by <?= htmlspecialchars($material['creator_name']) ?></small>
                            </div>
                        <?php endif; ?>
                        <!-- PDF View/Download Button -->
                        <?php if (!empty($material['file_url'])): ?>
                        <div class="mb-3">
                            <a href="<?= htmlspecialchars($material['file_url']) ?>" target="_blank" class="btn btn-sm btn-primary w-100">
                                <i class="fas fa-file-pdf me-1"></i>View / Download PDF
                            </a>
                        </div>
                        <?php endif; ?>
                        <div class="d-flex gap-2 mt-auto">
                            <?php if ($mStatus !== 'approved' && $mStatus !== 'published'): ?>
                                <form method="POST" class="flex-fill">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="material_id" value="<?= htmlspecialchars($material['id'] ?? '') ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-success w-100">
                                        <i class="fas fa-check me-1"></i>Approve
                                    </button>
                                </form>
                            <?php endif; ?>
                            <form method="POST" class="flex-fill">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="material_id" value="<?= htmlspecialchars($material['id'] ?? '') ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Are you sure you want to delete this material?')">
                                    <i class="fas fa-trash me-1"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="card border-0" style="background: var(--epc-card-bg);">
                <div class="card-body text-center py-5">
                    <i class="fas fa-bullhorn text-secondary" style="font-size: 3rem;opacity:.4;"></i>
                    <p class="text-secondary mt-3">No marketing materials found.</p>
                    <div class="d-flex justify-content-center gap-3 mt-3">
                        <a href="/enpharchem/marketing/seed-materials" class="btn btn-primary"><i class="fas fa-magic me-1"></i>Load EnPharChem Marketing Docs</a>
                        <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#createMaterialModal"><i class="fas fa-plus me-1"></i>Create Custom</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Create Material Modal -->
<div class="modal fade" id="createMaterialModal" tabindex="-1" aria-labelledby="createMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: var(--epc-card-bg); border: 1px solid rgba(255,255,255,0.1);">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title text-light" id="createMaterialModalLabel"><i class="bi bi-plus-circle me-2 text-warning"></i>Create Material</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="matTitle" class="form-label text-light">Title</label>
                            <input type="text" class="form-control bg-dark text-light border-secondary" id="matTitle" name="title" required>
                        </div>
                        <div class="col-12">
                            <label for="matDesc" class="form-label text-light">Description</label>
                            <textarea class="form-control bg-dark text-light border-secondary" id="matDesc" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="matType" class="form-label text-light">Material Type</label>
                            <select class="form-select bg-dark text-light border-secondary" id="matType" name="material_type" required>
                                <option value="brochure">Brochure</option>
                                <option value="whitepaper">Whitepaper</option>
                                <option value="case_study">Case Study</option>
                                <option value="datasheet">Datasheet</option>
                                <option value="presentation">Presentation</option>
                                <option value="video">Video</option>
                                <option value="infographic">Infographic</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="matCategory" class="form-label text-light">Category</label>
                            <input type="text" class="form-control bg-dark text-light border-secondary" id="matCategory" name="category">
                        </div>
                        <div class="col-md-6">
                            <label for="matAudience" class="form-label text-light">Target Audience</label>
                            <input type="text" class="form-control bg-dark text-light border-secondary" id="matAudience" name="target_audience">
                        </div>
                        <div class="col-md-6">
                            <label for="matFile" class="form-label text-light">File URL</label>
                            <input type="url" class="form-control bg-dark text-light border-secondary" id="matFile" name="file_url" placeholder="https://...">
                        </div>
                        <div class="col-md-6">
                            <label for="matStatus" class="form-label text-light">Status</label>
                            <select class="form-select bg-dark text-light border-secondary" id="matStatus" name="status">
                                <option value="draft">Draft</option>
                                <option value="review">Review</option>
                                <option value="approved">Approved</option>
                                <option value="published">Published</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Create Material</button>
                </div>
            </form>
        </div>
    </div>
</div>
