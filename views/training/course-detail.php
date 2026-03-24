<!-- Course Detail -->
<?php
$levelColors = ['beginner' => 'success', 'intermediate' => 'primary', 'advanced' => 'warning', 'expert' => 'danger'];
$lvlColor = $levelColors[$course['level'] ?? 'beginner'] ?? 'secondary';
$catLabels = [
    'process_sim_energy' => 'Process Sim - Energy', 'process_sim_chemicals' => 'Process Sim - Chemicals',
    'exchanger_design' => 'Exchanger Design', 'concurrent_feed' => 'Concurrent FEED',
    'subsurface_science' => 'Subsurface Science', 'energy_optimization' => 'Energy Optimization',
    'operations_support' => 'Operations Support', 'apc' => 'Advanced Process Control',
    'dynamic_optimization' => 'Dynamic Optimization', 'mes' => 'MES',
    'petroleum_supply_chain' => 'Petroleum Supply Chain', 'supply_chain' => 'Supply Chain Mgmt',
    'apm' => 'Asset Performance Mgmt', 'industrial_data_fabric' => 'Industrial Data Fabric',
    'digital_grid_mgmt' => 'Digital Grid Mgmt', 'general' => 'General',
];
$catLabel = $catLabels[$course['category'] ?? 'general'] ?? ucwords(str_replace('_', ' ', $course['category'] ?? 'General'));
?>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard" class="text-decoration-none" style="color: var(--epc-accent);">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/training" class="text-decoration-none" style="color: var(--epc-accent);">Training</a></li>
        <li class="breadcrumb-item active text-light" aria-current="page"><?= htmlspecialchars($course['title']) ?></li>
    </ol>
</nav>

<!-- Course Header -->
<div class="card border-0 mb-4" style="background: var(--epc-card-bg);">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="d-flex gap-2 mb-2">
                    <span class="badge bg-info"><?= htmlspecialchars($catLabel) ?></span>
                    <span class="badge bg-<?= $lvlColor ?>"><?= ucfirst($course['level'] ?? 'beginner') ?></span>
                    <span class="badge bg-dark border border-secondary"><?= htmlspecialchars($course['status'] ?? 'active') ?></span>
                </div>
                <h3 class="text-light mb-3"><?= htmlspecialchars($course['title']) ?></h3>
                <p class="text-secondary"><?= nl2br(htmlspecialchars($course['description'] ?? '')) ?></p>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark border-secondary">
                    <div class="card-body">
                        <div class="mb-2"><i class="fas fa-clock text-info me-2"></i><span class="text-light"><?= $course['duration_hours'] ?> hours</span></div>
                        <div class="mb-2"><i class="fas fa-user-tie text-info me-2"></i><span class="text-light"><?= htmlspecialchars($course['instructor'] ?? 'TBD') ?></span></div>
                        <div class="mb-2"><i class="fas fa-list text-info me-2"></i><span class="text-light"><?= count($lessons) ?> lessons</span></div>
                        <div class="mb-2"><i class="fas fa-question-circle text-info me-2"></i><span class="text-light"><?= $questionCount ?> assessment questions</span></div>
                        <?php if (!empty($course['prerequisites'])): ?>
                        <div class="mb-0"><i class="fas fa-lock text-warning me-2"></i><span class="text-secondary small"><?= htmlspecialchars($course['prerequisites']) ?></span></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Learning Objectives -->
<?php if (!empty($course['learning_objectives'])): ?>
<div class="card border-0 mb-4" style="background: var(--epc-card-bg);">
    <div class="card-header border-bottom border-secondary" style="background: var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="fas fa-bullseye text-success me-2"></i>Learning Objectives</h5>
    </div>
    <div class="card-body">
        <?php
        $objectives = explode("\n", $course['learning_objectives']);
        foreach ($objectives as $obj):
            $obj = trim($obj);
            if (empty($obj)) continue;
            $obj = ltrim($obj, '- ');
        ?>
            <div class="d-flex align-items-start mb-2">
                <i class="fas fa-check text-success me-2 mt-1"></i>
                <span class="text-light"><?= htmlspecialchars($obj) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Lessons Table -->
