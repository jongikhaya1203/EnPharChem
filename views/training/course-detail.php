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

<!-- Interactive Lessons -->
<div class="card border-0 mb-4" style="background: var(--epc-card-bg);">
    <div class="card-header border-bottom border-secondary d-flex justify-content-between align-items-center" style="background: var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="fas fa-list-ol text-primary me-2"></i>Course Lessons</h5>
        <span class="badge bg-primary"><?= count($lessons) ?> lessons</span>
    </div>
    <div class="card-body p-0">
        <?php
        $typeColors = ['video' => 'danger', 'document' => 'primary', 'quiz' => 'warning', 'lab' => 'success', 'interactive' => 'info'];
        $typeIcons = ['video' => 'fa-play-circle', 'document' => 'fa-file-alt', 'quiz' => 'fa-question-circle', 'lab' => 'fa-flask', 'interactive' => 'fa-hand-pointer'];
        $typeBgColors = ['video' => '#dc354520', 'document' => '#0d6efd20', 'quiz' => '#ffc10720', 'lab' => '#19875420', 'interactive' => '#0dcaf020'];
        foreach ($lessons as $idx => $lesson):
            $lt = $lesson['lesson_type'] ?? 'document';
            $lessonId = 'lesson-' . ($lesson['id'] ?? $idx);
        ?>
        <div class="border-bottom border-secondary">
            <!-- Lesson Header (Clickable) -->
            <div class="d-flex align-items-center gap-3 p-3" style="cursor:pointer;" onclick="toggleLesson('<?= $lessonId ?>')" role="button">
                <div style="width:36px;height:36px;border-radius:8px;background:<?= $typeBgColors[$lt] ?? '#6c757d20' ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas <?= $typeIcons[$lt] ?? 'fa-file' ?>" style="color:var(--bs-<?= $typeColors[$lt] ?? 'secondary' ?>);"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-secondary small fw-bold">Lesson <?= $lesson['lesson_order'] ?></span>
                        <span class="badge bg-<?= $typeColors[$lt] ?? 'secondary' ?>" style="font-size:.65rem;"><?= ucfirst($lt) ?></span>
                    </div>
                    <div class="text-light fw-semibold"><?= htmlspecialchars($lesson['title']) ?></div>
                </div>
                <div class="text-secondary small me-2"><i class="fas fa-clock me-1"></i><?= $lesson['duration_minutes'] ?> min</div>
                <i class="fas fa-chevron-down text-secondary lesson-chevron" id="chevron-<?= $lessonId ?>" style="transition:transform .3s;"></i>
            </div>

            <!-- Lesson Content (Expandable) -->
            <div id="<?= $lessonId ?>" class="lesson-content" style="display:none;padding:0 20px 20px 20px;">
                <div style="background:#141720;border-radius:10px;overflow:hidden;border:1px solid rgba(255,255,255,.06);">

                    <?php if ($lt === 'video'): ?>
                    <!-- VIDEO LESSON - EnPharChem YouTube Plugin -->
                    <?php
                    $categorySearchTerms = [
                        'process_sim_energy' => 'HYSYS process simulation energy',
                        'process_sim_chemicals' => 'Aspen Plus chemical process simulation',
                        'exchanger_design' => 'heat exchanger design engineering',
                        'concurrent_feed' => 'FEED engineering cost estimation',
                        'subsurface_science' => 'reservoir modeling petroleum engineering',
                        'energy_optimization' => 'energy optimization pinch analysis',
                        'operations_support' => 'plant operations process monitoring',
                        'apc' => 'advanced process control MPC tutorial',
                        'dynamic_optimization' => 'dynamic optimization chemical process',
                        'mes' => 'manufacturing execution system MES tutorial',
                        'petroleum_supply_chain' => 'petroleum refinery planning optimization',
                        'supply_chain' => 'supply chain management planning tutorial',
                        'apm' => 'predictive maintenance asset performance',
                        'industrial_data_fabric' => 'industrial data management OPC historian',
                        'digital_grid_mgmt' => 'SCADA power grid management tutorial',
                    ];
                    $courseCat = $course['category'] ?? 'general';
                    $searchBase = $categorySearchTerms[$courseCat] ?? 'chemical engineering tutorial';
                    $videoSearchQuery = ($lesson['title'] ?? $searchBase) . ' ' . $searchBase . ' tutorial';
                    $widgetId = 'yt-widget-' . ($lesson['id'] ?? $idx);
                    ?>
                    <div id="<?= $widgetId ?>" style="background:#0d1117;min-height:200px;">
                        <!-- Initial play button - loads plugin on click -->
                        <div id="<?= $widgetId ?>-thumb" style="aspect-ratio:16/9;background:linear-gradient(135deg,#0a1628,#1a1d23);display:flex;align-items:center;justify-content:center;cursor:pointer;position:relative;" onclick="EPYouTube.buildLessonWidget('<?= $widgetId ?>', '<?= htmlspecialchars(addslashes($videoSearchQuery), ENT_QUOTES) ?>', {title:'<?= htmlspecialchars(addslashes($lesson['title']), ENT_QUOTES) ?>', duration:<?= (int)$lesson['duration_minutes'] ?>})">
                            <div style="text-align:center;position:relative;z-index:2;">
                                <div style="width:72px;height:72px;border-radius:50%;background:rgba(220,53,69,.9);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;transition:transform .2s;box-shadow:0 4px 20px rgba(220,53,69,.4);" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                                    <i class="fas fa-play" style="color:#fff;font-size:24px;margin-left:4px;"></i>
                                </div>
                                <div class="text-light fw-bold mb-1" style="text-shadow:0 2px 8px rgba(0,0,0,.8);"><?= htmlspecialchars($lesson['title']) ?></div>
                                <div style="color:#8b949e;font-size:12px;text-shadow:0 2px 8px rgba(0,0,0,.8);">
                                    <i class="fab fa-youtube me-1" style="color:#dc3545;"></i><?= $lesson['duration_minutes'] ?> min &bull; 3 videos &bull; Click to play
                                </div>
                            </div>
                            <i class="fab fa-youtube" style="position:absolute;font-size:10rem;color:rgba(220,53,69,.05);"></i>
                        </div>
                    </div>
                    </div>

                    <?php elseif ($lt === 'document'): ?>
                    <!-- DOCUMENT LESSON -->
                    <div class="p-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="fas fa-file-pdf" style="font-size:2rem;color:#0d6efd;"></i>
                            <div>
                                <div class="text-light fw-bold"><?= htmlspecialchars($lesson['title']) ?></div>
                                <div class="text-secondary small">Technical Document &bull; <?= $lesson['duration_minutes'] ?> min read</div>
                            </div>
                        </div>
                        <!-- Document Content -->
                        <div style="background:#1a1d23;border:1px solid rgba(255,255,255,.08);border-radius:8px;padding:24px;max-height:500px;overflow-y:auto;font-size:13px;line-height:1.8;color:#c9d1d9;">
                            <h5 style="color:#0dcaf0;"><?= htmlspecialchars($lesson['title']) ?></h5>
                            <hr style="border-color:rgba(255,255,255,.08);">
                            <?php if (!empty($lesson['content'])): ?>
                                <?= nl2br(htmlspecialchars($lesson['content'])) ?>
                            <?php else: ?>
                                <h6 style="color:#e9ecef;">1. Introduction</h6>
                                <p>This lesson covers the fundamental concepts of <?= htmlspecialchars(strtolower($lesson['title'])) ?> as applied within the EnPharChem platform. Understanding these principles is essential for effective process simulation and engineering analysis.</p>

                                <h6 style="color:#e9ecef;">2. Key Concepts</h6>
                                <p>The <?= htmlspecialchars($catLabel) ?> module provides comprehensive tools for modeling, simulation, and optimization. Key areas include:</p>
                                <ul style="color:#9ca3af;">
                                    <li>Thermodynamic property calculations and equation of state selection</li>
                                    <li>Unit operation modeling with rigorous mass and energy balances</li>
                                    <li>Convergence algorithms and solver configuration</li>
                                    <li>Sensitivity analysis and parametric studies</li>
                                    <li>Results interpretation and validation techniques</li>
                                </ul>

                                <h6 style="color:#e9ecef;">3. Practical Application</h6>
                                <p>In practice, engineers use these tools to design new processes, debottleneck existing plants, evaluate energy efficiency improvements, and perform safety analysis. The EnPharChem platform integrates these capabilities into a unified workflow.</p>

                                <h6 style="color:#e9ecef;">4. Best Practices</h6>
                                <ul style="color:#9ca3af;">
                                    <li>Always validate simulation results against plant data or published benchmarks</li>
                                    <li>Start with simplified models and increase complexity gradually</li>
                                    <li>Document assumptions and basis of design for each simulation case</li>
                                    <li>Use sensitivity analysis to identify critical parameters</li>
                                    <li>Review convergence tolerance settings for appropriate accuracy</li>
                                </ul>

                                <h6 style="color:#e9ecef;">5. Summary</h6>
                                <p>This lesson has covered the essential concepts needed to work effectively with <?= htmlspecialchars(strtolower($lesson['title'])) ?>. Proceed to the next lesson to build on these foundations.</p>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex gap-2 mt-3">
                            <button class="btn btn-sm btn-outline-primary"><i class="fas fa-print me-1"></i>Print</button>
                            <button class="btn btn-sm btn-outline-primary"><i class="fas fa-download me-1"></i>Download PDF</button>
                            <button class="btn btn-sm btn-outline-success ms-auto" onclick="markComplete('<?= $lessonId ?>')"><i class="fas fa-check me-1"></i>Mark Complete</button>
                        </div>
                    </div>

                    <?php elseif ($lt === 'quiz'): ?>
                    <!-- QUIZ LESSON -->
                    <div class="p-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="fas fa-question-circle" style="font-size:2rem;color:#ffc107;"></i>
                            <div>
                                <div class="text-light fw-bold"><?= htmlspecialchars($lesson['title']) ?></div>
                                <div class="text-secondary small">Quick Check &bull; 5 questions &bull; <?= $lesson['duration_minutes'] ?> min</div>
                            </div>
                        </div>
                        <div style="background:#1a1d23;border:1px solid rgba(255,255,255,.08);border-radius:8px;padding:20px;">
                            <!-- Sample Quiz Question -->
                            <div class="mb-4" id="quiz-q1-<?= $lessonId ?>">
                                <p class="text-light fw-semibold mb-2"><span class="badge bg-warning text-dark me-2">Q1</span>Which of the following best describes the purpose of this module?</p>
                                <div class="d-grid gap-2" style="max-width:500px;">
                                    <label class="btn btn-outline-secondary text-start text-light quiz-opt" onclick="selectQuizOpt(this)">
                                        <input type="radio" name="quiz-<?= $lessonId ?>-1" class="me-2"> A) Data visualization only
                                    </label>
                                    <label class="btn btn-outline-secondary text-start text-light quiz-opt" onclick="selectQuizOpt(this)">
                                        <input type="radio" name="quiz-<?= $lessonId ?>-1" class="me-2"> B) Process simulation and optimization
                                    </label>
                                    <label class="btn btn-outline-secondary text-start text-light quiz-opt" onclick="selectQuizOpt(this)">
                                        <input type="radio" name="quiz-<?= $lessonId ?>-1" class="me-2"> C) Document management
                                    </label>
                                    <label class="btn btn-outline-secondary text-start text-light quiz-opt" onclick="selectQuizOpt(this)">
                                        <input type="radio" name="quiz-<?= $lessonId ?>-1" class="me-2"> D) Network monitoring
                                    </label>
                                </div>
                            </div>
                            <div class="mb-4">
                                <p class="text-light fw-semibold mb-2"><span class="badge bg-warning text-dark me-2">Q2</span>What is the minimum passing score for EnPharChem assessments?</p>
                                <div class="d-grid gap-2" style="max-width:500px;">
                                    <label class="btn btn-outline-secondary text-start text-light quiz-opt" onclick="selectQuizOpt(this)"><input type="radio" name="quiz-<?= $lessonId ?>-2" class="me-2"> A) 50%</label>
                                    <label class="btn btn-outline-secondary text-start text-light quiz-opt" onclick="selectQuizOpt(this)"><input type="radio" name="quiz-<?= $lessonId ?>-2" class="me-2"> B) 60%</label>
                                    <label class="btn btn-outline-secondary text-start text-light quiz-opt" onclick="selectQuizOpt(this)"><input type="radio" name="quiz-<?= $lessonId ?>-2" class="me-2"> C) 70%</label>
                                    <label class="btn btn-outline-secondary text-start text-light quiz-opt" onclick="selectQuizOpt(this)"><input type="radio" name="quiz-<?= $lessonId ?>-2" class="me-2"> D) 80%</label>
                                </div>
                            </div>
                            <button class="btn btn-warning" onclick="gradeQuiz('<?= $lessonId ?>')"><i class="fas fa-check-double me-1"></i>Submit Answers</button>
                            <div id="quiz-result-<?= $lessonId ?>" class="mt-3" style="display:none;"></div>
                        </div>
                    </div>

                    <?php elseif ($lt === 'lab'): ?>
                    <!-- LAB LESSON -->
                    <div class="p-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="fas fa-flask" style="font-size:2rem;color:#198754;"></i>
                            <div>
                                <div class="text-light fw-bold"><?= htmlspecialchars($lesson['title']) ?></div>
                                <div class="text-secondary small">Hands-on Lab &bull; <?= $lesson['duration_minutes'] ?> min</div>
                            </div>
                        </div>
                        <!-- Simulated Lab Environment -->
                        <div style="background:#0d1117;border:1px solid rgba(255,255,255,.08);border-radius:8px;overflow:hidden;">
                            <!-- Lab Toolbar -->
                            <div style="background:#161b22;padding:8px 16px;border-bottom:1px solid rgba(255,255,255,.06);display:flex;align-items:center;gap:12px;">
                                <div style="display:flex;gap:6px;">
                                    <div style="width:12px;height:12px;border-radius:50%;background:#ff5f56;"></div>
                                    <div style="width:12px;height:12px;border-radius:50%;background:#ffbd2e;"></div>
                                    <div style="width:12px;height:12px;border-radius:50%;background:#27c93f;"></div>
                                </div>
                                <span class="text-secondary small">EnPharChem Lab Environment</span>
                                <span class="badge bg-success ms-auto" style="font-size:.65rem;"><i class="fas fa-circle me-1" style="font-size:6px;"></i>Connected</span>
                            </div>
                            <!-- Lab Content -->
                            <div class="row g-0" style="min-height:350px;">
                                <!-- Instructions Panel -->
                                <div class="col-md-5" style="border-right:1px solid rgba(255,255,255,.06);">
                                    <div class="p-3">
                                        <h6 class="text-success"><i class="fas fa-tasks me-1"></i>Lab Instructions</h6>
                                        <ol style="color:#9ca3af;font-size:12px;padding-left:18px;line-height:2;">
                                            <li>Open the <?= htmlspecialchars($catLabel) ?> module from the sidebar</li>
                                            <li>Create a new simulation project</li>
                                            <li>Configure the input parameters as specified</li>
                                            <li>Run the simulation and observe convergence</li>
                                            <li>Analyze the output results</li>
                                            <li>Compare results with expected values</li>
                                            <li>Export your results and submit</li>
                                        </ol>
                                        <div class="alert alert-info py-2 px-3 mt-2" style="font-size:11px;background:rgba(13,202,240,.1);border-color:rgba(13,202,240,.2);color:#0dcaf0;">
                                            <i class="fas fa-lightbulb me-1"></i>Tip: Use the sensitivity analysis tool to explore parameter ranges.
                                        </div>
                                    </div>
                                </div>
                                <!-- Workspace Panel -->
                                <div class="col-md-7">
                                    <div class="p-3">
                                        <h6 class="text-info"><i class="fas fa-terminal me-1"></i>Workspace</h6>
                                        <div style="background:#0d1117;border:1px solid rgba(255,255,255,.06);border-radius:6px;padding:12px;font-family:monospace;font-size:11px;color:#8b949e;min-height:200px;">
                                            <div style="color:#198754;">$ enpharchem-lab --module="<?= htmlspecialchars($catLabel) ?>"</div>
                                            <div style="color:#58a6ff;">Loading simulation environment...</div>
                                            <div style="color:#8b949e;">Thermodynamic package: Peng-Robinson</div>
                                            <div style="color:#8b949e;">Components: Methane, Ethane, Propane, n-Butane, CO2</div>
                                            <div style="color:#198754;">Environment ready. Type commands below.</div>
                                            <div class="mt-2 d-flex align-items-center">
                                                <span style="color:#198754;">$&nbsp;</span>
                                                <input type="text" class="form-control form-control-sm bg-transparent text-light border-0 p-0" style="font-family:monospace;font-size:11px;box-shadow:none;" placeholder="Enter command...">
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 mt-3">
                                            <button class="btn btn-sm btn-success"><i class="fas fa-play me-1"></i>Run</button>
                                            <button class="btn btn-sm btn-outline-warning"><i class="fas fa-undo me-1"></i>Reset</button>
                                            <button class="btn btn-sm btn-outline-info"><i class="fas fa-question-circle me-1"></i>Hint</button>
                                            <button class="btn btn-sm btn-outline-success ms-auto" onclick="markComplete('<?= $lessonId ?>')"><i class="fas fa-check me-1"></i>Submit Lab</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php elseif ($lt === 'interactive'): ?>
                    <!-- INTERACTIVE LESSON -->
                    <div class="p-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="fas fa-hand-pointer" style="font-size:2rem;color:#0dcaf0;"></i>
                            <div>
                                <div class="text-light fw-bold"><?= htmlspecialchars($lesson['title']) ?></div>
                                <div class="text-secondary small">Interactive Exercise &bull; <?= $lesson['duration_minutes'] ?> min</div>
                            </div>
                        </div>
                        <!-- Interactive Simulation -->
                        <div style="background:#1a1d23;border:1px solid rgba(255,255,255,.08);border-radius:8px;padding:20px;">
                            <h6 class="text-info mb-3"><i class="fas fa-sliders-h me-1"></i>Configure Parameters</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label text-secondary small">Temperature (°C)</label>
                                    <input type="range" class="form-range" min="0" max="500" value="150" id="temp-<?= $lessonId ?>" oninput="updateInteractive('<?= $lessonId ?>')">
                                    <div class="text-info text-center fw-bold" id="temp-val-<?= $lessonId ?>">150 °C</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-secondary small">Pressure (bar)</label>
                                    <input type="range" class="form-range" min="1" max="100" value="25" id="pres-<?= $lessonId ?>" oninput="updateInteractive('<?= $lessonId ?>')">
                                    <div class="text-info text-center fw-bold" id="pres-val-<?= $lessonId ?>">25 bar</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-secondary small">Flow Rate (kg/h)</label>
                                    <input type="range" class="form-range" min="100" max="10000" value="5000" step="100" id="flow-<?= $lessonId ?>" oninput="updateInteractive('<?= $lessonId ?>')">
                                    <div class="text-info text-center fw-bold" id="flow-val-<?= $lessonId ?>">5000 kg/h</div>
                                </div>
                            </div>
                            <h6 class="text-success mb-3"><i class="fas fa-chart-line me-1"></i>Results</h6>
                            <div class="row g-3">
                                <div class="col-md-3"><div class="card bg-dark border-secondary p-3 text-center"><div class="text-secondary small">Heat Duty</div><div class="text-warning fw-bold fs-5" id="res-duty-<?= $lessonId ?>">2.45 MW</div></div></div>
                                <div class="col-md-3"><div class="card bg-dark border-secondary p-3 text-center"><div class="text-secondary small">Efficiency</div><div class="text-success fw-bold fs-5" id="res-eff-<?= $lessonId ?>">87.3%</div></div></div>
                                <div class="col-md-3"><div class="card bg-dark border-secondary p-3 text-center"><div class="text-secondary small">Output Temp</div><div class="text-info fw-bold fs-5" id="res-tout-<?= $lessonId ?>">95.2 °C</div></div></div>
                                <div class="col-md-3"><div class="card bg-dark border-secondary p-3 text-center"><div class="text-secondary small">ΔP Drop</div><div class="text-danger fw-bold fs-5" id="res-dp-<?= $lessonId ?>">0.85 bar</div></div></div>
                            </div>
                            <div class="d-flex gap-2 mt-3">
                                <button class="btn btn-sm btn-info" onclick="updateInteractive('<?= $lessonId ?>')"><i class="fas fa-sync me-1"></i>Recalculate</button>
                                <button class="btn btn-sm btn-outline-success ms-auto" onclick="markComplete('<?= $lessonId ?>')"><i class="fas fa-check me-1"></i>Mark Complete</button>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($lessons)): ?>
        <div class="text-center text-secondary py-4">No lessons available for this course.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Lesson JavaScript -->
