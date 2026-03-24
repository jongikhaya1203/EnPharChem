<!-- Training Courses -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard" class="text-decoration-none" style="color: var(--epc-accent);">Dashboard</a></li>
        <li class="breadcrumb-item active text-light" aria-current="page">Training</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-light mb-0"><i class="fas fa-graduation-cap me-2" style="color: var(--epc-accent);"></i>Training Courses</h2>
    <div class="d-flex gap-2">
        <a href="/enpharchem/training/my-certificates" class="btn btn-outline-warning">
            <i class="fas fa-certificate me-1"></i>My Certificates
        </a>
        <form method="POST" action="/enpharchem/training/seed" class="d-inline">
            <button type="submit" class="btn btn-success" onclick="this.innerHTML='<i class=\'fas fa-spinner fa-spin me-1\'></i>Seeding...'; this.disabled=true; this.form.submit();">
                <i class="fas fa-database me-1"></i>Seed All Training Material
            </button>
        </form>
    </div>
</div>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['flash_success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['flash_error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <i class="fas fa-book text-primary fs-3"></i>
                <div class="text-light fs-3 fw-bold"><?= number_format($totalCourses) ?></div>
                <div class="text-secondary small">Total Courses</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <i class="fas fa-check-circle text-success fs-3"></i>
                <div class="text-light fs-3 fw-bold"><?= number_format($completedCourses) ?></div>
                <div class="text-secondary small">My Completed</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <i class="fas fa-certificate text-warning fs-3"></i>
                <div class="text-light fs-3 fw-bold"><?= number_format($totalCerts) ?></div>
                <div class="text-secondary small">Certificates Earned</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <i class="fas fa-chart-line text-info fs-3"></i>
                <div class="text-light fs-3 fw-bold"><?= number_format($avgScore, 1) ?>%</div>
                <div class="text-secondary small">Avg Score</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 mb-4" style="background: var(--epc-card-bg);">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-secondary small">Filter by Category</label>
                <select id="filterCategory" class="form-select bg-dark text-light border-secondary">
                    <option value="">All Categories</option>
                    <?php
                    $catLabels = [
                        'process_sim_energy' => 'Process Sim - Energy',
                        'process_sim_chemicals' => 'Process Sim - Chemicals',
                        'exchanger_design' => 'Exchanger Design',
                        'concurrent_feed' => 'Concurrent FEED',
                        'subsurface_science' => 'Subsurface Science',
                        'energy_optimization' => 'Energy Optimization',
                        'operations_support' => 'Operations Support',
                        'apc' => 'Advanced Process Control',
                        'dynamic_optimization' => 'Dynamic Optimization',
                        'mes' => 'MES',
                        'petroleum_supply_chain' => 'Petroleum Supply Chain',
                        'supply_chain' => 'Supply Chain Mgmt',
                        'apm' => 'Asset Performance Mgmt',
                        'industrial_data_fabric' => 'Industrial Data Fabric',
                        'digital_grid_mgmt' => 'Digital Grid Mgmt',
                        'process_simulation' => 'Process Simulation',
                        'grid_mgmt' => 'Grid Mgmt',
                        'general' => 'General',
                    ];
                    foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($catLabels[$cat] ?? ucwords(str_replace('_', ' ', $cat))) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary small">Filter by Level</label>
                <select id="filterLevel" class="form-select bg-dark text-light border-secondary">
                    <option value="">All Levels</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                    <option value="expert">Expert</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary small">Search</label>
                <input type="text" id="searchCourses" class="form-control bg-dark text-light border-secondary" placeholder="Search courses...">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" onclick="resetFilters()">
                    <i class="fas fa-undo me-1"></i>Reset
                </button>
            </div>
        </div>
    </div>
</div>

<?php if (empty($courses)): ?>
    <div class="card border-0" style="background: var(--epc-card-bg);">
        <div class="card-body text-center py-5">
            <i class="fas fa-graduation-cap text-secondary" style="font-size: 4rem;"></i>
            <h4 class="text-light mt-3">No Training Courses Available</h4>
            <p class="text-secondary mb-4">Click "Seed All Training Material" to load 60 courses with lessons and assessments.</p>
            <form method="POST" action="/enpharchem/training/seed">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-database me-2"></i>Seed Training Data (60 Courses, 300+ Lessons, 600+ Questions)
                </button>
            </form>
        </div>
    </div>
<?php else: ?>
    <!-- Course count display -->
    <p class="text-secondary mb-3"><span id="courseCount"><?= count($courses) ?></span> courses found</p>

    <!-- Course Cards Grid -->
    <div class="row g-3" id="courseGrid">
        <?php
        $levelColors = ['beginner' => 'success', 'intermediate' => 'primary', 'advanced' => 'warning', 'expert' => 'danger'];
        $catColors = [
            'process_sim_energy' => 'info', 'process_sim_chemicals' => 'info', 'exchanger_design' => 'primary',
            'concurrent_feed' => 'secondary', 'subsurface_science' => 'dark', 'energy_optimization' => 'success',
            'operations_support' => 'warning', 'apc' => 'danger', 'dynamic_optimization' => 'primary',
            'mes' => 'info', 'petroleum_supply_chain' => 'warning', 'supply_chain' => 'secondary',
            'apm' => 'danger', 'industrial_data_fabric' => 'info', 'digital_grid_mgmt' => 'success',
            'process_simulation' => 'info', 'grid_mgmt' => 'success', 'general' => 'secondary',
        ];
        foreach ($courses as $course):
            $cid = $course['id'];
            $cat = $course['category'] ?? 'general';
            $lvl = $course['level'] ?? 'beginner';
            $att = $attempts[$cid] ?? null;
            $cert = $certificates[$cid] ?? null;
            $hasPassed = $att && $att['has_passed'];
        ?>
        <div class="col-lg-4 col-md-6 course-card" data-category="<?= htmlspecialchars($cat) ?>" data-level="<?= htmlspecialchars($lvl) ?>" data-title="<?= htmlspecialchars(strtolower($course['title'])) ?>">
            <div class="card border-0 h-100" style="background: var(--epc-card-bg); transition: transform 0.2s;" onmouseenter="this.style.transform='translateY(-4px)'" onmouseleave="this.style.transform='none'">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge bg-<?= $catColors[$cat] ?? 'secondary' ?> bg-opacity-75" style="font-size: 0.7rem;"><?= htmlspecialchars($catLabels[$cat] ?? ucwords(str_replace('_', ' ', $cat))) ?></span>
                        <span class="badge bg-<?= $levelColors[$lvl] ?? 'secondary' ?>"><?= ucfirst($lvl) ?></span>
                    </div>
                    <h6 class="text-light fw-bold mb-2"><?= htmlspecialchars($course['title']) ?></h6>
                    <p class="text-secondary small mb-3 flex-grow-1"><?= htmlspecialchars(substr($course['description'] ?? '', 0, 120)) ?>...</p>
                    <div class="d-flex justify-content-between text-secondary small mb-3">
                        <span><i class="fas fa-clock me-1"></i><?= $course['duration_hours'] ?>h</span>
                        <span><i class="fas fa-list me-1"></i><?= $course['lesson_count'] ?> lessons</span>
                        <span><i class="fas fa-question-circle me-1"></i><?= $course['question_count'] ?> Q</span>
                    </div>
                    <?php if ($att): ?>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-secondary">Best Score</span>
                                <span class="text-<?= $att['best_score'] >= 70 ? 'success' : 'danger' ?>"><?= number_format($att['best_score'], 1) ?>%</span>
                            </div>
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-<?= $att['best_score'] >= 70 ? 'success' : 'danger' ?>" style="width: <?= $att['best_score'] ?>%"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex gap-2">
                        <?php if ($hasPassed): ?>
                            <a href="/enpharchem/training/course?id=<?= $cid ?>" class="btn btn-success btn-sm flex-grow-1">
                                <i class="fas fa-check-circle me-1"></i>Completed
                            </a>
                            <?php if ($cert): ?>
                                <a href="/enpharchem/training/certificate?id=<?= $cert['cert_id'] ?>" class="btn btn-outline-warning btn-sm" target="_blank">
                                    <i class="fas fa-certificate"></i>
                                </a>
                            <?php endif; ?>
                        <?php elseif ($att): ?>
                            <a href="/enpharchem/training/course?id=<?= $cid ?>" class="btn btn-warning btn-sm flex-grow-1">
                                <i class="fas fa-redo me-1"></i>Continue
                            </a>
                        <?php else: ?>
                            <a href="/enpharchem/training/course?id=<?= $cid ?>" class="btn btn-primary btn-sm flex-grow-1">
                                <i class="fas fa-play me-1"></i>Start Course
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterCategory = document.getElementById('filterCategory');
    const filterLevel = document.getElementById('filterLevel');
    const searchInput = document.getElementById('searchCourses');

    function applyFilters() {
        const cat = filterCategory ? filterCategory.value : '';
        const lvl = filterLevel ? filterLevel.value : '';
        const search = searchInput ? searchInput.value.toLowerCase() : '';
        let visible = 0;

        document.querySelectorAll('.course-card').forEach(card => {
            const matchCat = !cat || card.dataset.category === cat;
            const matchLvl = !lvl || card.dataset.level === lvl;
            const matchSearch = !search || card.dataset.title.includes(search);
            const show = matchCat && matchLvl && matchSearch;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        const countEl = document.getElementById('courseCount');
        if (countEl) countEl.textContent = visible;
    }

    if (filterCategory) filterCategory.addEventListener('change', applyFilters);
    if (filterLevel) filterLevel.addEventListener('change', applyFilters);
    if (searchInput) searchInput.addEventListener('input', applyFilters);
});

function resetFilters() {
    document.getElementById('filterCategory').value = '';
    document.getElementById('filterLevel').value = '';
    document.getElementById('searchCourses').value = '';
    document.querySelectorAll('.course-card').forEach(c => c.style.display = '');
    const countEl = document.getElementById('courseCount');
    if (countEl) countEl.textContent = document.querySelectorAll('.course-card').length;
}
</script>
