<!-- Assessment Page -->
<style>
    .question-card { display: none; }
    .question-card.active { display: block; }
    .question-card.show-all { display: block; }
    .option-label { cursor: pointer; padding: 12px 16px; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; margin-bottom: 8px; transition: all 0.2s; display: block; }
    .option-label:hover { border-color: var(--epc-accent); background: rgba(13,202,240,0.05); }
    .option-label input:checked + span { color: var(--epc-accent); font-weight: 600; }
    .option-label:has(input:checked) { border-color: var(--epc-accent); background: rgba(13,202,240,0.1); }
    #timer { font-family: 'Courier New', monospace; font-size: 1.2rem; }
    #timer.warning { color: #ffc107 !important; }
    #timer.danger { color: #dc3545 !important; animation: pulse 1s infinite; }
    @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.5; } }
</style>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard" class="text-decoration-none" style="color: var(--epc-accent);">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/training" class="text-decoration-none" style="color: var(--epc-accent);">Training</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/training/course?id=<?= $course['id'] ?>" class="text-decoration-none" style="color: var(--epc-accent);"><?= htmlspecialchars($course['title']) ?></a></li>
        <li class="breadcrumb-item active text-light" aria-current="page">Assessment</li>
    </ol>
</nav>

<!-- Header -->
<div class="card border-0 mb-4" style="background: var(--epc-card-bg);">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="text-light mb-1"><i class="fas fa-clipboard-check me-2" style="color: var(--epc-accent);"></i>Assessment</h4>
                <p class="text-secondary mb-0"><?= htmlspecialchars($course['title']) ?></p>
            </div>
            <div class="col-md-3 text-center">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-clock text-info"></i>
                    <span id="timer" class="text-info fw-bold"><?= $timerMinutes ?>:00</span>
                </div>
                <small class="text-secondary">Time Remaining</small>
            </div>
            <div class="col-md-3 text-end">
                <div class="form-check form-switch d-inline-flex align-items-center gap-2">
                    <input class="form-check-input" type="checkbox" id="viewToggle" onchange="toggleView()">
                    <label class="form-check-label text-secondary" for="viewToggle">Show All Questions</label>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Progress Bar -->
<div class="mb-4">
    <div class="d-flex justify-content-between mb-1">
        <span class="text-secondary small">Progress</span>
        <span class="text-light small" id="progressText">Question 1 of <?= count($questions) ?></span>
    </div>
    <div class="progress" style="height: 6px; background: rgba(255,255,255,0.1);">
        <div id="progressBar" class="progress-bar bg-info" style="width: <?= count($questions) > 0 ? (1/count($questions))*100 : 0 ?>%"></div>
    </div>
</div>

<form id="assessmentForm" method="POST" action="/enpharchem/training/assessment">
    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
    <input type="hidden" name="started_at" value="<?= date('Y-m-d H:i:s') ?>">
    <input type="hidden" name="answers" id="answersJson" value="{}">

    <?php foreach ($questions as $idx => $q): ?>
    <div class="question-card <?= $idx === 0 ? 'active' : '' ?>" data-index="<?= $idx ?>">
        <div class="card border-0 mb-3" style="background: var(--epc-card-bg);">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="badge bg-dark border border-secondary">Question <?= $idx + 1 ?> of <?= count($questions) ?></span>
                    <span class="badge bg-<?= $q['difficulty'] === 'easy' ? 'success' : ($q['difficulty'] === 'hard' ? 'danger' : 'warning') ?>">
                        <?= ucfirst($q['difficulty'] ?? 'medium') ?> - <?= $q['points'] ?? 10 ?> pts
                    </span>
                </div>
                <h5 class="text-light mb-4"><?= htmlspecialchars($q['question']) ?></h5>

                <?php if ($q['question_type'] === 'true_false'): ?>
                    <label class="option-label text-light">
                        <input type="radio" name="q_<?= $q['id'] ?>" value="A" class="me-2 d-none" onchange="recordAnswer(<?= $q['id'] ?>, 'A')">
                        <span><strong>A.</strong> True</span>
                    </label>
                    <label class="option-label text-light">
                        <input type="radio" name="q_<?= $q['id'] ?>" value="B" class="me-2 d-none" onchange="recordAnswer(<?= $q['id'] ?>, 'B')">
                        <span><strong>B.</strong> False</span>
                    </label>
                <?php else: ?>
                    <?php foreach (['A' => 'option_a', 'B' => 'option_b', 'C' => 'option_c', 'D' => 'option_d'] as $letter => $field): ?>
                        <?php if (!empty($q[$field])): ?>
                        <label class="option-label text-light">
                            <input type="radio" name="q_<?= $q['id'] ?>" value="<?= $letter ?>" class="me-2 d-none" onchange="recordAnswer(<?= $q['id'] ?>, '<?= $letter ?>')">
                            <span><strong><?= $letter ?>.</strong> <?= htmlspecialchars($q[$field]) ?></span>
                        </label>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Navigation -->
    <div class="card border-0 mb-4" style="background: var(--epc-card-bg);">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <button type="button" id="prevBtn" class="btn btn-outline-secondary" onclick="navigateQuestion(-1)" disabled>
                    <i class="fas fa-chevron-left me-1"></i>Previous
                </button>
                <div id="questionDots" class="d-flex gap-1 flex-wrap justify-content-center">
                    <?php for ($i = 0; $i < count($questions); $i++): ?>
                    <button type="button" class="btn btn-sm btn-outline-secondary question-dot" data-index="<?= $i ?>" onclick="goToQuestion(<?= $i ?>)" style="width:32px;height:32px;padding:0;font-size:0.75rem;">
                        <?= $i + 1 ?>
                    </button>
                    <?php endfor; ?>
                </div>
                <button type="button" id="nextBtn" class="btn btn-outline-info" onclick="navigateQuestion(1)">
                    Next<i class="fas fa-chevron-right ms-1"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Unanswered warning and Submit -->
    <div id="unansweredWarning" class="alert alert-warning d-none mb-3">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <span id="unansweredText">You have unanswered questions.</span>
    </div>

    <div class="text-center mb-4">
        <button type="button" class="btn btn-primary btn-lg px-5" onclick="submitAssessment()">
            <i class="fas fa-paper-plane me-2"></i>Submit Assessment
        </button>
    </div>