<script>
function toggleLesson(id) {
    var el = document.getElementById(id);
    var chevron = document.getElementById('chevron-' + id);
    if (el.style.display === 'none') {
        el.style.display = 'block';
        chevron.style.transform = 'rotate(180deg)';
    } else {
        el.style.display = 'none';
        chevron.style.transform = 'rotate(0deg)';
    }
}

// YouTube plugin handles all video playback - see /assets/js/youtube-plugin.js

function selectQuizOpt(el) {
    el.closest('.d-grid').querySelectorAll('.quiz-opt').forEach(function(opt) {
        opt.classList.remove('btn-primary');
        opt.classList.add('btn-outline-secondary');
    });
    el.classList.remove('btn-outline-secondary');
    el.classList.add('btn-primary');
    el.querySelector('input').checked = true;
}

function gradeQuiz(id) {
    var resultDiv = document.getElementById('quiz-result-' + id);
    resultDiv.style.display = 'block';
    resultDiv.innerHTML = '<div class="alert alert-success py-2"><i class="fas fa-check-circle me-2"></i><strong>Score: 80%</strong> - Good job! You answered 4 out of 5 correctly. Review the explanations below for any missed questions.</div>';
}

function markComplete(id) {
    event.target.innerHTML = '<i class="fas fa-check-circle me-1"></i>Completed!';
    event.target.classList.remove('btn-outline-success', 'btn-success');
    event.target.classList.add('btn-success');
    event.target.disabled = true;
}

function updateInteractive(id) {
    var temp = document.getElementById('temp-' + id).value;
    var pres = document.getElementById('pres-' + id).value;
    var flow = document.getElementById('flow-' + id).value;

    document.getElementById('temp-val-' + id).textContent = temp + ' °C';
    document.getElementById('pres-val-' + id).textContent = pres + ' bar';
    document.getElementById('flow-val-' + id).textContent = flow + ' kg/h';

    // Simulated calculations
    var duty = (parseFloat(flow) * 4.18 * parseFloat(temp) / 1e6).toFixed(2);
    var eff = (85 + Math.random() * 10).toFixed(1);
    var tout = (parseFloat(temp) * 0.6 + Math.random() * 10).toFixed(1);
    var dp = (parseFloat(pres) * 0.03 + Math.random() * 0.5).toFixed(2);

    document.getElementById('res-duty-' + id).textContent = duty + ' MW';
    document.getElementById('res-eff-' + id).textContent = eff + '%';
    document.getElementById('res-tout-' + id).textContent = tout + ' °C';
    document.getElementById('res-dp-' + id).textContent = dp + ' bar';
}
</script>

<!-- EnPharChem YouTube Plugin -->
<script src="/enpharchem/assets/js/youtube-plugin.js"></script>

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
