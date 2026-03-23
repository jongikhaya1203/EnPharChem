<!-- CMS Pages Management -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard" class="text-decoration-none" style="color: var(--epc-accent);">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel" class="text-decoration-none" style="color: var(--epc-accent);">Control Panel</a></li>
        <li class="breadcrumb-item active text-light" aria-current="page">CMS Pages</li>
    </ol>
</nav>

<h2 class="text-light mb-4"><i class="bi bi-file-earmark-text-fill me-2" style="color: var(--epc-accent);"></i>CMS Pages</h2>

<!-- Stats Bar -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <div class="text-secondary small">Total Pages</div>
                <div class="text-light fs-3 fw-bold"><?= number_format($pageStats['total'] ?? 0) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <div class="text-secondary small">Published</div>
                <div class="text-success fs-3 fw-bold"><?= number_format($pageStats['published'] ?? 0) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <div class="text-secondary small">Drafts</div>
                <div class="text-warning fs-3 fw-bold"><?= number_format($pageStats['drafts'] ?? 0) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <div class="text-secondary small">Total Views</div>
                <div class="text-info fs-3 fw-bold"><?= number_format($pageStats['total_views'] ?? 0) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Pages Table -->
<div class="card border-0 mb-4" style="background: var(--epc-card-bg);">
    <div class="card-header d-flex justify-content-between align-items-center border-bottom border-secondary" style="background: var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="bi bi-journal-text me-2"></i>All Pages</h5>
        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#createPageModal">
            <i class="bi bi-plus-lg me-1"></i>Create Page
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0" style="background: transparent;">
                <thead>
                    <tr class="text-secondary small text-uppercase">
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pages)): ?>
                        <?php foreach ($pages as $page): ?>
                            <tr>
                                <td>
                                    <a href="/enpharchem/control-panel/cms/edit?id=<?= htmlspecialchars($page['id'] ?? '') ?>" class="text-decoration-none fw-semibold" style="color: var(--epc-accent);">
                                        <?= htmlspecialchars($page['title'] ?? '') ?>
                                    </a>
                                    <?php if (!empty($page['author_name'])): ?>
                                        <div class="text-secondary small">by <?= htmlspecialchars($page['author_name']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><code class="text-info"><?= htmlspecialchars($page['slug'] ?? '') ?></code></td>
                                <td>
                                    <?php
                                    $catColors = [
                                        'documentation' => 'bg-primary',
                                        'support' => 'bg-info',
                                        'product' => 'bg-success',
                                        'news' => 'bg-warning text-dark',
                                        'legal' => 'bg-secondary',
                                        'general' => 'bg-dark border border-secondary',
                                    ];
                                    $cat = $page['category'] ?? 'general';
                                    $catBadge = $catColors[$cat] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $catBadge ?>"><?= ucfirst($cat) ?></span>
                                </td>
                                <td>
                                    <?php
                                    $pStatus = $page['status'] ?? 'draft';
                                    $statusMap = [
                                        'published' => 'bg-success',
                                        'draft' => 'bg-warning text-dark',
                                        'archived' => 'bg-secondary',
                                    ];
                                    $pBadge = $statusMap[$pStatus] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $pBadge ?>"><?= ucfirst($pStatus) ?></span>
                                </td>
                                <td class="text-light"><?= number_format($page['views'] ?? $page['view_count'] ?? 0) ?></td>
                                <td class="text-secondary small"><?= htmlspecialchars($page['updated_at'] ?? '') ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="/enpharchem/control-panel/cms/edit?id=<?= htmlspecialchars($page['id'] ?? '') ?>" class="btn btn-sm btn-outline-info" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="page_id" value="<?= htmlspecialchars($page['id'] ?? '') ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Toggle Status">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        </form>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="page_id" value="<?= htmlspecialchars($page['id'] ?? '') ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this page?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-secondary py-4">No pages found. Create your first page!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Page Modal -->
<div class="modal fade" id="createPageModal" tabindex="-1" aria-labelledby="createPageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: var(--epc-card-bg); border: 1px solid rgba(255,255,255,0.1);">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title text-light" id="createPageModalLabel"><i class="bi bi-plus-circle me-2 text-success"></i>Create Page</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pageTitle" class="form-label text-light">Title</label>
                        <input type="text" class="form-control bg-dark text-light border-secondary" id="pageTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="pageCategory" class="form-label text-light">Category</label>
                        <select class="form-select bg-dark text-light border-secondary" id="pageCategory" name="category" required>
                            <option value="documentation">Documentation</option>
                            <option value="support">Support</option>
                            <option value="product">Product</option>
                            <option value="news">News</option>
                            <option value="legal">Legal</option>
                            <option value="general">General</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pageContent" class="form-label text-light">Content</label>
                        <textarea class="form-control bg-dark text-light border-secondary" id="pageContent" name="content" rows="8"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="pageMetaDesc" class="form-label text-light">Meta Description</label>
                        <textarea class="form-control bg-dark text-light border-secondary" id="pageMetaDesc" name="meta_description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="pageStatus" class="form-label text-light">Status</label>
                        <select class="form-select bg-dark text-light border-secondary" id="pageStatus" name="status">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Create Page</button>
                </div>
            </form>
        </div>
    </div>
</div>
