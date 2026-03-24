<!-- Assessment Results -->
<?php
$score = $attempt['score'] ?? 0;
$passed = $attempt['passed'] ?? 0;
$scoreColor = $score >= 90 ? 'success' : ($score >= 70 ? 'primary' : 'danger');
?>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard" class="text-decoration-none" style="color: var(--epc-accent);">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/training" class="text-decoration-none" style="color: var(--epc-accent);">Training</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/training/course?id=<?= $attempt['course_id'] ?>" class="text-decoration-none" style="color: var(--epc-accent);"><?= htmlspecialchars($attempt['course_title']) ?></a></li>
        <li class="breadcrumb-item active text-light" aria-current="page">Results</li>
    </ol>
</nav>

<!-- Score Display -->
<div class="card border-0 mb-4" style="background: var(--epc-card-bg);">
    <div class="card-body text-center py-5">
        <?php if ($passed): ?>
            <div class="mb-3"><i class="fas fa-trophy text-warning" style="font-size: 4rem;"></i></div>
            <h2 class="text-success mb-2">Congratulations! You Passed!</h2>
        <?php else: ?>
            <div class="mb-3"><i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i></div>
            <h2 class="text-danger mb-2">Assessment Not Passed</h2>
        <?php endif; ?>

        <div class="display-1 fw-bold text-<?= $scoreColor ?> mb-2"><?= number_format($score, 1) ?>%</div>
        <p class="text-secondary mb-0"><?= htmlspecialchars($attempt['course_title']) ?></p>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <div class="text-<?= $scoreColor ?> fs-3 fw-bold"><?= number_format($score, 1) ?>%</div>
                <div class="text-secondary small">Score</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <div class="text-light fs-3 fw-bold"><?= $attempt['correct_answers'] ?>/<?= $attempt['total_questions'] ?></div>
                <div class="text-secondary small">Correct Answers</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <div class="text-light fs-3 fw-bold"><?= $attempt['time_taken_minutes'] ?> min</div>
                <div class="text-secondary small">Time Taken</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0" style="background: var(--epc-card-bg);">
            <div class="card-body text-center">
                <div class="text-light fs-3 fw-bold"><?= $attempt['earned_points'] ?>/<?= $attempt['total_points'] ?></div>
                <div class="text-secondary small">Points Earned</div>
            </div>
        </div>
    </div>
</div>

<!-- Actions -->
<div class="d-flex justify-content-center gap-3 mb-4">
    <?php if ($passed && $certificate): ?>
        <a href="/enpharchem/training/certificate?id=<?= $certificate['id'] ?>" class="btn btn-warning btn-lg" target="_blank">
            <i class="fas fa-certificate me-2"></i>View Certificate
        </a>
    <?php endif; ?>
    <?php if (!$passed): ?>
        <a href="/enpharchem/training/assessment?course_id=<?= $attempt['course_id'] ?>" class="btn btn-primary btn-lg">
            <i class="fas fa-redo me-2"></i>Retake Assessment
        </a>
    <?php endif; ?>
    <a href="/enpharchem/training/course?id=<?= $attempt['course_id'] ?>" class="btn btn-outline-secondary btn-lg">
        <i class="fas fa-arrow-left me-2"></i>Back to Course
    </a>
</div>

<!-- Question-by-Question Review -->
<div class="card border-0 mb-4" style="background: var(--epc-card-bg);">
    <div class="card-header border-bottom border-secondary" style="background: var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="fas fa-list-check me-2"></i>Detailed Review</h5>
    </div>
    <div class="card-body">
        <?php foreach ($questions as $idx => $q):
            $qid = $q['id'];
            $ans = $answers[$qid] ?? null;
            $userAnswer = $ans['user_answer'] ?? '';
            $correctAnswer = $ans['correct_answer'] ?? strtoupper($q['correct_answer']);
            $isCorrect = $ans['is_correct'] ?? ($userAnswer === $correctAnswer);
            $optionMap = ['A' => $q['option_a'], 'B' => $q['option_b'], 'C' => $q['option_c'], 'D' => $q['option_d']];
        ?>
        <div class="mb-4 pb-3 <?= $idx < count($questions)-1 ? 'border-bottom border-secondary' : '' ?>">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary small">Question <?= $idx + 1 ?></span>
                <?php if ($isCorrect): ?>
                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Correct (+<?= $q['points'] ?? 10 ?> pts)</span>
                <?php else: ?>
                    <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Incorrect</span>
                <?php endif; ?>
            </div>
            <p class="text-light fw-semibold mb-2"><?= htmlspecialchars($q['question']) ?></p>

            <div class="row mb-2">
                <?php foreach (['A', 'B', 'C', 'D'] as $letter):
                    $optText = $optionMap[$letter] ?? '';
                    if (empty($optText)) continue;
                    $isUserChoice = ($userAnswer === $letter);
                    $isCorrectChoice = ($correctAnswer === $letter);
                    $bgClass = '';
                    $icon = '';
                    if ($isCorrectChoice && $isUserChoice) { $bgClass = 'bg-success bg-opacity-25 border-success'; $icon = '<i class="fas fa-check-circle text-success me-1"></i>'; }
                    elseif ($isCorrectChoice) { $bgClass = 'bg-success bg-opacity-10 border-success'; $icon = '<i class="fas fa-check text-success me-1"></i>'; }
                    elseif ($isUserChoice && !$isCorrect) { $bgClass = 'bg-danger bg-opacity-25 border-danger'; $icon = '<i class="fas fa-times-circle text-danger me-1"></i>'; }
                    else { $bgClass = ''; }
                ?>
                <div class="col-md-6 mb-1">
                    <div class="p-2 rounded border <?= $bgClass ?>" style="<?= empty($bgClass) ? 'border-color: rgba(255,255,255,0.1) !important;' : '' ?>">
                        <span class="text-light small"><?= $icon ?><strong><?= $letter ?>.</strong> <?= htmlspecialchars($optText) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (!$isCorrect && !empty($q['explanation'])): ?>
            <div class="alert alert-info bg-info bg-opacity-10 border-info py-2 mb-0">
                <i class="fas fa-lightbulb text-info me-1"></i>
                <span class="text-light small"><?= htmlspecialchars($q['explanation']) ?></span>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
