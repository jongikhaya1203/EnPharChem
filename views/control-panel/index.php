<!-- Control Panel Dashboard -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard" class="text-decoration-none" style="color: var(--epc-accent);">Dashboard</a></li>
        <li class="breadcrumb-item active text-light" aria-current="page">Control Panel</li>
    </ol>
</nav>

<h2 class="text-light mb-4"><i class="bi bi-gear-fill me-2" style="color: var(--epc-accent);"></i>Control Panel</h2>

<!-- Stats Row -->
<div class="row g-3 mb-5">
    <?php
    $statCards = [
        ['label' => 'Platform Users', 'key' => 'users', 'icon' => 'bi-people-fill', 'color' => '#0d6efd'],
        ['label' => 'AD Users', 'key' => 'ad_users', 'icon' => 'bi-person-badge-fill', 'color' => '#0dcaf0'],
        ['label' => 'CMS Pages', 'key' => 'cms_pages', 'icon' => 'bi-file-earmark-text-fill', 'color' => '#198754'],
        ['label' => 'Marketing Items', 'key' => 'marketing_materials', 'icon' => 'bi-megaphone-fill', 'color' => '#ffc107'],
        ['label' => 'Training Courses', 'key' => 'training_courses', 'icon' => 'bi-mortarboard-fill', 'color' => '#6f42c1'],
    ];
    foreach ($statCards as $sc): ?>
        <div class="col-xl col-md-4 col-sm-6">
            <div class="card border-0 h-100" style="background: var(--epc-card-bg);">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-3 p-3 me-3" style="background: <?= $sc['color'] ?>20;">
                        <i class="bi <?= $sc['icon'] ?> fs-4" style="color: <?= $sc['color'] ?>;"></i>
                    </div>
                    <div>
                        <div class="text-secondary small"><?= $sc['label'] ?></div>
                        <div class="text-light fs-4 fw-bold"><?= number_format($stats[$sc['key']] ?? 0) ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Feature Cards Grid -->
<h4 class="text-light mb-3">Management Modules</h4>
<div class="row g-4">
    <?php
    $features = [
        [
            'title' => 'Active Directory',
            'desc' => 'Manage AD groups, users, and synchronization settings for the organization.',
            'icon' => 'bi-shield-lock-fill',
            'link' => '/enpharchem/control-panel/active-directory',
            'color' => '#0dcaf0',
            'count_key' => 'ad_users',
            'count_label' => 'users',
        ],
        [
            'title' => 'CMS Pages',
            'desc' => 'Create and manage content pages, documentation, and support articles.',
            'icon' => 'bi-file-earmark-text-fill',
            'link' => '/enpharchem/control-panel/cms',
            'color' => '#198754',
            'count_key' => 'cms_pages',
            'count_label' => 'pages',
        ],
        [
            'title' => 'Marketing Material',
            'desc' => 'Manage brochures, whitepapers, case studies, and promotional content.',
            'icon' => 'bi-megaphone-fill',
            'link' => '/enpharchem/control-panel/marketing',
            'color' => '#ffc107',
            'count_key' => 'marketing_materials',
            'count_label' => 'items',
        ],
        [
            'title' => 'Training Material',
            'desc' => 'Manage training courses, lessons, quizzes, and enrollment tracking.',
            'icon' => 'bi-mortarboard-fill',
            'link' => '/enpharchem/control-panel/training',
            'color' => '#6f42c1',
            'count_key' => 'training_courses',
            'count_label' => 'courses',
        ],
        [
            'title' => 'Data Management',
            'desc' => 'Load sample data, reset databases, and manage import/export operations.',
            'icon' => 'bi-database-fill-gear',
            'link' => '/enpharchem/control-panel/data-management',
            'color' => '#dc3545',
            'count_key' => null,
            'count_label' => '',
        ],
        [
            'title' => 'Licensing Portal',
            'desc' => 'Issue and manage module licenses with grant/deny controls per license and user.',
            'icon' => 'bi-key-fill',
            'link' => '/enpharchem/control-panel/licensing',
            'color' => '#ffc107',
            'count_key' => null,
            'count_label' => '',
        ],
        [
            'title' => 'Formulae Control Sheets',
            'desc' => 'Interactive calculators for Energy, Pharmaceutical & Chemical engineering mixes with real-time formulas.',
            'icon' => 'bi-calculator-fill',
            'link' => '/enpharchem/control-panel/formulae',
            'color' => '#198754',
            'count_key' => null,
            'count_label' => '3 Sheets',
        ],
    ];
    foreach ($features as $f): ?>
        <div class="col-lg-4 col-md-6">
            <a href="<?= $f['link'] ?>" class="text-decoration-none">
                <div class="card border-0 h-100 position-relative overflow-hidden" style="background: var(--epc-card-bg); transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;" onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,0.3)';" onmouseleave="this.style.transform='none';this.style.boxShadow='none';">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="rounded-3 p-3" style="background: <?= $f['color'] ?>20;">
                                <i class="bi <?= $f['icon'] ?> fs-3" style="color: <?= $f['color'] ?>;"></i>
                            </div>
                            <?php if ($f['count_key']): ?>
                                <span class="badge rounded-pill" style="background: <?= $f['color'] ?>30; color: <?= $f['color'] ?>;">
                                    <?= number_format($stats[$f['count_key']] ?? 0) ?> <?= $f['count_label'] ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <h5 class="text-light mb-2"><?= $f['title'] ?></h5>
                        <p class="text-secondary small mb-3"><?= $f['desc'] ?></p>
                        <div class="d-flex align-items-center" style="color: <?= $f['color'] ?>;">
                            <span class="small fw-semibold">Manage</span>
                            <i class="bi bi-arrow-right ms-2"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>