<div class="card border-0 mb-4" style="background: var(--epc-card-bg);">
    <div class="card-header border-bottom border-secondary" style="background: var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="fas fa-list-ol text-primary me-2"></i>Course Lessons</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0" style="background: transparent;">
                <thead>
                    <tr class="text-secondary small text-uppercase">
                        <th style="width:60px">#</th>
                        <th>Title</th>
                        <th style="width:120px">Type</th>
                        <th style="width:100px">Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $typeColors = ['video' => 'danger', 'document' => 'primary', 'quiz' => 'warning', 'lab' => 'success', 'interactive' => 'info'];
                    $typeIcons = ['video' => 'fa-play-circle', 'document' => 'fa-file-alt', 'quiz' => 'fa-question', 'lab' => 'fa-flask', 'interactive' => 'fa-hand-pointer'];
                    foreach ($lessons as $lesson):
                        $lt = $lesson['lesson_type'] ?? 'document';
                    ?>
                    <tr>
                        <td class="text-secondary"><?= $lesson['lesson_order'] ?></td>
                        <td class="text-light"><?= htmlspecialchars($lesson['title']) ?></td>
                        <td>
                            <span class="badge bg-<?= $typeColors[$lt] ?? 'secondary' ?>">
                                <i class="fas <?= $typeIcons[$lt] ?? 'fa-file' ?> me-1"></i><?= ucfirst($lt) ?>
                            </span>
                        </td>
                        <td class="text-secondary"><?= $lesson['duration_minutes'] ?> min</td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($lessons)): ?>
                    <tr><td colspan="4" class="text-center text-secondary py-3">No lessons available.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Assessment Section -->
<div class="card border-0 mb-4" style="background: var(--epc-card-bg);">
    <div class="card-header border-bottom border-secondary" style="background: var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="fas fa-clipboard-check text-warning me-2"></i>Assessment</h5>
    </div>
    <div class="card-body">
        <?php if ($questionCount == 0): ?>
            <p class="text-secondary">No assessment questions available for this course yet.</p>
        <?php else: ?>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-light mb-1"><strong><?= $questionCount ?></strong> questions | Passing score: <strong>70%</strong></p>
                    <p class="text-secondary small mb-0">You can retake the assessment multiple times. Your best score counts.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <?php if ($certificate): ?>
                        <span class="badge bg-success fs-6 me-2"><i class="fas fa-check-circle me-1"></i>Passed - <?= number_format($bestAttempt['score'] ?? 0, 1) ?>%</span>
                        <a href="/enpharchem/training/certificate?id=<?= $certificate['id'] ?>" class="btn btn-warning" target="_blank">
                            <i class="fas fa-certificate me-1"></i>View Certificate
                        </a>
                    <?php elseif ($bestAttempt && !$bestAttempt['passed']): ?>
                        <span class="badge bg-danger fs-6 me-2">Best: <?= number_format($bestAttempt['score'], 1) ?>%</span>
                        <a href="/enpharchem/training/assessment?course_id=<?= $course['id'] ?>" class="btn btn-warning">
                            <i class="fas fa-redo me-1"></i>Retake Assessment
                        </a>
                    <?php else: ?>
                        <a href="/enpharchem/training/assessment?course_id=<?= $course['id'] ?>" class="btn btn-primary btn-lg">
                            <i class="fas fa-pen me-1"></i>Take Assessment
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Previous Attempts -->
<?php if (!empty($attempts)): ?>
<div class="card border-0 mb-4" style="background: var(--epc-card-bg);">
    <div class="card-header border-bottom border-secondary" style="background: var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="fas fa-history text-secondary me-2"></i>Previous Attempts</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0" style="background: transparent;">
                <thead>
                    <tr class="text-secondary small text-uppercase">
                        <th>Date</th>
                        <th>Score</th>
                        <th>Result</th>
                        <th>Correct</th>
                        <th>Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attempts as $att): ?>
                    <tr>
                        <td class="text-light"><?= date('M d, Y H:i', strtotime($att['completed_at'] ?? $att['created_at'])) ?></td>
                        <td>
                            <span class="text-<?= $att['score'] >= 90 ? 'success' : ($att['score'] >= 70 ? 'primary' : 'danger') ?> fw-bold">
                                <?= number_format($att['score'], 1) ?>%
                            </span>
                        </td>
                        <td>
                            <?php if ($att['passed']): ?>
                                <span class="badge bg-success">Passed</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Failed</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-secondary"><?= $att['correct_answers'] ?>/<?= $att['total_questions'] ?></td>
                        <td class="text-secondary"><?= $att['time_taken_minutes'] ?> min</td>
                        <td>
                            <a href="/enpharchem/training/results?attempt_id=<?= $att['id'] ?>" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-eye me-1"></i>Review
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="mb-4">
    <a href="/enpharchem/training" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Courses
    </a>
</div>