</form>

<script>
const totalQuestions = <?= count($questions) ?>;
let currentQuestion = 0;
let answers = {};
let showAll = false;
let timerSeconds = <?= $timerMinutes ?> * 60;
let timerInterval;

// Timer
function startTimer() {
    timerInterval = setInterval(function() {
        timerSeconds--;
        if (timerSeconds <= 0) {
            clearInterval(timerInterval);
            alert('Time is up! Your assessment will be submitted.');
            submitAssessment();
            return;
        }
        const m = Math.floor(timerSeconds / 60);
        const s = timerSeconds % 60;
        const el = document.getElementById('timer');
        el.textContent = m + ':' + (s < 10 ? '0' : '') + s;

        if (timerSeconds <= 60) el.className = 'fw-bold danger';
        else if (timerSeconds <= 300) el.className = 'fw-bold warning';
    }, 1000);
}
startTimer();

function recordAnswer(qid, value) {
    answers[qid] = value;
    updateDots();
    updateProgress();
}

function navigateQuestion(dir) {
    if (showAll) return;
    const cards = document.querySelectorAll('.question-card');
    cards[currentQuestion].classList.remove('active');
    currentQuestion = Math.max(0, Math.min(totalQuestions - 1, currentQuestion + dir));
    cards[currentQuestion].classList.add('active');
    updateNavButtons();
    updateProgress();
}

function goToQuestion(idx) {
    if (showAll) return;
    const cards = document.querySelectorAll('.question-card');
    cards[currentQuestion].classList.remove('active');
    currentQuestion = idx;
    cards[currentQuestion].classList.add('active');
    updateNavButtons();
    updateProgress();
}

function updateNavButtons() {
    document.getElementById('prevBtn').disabled = currentQuestion === 0;
    document.getElementById('nextBtn').disabled = currentQuestion === totalQuestions - 1;
}

function updateProgress() {
    const answered = Object.keys(answers).length;
    document.getElementById('progressText').textContent = showAll
        ? answered + ' of ' + totalQuestions + ' answered'
        : 'Question ' + (currentQuestion + 1) + ' of ' + totalQuestions + ' (' + answered + ' answered)';
    document.getElementById('progressBar').style.width = (answered / totalQuestions * 100) + '%';
}

function updateDots() {
    document.querySelectorAll('.question-dot').forEach(dot => {
        const idx = parseInt(dot.dataset.index);
        const cards = document.querySelectorAll('.question-card');
        const qInputs = cards[idx].querySelectorAll('input[type=radio]');
        let isAnswered = false;
        qInputs.forEach(inp => { if (inp.checked) isAnswered = true; });
        dot.className = isAnswered
            ? 'btn btn-sm btn-success question-dot'
            : 'btn btn-sm btn-outline-secondary question-dot';
        if (idx === currentQuestion && !showAll) dot.classList.add('border-info');
    });
}

function toggleView() {
    showAll = document.getElementById('viewToggle').checked;
    const cards = document.querySelectorAll('.question-card');
    cards.forEach(c => {
        if (showAll) {
            c.classList.add('show-all');
            c.classList.remove('active');
        } else {
            c.classList.remove('show-all');
        }
    });
    if (!showAll) {
        cards[currentQuestion].classList.add('active');
    }
    document.getElementById('prevBtn').disabled = showAll;
    document.getElementById('nextBtn').disabled = showAll;
    updateProgress();
}

function submitAssessment() {
    const unanswered = totalQuestions - Object.keys(answers).length;
    if (unanswered > 0) {
        const warning = document.getElementById('unansweredWarning');
        document.getElementById('unansweredText').textContent = 'You have ' + unanswered + ' unanswered question(s). Submit anyway?';
        warning.classList.remove('d-none');
        if (!confirm('You have ' + unanswered + ' unanswered question(s). Are you sure you want to submit?')) {
            return;
        }
    }
    document.getElementById('answersJson').value = JSON.stringify(answers);
    document.getElementById('assessmentForm').submit();
}

// Init
updateDots();
updateProgress();
</script>
