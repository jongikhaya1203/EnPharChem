<!-- CMS Pages Management -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard" class="text-decoration-none" style="color: var(--epc-accent);">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel" class="text-decoration-none" style="color: var(--epc-accent);">Control Panel</a></li>
        <li class="breadcrumb-item active text-light" aria-current="page">CMS Pages</li>
    </ol>
</nav>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'branding_saved'): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Branding settings saved successfully. Refresh to see changes.</div>
<?php endif; ?>

<h2 class="text-light mb-4"><i class="fas fa-file-alt me-2" style="color: var(--epc-accent);"></i>CMS & Branding</h2>

<?php $branding = $branding ?? []; ?>

<!-- Branding & Logo Section -->
<div class="card border-0 mb-4" style="background: var(--epc-card-bg); border-top: 3px solid var(--epc-accent) !important;">
    <div class="card-header d-flex justify-content-between align-items-center border-bottom border-secondary" style="background: var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="fas fa-palette me-2" style="color:var(--epc-accent);"></i>Branding & Logo</h5>
        <span class="badge bg-info">Dashboard Customization</span>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save_branding">

            <div class="row g-4">
                <!-- Logo Upload Section -->
                <div class="col-lg-4">
                    <div class="text-center p-4" style="background:rgba(255,255,255,.03);border-radius:12px;border:1px dashed rgba(255,255,255,.15);">
                        <div class="mb-3">
                            <?php if (!empty($branding['logo_url'])): ?>
                                <img src="<?= htmlspecialchars($branding['logo_url']) ?>" alt="Logo" style="max-width:120px;max-height:120px;border-radius:12px;margin-bottom:10px;">
                            <?php else: ?>
                                <div style="width:80px;height:80px;border-radius:16px;background:linear-gradient(135deg,<?= htmlspecialchars($branding['primary_color'] ?? '#0d6efd') ?>,<?= htmlspecialchars($branding['accent_color'] ?? '#0dcaf0') ?>);display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:2rem;margin-bottom:10px;">
                                    <i class="fas <?= htmlspecialchars($branding['logo_icon'] ?? 'fa-atom') ?>"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <label class="form-label text-light d-block">Upload Logo</label>
                        <input type="file" name="logo_file" class="form-control form-control-sm bg-dark text-light border-secondary" accept="image/*">
                        <small class="text-secondary d-block mt-2">PNG, JPG, SVG, WebP (max 5MB)</small>

                        <div class="mt-3">
                            <label class="form-label text-secondary small">Or Logo URL</label>
                            <input type="url" name="logo_url" class="form-control form-control-sm bg-dark text-light border-secondary"
                                   value="<?= htmlspecialchars($branding['logo_url'] ?? '') ?>" placeholder="https://example.com/logo.png">
                        </div>

                        <div class="mt-3">
                            <label class="form-label text-secondary small">Logo Icon (if no image)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-dark border-secondary text-light"><i class="fas <?= htmlspecialchars($branding['logo_icon'] ?? 'fa-atom') ?>"></i></span>
                                <input type="text" name="logo_icon" class="form-control bg-dark text-light border-secondary"
                                       value="<?= htmlspecialchars($branding['logo_icon'] ?? 'fa-atom') ?>" placeholder="fa-atom">
                            </div>
                            <small class="text-secondary">Font Awesome class (e.g., fa-atom, fa-flask, fa-industry)</small>
                        </div>
                    </div>
                </div>

                <!-- Site Name & Text -->
                <div class="col-lg-8">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-light"><i class="fas fa-heading me-1"></i>Site Name (before accent)</label>
                            <input type="text" name="site_name" class="form-control bg-dark text-light border-secondary"
                                   value="<?= htmlspecialchars($branding['site_name'] ?? 'EnPharChem') ?>" placeholder="EnPharChem">
                            <small class="text-secondary">Text shown in navbar & title</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-light"><i class="fas fa-highlighter me-1"></i>Accent Text (colored part)</label>
                            <input type="text" name="site_name_accent" class="form-control bg-dark text-light border-secondary"
                                   value="<?= htmlspecialchars($branding['site_name_accent'] ?? 'Phar') ?>" placeholder="Phar">
                            <small class="text-secondary">The highlighted portion of the name</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-light"><i class="fas fa-quote-left me-1"></i>Site Tagline</label>
                            <input type="text" name="site_tagline" class="form-control bg-dark text-light border-secondary"
                                   value="<?= htmlspecialchars($branding['site_tagline'] ?? 'Energy, Pharmaceutical & Chemical Engineering Platform') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-light"><i class="fas fa-tachometer-alt me-1"></i>Dashboard Title</label>
                            <input type="text" name="dashboard_title" class="form-control bg-dark text-light border-secondary"
                                   value="<?= htmlspecialchars($branding['dashboard_title'] ?? 'Dashboard') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-light"><i class="fas fa-align-left me-1"></i>Dashboard Subtitle</label>
                            <input type="text" name="dashboard_subtitle" class="form-control bg-dark text-light border-secondary"
                                   value="<?= htmlspecialchars($branding['dashboard_subtitle'] ?? '') ?>" placeholder="Welcome to your engineering workspace">
                        </div>
                        <div class="col-12">
                            <label class="form-label text-light"><i class="fas fa-copyright me-1"></i>Footer Text</label>
                            <input type="text" name="footer_text" class="form-control bg-dark text-light border-secondary"
                                   value="<?= htmlspecialchars($branding['footer_text'] ?? '© 2026 EnPharChem Technologies — Energy, Pharmaceutical & Chemical Engineering Platform') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-light"><i class="fas fa-building me-1"></i>Company Name</label>
                            <input type="text" name="company_name" class="form-control bg-dark text-light border-secondary"
                                   value="<?= htmlspecialchars($branding['company_name'] ?? 'EnPharChem Technologies') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-light"><i class="fas fa-envelope me-1"></i>Company Email</label>
                            <input type="email" name="company_email" class="form-control bg-dark text-light border-secondary"
                                   value="<?= htmlspecialchars($branding['company_email'] ?? 'info@enpharchem.com') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-light"><i class="fas fa-paint-brush me-1"></i>Primary Color</label>
                            <div class="input-group input-group-sm">
                                <input type="color" name="primary_color" class="form-control form-control-color bg-dark border-secondary" style="height:38px;"
                                       value="<?= htmlspecialchars($branding['primary_color'] ?? '#0d6efd') ?>">
                                <input type="text" class="form-control bg-dark text-light border-secondary"
                                       value="<?= htmlspecialchars($branding['primary_color'] ?? '#0d6efd') ?>" readonly style="max-width:100px;">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-light"><i class="fas fa-paint-brush me-1"></i>Accent Color</label>
                            <div class="input-group input-group-sm">
                                <input type="color" name="accent_color" class="form-control form-control-color bg-dark border-secondary" style="height:38px;"
                                       value="<?= htmlspecialchars($branding['accent_color'] ?? '#0dcaf0') ?>">
                                <input type="text" class="form-control bg-dark text-light border-secondary"
                                       value="<?= htmlspecialchars($branding['accent_color'] ?? '#0dcaf0') ?>" readonly style="max-width:100px;">
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="d-flex gap-2 w-100">
                                <button type="submit" class="btn btn-primary flex-fill"><i class="fas fa-save me-1"></i>Save Branding</button>
                                <a href="/enpharchem/dashboard" class="btn btn-outline-secondary" target="_blank"><i class="fas fa-eye me-1"></i>Preview</a>
                            </div>
                        </div>
                    </div>

                    <!-- Live Preview -->
                    <div class="mt-4 p-3" style="background:#1a1d23;border-radius:8px;border:1px solid rgba(255,255,255,.08);">
                        <small class="text-secondary d-block mb-2"><i class="fas fa-desktop me-1"></i>Navbar Preview</small>
                        <div class="d-flex align-items-center gap-2">
                            <?php if (!empty($branding['logo_url'])): ?>
                                <img src="<?= htmlspecialchars($branding['logo_url']) ?>" style="width:34px;height:34px;border-radius:8px;">
                            <?php else: ?>
                                <div style="width:34px;height:34px;border-radius:8px;background:linear-gradient(135deg,<?= htmlspecialchars($branding['primary_color'] ?? '#0d6efd') ?>,<?= htmlspecialchars($branding['accent_color'] ?? '#0dcaf0') ?>);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.9rem;">
                                    <i class="fas <?= htmlspecialchars($branding['logo_icon'] ?? 'fa-atom') ?>"></i>
                                </div>
                            <?php endif; ?>
                            <span style="font-weight:700;font-size:1.1rem;color:#fff;">
                                <?= htmlspecialchars($branding['site_name'] ?? 'En') ?><span style="color:<?= htmlspecialchars($branding['accent_color'] ?? '#0dcaf0') ?>;"><?= htmlspecialchars($branding['site_name_accent'] ?? 'Phar') ?></span>Chem
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

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
