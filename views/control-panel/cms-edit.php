<!-- CMS Page Editor -->
<?php
$isEditing = !empty($page);
$pageTitle = $isEditing ? 'Edit Page' : 'Create Page';
$formAction = $isEditing
    ? '/enpharchem/control-panel/cms/edit?id=' . htmlspecialchars($page['id'] ?? '')
    : '/enpharchem/control-panel/cms/edit';
?>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard" class="text-decoration-none" style="color: var(--epc-accent);">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel" class="text-decoration-none" style="color: var(--epc-accent);">Control Panel</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel/cms" class="text-decoration-none" style="color: var(--epc-accent);">CMS Pages</a></li>
        <li class="breadcrumb-item active text-light" aria-current="page"><?= $pageTitle ?></li>
    </ol>
</nav>

<h2 class="text-light mb-4">
    <i class="bi bi-<?= $isEditing ? 'pencil-square' : 'plus-circle' ?> me-2" style="color: var(--epc-accent);"></i>
    <?= $pageTitle ?>
</h2>

<form method="POST" action="<?= $formAction ?>">
    <input type="hidden" name="action" value="save">

    <div class="row g-4">
        <!-- Main Content (Left - 8 cols) -->
        <div class="col-lg-8">
            <div class="card border-0" style="background: var(--epc-card-bg);">
                <div class="card-body">
                    <div class="mb-4">
                        <label for="editTitle" class="form-label text-light">Title</label>
                        <input type="text" class="form-control form-control-lg bg-dark text-light border-secondary" id="editTitle" name="title" value="<?= htmlspecialchars($page['title'] ?? '') ?>" required placeholder="Page title...">
                    </div>
                    <div class="mb-4">
                        <label for="editSlug" class="form-label text-light">Slug</label>
                        <input type="text" class="form-control bg-dark text-light border-secondary" id="editSlug" name="slug" value="<?= htmlspecialchars($page['slug'] ?? '') ?>" <?= $isEditing ? 'readonly' : '' ?> placeholder="page-url-slug">
                    </div>
                    <div class="mb-0">
                        <label for="editContent" class="form-label text-light">Content</label>
                        <textarea class="form-control bg-dark text-light border-secondary" id="editContent" name="content" style="min-height: 400px; font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; font-size: 0.9rem;" placeholder="Write your content here..."><?= htmlspecialchars($page['content'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar (Right - 4 cols) -->
        <div class="col-lg-4">
            <div class="card border-0 mb-4" style="background: var(--epc-card-bg);">
                <div class="card-header border-bottom border-secondary" style="background: var(--epc-card-bg);">
                    <h6 class="text-light mb-0"><i class="bi bi-gear me-2"></i>Page Settings</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="editCategory" class="form-label text-light">Category</label>
                        <select class="form-select bg-dark text-light border-secondary" id="editCategory" name="category" required>
                            <?php
                            $defaultCategories = ['documentation', 'support', 'product', 'news', 'legal', 'general'];
                            $cats = !empty($categories) ? $categories : $defaultCategories;
                            foreach ($cats as $cat):
                                $catVal = is_array($cat) ? ($cat['value'] ?? $cat) : $cat;
                                $catLabel = ucfirst($catVal);
                                $selected = ($page['category'] ?? '') === $catVal ? 'selected' : '';
                            ?>
                                <option value="<?= htmlspecialchars($catVal) ?>" <?= $selected ?>><?= htmlspecialchars($catLabel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editStatus" class="form-label text-light">Status</label>
                        <select class="form-select bg-dark text-light border-secondary" id="editStatus" name="status">
                            <option value="draft" <?= ($page['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= ($page['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                            <option value="archived" <?= ($page['status'] ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editMetaDesc" class="form-label text-light">Meta Description</label>
                        <textarea class="form-control bg-dark text-light border-secondary" id="editMetaDesc" name="meta_description" rows="3" placeholder="Brief description for SEO..."><?= htmlspecialchars($page['meta_description'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editMetaKeywords" class="form-label text-light">Meta Keywords</label>
                        <input type="text" class="form-control bg-dark text-light border-secondary" id="editMetaKeywords" name="meta_keywords" value="<?= htmlspecialchars($page['meta_keywords'] ?? '') ?>" placeholder="keyword1, keyword2, ...">
                    </div>
                    <div class="mb-0">
                        <label for="editFeaturedImage" class="form-label text-light">Featured Image URL</label>
                        <input type="url" class="form-control bg-dark text-light border-secondary" id="editFeaturedImage" name="featured_image" value="<?= htmlspecialchars($page['featured_image'] ?? '') ?>" placeholder="https://...">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-check-lg me-2"></i>Save Page
                </button>
                <a href="/enpharchem/control-panel/cms" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg me-2"></i>Cancel
                </a>
            </div>
        </div>
    </div>
</form>
