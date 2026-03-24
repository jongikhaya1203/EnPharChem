<?php
/**
 * EnPharChem Platform - Training, Assessment & Certificate Controller
 */

class TrainingController extends BaseController {

    /**
     * List all training courses with lesson counts, assessment status, certificate status
     */
    public function courses() {
        $userId = $this->user['id'] ?? 0;

        // Get all active courses with lesson counts
        $courses = $this->db->fetchAll(
            "SELECT tc.*,
                    (SELECT COUNT(*) FROM training_lessons tl WHERE tl.course_id = tc.id) as lesson_count,
                    (SELECT COUNT(*) FROM assessment_questions aq WHERE aq.course_id = tc.id) as question_count
             FROM training_courses tc
             WHERE tc.status = 'active'
             ORDER BY tc.category, FIELD(tc.level, 'beginner','intermediate','advanced','expert'), tc.title"
        );

        // Get user's best attempts per course
        $attempts = [];
        if ($userId) {
            $rows = $this->db->fetchAll(
                "SELECT course_id, MAX(score) as best_score, MAX(passed) as has_passed,
                        COUNT(*) as attempt_count
                 FROM assessment_attempts
                 WHERE user_id = ?
                 GROUP BY course_id",
                [$userId]
            );
            foreach ($rows as $r) {
                $attempts[$r['course_id']] = $r;
            }
        }

        // Get user's certificates
        $certificates = [];
        if ($userId) {
            $rows = $this->db->fetchAll(
                "SELECT course_id, id as cert_id, certificate_number FROM certificates WHERE user_id = ? AND status = 'active'",
                [$userId]
            );
            foreach ($rows as $r) {
                $certificates[$r['course_id']] = $r;
            }
        }

        // Stats
        $totalCourses = count($courses);
        $completedCourses = count(array_filter($attempts, fn($a) => $a['has_passed']));
        $totalCerts = count($certificates);
        $avgScore = 0;
        if (!empty($attempts)) {
            $scores = array_column($attempts, 'best_score');
            $avgScore = array_sum($scores) / count($scores);
        }

        // Get distinct categories for filter
        $categories = array_unique(array_column($courses, 'category'));

        $this->view('training/courses', [
            'pageTitle' => 'Training Courses',
            'courses' => $courses,
            'attempts' => $attempts,
            'certificates' => $certificates,
            'totalCourses' => $totalCourses,
            'completedCourses' => $completedCourses,
            'totalCerts' => $totalCerts,
            'avgScore' => $avgScore,
            'categories' => $categories,
        ]);
    }

    /**
     * Show single course detail
     */
    public function courseDetail() {
        $id = (int) $this->getParam('id');
        if (!$id) $this->redirect('training');

        $course = $this->db->fetch("SELECT * FROM training_courses WHERE id = ?", [$id]);
        if (!$course) $this->redirect('training');

        $lessons = $this->db->fetchAll(
            "SELECT * FROM training_lessons WHERE course_id = ? ORDER BY lesson_order",
            [$id]
        );

        $questionCount = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM assessment_questions WHERE course_id = ?",
            [$id]
        )['cnt'] ?? 0;

        $userId = $this->user['id'] ?? 0;
        $attempts = [];
        $bestAttempt = null;
        $certificate = null;

        if ($userId) {
            $attempts = $this->db->fetchAll(
                "SELECT * FROM assessment_attempts WHERE user_id = ? AND course_id = ? ORDER BY created_at DESC",
                [$userId, $id]
            );
            $bestAttempt = $this->db->fetch(
                "SELECT * FROM assessment_attempts WHERE user_id = ? AND course_id = ? ORDER BY score DESC LIMIT 1",
                [$userId, $id]
            );
            $certificate = $this->db->fetch(
                "SELECT * FROM certificates WHERE user_id = ? AND course_id = ? AND status = 'active' LIMIT 1",
                [$userId, $id]
            );
        }

        $this->view('training/course-detail', [
            'pageTitle' => $course['title'],
            'course' => $course,
            'lessons' => $lessons,
            'questionCount' => $questionCount,
            'attempts' => $attempts,
            'bestAttempt' => $bestAttempt,
            'certificate' => $certificate,
        ]);
    }

    /**
     * Take assessment - GET shows questions, POST grades
     */
    public function takeAssessment() {
        $userId = $this->user['id'] ?? 0;
        if (!$userId) $this->redirect('login');

        if ($this->isPost()) {
            // Grade the assessment
            $courseId = (int) ($_POST['course_id'] ?? 0);
            $answersJson = $_POST['answers'] ?? '{}';
            $startedAt = $_POST['started_at'] ?? date('Y-m-d H:i:s');
            $answers = json_decode($answersJson, true) ?: [];

            $questions = $this->db->fetchAll(
                "SELECT * FROM assessment_questions WHERE course_id = ? ORDER BY sort_order, id",
                [$courseId]
            );

            $totalPoints = 0;
            $earnedPoints = 0;
            $correctCount = 0;
            $totalCount = count($questions);
            $detailedAnswers = [];

            foreach ($questions as $q) {
                $qid = $q['id'];
                $userAnswer = strtoupper(trim($answers[$qid] ?? ''));
                $correctAnswer = strtoupper(trim($q['correct_answer']));
                $isCorrect = ($userAnswer === $correctAnswer);
                $totalPoints += $q['points'];

                if ($isCorrect) {
                    $earnedPoints += $q['points'];
                    $correctCount++;
                }

                $detailedAnswers[$qid] = [
                    'user_answer' => $userAnswer,
                    'correct_answer' => $correctAnswer,
                    'is_correct' => $isCorrect,
                    'points' => $q['points'],
                    'earned' => $isCorrect ? $q['points'] : 0,
                ];
            }

            $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
            $passed = $score >= 70 ? 1 : 0;
            $completedAt = date('Y-m-d H:i:s');

            // Calculate time taken
            $startTime = strtotime($startedAt);
            $endTime = strtotime($completedAt);
            $timeTaken = max(1, round(($endTime - $startTime) / 60));

            // Save attempt
            $attemptId = $this->db->insert('assessment_attempts', [
                'user_id' => $userId,
                'course_id' => $courseId,
                'score' => $score,
                'total_points' => $totalPoints,
                'earned_points' => $earnedPoints,
                'total_questions' => $totalCount,
                'correct_answers' => $correctCount,
                'time_taken_minutes' => $timeTaken,
                'passed' => $passed,
                'answers' => json_encode($detailedAnswers),
                'started_at' => $startedAt,
                'completed_at' => $completedAt,
            ]);

            // If passed, create certificate (if not already exists)
            if ($passed) {
                $existingCert = $this->db->fetch(
                    "SELECT id FROM certificates WHERE user_id = ? AND course_id = ? AND status = 'active'",
                    [$userId, $courseId]
                );

                if (!$existingCert) {
                    $course = $this->db->fetch("SELECT * FROM training_courses WHERE id = ?", [$courseId]);
                    $certNumber = 'EPC-' . strtoupper(substr(md5($userId . $courseId . time()), 0, 8)) . '-' . date('Y');

                    $this->db->insert('certificates', [
                        'user_id' => $userId,
                        'course_id' => $courseId,
                        'attempt_id' => $attemptId,
                        'certificate_number' => $certNumber,
                        'first_name' => $this->user['first_name'] ?? 'Student',
                        'last_name' => $this->user['last_name'] ?? '',
                        'course_title' => $course['title'],
                        'course_level' => $course['level'],
                        'score' => $score,
                        'issue_date' => date('Y-m-d'),
                        'expiry_date' => date('Y-m-d', strtotime('+2 years')),
                        'status' => 'active',
                    ]);
                }
            }

            $this->redirect('training/results?attempt_id=' . $attemptId);
            return;
        }

        // GET - Show assessment form
        $courseId = (int) $this->getParam('course_id');
        if (!$courseId) $this->redirect('training');

        $course = $this->db->fetch("SELECT * FROM training_courses WHERE id = ?", [$courseId]);
        if (!$course) $this->redirect('training');

        $questions = $this->db->fetchAll(
            "SELECT * FROM assessment_questions WHERE course_id = ? ORDER BY sort_order, id",
            [$courseId]
        );

        // Shuffle questions
        shuffle($questions);

        // Timer: course duration / 4 in minutes (min 15, max 120)
        $timerMinutes = max(15, min(120, (int)(($course['duration_hours'] ?? 1) * 60 / 4)));

        $this->view('training/assessment', [
            'pageTitle' => 'Assessment: ' . $course['title'],
            'course' => $course,
            'questions' => $questions,
            'timerMinutes' => $timerMinutes,
        ]);
    }

    /**
     * Show assessment results
     */
    public function assessmentResults() {
        $attemptId = (int) $this->getParam('attempt_id');
        if (!$attemptId) $this->redirect('training');

        $attempt = $this->db->fetch(
            "SELECT aa.*, tc.title as course_title, tc.level as course_level, tc.category
             FROM assessment_attempts aa
             JOIN training_courses tc ON aa.course_id = tc.id
             WHERE aa.id = ?",
            [$attemptId]
        );
        if (!$attempt) $this->redirect('training');

        $questions = $this->db->fetchAll(
            "SELECT * FROM assessment_questions WHERE course_id = ? ORDER BY sort_order, id",
            [$attempt['course_id']]
        );

        $answers = json_decode($attempt['answers'] ?? '{}', true) ?: [];

        // Get certificate if passed
        $certificate = null;
        if ($attempt['passed']) {
            $certificate = $this->db->fetch(
                "SELECT * FROM certificates WHERE attempt_id = ? OR (user_id = ? AND course_id = ? AND status = 'active') LIMIT 1",
                [$attemptId, $attempt['user_id'], $attempt['course_id']]
            );
        }

        $this->view('training/results', [
            'pageTitle' => 'Assessment Results',
            'attempt' => $attempt,
            'questions' => $questions,
            'answers' => $answers,
            'certificate' => $certificate,
        ]);
    }

    /**
     * Show printable certificate (standalone HTML, no layout)
     */
    public function certificate() {
        $certId = (int) $this->getParam('id');
        if (!$certId) $this->redirect('training/my-certificates');

        $cert = $this->db->fetch("SELECT * FROM certificates WHERE id = ?", [$certId]);
        if (!$cert) $this->redirect('training/my-certificates');

        $this->viewWithoutLayout('training/certificate', [
            'cert' => $cert,
        ]);
    }

    /**
     * List all certificates for current user
     */
    public function myCertificates() {
        $userId = $this->user['id'] ?? 0;

        $certificates = $this->db->fetchAll(
            "SELECT c.*, tc.category
             FROM certificates c
             LEFT JOIN training_courses tc ON c.course_id = tc.id
             WHERE c.user_id = ?
             ORDER BY c.issue_date DESC",
            [$userId]
        );

        $this->view('training/my-certificates', [
            'pageTitle' => 'My Certificates',
            'certificates' => $certificates,
        ]);
    }

    /**
     * Seed ALL training data: 60 courses, 300+ lessons, 600+ questions
     */
    public function seedTraining() {
        if (!$this->isPost()) $this->redirect('training');

        set_time_limit(300);

        try {
            $pdo = $this->db->getConnection();

            // Ensure tables exist by running migration SQL
            $sqlFile = APP_ROOT . '/database/training_assessment_tables.sql';
            if (file_exists($sqlFile)) {
                $sql = file_get_contents($sqlFile);
                $statements = array_filter(
                    array_map('trim', explode(';', $sql)),
                    function ($s) {
                        $clean = preg_replace('/--.*$/m', '', $s);
                        return trim($clean) !== '';
                    }
                );
                foreach ($statements as $stmt) {
                    try { $pdo->exec($stmt); } catch (PDOException $e) { /* table exists */ }
                }
            }

            // Clean existing seed data
            $pdo->exec("DELETE FROM certificates WHERE 1=1");
            $pdo->exec("DELETE FROM assessment_attempts WHERE 1=1");
            $pdo->exec("DELETE FROM assessment_questions WHERE 1=1");
            $pdo->exec("DELETE FROM training_lessons WHERE 1=1");
            $pdo->exec("DELETE FROM training_courses WHERE 1=1");

            // ═══════════════════════════════════════════════════
            // 15 CATEGORIES
            // ═══════════════════════════════════════════════════
            $categories = $this->getSeedCategories();

            $courseCount = 0;
            $lessonCount = 0;
            $questionCount = 0;

            foreach ($categories as $catKey => $catData) {
                foreach (['beginner', 'intermediate', 'advanced', 'expert'] as $level) {
                    $title = $catData['name'] . ' - ' . ucfirst($level);
                    $durations = ['beginner' => 4, 'intermediate' => 8, 'advanced' => 16, 'expert' => 24];
                    $duration = $durations[$level];

                    $prereqs = '';
                    if ($level === 'intermediate') $prereqs = $catData['name'] . ' - Beginner';
                    if ($level === 'advanced') $prereqs = $catData['name'] . ' - Intermediate';
                    if ($level === 'expert') $prereqs = $catData['name'] . ' - Advanced';

                    $objectives = $this->getLearningObjectives($catKey, $level);
                    $description = $this->getCourseDescription($catKey, $level, $catData);

                    $instructors = ['Dr. Sarah Chen', 'Prof. Michael Rivera', 'Dr. Aisha Patel', 'Eng. James Thornton', 'Dr. Elena Volkov', 'Prof. Hassan Al-Rashid', 'Dr. Lisa Yamamoto', 'Eng. Robert MacFarlane'];

                    $courseId = $this->db->insert('training_courses', [
                        'title' => $title,
                        'description' => $description,
                        'category' => $catKey,
                        'level' => $level,
                        'duration_hours' => $duration,
                        'instructor' => $instructors[array_rand($instructors)],
                        'prerequisites' => $prereqs,
                        'learning_objectives' => $objectives,
                        'enrollment_count' => rand(5, 200),
                        'max_enrollment' => 50,
                        'status' => 'active',
                    ]);
                    $courseCount++;

                    // Create lessons (5-8 per course)
                    $lessons = $this->getSeedLessons($catKey, $level);
                    foreach ($lessons as $idx => $lesson) {
                        $this->db->insert('training_lessons', [
                            'course_id' => $courseId,
                            'title' => $lesson['title'],
                            'description' => $lesson['description'] ?? '',
                            'lesson_order' => $idx + 1,
                            'lesson_type' => $lesson['type'],
                            'duration_minutes' => $lesson['duration'],
                            'status' => 'published',
                        ]);
                        $lessonCount++;
                    }

                    // Create assessment questions (10 per course)
                    $questions = $this->getSeedQuestions($catKey, $level);
                    foreach ($questions as $idx => $q) {
                        $this->db->insert('assessment_questions', [
                            'course_id' => $courseId,
                            'question' => $q['question'],
                            'question_type' => $q['type'] ?? 'multiple_choice',
                            'option_a' => $q['a'],
                            'option_b' => $q['b'],
                            'option_c' => $q['c'],
                            'option_d' => $q['d'],
                            'correct_answer' => $q['answer'],
                            'explanation' => $q['explanation'],
                            'difficulty' => $q['difficulty'] ?? 'medium',
                            'points' => $q['points'] ?? 10,
                            'sort_order' => $idx + 1,
                        ]);
                        $questionCount++;
                    }
                }
            }

            $_SESSION['flash_success'] = "Training data seeded successfully: {$courseCount} courses, {$lessonCount} lessons, {$questionCount} assessment questions.";

        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Seed error: " . $e->getMessage();
        }

        $this->redirect('training');
    }

    // ═══════════════════════════════════════════════════════════════
    // SEED DATA HELPER METHODS
    // ═══════════════════════════════════════════════════════════════

    private function getSeedCategories() {
        return [
            'process_sim_energy' => [
                'name' => 'Process Simulation for Energy',
                'desc' => 'Aspen HYSYS, thermodynamic modeling, and energy process simulation',
                'modules' => ['HYSYS Steady-State', 'HYSYS Dynamics', 'Upstream Processing', 'Gas Processing', 'Refining Models'],
            ],
            'process_sim_chemicals' => [
                'name' => 'Process Simulation for Chemicals',
                'desc' => 'Aspen Plus, polymer modeling, and chemical process simulation',
                'modules' => ['Aspen Plus', 'Polymer Modeling', 'Batch Processing', 'Electrolyte Systems', 'Solids Handling'],
            ],
            'exchanger_design' => [
                'name' => 'Exchanger Design & Rating',
                'desc' => 'Heat exchanger design, rating, and optimization using HTRI/EDR',
                'modules' => ['Shell & Tube Design', 'Plate Exchangers', 'Air Coolers', 'Fired Heaters', 'TEMA Standards'],
            ],
            'concurrent_feed' => [
                'name' => 'Concurrent FEED Engineering',
                'desc' => 'Front-End Engineering Design with concurrent workflows',
                'modules' => ['FEED Methodology', 'Cost Estimation', 'Equipment Sizing', 'P&ID Development', 'Hazop Integration'],
            ],
            'subsurface_science' => [
                'name' => 'Subsurface Science & Engineering',
                'desc' => 'Reservoir simulation, well modeling, and subsurface analytics',
                'modules' => ['Reservoir Simulation', 'Well Performance', 'PVT Analysis', 'History Matching', 'Production Optimization'],
            ],
            'energy_optimization' => [
                'name' => 'Energy Optimization',
                'desc' => 'Pinch analysis, energy integration, and utility optimization',
                'modules' => ['Pinch Analysis', 'Heat Integration', 'Utility Systems', 'Cogeneration', 'Carbon Footprint'],
            ],
            'operations_support' => [
                'name' => 'Operations Support & Performance',
                'desc' => 'Plant performance monitoring, troubleshooting, and optimization',
                'modules' => ['Online Monitoring', 'What-If Analysis', 'Debottlenecking', 'KPI Dashboards', 'Alarm Management'],
            ],
            'apc' => [
                'name' => 'Advanced Process Control',
                'desc' => 'Model predictive control, inferential sensors, and multivariable control',
                'modules' => ['DMC/MPC Fundamentals', 'Model Identification', 'Controller Design', 'Inferential Sensors', 'APC Benefits Tracking'],
            ],
            'dynamic_optimization' => [
                'name' => 'Dynamic Optimization',
                'desc' => 'Real-time optimization, dynamic simulation, and transient analysis',
                'modules' => ['RTO Fundamentals', 'Dynamic Modeling', 'LP/NLP Optimization', 'Transition Management', 'Closed-Loop RTO'],
            ],
            'mes' => [
                'name' => 'Manufacturing Execution Systems',
                'desc' => 'MES/MOM, batch management, historian, and production tracking',
                'modules' => ['ISA-95 Framework', 'Batch Management', 'Data Historian', 'OEE Tracking', 'Recipe Management'],
            ],
            'petroleum_supply_chain' => [
                'name' => 'Petroleum Supply Chain',
                'desc' => 'Crude scheduling, refinery planning, and distribution optimization',
                'modules' => ['Crude Assay Management', 'LP Planning', 'Scheduling', 'Blending Optimization', 'Distribution Logistics'],
            ],
            'supply_chain' => [
                'name' => 'Supply Chain Management',
                'desc' => 'Demand planning, inventory optimization, and network design',
                'modules' => ['Demand Forecasting', 'Inventory Optimization', 'S&OP Process', 'Network Design', 'Transportation Planning'],
            ],
            'apm' => [
                'name' => 'Asset Performance Management',
                'desc' => 'Predictive maintenance, reliability engineering, and asset analytics',
                'modules' => ['Condition Monitoring', 'Predictive Analytics', 'RBI/RCM', 'Failure Mode Analysis', 'Maintenance Optimization'],
            ],
            'industrial_data_fabric' => [
                'name' => 'Industrial Data Fabric',
                'desc' => 'Data integration, contextualization, and industrial IoT',
                'modules' => ['Data Architecture', 'OPC UA/MQTT', 'Data Lake Design', 'Edge Computing', 'AI/ML Pipelines'],
            ],
            'digital_grid_mgmt' => [
                'name' => 'Digital Grid Management',
                'desc' => 'Power grid optimization, SCADA, and renewable integration',
                'modules' => ['Grid Modeling', 'SCADA Systems', 'Load Forecasting', 'Renewable Integration', 'Microgrid Design'],
            ],
        ];
    }

    private function getCourseDescription($catKey, $level, $catData) {
        $moduleList = implode(', ', $catData['modules']);
        $levelDescriptions = [
            'beginner' => "Introduction to {$catData['desc']}. This foundational course covers core concepts and basic workflows for {$moduleList}. No prior experience required.",
            'intermediate' => "Build upon foundational knowledge of {$catData['desc']}. Covers intermediate topics including practical applications of {$moduleList}. Hands-on exercises and case studies included.",
            'advanced' => "Deep dive into {$catData['desc']}. Advanced topics cover optimization, troubleshooting, and best practices for {$moduleList}. Industry case studies and complex problem-solving.",
            'expert' => "Master-level course on {$catData['desc']}. Expert topics include cutting-edge methodologies, custom solutions, and leadership in {$moduleList}. Capstone project required.",
        ];
        return $levelDescriptions[$level];
    }

    private function getLearningObjectives($catKey, $level) {
        $allObjectives = [
            'process_sim_energy' => [
                'beginner' => "- Understand thermodynamic principles used in energy process simulation\n- Navigate HYSYS interface and build basic flowsheets\n- Select appropriate equation of state for hydrocarbon systems\n- Perform simple heat and material balance calculations\n- Interpret simulation results and convergence reports",
                'intermediate' => "- Design and optimize distillation columns in HYSYS\n- Model compressor trains and expander systems\n- Implement recycle loops and convergence strategies\n- Perform sensitivity analysis and case studies\n- Model gas processing facilities (dehydration, sweetening)",
                'advanced' => "- Build dynamic models for startup/shutdown procedures\n- Implement custom unit operations and extensions\n- Optimize LNG and NGL recovery processes\n- Perform flare system analysis and relief valve sizing\n- Integrate HYSYS with real-time plant data",
                'expert' => "- Develop digital twin frameworks for operating facilities\n- Lead simulation-based FEED studies\n- Design advanced control strategies using dynamic simulation\n- Implement AI-assisted process optimization\n- Mentor teams on simulation best practices",
            ],
            'process_sim_chemicals' => [
                'beginner' => "- Understand activity coefficient models for chemical systems\n- Build basic Aspen Plus flowsheets for chemical processes\n- Select appropriate property methods (NRTL, UNIQUAC, Wilson)\n- Perform VLE/LLE calculations and phase envelope analysis\n- Set up simple reactor models (RStoic, REquil, RGibbs)",
                'intermediate' => "- Design and rate distillation columns with RadFrac\n- Model electrolyte systems and acid gas processes\n- Implement batch processing workflows\n- Use sensitivity and optimization tools effectively\n- Model solid handling operations (crystallization, drying)",
                'advanced' => "- Develop custom property packages and user models\n- Optimize polymerization reactor systems\n- Implement pressure safety analysis workflows\n- Model complex reaction kinetics (LHHW, power law)\n- Perform process economics and capital cost estimation",
                'expert' => "- Integrate Aspen Plus with external tools (MATLAB, Python)\n- Lead technology evaluation studies using simulation\n- Develop enterprise simulation templates and standards\n- Implement model-based process development\n- Design green chemistry and sustainability analysis frameworks",
            ],
            'exchanger_design' => [
                'beginner' => "- Understand LMTD and epsilon-NTU methods\n- Identify TEMA exchanger types and nomenclature\n- Read and interpret exchanger data sheets\n- Perform basic thermal and hydraulic calculations\n- Understand fouling factors and their impact",
                'intermediate' => "- Design shell and tube exchangers using EDR/HTRI\n- Optimize baffle configuration and tube layout\n- Rate existing exchangers under new conditions\n- Design air-cooled exchangers and fan systems\n- Handle phase change (condensation, boiling) design",
                'advanced' => "- Perform vibration analysis and tube failure prevention\n- Design for difficult services (high pressure, corrosive)\n- Optimize heat exchanger networks using pinch analysis\n- Design plate and frame, spiral, and compact exchangers\n- Implement mechanical design per ASME standards",
                'expert' => "- Lead heat exchanger network retrofit projects\n- Develop custom correlations for proprietary geometries\n- Implement digital twin monitoring for exchangers\n- Design fired heaters and waste heat recovery systems\n- Mentor teams on exchanger design best practices",
            ],
            'concurrent_feed' => [
                'beginner' => "- Understand FEED methodology and project phases\n- Read and interpret P&IDs and PFDs\n- Understand equipment sizing fundamentals\n- Navigate cost estimation databases\n- Understand safety and environmental regulations",
                'intermediate' => "- Develop preliminary P&IDs for process units\n- Perform equipment sizing for common unit operations\n- Conduct Class 3 cost estimates (+/-20%)\n- Implement concurrent engineering workflows\n- Coordinate with discipline engineering teams",
                'advanced' => "- Lead FEED study execution for complex facilities\n- Perform Class 2 cost estimates (+/-10%)\n- Integrate HAZOP findings into design\n- Optimize plot plans and piping layouts\n- Develop construction execution strategies",
                'expert' => "- Direct multi-discipline FEED teams\n- Implement value engineering methodologies\n- Develop contracting and procurement strategies\n- Lead technology selection and licensing evaluation\n- Establish FEED quality assurance frameworks",
            ],
            'subsurface_science' => [
                'beginner' => "- Understand reservoir rock and fluid properties\n- Interpret PVT reports and phase behavior\n- Navigate reservoir simulation software interfaces\n- Understand basic well performance calculations\n- Read and interpret well log data",
                'intermediate' => "- Build sector and full-field reservoir models\n- Perform decline curve analysis and forecasting\n- Model well inflow and tubing performance\n- Conduct basic history matching workflows\n- Analyze production data using modern techniques",
                'advanced' => "- Implement enhanced oil recovery models (WAG, polymer)\n- Perform uncertainty analysis and probabilistic forecasting\n- Build compositional models for gas condensate reservoirs\n- Design and optimize hydraulic fracture treatments\n- Integrate geomechanics with reservoir simulation",
                'expert' => "- Develop integrated asset models (reservoir-to-surface)\n- Lead field development planning studies\n- Implement machine learning for production optimization\n- Design smart well completion strategies\n- Direct subsurface team workflows and standards",
            ],
            'energy_optimization' => [
                'beginner' => "- Understand principles of energy conservation in process plants\n- Perform basic pinch analysis calculations\n- Identify heat integration opportunities\n- Understand utility system fundamentals\n- Calculate simple payback for energy projects",
                'intermediate' => "- Design heat exchanger networks using pinch technology\n- Optimize steam and power utility systems\n- Conduct site-wide energy audits\n- Model cogeneration and trigeneration systems\n- Analyze cooling water and refrigeration systems",
                'advanced' => "- Implement advanced pinch analysis (water, hydrogen)\n- Optimize total site heat integration\n- Design combined heat and power systems\n- Perform carbon footprint analysis and reduction planning\n- Integrate renewable energy sources into process plants",
                'expert' => "- Lead energy transition strategy development\n- Implement real-time energy optimization systems\n- Design net-zero emission process facilities\n- Develop enterprise energy management frameworks\n- Evaluate carbon capture and storage integration",
            ],
            'operations_support' => [
                'beginner' => "- Understand online process monitoring fundamentals\n- Interpret key performance indicators for plant operations\n- Navigate operator decision support interfaces\n- Understand alarm management principles\n- Read and interpret trend displays and historian data",
                'intermediate' => "- Configure what-if scenarios for troubleshooting\n- Build and maintain KPI dashboards\n- Perform basic debottlenecking analysis\n- Implement alarm rationalization workflows\n- Analyze shift handover and operational data",
                'advanced' => "- Implement advanced pattern recognition for early fault detection\n- Optimize operating envelopes using simulation\n- Perform detailed debottlenecking studies with economic analysis\n- Design operator training simulators\n- Integrate multiple data sources for comprehensive monitoring",
                'expert' => "- Lead operations excellence transformation programs\n- Implement AI-powered advisory systems\n- Develop autonomous operations roadmaps\n- Design enterprise performance management frameworks\n- Direct digital transformation initiatives for operations",
            ],
            'apc' => [
                'beginner' => "- Understand feedback and feedforward control principles\n- Identify benefits and applications of APC\n- Understand DMC/MPC technology fundamentals\n- Read and interpret controller performance metrics\n- Understand model identification basics",
                'intermediate' => "- Perform step testing and model identification\n- Configure DMC/MPC controllers for common applications\n- Tune controller parameters for robust performance\n- Implement inferential property estimators\n- Calculate and track APC benefits",
                'advanced' => "- Design multivariable control strategies for complex units\n- Implement nonlinear MPC applications\n- Troubleshoot controller performance issues\n- Integrate APC with real-time optimization\n- Handle constrained optimization in MPC frameworks",
                'expert' => "- Lead enterprise-wide APC deployment programs\n- Develop custom control algorithms and extensions\n- Implement machine learning-based soft sensors\n- Design adaptive and self-tuning MPC strategies\n- Evaluate and select APC technology platforms",
            ],
            'dynamic_optimization' => [
                'beginner' => "- Understand real-time optimization fundamentals\n- Differentiate between LP, NLP, and MILP formulations\n- Navigate RTO software interfaces\n- Understand dynamic simulation principles\n- Identify optimization opportunities in process plants",
                'intermediate' => "- Build LP models for refinery planning\n- Configure RTO applications for process units\n- Perform model validation and bias updating\n- Implement transition management strategies\n- Analyze optimization results and sensitivities",
                'advanced' => "- Develop NLP optimization models for complex processes\n- Implement closed-loop RTO with APC integration\n- Design multi-unit optimization strategies\n- Handle uncertainty in optimization models\n- Perform rigorous dynamic optimization",
                'expert' => "- Lead enterprise optimization strategy development\n- Implement site-wide integrated planning and scheduling\n- Design AI-augmented optimization frameworks\n- Develop custom optimization solvers and algorithms\n- Direct real-time value chain optimization programs",
            ],
            'mes' => [
                'beginner' => "- Understand ISA-95/ISA-88 framework and terminology\n- Navigate MES system interfaces\n- Understand data historian fundamentals\n- Identify key MES functions (production tracking, quality)\n- Read and interpret batch reports",
                'intermediate' => "- Configure batch recipes and procedures (ISA-88)\n- Set up data historian tags and collection rates\n- Implement OEE tracking and reporting\n- Configure material tracking and genealogy\n- Design basic MES workflows and events",
                'advanced' => "- Implement exception-based data compression algorithms\n- Design complex batch management strategies\n- Integrate MES with ERP systems (SAP, Oracle)\n- Develop custom reports and analytics dashboards\n- Implement electronic batch records (EBR)",
                'expert' => "- Lead MES/MOM digital transformation programs\n- Architect enterprise-wide MES deployments\n- Implement real-time quality management systems\n- Design Industry 4.0 integration strategies\n- Develop regulatory compliance frameworks (FDA, GMP)",
            ],
            'petroleum_supply_chain' => [
                'beginner' => "- Understand crude oil assay analysis and characterization\n- Navigate LP-based refinery planning tools\n- Understand basic scheduling concepts\n- Identify key supply chain components in petroleum\n- Read and interpret blending specifications",
                'intermediate' => "- Build LP models for refinery production planning\n- Implement crude oil scheduling workflows\n- Optimize product blending operations\n- Perform crude assay management and evaluation\n- Coordinate between planning and scheduling functions",
                'advanced' => "- Develop multi-period planning models with inventory\n- Implement detailed scheduling under uncertainty\n- Optimize crude oil procurement strategies\n- Design distribution and logistics networks\n- Integrate planning with real-time optimization",
                'expert' => "- Lead integrated value chain optimization programs\n- Implement AI-driven demand forecasting\n- Design digital twin platforms for supply chain\n- Develop strategic crude portfolio optimization\n- Direct enterprise-wide S&OP implementation",
            ],
            'supply_chain' => [
                'beginner' => "- Understand supply chain management fundamentals\n- Navigate demand planning software interfaces\n- Understand inventory management principles\n- Identify key supply chain KPIs\n- Read and interpret forecast accuracy reports",
                'intermediate' => "- Build statistical demand forecast models\n- Implement safety stock optimization\n- Configure S&OP process workflows\n- Perform basic network design analysis\n- Analyze transportation routes and costs",
                'advanced' => "- Implement machine learning demand forecasting\n- Optimize multi-echelon inventory networks\n- Design resilient supply chain strategies\n- Perform scenario planning under disruptions\n- Integrate financial planning with operations",
                'expert' => "- Lead end-to-end supply chain transformation\n- Implement digital supply chain twin platforms\n- Design autonomous planning and fulfillment systems\n- Develop sustainability-focused supply chains\n- Direct enterprise S&OP/IBP programs",
            ],
            'apm' => [
                'beginner' => "- Understand asset lifecycle management concepts\n- Navigate condition monitoring interfaces\n- Understand basic vibration analysis principles\n- Identify common failure modes in rotating equipment\n- Read and interpret maintenance work orders",
                'intermediate' => "- Implement vibration, thermography, and oil analysis programs\n- Perform risk-based inspection planning\n- Configure predictive maintenance workflows\n- Calculate equipment reliability metrics (MTBF, MTTR)\n- Design basic maintenance optimization strategies",
                'advanced' => "- Implement machine learning for failure prediction\n- Perform reliability-centered maintenance analysis\n- Design asset investment planning models\n- Integrate IoT sensors with APM platforms\n- Develop remaining useful life estimation models",
                'expert' => "- Lead enterprise asset management transformation\n- Implement prescriptive maintenance strategies\n- Design digital twin platforms for critical assets\n- Develop asset performance benchmarking frameworks\n- Direct Industry 4.0 maintenance programs",
            ],
            'industrial_data_fabric' => [
                'beginner' => "- Understand industrial data architecture concepts\n- Navigate data integration platform interfaces\n- Understand OPC UA and MQTT protocols\n- Identify data quality challenges in industrial settings\n- Read and interpret data flow diagrams",
                'intermediate' => "- Configure OPC UA server/client connections\n- Implement data contextualization and tagging\n- Design basic data lake architectures\n- Set up edge computing gateways\n- Build data pipelines for time-series data",
                'advanced' => "- Implement ML pipelines for industrial data\n- Design scalable data fabric architectures\n- Build real-time streaming analytics platforms\n- Implement data governance frameworks\n- Integrate IT/OT data for unified analytics",
                'expert' => "- Lead enterprise data fabric strategy\n- Implement AI-ready industrial data platforms\n- Design federated data mesh architectures\n- Develop data monetization strategies\n- Direct digital transformation data programs",
            ],
            'digital_grid_mgmt' => [
                'beginner' => "- Understand power grid fundamentals and load flow\n- Navigate SCADA system interfaces\n- Understand renewable energy basics\n- Identify grid stability challenges\n- Read and interpret single-line diagrams",
                'intermediate' => "- Perform load flow and contingency analysis\n- Configure SCADA alarms and event management\n- Model distributed energy resources\n- Implement load forecasting techniques\n- Design basic microgrid architectures",
                'advanced' => "- Implement advanced grid optimization algorithms\n- Design grid-scale energy storage integration\n- Perform transient stability analysis\n- Implement demand response programs\n- Optimize renewable integration strategies",
                'expert' => "- Lead grid modernization programs\n- Implement AI-driven grid management systems\n- Design virtual power plant platforms\n- Develop grid resilience and cybersecurity frameworks\n- Direct energy transition strategy for utilities",
            ],
        ];

        return $allObjectives[$catKey][$level] ?? "- Complete all course modules\n- Pass the final assessment\n- Demonstrate competency in key areas";
    }

    private function getSeedLessons($catKey, $level) {
        $types = ['video', 'document', 'quiz', 'lab', 'interactive'];

        $allLessons = [
            'process_sim_energy' => [
                'beginner' => [
                    ['title' => 'Introduction to Process Simulation Concepts', 'type' => 'video', 'duration' => 30],
                    ['title' => 'HYSYS Interface Navigation and Workspace Setup', 'type' => 'interactive', 'duration' => 45],
                    ['title' => 'Equation of State Selection for Hydrocarbon Systems', 'type' => 'document', 'duration' => 25],
                    ['title' => 'Building Your First Flowsheet: Simple Separator', 'type' => 'lab', 'duration' => 60],
                    ['title' => 'Heat and Material Balance Fundamentals', 'type' => 'video', 'duration' => 35],
                    ['title' => 'Stream Properties and Phase Behavior', 'type' => 'document', 'duration' => 20],
                    ['title' => 'Knowledge Check: Simulation Basics', 'type' => 'quiz', 'duration' => 15],
                ],
                'intermediate' => [
                    ['title' => 'Distillation Column Modeling in HYSYS', 'type' => 'video', 'duration' => 45],
                    ['title' => 'Compressor and Expander Modeling', 'type' => 'lab', 'duration' => 50],
                    ['title' => 'Recycle Loops and Convergence Strategies', 'type' => 'document', 'duration' => 30],
                    ['title' => 'Gas Dehydration TEG Process Simulation', 'type' => 'lab', 'duration' => 60],
                    ['title' => 'Amine Sweetening Unit Modeling', 'type' => 'lab', 'duration' => 55],
                    ['title' => 'Sensitivity Analysis and Case Studies', 'type' => 'interactive', 'duration' => 40],
                    ['title' => 'Mid-Course Assessment', 'type' => 'quiz', 'duration' => 20],
                    ['title' => 'NGL Recovery Process Design', 'type' => 'lab', 'duration' => 60],
                ],
                'advanced' => [
                    ['title' => 'Dynamic Simulation Fundamentals in HYSYS', 'type' => 'video', 'duration' => 50],
                    ['title' => 'Startup and Shutdown Procedure Modeling', 'type' => 'lab', 'duration' => 60],
                    ['title' => 'Custom Unit Operations and Extensions', 'type' => 'document', 'duration' => 40],
                    ['title' => 'LNG Liquefaction Process Optimization', 'type' => 'lab', 'duration' => 60],
                    ['title' => 'Flare System Analysis and Relief Sizing', 'type' => 'lab', 'duration' => 55],
                    ['title' => 'Real-Time Data Integration', 'type' => 'interactive', 'duration' => 45],
                ],
                'expert' => [
                    ['title' => 'Digital Twin Architecture and Implementation', 'type' => 'video', 'duration' => 55],
                    ['title' => 'Simulation-Based FEED Methodology', 'type' => 'document', 'duration' => 45],
                    ['title' => 'Advanced Control Strategy Design', 'type' => 'lab', 'duration' => 60],
                    ['title' => 'AI-Assisted Process Optimization Workshop', 'type' => 'lab', 'duration' => 60],
                    ['title' => 'Enterprise Simulation Standards Development', 'type' => 'document', 'duration' => 40],
                    ['title' => 'Capstone: End-to-End Facility Model', 'type' => 'lab', 'duration' => 60],
                    ['title' => 'Expert Certification Assessment', 'type' => 'quiz', 'duration' => 30],
                ],
            ],
            'process_sim_chemicals' => [
                'beginner' => [
                    ['title' => 'Aspen Plus Interface and Workflow Overview', 'type' => 'video', 'duration' => 30],
                    ['title' => 'Property Method Selection for Chemical Systems', 'type' => 'document', 'duration' => 25],
                    ['title' => 'VLE and LLE Calculations in Aspen Plus', 'type' => 'lab', 'duration' => 45],
                    ['title' => 'Basic Reactor Models: RStoic, REquil, RGibbs', 'type' => 'lab', 'duration' => 50],
                    ['title' => 'Simple Separation Process Flowsheet', 'type' => 'lab', 'duration' => 55],
                    ['title' => 'Component Database and Property Analysis', 'type' => 'interactive', 'duration' => 30],
                    ['title' => 'Fundamentals Knowledge Check', 'type' => 'quiz', 'duration' => 15],
                ],
                'intermediate' => [
                    ['title' => 'RadFrac Column Design and Optimization', 'type' => 'lab', 'duration' => 55],
                    ['title' => 'Electrolyte System Modeling (ELECNRTL)', 'type' => 'video', 'duration' => 40],
                    ['title' => 'Batch Process Simulation with Aspen Batch', 'type' => 'lab', 'duration' => 50],
                    ['title' => 'Solids Handling: Crystallization and Drying', 'type' => 'lab', 'duration' => 45],
                    ['title' => 'Design Specifications and Calculator Blocks', 'type' => 'interactive', 'duration' => 35],
                    ['title' => 'Optimization and Sensitivity Tools', 'type' => 'document', 'duration' => 30],
                ],
                'advanced' => [
                    ['title' => 'Custom Property Package Development', 'type' => 'lab', 'duration' => 60],
                    ['title' => 'Polymerization Reactor Modeling', 'type' => 'lab', 'duration' => 55],
                    ['title' => 'Complex Reaction Kinetics (LHHW, Power Law)', 'type' => 'document', 'duration' => 40],
                    ['title' => 'Pressure Safety Analysis Workflows', 'type' => 'video', 'duration' => 35],
                    ['title' => 'Process Economics and Capital Cost Estimation', 'type' => 'interactive', 'duration' => 45],
                    ['title' => 'Advanced Assessment', 'type' => 'quiz', 'duration' => 25],
                ],
                'expert' => [
                    ['title' => 'External Tool Integration (MATLAB, Python)', 'type' => 'lab', 'duration' => 60],
                    ['title' => 'Technology Evaluation Using Simulation', 'type' => 'document', 'duration' => 45],
                    ['title' => 'Enterprise Simulation Template Development', 'type' => 'lab', 'duration' => 55],
                    ['title' => 'Model-Based Process Development', 'type' => 'video', 'duration' => 40],
                    ['title' => 'Green Chemistry and Sustainability Analysis', 'type' => 'interactive', 'duration' => 50],
                ],
            ],
            'exchanger_design' => [
                'beginner' => [
                    ['title' => 'Heat Transfer Fundamentals Review', 'type' => 'video', 'duration' => 30],
                    ['title' => 'LMTD and Epsilon-NTU Methods', 'type' => 'document', 'duration' => 25],
                    ['title' => 'TEMA Exchanger Types and Nomenclature', 'type' => 'video', 'duration' => 35],
                    ['title' => 'Reading Exchanger Data Sheets', 'type' => 'interactive', 'duration' => 30],
                    ['title' => 'Basic Thermal and Hydraulic Calculations', 'type' => 'lab', 'duration' => 50],
                    ['title' => 'Fouling Factors and Their Impact', 'type' => 'document', 'duration' => 20],
                    ['title' => 'Basics Knowledge Assessment', 'type' => 'quiz', 'duration' => 15],
                ],
                'intermediate' => [
                    ['title' => 'Shell and Tube Design Using EDR/HTRI', 'type' => 'lab', 'duration' => 60],
                    ['title' => 'Baffle Configuration and Tube Layout Optimization', 'type' => 'lab', 'duration' => 50],
                    ['title' => 'Rating Existing Exchangers', 'type' => 'interactive', 'duration' => 40],
                    ['title' => 'Air-Cooled Exchanger Design', 'type' => 'lab', 'duration' => 55],
                    ['title' => 'Phase Change Design: Condensation', 'type' => 'video', 'duration' => 35],
                    ['title' => 'Phase Change Design: Boiling', 'type' => 'video', 'duration' => 35],
                    ['title' => 'Design Practice Assessment', 'type' => 'quiz', 'duration' => 20],
                ],
                'advanced' => [
                    ['title' => 'Vibration Analysis and Tube Failure Prevention', 'type' => 'document', 'duration' => 40],
                    ['title' => 'High-Pressure and Corrosive Service Design', 'type' => 'lab', 'duration' => 55],
                    ['title' => 'Heat Exchanger Network Optimization', 'type' => 'lab', 'duration' => 60],
                    ['title' => 'Plate, Spiral, and Compact Exchangers', 'type' => 'video', 'duration' => 35],
                    ['title' => 'Mechanical Design per ASME Standards', 'type' => 'document', 'duration' => 45],
                    ['title' => 'Advanced Design Challenge', 'type' => 'quiz', 'duration' => 25],
                ],
                'expert' => [
                    ['title' => 'Heat Exchanger Network Retrofit Projects', 'type' => 'lab', 'duration' => 60],
                    ['title' => 'Custom Correlations for Proprietary Geometries', 'type' => 'document', 'duration' => 50],
                    ['title' => 'Digital Twin Monitoring for Exchangers', 'type' => 'interactive', 'duration' => 45],
                    ['title' => 'Fired Heater and WHR System Design', 'type' => 'lab', 'duration' => 60],
                    ['title' => 'Expert Capstone Project', 'type' => 'lab', 'duration' => 60],
                ],
            ],
        ];

        // For categories not fully defined above, generate generic but relevant lessons
        if (isset($allLessons[$catKey][$level])) {
            return $allLessons[$catKey][$level];
        }

        $cats = $this->getSeedCategories();
        $catName = $cats[$catKey]['name'] ?? $catKey;
        $modules = $cats[$catKey]['modules'] ?? ['Module 1', 'Module 2', 'Module 3'];

        $lessonTemplates = [
            'beginner' => [
                ['title' => "Introduction to {$catName}", 'type' => 'video', 'duration' => 30],
                ['title' => "Fundamental Concepts and Terminology", 'type' => 'document', 'duration' => 25],
                ['title' => "{$modules[0]}: Getting Started", 'type' => 'interactive', 'duration' => 40],
                ['title' => "{$modules[1]}: Core Principles", 'type' => 'video', 'duration' => 35],
                ['title' => "Hands-On: Basic {$modules[2]} Workflow", 'type' => 'lab', 'duration' => 50],
                ['title' => "Industry Standards and Best Practices", 'type' => 'document', 'duration' => 20],
                ['title' => "Beginner Knowledge Check", 'type' => 'quiz', 'duration' => 15],
            ],
            'intermediate' => [
                ['title' => "{$modules[0]}: Intermediate Applications", 'type' => 'lab', 'duration' => 50],
                ['title' => "{$modules[1]}: Practical Workflows", 'type' => 'lab', 'duration' => 55],
                ['title' => "{$modules[2]}: Configuration and Setup", 'type' => 'interactive', 'duration' => 40],
                ['title' => "{$modules[3]}: Analysis Techniques", 'type' => 'video', 'duration' => 35],
                ['title' => "Case Study: Real-World Implementation", 'type' => 'document', 'duration' => 30],
                ['title' => "{$modules[4]}: Hands-On Practice", 'type' => 'lab', 'duration' => 45],
                ['title' => "Intermediate Assessment", 'type' => 'quiz', 'duration' => 20],
                ['title' => "Integration Workshop", 'type' => 'lab', 'duration' => 55],
            ],
            'advanced' => [
                ['title' => "Advanced {$modules[0]} Methodologies", 'type' => 'video', 'duration' => 45],
                ['title' => "{$modules[1]}: Optimization Techniques", 'type' => 'lab', 'duration' => 60],
                ['title' => "Troubleshooting and Diagnostics", 'type' => 'interactive', 'duration' => 40],
                ['title' => "{$modules[2]}: Advanced Configuration", 'type' => 'lab', 'duration' => 55],
                ['title' => "Industry Case Study: Complex Scenarios", 'type' => 'document', 'duration' => 35],
                ['title' => "{$modules[3]}: Performance Optimization", 'type' => 'lab', 'duration' => 50],
            ],
            'expert' => [
                ['title' => "Enterprise Strategy for {$catName}", 'type' => 'document', 'duration' => 45],
                ['title' => "Leading {$modules[0]} Implementation Programs", 'type' => 'video', 'duration' => 50],
                ['title' => "Custom Solutions and Extensions Development", 'type' => 'lab', 'duration' => 60],
                ['title' => "{$modules[1]}: Cutting-Edge Applications", 'type' => 'lab', 'duration' => 55],
                ['title' => "AI and Digital Transformation in {$catName}", 'type' => 'interactive', 'duration' => 45],
                ['title' => "Capstone Project: End-to-End Solution", 'type' => 'lab', 'duration' => 60],
                ['title' => "Expert Certification Assessment", 'type' => 'quiz', 'duration' => 30],
            ],
        ];

        return $lessonTemplates[$level] ?? $lessonTemplates['beginner'];
    }

    private function getSeedQuestions($catKey, $level) {
        $allQuestions = [
            'process_sim_energy' => [
                'beginner' => [
                    ['question' => 'What equation of state is most commonly used for hydrocarbon systems in HYSYS?', 'a' => 'NRTL', 'b' => 'Peng-Robinson', 'c' => 'Wilson', 'd' => 'UNIQUAC', 'answer' => 'B', 'explanation' => 'Peng-Robinson (PR) is the most widely used EOS for hydrocarbon systems as it accurately predicts vapor-liquid equilibria for non-polar and slightly polar components in oil and gas applications.', 'difficulty' => 'easy'],
                    ['question' => 'In HYSYS, what does a "fully defined" stream require at minimum?', 'a' => 'Temperature and pressure only', 'b' => 'Temperature, pressure, flow rate, and composition', 'c' => 'Composition and flow rate only', 'd' => 'Any two thermodynamic properties', 'answer' => 'B', 'explanation' => 'A fully defined stream in HYSYS requires temperature, pressure, flow rate, and composition to solve all thermodynamic properties.', 'difficulty' => 'easy'],
                    ['question' => 'What is the primary advantage of the Soave-Redlich-Kwong (SRK) EOS over the original Redlich-Kwong?', 'a' => 'Better liquid density predictions', 'b' => 'Improved vapor pressure predictions using acentric factor', 'c' => 'Faster computation', 'd' => 'Handles electrolytes', 'answer' => 'B', 'explanation' => 'SRK introduced the acentric factor into the attractive term, greatly improving vapor pressure predictions for pure components and mixtures.', 'difficulty' => 'medium'],
                    ['question' => 'Which unit operation in HYSYS would you use to model a simple flash drum?', 'a' => 'Distillation Column', 'b' => 'Separator', 'c' => 'Component Splitter', 'd' => 'Mixer', 'answer' => 'B', 'explanation' => 'The Separator unit operation models a flash drum (two-phase or three-phase) performing thermodynamic equilibrium separation at given conditions.', 'difficulty' => 'easy'],
                    ['question' => 'In steady-state simulation, what does "converged" mean?', 'a' => 'All variables have been set by the user', 'b' => 'The iterative calculations have reached a solution within tolerance', 'c' => 'The simulation is running', 'd' => 'The flowsheet has been saved', 'answer' => 'B', 'explanation' => 'Convergence means the iterative solver has found a solution where all equations are satisfied within the specified numerical tolerance.', 'difficulty' => 'easy'],
                    ['question' => 'What is the Gibbs Phase Rule formula?', 'a' => 'F = C - P + 2', 'b' => 'F = C + P - 2', 'c' => 'F = C * P + 2', 'd' => 'F = 2C - P', 'answer' => 'A', 'explanation' => 'The Gibbs Phase Rule is F = C - P + 2, where F is degrees of freedom, C is number of components, and P is number of phases.', 'difficulty' => 'medium'],
                    ['question' => 'Which property package would you select for a steam system simulation in HYSYS?', 'a' => 'Peng-Robinson', 'b' => 'ASME Steam Tables', 'c' => 'NRTL', 'd' => 'CPA', 'answer' => 'B', 'explanation' => 'ASME Steam Tables (or IAPWS-IF97) provide the most accurate properties for pure water/steam systems.', 'difficulty' => 'medium'],
                    ['question' => 'What happens when you specify too many variables for a unit operation in HYSYS?', 'a' => 'HYSYS ignores extra specifications', 'b' => 'The simulation runs faster', 'c' => 'The unit operation becomes over-specified and will not converge', 'd' => 'All values are averaged', 'answer' => 'C', 'explanation' => 'Over-specification provides more equations than unknowns, leading to conflicting constraints and convergence failure.', 'difficulty' => 'medium'],
                    ['question' => 'In HYSYS, what does the Adjust (Controller) logical operation do?', 'a' => 'Controls plant hardware', 'b' => 'Iteratively adjusts a variable to meet a specified target', 'c' => 'Adjusts the display settings', 'd' => 'Automatically saves the file', 'answer' => 'B', 'explanation' => 'The Adjust operation iteratively manipulates one variable to drive another variable to a target value, similar to a design specification.', 'difficulty' => 'medium'],
                    ['question' => 'The critical temperature of methane is approximately:', 'a' => '-82.6 degrees C', 'b' => '-161.5 degrees C', 'c' => '32.2 degrees C', 'd' => '96.7 degrees C', 'answer' => 'A', 'explanation' => 'Methane has a critical temperature of -82.6 C (190.6 K). -161.5 C is its normal boiling point, a common confusion.', 'difficulty' => 'hard'],
                ],
                'intermediate' => [
                    ['question' => 'In a TEG dehydration unit, what is the typical lean TEG concentration?', 'a' => '95.0 wt%', 'b' => '98.5 wt%', 'c' => '99.5 wt%', 'd' => '99.95 wt%', 'answer' => 'C', 'explanation' => 'Standard TEG dehydration uses ~99.5 wt% lean TEG. Enhanced stripping (Stahl column or Drizo) can achieve 99.95%+.', 'difficulty' => 'medium'],
                    ['question' => 'What is the primary purpose of the Murphree efficiency in distillation modeling?', 'a' => 'To calculate column diameter', 'b' => 'To account for non-ideal stage behavior (departure from equilibrium)', 'c' => 'To determine reboiler duty', 'd' => 'To set the feed tray location', 'answer' => 'B', 'explanation' => 'Murphree efficiency accounts for the fact that real trays do not achieve perfect thermodynamic equilibrium between vapor and liquid phases.', 'difficulty' => 'medium'],
                    ['question' => 'In HYSYS recycle convergence, the Wegstein method accelerates convergence by:', 'a' => 'Using random perturbations', 'b' => 'Estimating the derivative from successive iterations to predict the solution', 'c' => 'Ignoring recycle streams', 'd' => 'Running multiple simulations in parallel', 'answer' => 'B', 'explanation' => 'Wegstein method uses the ratio of successive iteration changes to estimate convergence direction, accelerating the direct substitution method.', 'difficulty' => 'hard'],
                    ['question' => 'What is the typical approach temperature for a shell and tube LNG exchanger?', 'a' => '1-3 degrees C', 'b' => '10-15 degrees C', 'c' => '25-30 degrees C', 'd' => '50+ degrees C', 'answer' => 'A', 'explanation' => 'LNG exchangers (especially coil-wound) operate with very close approach temperatures (1-3 C) to maximize thermodynamic efficiency.', 'difficulty' => 'hard'],
                    ['question' => 'In amine sweetening, MDEA is preferred over MEA for selective H2S removal because:', 'a' => 'MDEA is cheaper', 'b' => 'MDEA has a faster reaction rate with CO2', 'c' => 'MDEA reacts with H2S quickly but absorbs CO2 slowly (kinetic selectivity)', 'd' => 'MDEA requires less regeneration energy', 'answer' => 'C', 'explanation' => 'MDEA is a tertiary amine that reacts with H2S via fast proton transfer but absorbs CO2 only through slower base-catalyzed hydration, providing kinetic selectivity.', 'difficulty' => 'medium'],
                    ['question' => 'The minimum reflux ratio in distillation is determined by:', 'a' => 'The condenser cooling water temperature', 'b' => 'The Underwood equations using feed composition and relative volatility', 'c' => 'The reboiler type', 'd' => 'The column diameter', 'answer' => 'B', 'explanation' => 'The Underwood equations relate minimum reflux to feed quality, composition, and relative volatilities for a given separation.', 'difficulty' => 'medium'],
                    ['question' => 'What is the Joule-Thomson effect relevant to in gas processing?', 'a' => 'Reaction rates', 'b' => 'Temperature change during isenthalpic expansion (e.g., through a valve)', 'c' => 'Pump efficiency', 'd' => 'Corrosion rates', 'answer' => 'B', 'explanation' => 'The Joule-Thomson effect describes the temperature change when gas expands through a valve at constant enthalpy. It is critical in refrigeration and NGL recovery processes.', 'difficulty' => 'easy'],
                    ['question' => 'In a turboexpander-based NGL recovery plant, what is the typical ethane recovery?', 'a' => '50-60%', 'b' => '70-80%', 'c' => '90-95%+', 'd' => '100%', 'answer' => 'C', 'explanation' => 'Modern turboexpander plants (GSP, RSV, IPSI-1 processes) routinely achieve 90-95%+ ethane recovery.', 'difficulty' => 'medium'],
                    ['question' => 'What does the "Inside-Out" algorithm refer to in distillation simulation?', 'a' => 'Solving from inside the column outward', 'b' => 'A two-loop method separating thermodynamic and mass/energy balance calculations', 'c' => 'An iterative correction method for outside conditions', 'd' => 'A graphical design method', 'answer' => 'B', 'explanation' => 'The Inside-Out algorithm uses an inner loop with simplified thermodynamics and an outer loop that updates thermodynamic parameters, making column convergence more robust.', 'difficulty' => 'hard'],
                    ['question' => 'In HYSYS, what is the purpose of the "Spreadsheet" operation?', 'a' => 'To export data to Excel', 'b' => 'To perform custom calculations on simulation variables within the flowsheet', 'c' => 'To create reports', 'd' => 'To import plant data', 'answer' => 'B', 'explanation' => 'The Spreadsheet operation allows users to import simulation variables, perform custom calculations, and export results back to the flowsheet.', 'difficulty' => 'easy'],
                ],
                'advanced' => [
                    ['question' => 'In HYSYS Dynamics, what is the integrator type that provides the best stability for stiff systems?', 'a' => 'Explicit Euler', 'b' => 'Implicit Euler (backward differentiation)', 'c' => 'Runge-Kutta 4th order', 'd' => 'Adams-Bashforth', 'answer' => 'B', 'explanation' => 'Implicit methods like backward Euler/BDF are unconditionally stable for stiff systems common in chemical processes (fast reactions, small time constants).', 'difficulty' => 'hard'],
                    ['question' => 'What is the recommended approach for modeling a pressure relief valve in HYSYS Dynamics?', 'a' => 'Use a standard valve with fixed Cv', 'b' => 'Use a Relief Valve operation with API 520 sizing', 'c' => 'Use an Adjust operation', 'd' => 'Ignore pressure relief in simulation', 'answer' => 'B', 'explanation' => 'HYSYS Dynamics has a dedicated Relief Valve operation that follows API 520/521 guidelines for proper sizing and dynamic response.', 'difficulty' => 'medium'],
                    ['question' => 'For an LNG MCHE (Main Cryogenic Heat Exchanger), what is the preferred HYSYS model?', 'a' => 'Simple Heater', 'b' => 'LNG Exchanger (multi-stream)', 'c' => 'Shell and Tube Exchanger', 'd' => 'Air Cooler', 'answer' => 'B', 'explanation' => 'The LNG Exchanger model in HYSYS handles multi-stream coil-wound exchangers with multiple passes and proper temperature cross handling.', 'difficulty' => 'medium'],
                    ['question' => 'What is "equation-oriented" (EO) mode in HYSYS and when is it beneficial?', 'a' => 'A mode that solves all equations simultaneously rather than sequentially', 'b' => 'A mode for equation display', 'c' => 'A reporting format', 'd' => 'A way to export equations', 'answer' => 'A', 'explanation' => 'EO mode solves all flowsheet equations simultaneously using sparse matrix techniques, beneficial for highly interconnected processes and optimization problems.', 'difficulty' => 'hard'],
                    ['question' => 'In flare header modeling, what is the critical parameter for two-phase flow calculations?', 'a' => 'Color of the pipe', 'b' => 'Back-pressure at each source and Mach number at the flare tip', 'c' => 'Ambient temperature only', 'd' => 'Pipe material grade', 'answer' => 'B', 'explanation' => 'Flare header design must check that back-pressure at each relief source stays within API 521 limits and that the Mach number does not exceed design limits.', 'difficulty' => 'hard'],
                    ['question' => 'What is the maximum recommended Mach number at the flare tip for a conventional elevated flare?', 'a' => '0.2', 'b' => '0.5', 'c' => '0.7', 'd' => '1.0', 'answer' => 'B', 'explanation' => 'API 521 recommends a maximum Mach number of 0.5 at the flare tip for stable combustion, though 0.2 is preferred for smokeless operation.', 'difficulty' => 'hard'],
                    ['question' => 'In dynamic simulation, what is a "pressure-flow" solver?', 'a' => 'A solver that calculates only pressure drops', 'b' => 'A solver that simultaneously solves pressure and flow relationships across the flowsheet using holdup equations', 'c' => 'A tool for pump curves', 'd' => 'A method for steady-state only', 'answer' => 'B', 'explanation' => 'The pressure-flow solver uses vessel holdups and resistance equations to dynamically determine flows and pressures throughout the network.', 'difficulty' => 'medium'],
                    ['question' => 'What is the CPA (Cubic Plus Association) EOS used for?', 'a' => 'Only for hydrocarbons', 'b' => 'Systems containing associating compounds like water, glycols, and methanol with hydrocarbons', 'c' => 'Ideal gas calculations', 'd' => 'Solid modeling only', 'answer' => 'B', 'explanation' => 'CPA extends the SRK EOS with an association term to handle hydrogen-bonding fluids (water, methanol, glycols) mixed with hydrocarbons.', 'difficulty' => 'medium'],
                    ['question' => 'What is "blowdown analysis" in the context of dynamic simulation?', 'a' => 'Analyzing marketing strategies', 'b' => 'Simulating emergency depressurization to determine minimum metal temperature and stress', 'c' => 'Analyzing blowdown drums only', 'd' => 'A static calculation', 'answer' => 'B', 'explanation' => 'Blowdown analysis simulates emergency depressurization to verify that vessel wall temperatures remain above minimum design temperature (to prevent brittle fracture).', 'difficulty' => 'medium'],
                    ['question' => 'In HYSYS, what is the difference between "Sizing" mode and "Rating" mode for a heat exchanger?', 'a' => 'There is no difference', 'b' => 'Sizing determines geometry for a duty; Rating evaluates performance of existing geometry', 'c' => 'Rating is always faster', 'd' => 'Sizing only works for air coolers', 'answer' => 'B', 'explanation' => 'Sizing mode determines the required exchanger dimensions for a given duty. Rating mode evaluates whether an existing exchanger can meet the required duty under new conditions.', 'difficulty' => 'easy'],
                ],
                'expert' => [
                    ['question' => 'In a digital twin implementation, what is the recommended update frequency for a refinery-scale model?', 'a' => 'Once per day', 'b' => 'Every 1-5 minutes for real-time optimization', 'c' => 'Once per month', 'd' => 'Only during turnarounds', 'answer' => 'B', 'explanation' => 'Real-time digital twins typically update every 1-5 minutes to capture process dynamics and enable online optimization and decision support.', 'difficulty' => 'medium'],
                    ['question' => 'What is "model predictive simulation" in the context of HYSYS-APC integration?', 'a' => 'Predicting stock markets', 'b' => 'Using the rigorous simulation model to generate step-response models for MPC controller design', 'c' => 'A marketing concept', 'd' => 'Manual calculations', 'answer' => 'B', 'explanation' => 'Model predictive simulation uses HYSYS dynamic models to generate high-fidelity step-response models for DMC/MPC controller design, avoiding costly plant tests.', 'difficulty' => 'hard'],
                    ['question' => 'For simulation-based FEED, what is the recommended approach for handling design margins?', 'a' => 'Apply 50% margin to everything', 'b' => 'Use probabilistic analysis (Monte Carlo) to establish appropriate margins for each equipment item', 'c' => 'Use no margins', 'd' => 'Apply margins only to pumps', 'answer' => 'B', 'explanation' => 'Probabilistic analysis provides risk-informed design margins, avoiding both under-design (risk) and over-design (unnecessary cost).', 'difficulty' => 'hard'],
                    ['question' => 'What is the "state estimation" problem in real-time process optimization?', 'a' => 'Estimating which US state the plant is in', 'b' => 'Reconciling plant measurements with model predictions to determine the true process state', 'c' => 'A simple averaging calculation', 'd' => 'Counting sensors', 'answer' => 'B', 'explanation' => 'State estimation (or data reconciliation) uses optimization to find the most likely process state that satisfies conservation laws while matching noisy measurements.', 'difficulty' => 'medium'],
                    ['question' => 'In enterprise simulation governance, what is the key benefit of a "single source of truth" simulation model?', 'a' => 'Reducing software license costs', 'b' => 'Ensuring consistent engineering data across all project disciplines and lifecycle phases', 'c' => 'Making simulations run faster', 'd' => 'Eliminating the need for engineers', 'answer' => 'B', 'explanation' => 'A single authoritative model prevents inconsistencies between disciplines and ensures design changes propagate correctly through all engineering workflows.', 'difficulty' => 'medium'],
                    ['question' => 'What is the concept of "auto-tuning" in the context of simulation model calibration?', 'a' => 'Tuning a radio', 'b' => 'Automatically adjusting model parameters to minimize the difference between model outputs and plant data', 'c' => 'Adjusting display settings', 'd' => 'Automatic file saving', 'answer' => 'B', 'explanation' => 'Auto-tuning uses optimization algorithms (e.g., SQP, genetic algorithms) to calibrate model parameters against plant data for maximum accuracy.', 'difficulty' => 'easy'],
                    ['question' => 'For AI-assisted process optimization, which machine learning approach is most suitable for process yield prediction?', 'a' => 'Image classification CNNs', 'b' => 'Gaussian Process Regression or gradient-boosted trees trained on historical process data', 'c' => 'Natural language processing', 'd' => 'Reinforcement learning only', 'answer' => 'B', 'explanation' => 'GPR and GBT models are well-suited for process yield prediction as they handle tabular process data well and provide uncertainty estimates (GPR).', 'difficulty' => 'hard'],
                    ['question' => 'What is the role of a "reduced-order model" (ROM) in real-time optimization?', 'a' => 'A model with fewer colors', 'b' => 'A simplified model that captures key dynamics with much faster execution than the rigorous model', 'c' => 'A model for small plants only', 'd' => 'A financial model', 'answer' => 'B', 'explanation' => 'ROMs use techniques like POD or system identification to create fast-executing models suitable for real-time optimization while preserving essential process behavior.', 'difficulty' => 'hard'],
                    ['question' => 'In a HYSYS-Excel integration via automation, what interface technology is used?', 'a' => 'REST API', 'b' => 'COM/OLE Automation', 'c' => 'GraphQL', 'd' => 'SOAP', 'answer' => 'B', 'explanation' => 'HYSYS uses COM (Component Object Model) / OLE Automation for programmatic access from Excel VBA, Python (win32com), and other languages.', 'difficulty' => 'medium'],
                    ['question' => 'What is "workflow orchestration" in enterprise simulation management?', 'a' => 'Playing music', 'b' => 'Automating the sequence of simulation runs, data transfers, and approvals across tools and teams', 'c' => 'Organizing file folders', 'd' => 'Scheduling meetings', 'answer' => 'B', 'explanation' => 'Workflow orchestration automates complex simulation workflows involving multiple tools, ensuring proper sequencing, data handoff, and quality gates.', 'difficulty' => 'medium'],
                ],
            ],
            'process_sim_chemicals' => [
                'beginner' => [
                    ['question' => 'Which property method is most appropriate for modeling an acetone-water mixture at atmospheric pressure?', 'a' => 'Peng-Robinson', 'b' => 'NRTL', 'c' => 'Ideal', 'd' => 'Lee-Kesler', 'answer' => 'B', 'explanation' => 'NRTL (Non-Random Two-Liquid) is ideal for polar and partially miscible liquid systems like acetone-water at low to moderate pressures.', 'difficulty' => 'easy'],
                    ['question' => 'In Aspen Plus, the RGibbs reactor minimizes:', 'a' => 'Reactor volume', 'b' => 'Reaction rate', 'c' => 'Total Gibbs free energy to determine equilibrium composition', 'd' => 'Capital cost', 'answer' => 'C', 'explanation' => 'RGibbs finds chemical and phase equilibrium by minimizing total Gibbs free energy, useful when reaction stoichiometry is unknown or complex.', 'difficulty' => 'medium'],
                    ['question' => 'What does UNIQUAC stand for?', 'a' => 'Universal Quasi-Chemical activity coefficient model', 'b' => 'Unified Quantitative Analysis for Chemicals', 'c' => 'Universal Quality Assurance for Chemistry', 'd' => 'Unique Quadratic Chemical model', 'answer' => 'A', 'explanation' => 'UNIQUAC (Universal Quasi-Chemical) is an activity coefficient model based on local composition theory, handling size and shape differences between molecules.', 'difficulty' => 'easy'],
                    ['question' => 'In Aspen Plus, what is the difference between RStoic and RYield reactors?', 'a' => 'No difference', 'b' => 'RStoic needs stoichiometry and conversion; RYield specifies product yields directly', 'c' => 'RYield is always more accurate', 'd' => 'RStoic cannot handle multiple reactions', 'answer' => 'B', 'explanation' => 'RStoic models reactions by stoichiometry with specified conversion. RYield specifies product distribution directly without reaction stoichiometry.', 'difficulty' => 'medium'],
                    ['question' => 'For a system with immiscible liquids (e.g., oil-water), which phase equilibrium check is important?', 'a' => 'Only VLE', 'b' => 'LLE (Liquid-Liquid Equilibrium)', 'c' => 'Only SLE', 'd' => 'No equilibrium check needed', 'answer' => 'B', 'explanation' => 'LLE analysis is critical for systems with immiscible liquids to correctly predict phase splitting and composition in each liquid phase.', 'difficulty' => 'easy'],
                    ['question' => 'What is the Antoine equation used for?', 'a' => 'Calculating reaction rates', 'b' => 'Correlating vapor pressure as a function of temperature', 'c' => 'Determining pipe size', 'd' => 'Calculating heat capacity', 'answer' => 'B', 'explanation' => 'The Antoine equation (log P = A - B/(C+T)) is a semi-empirical correlation for vapor pressure vs. temperature.', 'difficulty' => 'easy'],
                    ['question' => 'In Aspen Plus, what is a "Design Spec" used for?', 'a' => 'Specifying pipe design', 'b' => 'Manipulating a flowsheet variable to achieve a target specification', 'c' => 'Designing the interface', 'd' => 'Setting hardware specifications', 'answer' => 'B', 'explanation' => 'Design Spec iteratively adjusts a manipulated variable (e.g., reflux ratio) to meet a target specification (e.g., product purity).', 'difficulty' => 'medium'],
                    ['question' => 'The Wilson equation is NOT suitable for:', 'a' => 'Polar systems', 'b' => 'Systems exhibiting liquid-liquid immiscibility', 'c' => 'Alcohol-water systems', 'd' => 'Moderate pressure systems', 'answer' => 'B', 'explanation' => 'The Wilson equation cannot predict liquid-liquid phase splitting (LLE) because its mathematical form always yields a single liquid phase.', 'difficulty' => 'hard'],
                    ['question' => 'In process simulation, what is a "tear stream"?', 'a' => 'A damaged pipe', 'b' => 'A stream in a recycle loop where the solver breaks the loop for iterative convergence', 'c' => 'A stream with missing data', 'd' => 'An output stream', 'answer' => 'B', 'explanation' => 'A tear stream is where the sequential solver breaks a recycle loop, iterating until the assumed and calculated values converge.', 'difficulty' => 'medium'],
                    ['question' => 'What is the Rachford-Rice equation used for?', 'a' => 'Cooking recipes', 'b' => 'Flash calculations - determining vapor fraction given T, P, and composition', 'c' => 'Column sizing', 'd' => 'Cost estimation', 'answer' => 'B', 'explanation' => 'The Rachford-Rice equation solves for the vapor-to-feed ratio in a flash calculation, given temperature, pressure, and feed composition with K-values.', 'difficulty' => 'hard'],
                ],
                'intermediate' => [
                    ['question' => 'In RadFrac, what does the "Vary/Adjust" feature allow you to do?', 'a' => 'Change column color', 'b' => 'Adjust a column parameter (e.g., reflux ratio) to meet a specification (e.g., distillate purity)', 'c' => 'Vary the number of components', 'd' => 'Adjust simulation speed', 'answer' => 'B', 'explanation' => 'RadFrac Design Spec/Vary allows specification of a target (e.g., 99.5% purity) by varying a column parameter (e.g., reflux ratio).', 'difficulty' => 'easy'],
                    ['question' => 'For electrolyte systems in Aspen Plus, what is the recommended property method?', 'a' => 'NRTL', 'b' => 'Peng-Robinson', 'c' => 'ELECNRTL', 'd' => 'UNIQUAC', 'answer' => 'C', 'explanation' => 'ELECNRTL extends NRTL to handle electrolyte species (ions, salts) with Born correction and is the standard for acid gas, amine, and caustic systems.', 'difficulty' => 'medium'],
                    ['question' => 'In batch distillation, what changes over time compared to continuous distillation?', 'a' => 'Nothing changes', 'b' => 'The feed composition in the still changes as lighter components are removed', 'c' => 'The column diameter changes', 'd' => 'The number of trays changes', 'answer' => 'B', 'explanation' => 'In batch distillation, the pot composition changes over time as lighter components are removed overhead, requiring more reflux to maintain product purity.', 'difficulty' => 'medium'],
                    ['question' => 'What is the purpose of a "Sensitivity Analysis" block in Aspen Plus?', 'a' => 'To test emotional sensitivity', 'b' => 'To systematically vary input parameters and observe their effect on outputs', 'c' => 'To delete sensitive data', 'd' => 'To check security', 'answer' => 'B', 'explanation' => 'Sensitivity Analysis sweeps one or more input variables over a range and tabulates/plots the resulting changes in key output variables.', 'difficulty' => 'easy'],
                    ['question' => 'In solids handling, what is a "PSD" (Particle Size Distribution)?', 'a' => 'A database type', 'b' => 'The statistical distribution of particle diameters in a solid stream', 'c' => 'A control system', 'd' => 'A type of pump', 'answer' => 'B', 'explanation' => 'PSD describes the proportion of particles at each size, critical for modeling crystallization, filtration, drying, and particle transport.', 'difficulty' => 'easy'],
                    ['question' => 'What does the UNIFAC method estimate?', 'a' => 'Equipment costs', 'b' => 'Activity coefficients from functional group contributions when experimental data is unavailable', 'c' => 'Reaction rates', 'd' => 'Pipe friction factors', 'answer' => 'B', 'explanation' => 'UNIFAC (UNIQUAC Functional-group Activity Coefficients) predicts activity coefficients using group contribution, useful when no binary interaction data exists.', 'difficulty' => 'medium'],
                    ['question' => 'In Aspen Plus, what is the difference between RCSTR and RPlug reactors?', 'a' => 'No difference', 'b' => 'RCSTR assumes perfect mixing; RPlug assumes plug flow with no axial mixing', 'c' => 'RCSTR is always faster', 'd' => 'RPlug does not need kinetics', 'answer' => 'B', 'explanation' => 'RCSTR models a continuous stirred tank reactor (perfect mixing). RPlug models a tubular plug flow reactor (no back-mixing).', 'difficulty' => 'medium'],
                    ['question' => 'When modeling a crystallization process, what is "metastable zone width"?', 'a' => 'The width of the crystallizer', 'b' => 'The supersaturation range between saturation and spontaneous nucleation', 'c' => 'A safety margin', 'd' => 'The crystal size', 'answer' => 'B', 'explanation' => 'The metastable zone width is the supersaturation region where crystal growth occurs but spontaneous nucleation does not. Operating within it controls crystal size.', 'difficulty' => 'hard'],
                    ['question' => 'What is "regression" of VLE data in Aspen Plus?', 'a' => 'Statistical analysis of sales data', 'b' => 'Fitting binary interaction parameters to match experimental VLE data', 'c' => 'A simulation error', 'd' => 'Removing data from the database', 'answer' => 'B', 'explanation' => 'Data regression fits model parameters (e.g., NRTL binary interaction parameters) to experimental VLE/LLE data using optimization to minimize residuals.', 'difficulty' => 'medium'],
                    ['question' => 'In Aspen Plus, what is the "Calculator" block used for?', 'a' => 'Basic arithmetic only', 'b' => 'Executing Fortran or Excel code to perform custom calculations within the flowsheet', 'c' => 'Calculating license fees', 'd' => 'Drawing graphs', 'answer' => 'B', 'explanation' => 'Calculator blocks execute user-written Fortran or Excel code, enabling custom calculations that reference and modify flowsheet variables.', 'difficulty' => 'medium'],
                ],
                'advanced' => [
                    ['question' => 'In polymer modeling, what is the "method of moments" used for?', 'a' => 'Statistical sampling', 'b' => 'Tracking molecular weight distribution through moments (Mn, Mw, Mz) without resolving the full distribution', 'c' => 'A photographic technique', 'd' => 'Calculating torque', 'answer' => 'B', 'explanation' => 'Method of moments tracks the zeroth, first, and second moments of the molecular weight distribution, enabling calculation of Mn, Mw, and PDI.', 'difficulty' => 'hard'],
                    ['question' => 'The LHHW (Langmuir-Hinshelwood-Hougen-Watson) kinetics model is used for:', 'a' => 'Homogeneous reactions only', 'b' => 'Heterogeneous catalytic reactions with adsorption, surface reaction, and desorption steps', 'c' => 'Nuclear reactions', 'd' => 'Financial modeling', 'answer' => 'B', 'explanation' => 'LHHW describes heterogeneous catalytic kinetics including competitive adsorption, surface reaction, and desorption on catalyst sites.', 'difficulty' => 'medium'],
                    ['question' => 'What is the PC-SAFT equation of state particularly good for?', 'a' => 'Ideal gases only', 'b' => 'Polymer solutions, associating fluids, and complex molecular interactions', 'c' => 'Steam tables', 'd' => 'Vacuum systems only', 'answer' => 'B', 'explanation' => 'PC-SAFT (Perturbed Chain Statistical Associating Fluid Theory) excels for polymer solutions, long-chain molecules, and associating fluids.', 'difficulty' => 'medium'],
                    ['question' => 'In process economics, what is the Lang factor used for?', 'a' => 'Language translation', 'b' => 'Estimating total installed cost as a multiple of major equipment purchase cost', 'c' => 'Calculating reaction rates', 'd' => 'Sizing columns', 'answer' => 'B', 'explanation' => 'The Lang factor (typically 3-6x) multiplies total delivered equipment cost to estimate total installed plant cost, including piping, civil, electrical, etc.', 'difficulty' => 'easy'],
                    ['question' => 'What is "pinch point" in reactive distillation modeling?', 'a' => 'A physical restriction', 'b' => 'A composition where the driving force for separation approaches zero due to reaction equilibrium effects', 'c' => 'The hottest point', 'd' => 'A pressure drop', 'answer' => 'B', 'explanation' => 'In reactive distillation, pinch points occur where reaction and separation equilibria interact, creating compositions where further separation is thermodynamically limited.', 'difficulty' => 'hard'],
                    ['question' => 'For modeling supercritical CO2 extraction, which property method is recommended?', 'a' => 'NRTL', 'b' => 'Peng-Robinson with modified mixing rules (Wong-Sandler or MHV2)', 'c' => 'Ideal', 'd' => 'Wilson', 'answer' => 'B', 'explanation' => 'PR with advanced mixing rules (Wong-Sandler or MHV2) handles the highly non-ideal behavior near the critical point of CO2.', 'difficulty' => 'hard'],
                    ['question' => 'What is "user model" (User2, User3) in Aspen Plus?', 'a' => 'A user login model', 'b' => 'A custom unit operation written in Fortran or C that plugs into the Aspen Plus simulation', 'c' => 'A template file', 'd' => 'A graphical design tool', 'answer' => 'B', 'explanation' => 'User models allow engineers to code proprietary or custom unit operations in Fortran/C and integrate them into the Aspen Plus simulation framework.', 'difficulty' => 'medium'],
                    ['question' => 'In azeotropic distillation, what is an "entrainer"?', 'a' => 'A person who trains engineers', 'b' => 'A third component added to form a new azeotrope that aids in separating the original mixture', 'c' => 'A type of column packing', 'd' => 'A condenser type', 'answer' => 'B', 'explanation' => 'An entrainer forms a new azeotrope (often heterogeneous) that enables separation of the original binary azeotrope by creating a phase split.', 'difficulty' => 'medium'],
                    ['question' => 'What is the Chilton-Colburn analogy?', 'a' => 'A historical comparison', 'b' => 'A relationship between heat transfer, mass transfer, and friction factor coefficients', 'c' => 'A cost estimation method', 'd' => 'A separation sequence', 'answer' => 'B', 'explanation' => 'The Chilton-Colburn analogy relates jH (heat), jD (mass), and f/2 (friction), enabling estimation of one transport coefficient from another.', 'difficulty' => 'hard'],
                    ['question' => 'In Aspen Plus, what is the "Optimization" block used for?', 'a' => 'Making the software run faster', 'b' => 'Finding optimal values of manipulated variables to minimize/maximize an objective function subject to constraints', 'c' => 'Optimizing disk space', 'd' => 'Reducing memory usage', 'answer' => 'B', 'explanation' => 'The Optimization block uses SQP or other algorithms to find optimal operating conditions (e.g., minimize energy, maximize yield) subject to process constraints.', 'difficulty' => 'easy'],
                ],
                'expert' => [
                    ['question' => 'When integrating Aspen Plus with Python, which interface is recommended for modern deployments?', 'a' => 'REST API', 'b' => 'Aspen Plus COM Automation via pywin32 or the APWN type library', 'c' => 'Direct file editing', 'd' => 'Email integration', 'answer' => 'B', 'explanation' => 'Aspen Plus COM Automation allows Python to control simulations, modify inputs, run cases, and extract results programmatically.', 'difficulty' => 'medium'],
                    ['question' => 'What is "surrogate-assisted optimization" in process design?', 'a' => 'Having a substitute engineer', 'b' => 'Using fast ML surrogate models (Kriging, neural nets) to guide optimization of expensive rigorous simulations', 'c' => 'A project management method', 'd' => 'Using default settings', 'answer' => 'B', 'explanation' => 'Surrogate-assisted optimization trains fast ML models from limited rigorous simulation runs, dramatically reducing the computational cost of optimization.', 'difficulty' => 'hard'],
                    ['question' => 'In enterprise simulation templates, what is the benefit of "model libraries"?', 'a' => 'Organizing books', 'b' => 'Reusable, validated sub-models that ensure consistency and reduce development time', 'c' => 'Storing old models', 'd' => 'Backup purposes only', 'answer' => 'B', 'explanation' => 'Model libraries provide validated, reusable building blocks (e.g., standard reactor configurations, utility models) ensuring consistency across projects.', 'difficulty' => 'easy'],
                    ['question' => 'What is "techno-economic analysis" (TEA) in process development?', 'a' => 'Studying tea production', 'b' => 'Combining technical process models with economic models to evaluate technology viability and compare alternatives', 'c' => 'A marketing study', 'd' => 'A safety analysis', 'answer' => 'B', 'explanation' => 'TEA integrates process simulation with capital and operating cost models, discounted cash flow, and sensitivity analysis to assess technology viability.', 'difficulty' => 'easy'],
                    ['question' => 'For green chemistry evaluation, what metric does the E-factor measure?', 'a' => 'Energy consumption', 'b' => 'Mass of waste per mass of product', 'c' => 'Enzyme concentration', 'd' => 'Emission temperature', 'answer' => 'B', 'explanation' => 'The E-factor (Environmental factor) is kg waste / kg product, measuring process efficiency. Lower E-factor means less waste and greener process.', 'difficulty' => 'medium'],
                    ['question' => 'In life cycle assessment (LCA) integrated with process simulation, what is the "functional unit"?', 'a' => 'A plant unit operation', 'b' => 'The quantified performance of a product system for use as a reference unit (e.g., 1 kg of product)', 'c' => 'A measurement device', 'd' => 'The control room', 'answer' => 'B', 'explanation' => 'The functional unit provides the reference to which all LCA inputs and outputs are normalized, enabling fair comparison between alternatives.', 'difficulty' => 'medium'],
                    ['question' => 'What is "model-based design of experiments" (MBDoE)?', 'a' => 'Designing laboratory furniture', 'b' => 'Using process models to optimally plan experiments that maximize information gain for parameter estimation', 'c' => 'A documentation standard', 'd' => 'Random experimentation', 'answer' => 'B', 'explanation' => 'MBDoE uses the Fisher Information Matrix to design experiments that provide maximum information for model parameter estimation, reducing experimental effort.', 'difficulty' => 'hard'],
                    ['question' => 'In continuous pharmaceutical manufacturing simulation, what is PAT?', 'a' => 'A person\'s name', 'b' => 'Process Analytical Technology - real-time monitoring and control of critical quality attributes', 'c' => 'A payment system', 'd' => 'A software license', 'answer' => 'B', 'explanation' => 'PAT (FDA initiative) uses real-time sensors and models to monitor and control critical quality and performance attributes during manufacturing.', 'difficulty' => 'medium'],
                    ['question' => 'What is the advantage of "equation-oriented" (EO) vs "sequential modular" (SM) in Aspen Plus?', 'a' => 'EO is always faster', 'b' => 'EO solves all equations simultaneously, better for optimization and tight specifications; SM solves block-by-block, better for initial flowsheet development', 'c' => 'There is no difference', 'd' => 'SM is always preferred', 'answer' => 'B', 'explanation' => 'EO simultaneous solution is superior for optimization and highly coupled systems. SM sequential solution is more intuitive for building and debugging flowsheets.', 'difficulty' => 'hard'],
                    ['question' => 'In process intensification simulation, what is a "dividing wall column"?', 'a' => 'A structural wall', 'b' => 'A single column with an internal partition that achieves the separation of a conventional 2-column sequence', 'c' => 'A safety barrier', 'd' => 'A type of firewall', 'answer' => 'B', 'explanation' => 'A dividing wall column (DWC) uses an internal partition to achieve 3-product separation in one shell, saving 30%+ energy vs conventional sequences.', 'difficulty' => 'medium'],
                ],
            ],
            'exchanger_design' => [
                'beginner' => [
                    ['question' => 'The LMTD correction factor F for a true counter-current exchanger is:', 'a' => 'Always 1.0', 'b' => 'Always less than 1.0', 'c' => 'Depends on the temperature approach', 'd' => 'Always greater than 1.0', 'answer' => 'A', 'explanation' => 'For true counter-current flow, F = 1.0 by definition. The LMTD correction factor only deviates from 1.0 for multi-pass and cross-flow arrangements.', 'difficulty' => 'easy'],
                    ['question' => 'In TEMA nomenclature, what does "AES" describe?', 'a' => 'A type of pump', 'b' => 'Front head type A (channel and removable cover), Shell type E (one-pass), Rear head type S (floating head)', 'c' => 'An electrical standard', 'd' => 'A safety classification', 'answer' => 'B', 'explanation' => 'TEMA uses 3 letters: front head type, shell type, and rear head type. AES = removable channel cover, single-pass shell, floating head.', 'difficulty' => 'medium'],
                    ['question' => 'What is the primary purpose of baffles in a shell and tube exchanger?', 'a' => 'Structural support only', 'b' => 'To direct shell-side flow across the tube bundle, improving heat transfer', 'c' => 'To reduce noise', 'd' => 'To prevent corrosion', 'answer' => 'B', 'explanation' => 'Baffles direct shell-side flow perpendicular to the tubes, creating turbulence and improving heat transfer coefficient. They also support the tube bundle.', 'difficulty' => 'easy'],
                    ['question' => 'A fouling factor (Rf) is added to heat exchanger design to:', 'a' => 'Improve cleanliness', 'b' => 'Account for additional thermal resistance due to deposit buildup over time', 'c' => 'Increase flow rate', 'd' => 'Reduce tube thickness', 'answer' => 'B', 'explanation' => 'Fouling factors provide extra surface area to maintain heat transfer performance as deposits accumulate on tube and shell surfaces.', 'difficulty' => 'easy'],
                    ['question' => 'The overall heat transfer coefficient U is typically expressed in units of:', 'a' => 'W/m', 'b' => 'W/(m2*K)', 'c' => 'J/kg', 'd' => 'Pa*s', 'answer' => 'B', 'explanation' => 'U has units of W/(m2*K) or BTU/(hr*ft2*F), representing heat transfer rate per unit area per unit temperature difference.', 'difficulty' => 'easy'],
                    ['question' => 'What is the typical tube OD used in shell and tube exchangers?', 'a' => '1/4 inch', 'b' => '3/4 inch or 1 inch', 'c' => '4 inches', 'd' => '12 inches', 'answer' => 'B', 'explanation' => '3/4" (19.05mm) and 1" (25.4mm) OD tubes are the most common sizes, balancing heat transfer, pressure drop, fouling, and mechanical cleaning.', 'difficulty' => 'medium'],
                    ['question' => 'In a condenser, the controlling resistance is typically on:', 'a' => 'The tube side', 'b' => 'The condensing (shell) side', 'c' => 'Both sides equally', 'd' => 'Neither side', 'answer' => 'A', 'explanation' => 'Condensing coefficients are typically very high, so the coolant (tube side) and fouling resistances usually control the overall U.', 'difficulty' => 'medium'],
                    ['question' => 'What does "TEMA R" class indicate?', 'a' => 'Residential application', 'b' => 'Severe service requirements for petroleum and related processing', 'c' => 'Reduced cost', 'd' => 'Research grade', 'answer' => 'B', 'explanation' => 'TEMA R (Refinery) class specifies more stringent requirements for severe-duty applications in petroleum, chemical, and related industries.', 'difficulty' => 'medium'],
                    ['question' => 'The tube pitch ratio (pitch/OD) should typically be at least:', 'a' => '1.0', 'b' => '1.25', 'c' => '2.0', 'd' => '3.0', 'answer' => 'B', 'explanation' => 'TEMA specifies minimum tube pitch ratio of 1.25 to ensure adequate space for mechanical cleaning and tube-to-tubesheet welding.', 'difficulty' => 'medium'],
                    ['question' => 'What is the primary advantage of counter-current flow over co-current flow?', 'a' => 'Lower cost', 'b' => 'Higher mean temperature difference (LMTD) for the same terminal temperatures', 'c' => 'Easier construction', 'd' => 'Lower pressure drop', 'answer' => 'B', 'explanation' => 'Counter-current flow always yields a higher LMTD than co-current for the same inlet/outlet temperatures, requiring less heat transfer area.', 'difficulty' => 'easy'],
                ],
                'intermediate' => [
                    ['question' => 'In HTRI Xchanger Suite, what is the significance of the "vibration" warning?', 'a' => 'The software is unstable', 'b' => 'Potential flow-induced vibration damage to tubes due to high shell-side velocity', 'c' => 'An earthquake alert', 'd' => 'A computer hardware issue', 'answer' => 'B', 'explanation' => 'HTRI checks for flow-induced tube vibration using criteria like critical velocity, vortex shedding, and fluid-elastic instability to prevent tube failure.', 'difficulty' => 'medium'],
                    ['question' => 'For a kettle reboiler, what is the typical maximum heat flux to avoid film boiling?', 'a' => '1,000 W/m2', 'b' => '10,000-40,000 W/m2 depending on fluid', 'c' => '1,000,000 W/m2', 'd' => 'There is no limit', 'answer' => 'B', 'explanation' => 'Critical heat flux for kettle reboilers typically ranges from 10,000-40,000 W/m2. Exceeding this causes film boiling and dramatic decrease in heat transfer.', 'difficulty' => 'hard'],
                    ['question' => 'What is the "Delaware method" for shell-side heat transfer?', 'a' => 'A US state regulation', 'b' => 'A widely used correlation method that accounts for baffle window flow, cross-flow, and leakage streams', 'c' => 'A cleaning method', 'd' => 'A manufacturing technique', 'answer' => 'B', 'explanation' => 'The Bell-Delaware method corrects ideal cross-flow coefficients for baffle leakage, bypass, and window effects, providing accurate shell-side predictions.', 'difficulty' => 'medium'],
                    ['question' => 'In plate heat exchanger design, what is the "chevron angle"?', 'a' => 'The angle of the frame', 'b' => 'The corrugation angle on the plate that controls turbulence and pressure drop', 'c' => 'An installation angle', 'd' => 'The gasket angle', 'answer' => 'B', 'explanation' => 'The chevron (corrugation) angle determines the flow characteristics. Higher angles increase turbulence and heat transfer but also pressure drop.', 'difficulty' => 'medium'],
                    ['question' => 'What is the "pinch temperature" in heat exchanger design?', 'a' => 'The point where someone gets hurt', 'b' => 'The minimum temperature approach between hot and cold composite curves', 'c' => 'The maximum temperature', 'd' => 'The inlet temperature', 'answer' => 'B', 'explanation' => 'The pinch temperature is where the composite curves are closest, representing the thermodynamic bottleneck for heat recovery.', 'difficulty' => 'easy'],
                    ['question' => 'For air cooler design, what is the typical face velocity range?', 'a' => '0.1-0.5 m/s', 'b' => '2-4 m/s', 'c' => '10-20 m/s', 'd' => '50+ m/s', 'answer' => 'B', 'explanation' => 'Air cooler face velocity typically ranges from 2-4 m/s, balancing fan power consumption against air-side heat transfer coefficient.', 'difficulty' => 'medium'],
                    ['question' => 'In two-phase flow condensation, what is the Nusselt model assumption?', 'a' => 'Turbulent film', 'b' => 'Laminar film condensation with gravity-driven drainage', 'c' => 'No condensation', 'd' => 'Dropwise condensation', 'answer' => 'B', 'explanation' => 'Nusselt film condensation theory assumes smooth laminar film flow under gravity, providing the baseline condensation coefficient for vertical and horizontal tubes.', 'difficulty' => 'hard'],
                    ['question' => 'What is "thermal design margin" (or overdesign) in exchanger specifications?', 'a' => 'Safety factor for mechanical stress', 'b' => 'The percentage of excess surface area beyond the calculated requirement', 'c' => 'The profit margin', 'd' => 'The paint thickness', 'answer' => 'B', 'explanation' => 'Overdesign (typically 10-25%) provides extra surface area to compensate for uncertainties in fouling, physical properties, and operating conditions.', 'difficulty' => 'easy'],
                    ['question' => 'In nucleate boiling, the Zuber correlation is used to calculate:', 'a' => 'Bubble size', 'b' => 'Critical (maximum) heat flux', 'c' => 'Superheat degree', 'd' => 'Bubble frequency', 'answer' => 'B', 'explanation' => 'The Zuber correlation predicts the critical heat flux (CHF) - the maximum heat flux before transition to film boiling occurs.', 'difficulty' => 'hard'],
                    ['question' => 'What is the main advantage of a U-tube exchanger over a fixed tubesheet design?', 'a' => 'Lower cost always', 'b' => 'Accommodates differential thermal expansion between shell and tubes', 'c' => 'Better tube-side cleaning', 'd' => 'More tubes per shell', 'answer' => 'B', 'explanation' => 'U-tube design allows tubes to expand freely, eliminating thermal stress from differential expansion. However, the inner tubes are difficult to clean mechanically.', 'difficulty' => 'medium'],
                ],
                'advanced' => [
                    ['question' => 'In tube vibration analysis, what is "fluid-elastic instability"?', 'a' => 'Flexible tubing', 'b' => 'A self-excited vibration mechanism where tube motion feeds back to increase fluid forces, causing rapid failure', 'c' => 'Elastic deformation of the shell', 'd' => 'Pipe flexibility analysis', 'answer' => 'B', 'explanation' => 'Fluid-elastic instability is the most dangerous vibration mechanism where tube displacement increases fluid forces in a positive feedback loop, leading to rapid failure.', 'difficulty' => 'hard'],
                    ['question' => 'What is the Kern method primarily used for?', 'a' => 'Only shell-side calculations with simplifying assumptions (no leakage corrections)', 'b' => 'Tube-side calculations only', 'c' => 'Vibration analysis', 'd' => 'Cost estimation', 'answer' => 'A', 'explanation' => 'The Kern method provides simplified shell-side calculations without correction for leakage, bypass, and window effects. It is less accurate but useful for preliminary sizing.', 'difficulty' => 'medium'],
                    ['question' => 'In heat exchanger network synthesis, what is the "minimum number of units" formula?', 'a' => 'N = S + L - 1 where S is streams and L is independent loops', 'b' => 'N = S - 1 where S is the total number of hot and cold streams plus utilities', 'c' => 'N = 2S', 'd' => 'N = S/2', 'answer' => 'B', 'explanation' => 'For a minimum number of exchangers, N = S - 1 (where S includes all process streams and utility streams), assuming no stream splitting.', 'difficulty' => 'hard'],
                    ['question' => 'For a large ASME pressure vessel (exchanger shell), what code governs the mechanical design?', 'a' => 'API 610', 'b' => 'ASME Section VIII Division 1 (or Division 2 for higher pressures)', 'c' => 'ASME B31.3', 'd' => 'API 520', 'answer' => 'B', 'explanation' => 'ASME Section VIII Division 1 (or Division 2 for advanced analysis) governs pressure vessel design, including heat exchanger shells and heads.', 'difficulty' => 'medium'],
                    ['question' => 'What is "maldistribution" in plate heat exchangers?', 'a' => 'A business model', 'b' => 'Uneven flow distribution across the plate causing reduced heat transfer and local hot/cold spots', 'c' => 'A type of fouling', 'd' => 'A manufacturing defect only', 'answer' => 'B', 'explanation' => 'Flow maldistribution causes some channels to have much higher or lower velocities, reducing effective heat transfer area and potentially causing fouling or vibration.', 'difficulty' => 'medium'],
                    ['question' => 'In fired heater design, what is "radiant section efficiency"?', 'a' => 'The percentage of total fuel energy absorbed in the radiant (firebox) section', 'b' => 'The efficiency of the fans', 'c' => 'The burner turndown ratio', 'd' => 'Stack temperature', 'answer' => 'A', 'explanation' => 'Radiant section efficiency is the fraction of released heat absorbed by process tubes in the firebox, typically 45-55% of total duty.', 'difficulty' => 'medium'],
                    ['question' => 'What is the "temperature cross" problem in multi-pass exchangers?', 'a' => 'Crossing pipes', 'b' => 'When the cold outlet exceeds the hot outlet temperature, making F-factor undefined or impractical', 'c' => 'A temperature measurement error', 'd' => 'Overheating', 'answer' => 'B', 'explanation' => 'Temperature cross occurs when Tc_out > Th_out, which is thermodynamically impossible in a single-pass exchanger and causes F-factor to drop below practical limits in multi-pass.', 'difficulty' => 'hard'],
                    ['question' => 'In waste heat recovery, what is an "economizer"?', 'a' => 'A cost-saving device', 'b' => 'A heat exchanger that preheats boiler feedwater using flue gas', 'c' => 'A type of control valve', 'd' => 'A financial instrument', 'answer' => 'B', 'explanation' => 'An economizer recovers sensible heat from flue gases to preheat boiler feedwater, improving overall boiler efficiency by 5-10%.', 'difficulty' => 'easy'],
                    ['question' => 'What is the "area ratio" method in exchanger network retrofit?', 'a' => 'Comparing geographic areas', 'b' => 'Using the ratio of existing to required heat transfer area to evaluate whether existing exchangers can meet new duties', 'c' => 'A painting technique', 'd' => 'A financial ratio', 'answer' => 'B', 'explanation' => 'The area ratio method quickly screens which existing exchangers have sufficient area for new operating conditions, guiding retrofit priorities.', 'difficulty' => 'medium'],
                    ['question' => 'In compact heat exchangers, what does "surface area density" (beta) represent?', 'a' => 'Material density', 'b' => 'Heat transfer surface area per unit volume (m2/m3)', 'c' => 'Weight per area', 'd' => 'Fin thickness', 'answer' => 'B', 'explanation' => 'Surface area density (beta) is the ratio of heat transfer area to volume. Compact exchangers have beta > 700 m2/m3 (vs ~100 for shell & tube).', 'difficulty' => 'medium'],
                ],
                'expert' => [
                    ['question' => 'In digital twin monitoring of heat exchangers, which parameter best indicates fouling progression?', 'a' => 'Outlet temperature only', 'b' => 'Trending of cleanliness factor (actual U / clean U) over time', 'c' => 'Visual inspection', 'd' => 'Pump current', 'answer' => 'B', 'explanation' => 'The cleanliness factor (or fouling resistance trend) directly shows fouling buildup rate and predicts when cleaning is needed.', 'difficulty' => 'medium'],
                    ['question' => 'For a proprietary enhanced tube geometry, how are heat transfer correlations typically developed?', 'a' => 'Using standard smooth tube correlations', 'b' => 'From experimental testing over a range of Reynolds numbers, then fitting to a modified Nusselt correlation', 'c' => 'By CFD simulation only', 'd' => 'By estimation', 'answer' => 'B', 'explanation' => 'Enhanced tube correlations are developed from experimental data, fitting Nu = C * Re^m * Pr^n with geometry-specific constants determined from testing.', 'difficulty' => 'hard'],
                    ['question' => 'What is the "stream analysis" method by Palen and Taborek?', 'a' => 'Analyzing river streams', 'b' => 'A method that divides shell-side flow into distinct streams (cross, window, leakage, bypass) for accurate heat transfer prediction', 'c' => 'A financial analysis', 'd' => 'A data streaming protocol', 'answer' => 'B', 'explanation' => 'Stream analysis identifies five distinct flow streams on the shell-side, providing more accurate heat transfer and pressure drop predictions than the Kern method.', 'difficulty' => 'hard'],
                    ['question' => 'In a large retrofit project, what is the first step in heat exchanger network optimization?', 'a' => 'Replace all exchangers', 'b' => 'Data reconciliation and validation of current operating conditions', 'c' => 'Order new equipment', 'd' => 'Shut down the plant', 'answer' => 'B', 'explanation' => 'Accurate data reconciliation ensures the current network model matches reality before any optimization or retrofit design begins.', 'difficulty' => 'medium'],
                    ['question' => 'For a multi-million dollar exchanger network retrofit, what is the typical payback period expected by management?', 'a' => '1 month', 'b' => '1-3 years', 'c' => '10-15 years', 'd' => '25+ years', 'answer' => 'B', 'explanation' => 'Energy-related retrofit projects typically need a 1-3 year payback to gain management approval, depending on energy costs and carbon pricing.', 'difficulty' => 'easy'],
                    ['question' => 'What is the "entransy" concept in heat transfer optimization?', 'a' => 'A type of entropy', 'b' => 'A quantity (0.5*Q*T) that measures heat transfer ability, used for optimizing heat exchanger networks', 'c' => 'A software feature', 'd' => 'An entrance design', 'answer' => 'B', 'explanation' => 'Entransy (0.5*mcT^2 or 0.5*QT) quantifies heat transfer potential, analogous to exergy but specific to heat transfer system optimization.', 'difficulty' => 'hard'],
                    ['question' => 'In a coil-wound heat exchanger for LNG, what is the typical number of tube layers?', 'a' => '1-2', 'b' => '5-15+ layers wound helically', 'c' => 'Exactly 3', 'd' => '50+', 'answer' => 'B', 'explanation' => 'Coil-wound exchangers (CWHE) for LNG service typically have 5-15+ concentric tube layers wound helically around a mandrel, handling multiple streams.', 'difficulty' => 'hard'],
                    ['question' => 'What is the "NTU" in the epsilon-NTU method?', 'a' => 'A university name', 'b' => 'Number of Transfer Units = UA/Cmin, a dimensionless measure of exchanger size', 'c' => 'A noise measurement', 'd' => 'A tube count', 'answer' => 'B', 'explanation' => 'NTU = UA/Cmin represents the dimensionless "size" of the exchanger. Higher NTU means more heat transfer capability relative to the minimum capacity rate.', 'difficulty' => 'medium'],
                    ['question' => 'In AI-based fouling prediction, which features are most predictive?', 'a' => 'Exchanger color', 'b' => 'Historical U-value trends, fluid velocity, surface temperature, and crude assay properties', 'c' => 'Weather data only', 'd' => 'Operator names', 'answer' => 'B', 'explanation' => 'Fouling ML models use process parameters (velocity, temperature, composition) and historical fouling patterns to predict fouling rates and optimal cleaning schedules.', 'difficulty' => 'medium'],
                    ['question' => 'What is "constructal design" in heat exchanger optimization?', 'a' => 'Following building codes', 'b' => 'Using the constructal law to design flow architectures that maximize access for heat currents through the system', 'c' => 'A construction method', 'd' => 'A type of fin design only', 'answer' => 'B', 'explanation' => 'Constructal design applies the constructal law (flow systems evolve toward easier access) to optimize heat exchanger geometries for maximum thermal performance.', 'difficulty' => 'hard'],
                ],
            ],
        ];

        // Return specific questions if available, otherwise generate from templates
        if (isset($allQuestions[$catKey][$level])) {
            return $allQuestions[$catKey][$level];
        }

        // Generate questions for remaining categories
        return $this->generateCategoryQuestions($catKey, $level);
    }

    private function generateCategoryQuestions($catKey, $level) {
        $questionSets = [
            'concurrent_feed' => [
                'beginner' => [
                    ['question' => 'What does FEED stand for in engineering project context?', 'a' => 'Final Engineering and Evaluation Design', 'b' => 'Front-End Engineering Design', 'c' => 'First Equipment Engineering Document', 'd' => 'Formal Engineering and Estimation Draft', 'answer' => 'B', 'explanation' => 'FEED (Front-End Engineering Design) is the detailed engineering phase that defines the project scope, cost, and schedule before final investment decision.', 'difficulty' => 'easy'],
                    ['question' => 'A Class 3 cost estimate typically has an accuracy range of:', 'a' => '+/- 5%', 'b' => '+/- 10-20%', 'c' => '+/- 50%', 'd' => '+/- 100%', 'answer' => 'B', 'explanation' => 'Class 3 estimates (AACE) have accuracy of -10% to -20% on the low side and +10% to +30% on the high side, typical of FEED-level estimates.', 'difficulty' => 'medium'],
                    ['question' => 'In P&ID development, what does a double block and bleed valve arrangement provide?', 'a' => 'Faster flow', 'b' => 'Positive isolation for maintenance safety', 'c' => 'Better mixing', 'd' => 'Reduced cost', 'answer' => 'B', 'explanation' => 'Double block and bleed provides positive isolation by using two block valves with a bleed valve between them to verify seal integrity.', 'difficulty' => 'medium'],
                    ['question' => 'What is the purpose of a HAZOP study during FEED?', 'a' => 'To calculate project costs', 'b' => 'To systematically identify hazards and operability problems by examining deviations from design intent', 'c' => 'To schedule construction', 'd' => 'To hire contractors', 'answer' => 'B', 'explanation' => 'HAZOP uses guide words (No, More, Less, Reverse, etc.) applied to process parameters to systematically identify potential hazards and operability issues.', 'difficulty' => 'easy'],
                    ['question' => 'What is the difference between a PFD and a P&ID?', 'a' => 'They are the same', 'b' => 'PFD shows overall process flow and major equipment; P&ID shows detailed piping, instrumentation, and all control elements', 'c' => 'PFD is for piping only', 'd' => 'P&ID is a simplified version of PFD', 'answer' => 'B', 'explanation' => 'PFD (Process Flow Diagram) shows the big picture with major equipment and flows. P&ID (Piping & Instrumentation Diagram) shows all piping details, instruments, and controls.', 'difficulty' => 'easy'],
                    ['question' => 'In equipment sizing, what is the "design pressure" typically set to?', 'a' => 'Equal to operating pressure', 'b' => 'Operating pressure plus a margin (typically 10% or 25 psi, whichever is greater)', 'c' => 'Half of operating pressure', 'd' => 'Atmospheric pressure', 'answer' => 'B', 'explanation' => 'Design pressure exceeds normal operating pressure by a margin to accommodate pressure excursions without lifting the relief valve during normal operation.', 'difficulty' => 'medium'],
                    ['question' => 'What is "concurrent engineering" in FEED?', 'a' => 'Working on one task at a time', 'b' => 'Multiple disciplines working simultaneously and iteratively, sharing information in real-time', 'c' => 'Sequential engineering phases', 'd' => 'Outsourcing all work', 'answer' => 'B', 'explanation' => 'Concurrent engineering overlaps discipline activities (process, mechanical, civil, instrumentation) to reduce schedule and improve quality through early integration.', 'difficulty' => 'easy'],
                    ['question' => 'In cost estimation, what is an "allowance for indeterminates" (contingency)?', 'a' => 'Profit margin', 'b' => 'A percentage added to the estimate to cover unforeseen costs within the defined scope', 'c' => 'Tax payment', 'd' => 'Insurance premium', 'answer' => 'B', 'explanation' => 'Contingency covers expected but undefined costs within scope. It decreases as project definition improves (from ~30% at FEED to ~10% at detailed design).', 'difficulty' => 'medium'],
                    ['question' => 'What does "FIV" stand for in final investment decision context?', 'a' => 'Final Investment Verification', 'b' => 'Front-end Integrated Validation', 'c' => 'FID (Final Investment Decision) is the correct term - authorizing project execution', 'd' => 'Fiscal Investment Value', 'answer' => 'C', 'explanation' => 'FID (Final Investment Decision) is the gate where management approves capital expenditure and authorizes project execution based on FEED deliverables.', 'difficulty' => 'medium'],
                    ['question' => 'In line sizing during FEED, what is the typical erosional velocity limit for mixed-phase flow?', 'a' => '0.1 m/s', 'b' => 'Calculated from C/sqrt(rho_mix) per API 14E, typically 10-25 m/s', 'c' => '100 m/s', 'd' => 'No limit exists', 'answer' => 'B', 'explanation' => 'API 14E provides erosional velocity Ve = C/sqrt(rho_mix) where C is typically 100-150 for continuous service, giving velocities of 10-25 m/s.', 'difficulty' => 'hard'],
                ],
                'intermediate' => [
                    ['question' => 'What is the "plot plan" in FEED development?', 'a' => 'A gardening plan', 'b' => 'The layout drawing showing equipment arrangement, spacing, and facility organization', 'c' => 'A financial plan', 'd' => 'A project schedule', 'answer' => 'B', 'explanation' => 'The plot plan defines equipment arrangement considering safety distances, access for maintenance, piping runs, and constructability.', 'difficulty' => 'easy'],
                    ['question' => 'In FEED, what is the typical engineering completion percentage?', 'a' => '5-10%', 'b' => '25-35%', 'c' => '70-80%', 'd' => '100%', 'answer' => 'B', 'explanation' => 'FEED typically achieves 25-35% engineering completion - enough to define scope, estimate cost (+/-10-15%), and support FID.', 'difficulty' => 'medium'],
                    ['question' => 'What is "value engineering" in FEED?', 'a' => 'Cost cutting without analysis', 'b' => 'Systematic analysis of functions to achieve required functions at lowest total cost without sacrificing quality/safety', 'c' => 'Adding more equipment', 'd' => 'Increasing project scope', 'answer' => 'B', 'explanation' => 'Value engineering systematically examines each function to eliminate unnecessary costs while maintaining performance, reliability, and safety requirements.', 'difficulty' => 'medium'],
                    ['question' => 'In FEED deliverables, what is a "basis of design" document?', 'a' => 'A foundation drawing', 'b' => 'A document defining all design parameters, criteria, codes, and standards used for the project', 'c' => 'A marketing brochure', 'd' => 'An equipment datasheet', 'answer' => 'B', 'explanation' => 'The basis of design captures all design premises: ambient conditions, feedstock specs, product specs, design codes, utility conditions, and client preferences.', 'difficulty' => 'easy'],
                    ['question' => 'What is the role of a "tie-in list" in a brownfield FEED project?', 'a' => 'Listing necktie specifications', 'b' => 'Documenting all connection points between new facilities and existing plant infrastructure', 'c' => 'A materials list', 'd' => 'A staffing document', 'answer' => 'B', 'explanation' => 'The tie-in list identifies every connection between new and existing systems, critical for planning shutdowns and construction sequencing.', 'difficulty' => 'medium'],
                    ['question' => 'In concurrent FEED, what is "3D model review"?', 'a' => 'Watching 3D movies', 'b' => 'Using a 3D CAD model to verify equipment layout, piping routes, access, and clash detection', 'c' => 'A software update', 'd' => 'A type of inspection', 'answer' => 'B', 'explanation' => '3D model reviews use CAD tools (Smart 3D, PDMS, E3D) to verify constructability, maintainability, and detect clashes before construction.', 'difficulty' => 'easy'],
                    ['question' => 'What is "constructability review" during FEED?', 'a' => 'Reviewing construction worker resumes', 'b' => 'Systematic evaluation of design to ensure it can be built safely, efficiently, and economically', 'c' => 'A financial audit', 'd' => 'A safety inspection', 'answer' => 'B', 'explanation' => 'Constructability review involves construction experts evaluating design for module sizes, crane access, lift plans, and construction sequence optimization.', 'difficulty' => 'medium'],
                    ['question' => 'In project risk management during FEED, what is a "risk register"?', 'a' => 'A cash register', 'b' => 'A structured list of identified risks with probability, impact, owner, and mitigation plans', 'c' => 'A government filing', 'd' => 'An attendance sheet', 'answer' => 'B', 'explanation' => 'The risk register documents all identified project risks, their likelihood, consequence, risk score, mitigation measures, and responsible persons.', 'difficulty' => 'easy'],
                    ['question' => 'What is the purpose of "long-lead item" identification during FEED?', 'a' => 'Finding heavy equipment', 'b' => 'Identifying equipment with long procurement times to initiate early ordering and prevent schedule delays', 'c' => 'Ordering office supplies', 'd' => 'Selecting contractors', 'answer' => 'B', 'explanation' => 'Long-lead items (compressors, special alloy vessels, large transformers) require early procurement during FEED to prevent critical path delays.', 'difficulty' => 'medium'],
                    ['question' => 'In FEED quality assurance, what is an "interdisciplinary check" (IDC)?', 'a' => 'A health check', 'b' => 'A review where each discipline verifies consistency with other disciplines\' documents and drawings', 'c' => 'A software compatibility check', 'd' => 'A financial audit', 'answer' => 'B', 'explanation' => 'IDC ensures consistency between process, mechanical, piping, electrical, instrumentation, and civil designs before documents are issued.', 'difficulty' => 'medium'],
                ],
                'advanced' => [
                    ['question' => 'In a modular construction approach, what determines the maximum module size?', 'a' => 'Project budget only', 'b' => 'Transportation route constraints (road width, bridge capacity, port crane capacity)', 'c' => 'Aesthetic preferences', 'd' => 'Labor availability only', 'answer' => 'B', 'explanation' => 'Module size is limited by the transportation route: road/rail dimensions, bridge load ratings, port crane capacity, and barge dimensions.', 'difficulty' => 'medium'],
                    ['question' => 'What is "brownfield design" in FEED?', 'a' => 'Designing on contaminated land', 'b' => 'Designing new facilities within or adjacent to existing operating plants, requiring careful integration', 'c' => 'Using brown materials', 'd' => 'A farming concept', 'answer' => 'B', 'explanation' => 'Brownfield design involves integrating new facilities with existing infrastructure, requiring detailed surveys, tie-in planning, and phased construction.', 'difficulty' => 'easy'],
                    ['question' => 'In advanced FEED, what is "digital delivery" of engineering?', 'a' => 'Email attachments', 'b' => 'Delivering intelligent 3D models with linked data (rather than traditional 2D drawings) as the primary engineering deliverable', 'c' => 'Online meetings', 'd' => 'USB drives', 'answer' => 'B', 'explanation' => 'Digital delivery uses intelligent 3D models as the master engineering deliverable, with drawings extracted automatically, enabling BIM and digital handover.', 'difficulty' => 'medium'],
                    ['question' => 'What is the "RAM analysis" in FEED?', 'a' => 'Computer memory analysis', 'b' => 'Reliability, Availability, and Maintainability analysis to determine required sparing and plant availability', 'c' => 'A vehicle type', 'd' => 'A financial ratio', 'answer' => 'B', 'explanation' => 'RAM analysis models equipment failure rates and maintenance to determine plant availability, identify bottlenecks, and optimize sparing philosophy.', 'difficulty' => 'medium'],
                    ['question' => 'In FEED execution, what is a "hold point"?', 'a' => 'A pause in a football game', 'b' => 'A mandatory review/approval gate where work cannot proceed until formal sign-off', 'c' => 'A structural bracket', 'd' => 'A financial freeze', 'answer' => 'B', 'explanation' => 'Hold points are mandatory quality gates where specific reviews or approvals must be completed before the next phase of work can proceed.', 'difficulty' => 'medium'],
                    ['question' => 'What is "technology qualification" (TQ) in FEED for novel processes?', 'a' => 'Staff certification', 'b' => 'A systematic process to verify that new technology will perform reliably under project-specific conditions', 'c' => 'Software licensing', 'd' => 'Equipment painting', 'answer' => 'B', 'explanation' => 'TQ (per DNV-RP-A203 or similar) provides a structured approach to qualify new technology, reducing risk of first-of-a-kind equipment failure.', 'difficulty' => 'hard'],
                    ['question' => 'In lifecycle cost analysis during FEED, what is typically the largest cost component?', 'a' => 'Engineering design costs', 'b' => 'Operating costs over the plant lifetime (energy, maintenance, feedstock)', 'c' => 'Initial capital cost', 'd' => 'Decommissioning cost', 'answer' => 'B', 'explanation' => 'Operating costs over a 20-30 year plant life typically far exceed capital cost, making lifecycle cost analysis essential for design optimization.', 'difficulty' => 'hard'],
                    ['question' => 'What is the "Manning level" study in FEED?', 'a' => 'A personnel analysis', 'b' => 'Determining the required number of operations and maintenance staff for the new facility', 'c' => 'A sports analysis', 'd' => 'A financial study', 'answer' => 'B', 'explanation' => 'The manning study defines operator and maintenance staffing requirements, considering automation level, shift patterns, and regulatory requirements.', 'difficulty' => 'medium'],
                    ['question' => 'In FEED, what is "flare load study" used for?', 'a' => 'Studying sunlight', 'b' => 'Determining the maximum combined flow to the flare system from all emergency and operating scenarios', 'c' => 'A lighting design', 'd' => 'A fashion study', 'answer' => 'B', 'explanation' => 'Flare load study identifies all relief scenarios, combines simultaneous cases, and sizes the flare system (header, KO drum, tip) for the controlling scenario.', 'difficulty' => 'hard'],
                    ['question' => 'What is "SIMOPS" (Simultaneous Operations) planning in brownfield FEED?', 'a' => 'A simulation game', 'b' => 'Planning construction activities that occur while the existing plant continues to operate, managing combined risks', 'c' => 'A software tool', 'd' => 'A marketing strategy', 'answer' => 'B', 'explanation' => 'SIMOPS planning manages the combined risks of construction activities adjacent to or within an operating facility, covering hot work, crane lifts, and excavation near live equipment.', 'difficulty' => 'hard'],
                ],
                'expert' => [
                    ['question' => 'In mega-project FEED, what is the "PDRI" (Project Definition Rating Index)?', 'a' => 'A stock market index', 'b' => 'A tool that scores the completeness of project scope definition, predicting project success probability', 'c' => 'A drilling rate index', 'd' => 'A productivity index', 'answer' => 'B', 'explanation' => 'PDRI (CII tool) evaluates 70+ elements of scope definition. Projects with low PDRI scores (well-defined) have significantly better cost and schedule performance.', 'difficulty' => 'hard'],
                    ['question' => 'What is "integrated project delivery" (IPD) in FEED execution?', 'a' => 'Sending packages', 'b' => 'A collaborative delivery approach aligning interests of owner, designer, and contractor through shared risk/reward contracts', 'c' => 'A mail service', 'd' => 'Software integration', 'answer' => 'B', 'explanation' => 'IPD uses relational contracts (alliance, IPD) where all parties share project risk and reward, incentivizing collaboration over adversarial behavior.', 'difficulty' => 'medium'],
                    ['question' => 'In advanced project controls, what is "Earned Value Management" (EVM)?', 'a' => 'A banking system', 'b' => 'A methodology integrating scope, schedule, and cost to measure project performance and forecast completion', 'c' => 'A reward program', 'd' => 'A tax system', 'answer' => 'B', 'explanation' => 'EVM uses Planned Value, Earned Value, and Actual Cost to calculate SPI, CPI, and forecast at-completion costs and dates.', 'difficulty' => 'medium'],
                    ['question' => 'What is the role of a "project execution plan" (PEP) developed during FEED?', 'a' => 'A PE exam study plan', 'b' => 'A comprehensive document defining how the project will be executed: contracting strategy, schedule, resources, quality, safety', 'c' => 'A recipe book', 'd' => 'A gym workout plan', 'answer' => 'B', 'explanation' => 'The PEP is the master planning document covering contracting strategy, schedule philosophy, resource plan, quality management, and HSE requirements.', 'difficulty' => 'easy'],
                    ['question' => 'In technology licensing evaluation during FEED, what is the key deliverable?', 'a' => 'Marketing material', 'b' => 'A technology comparison matrix with technical, economic, and risk assessments of alternative technologies', 'c' => 'A patent application', 'd' => 'A training manual', 'answer' => 'B', 'explanation' => 'Technology comparison evaluates alternatives across technical performance, CAPEX, OPEX, reliability, scalability, and technology maturity (TRL).', 'difficulty' => 'medium'],
                    ['question' => 'What is the "sanction estimate" in FEED?', 'a' => 'A penalty calculation', 'b' => 'The final cost estimate supporting the Final Investment Decision (FID), typically Class 2-3 accuracy', 'c' => 'A discount calculation', 'd' => 'A tax estimate', 'answer' => 'B', 'explanation' => 'The sanction (appropriation) estimate is the cost basis for FID, required to be within +/-10-15% accuracy for major capital projects.', 'difficulty' => 'medium'],
                    ['question' => 'In FEED for hydrogen/ammonia projects, what is the primary design challenge compared to conventional hydrocarbon facilities?', 'a' => 'Color selection', 'b' => 'Material selection for hydrogen embrittlement, wide flammability range, and low ignition energy', 'c' => 'Pipe size only', 'd' => 'Labor availability', 'answer' => 'B', 'explanation' => 'Hydrogen has a wide flammability range (4-75%), low ignition energy, and causes embrittlement in carbon steel, requiring special material selection and safety design.', 'difficulty' => 'hard'],
                    ['question' => 'What is "CAPEX phasing" in FEED financial analysis?', 'a' => 'Equipment purchasing phases', 'b' => 'Distributing capital expenditure over time based on engineering, procurement, and construction progress', 'c' => 'A marketing phase', 'd' => 'A training schedule', 'answer' => 'B', 'explanation' => 'CAPEX phasing models cash flow timing based on procurement milestones, construction progress, and payment terms for financial planning.', 'difficulty' => 'medium'],
                    ['question' => 'In advanced FEED planning, what is a "critical path" schedule?', 'a' => 'The most dangerous route', 'b' => 'The longest sequence of dependent activities that determines the minimum project duration', 'c' => 'A backup plan', 'd' => 'The easiest tasks', 'answer' => 'B', 'explanation' => 'The critical path identifies activities where any delay directly delays project completion. Float analysis identifies near-critical paths for risk management.', 'difficulty' => 'easy'],
                    ['question' => 'What is "design-to-capacity" vs "design-to-cost" approach in FEED?', 'a' => 'They are identical', 'b' => 'Design-to-capacity maximizes production output; design-to-cost optimizes design within a fixed budget constraint', 'c' => 'Only capacity matters', 'd' => 'Only cost matters', 'answer' => 'B', 'explanation' => 'Design-to-capacity prioritizes meeting production targets. Design-to-cost sets a budget ceiling and optimizes capacity and features within that constraint.', 'difficulty' => 'medium'],
                ],
            ],
            'subsurface_science' => $this->getSubsurfaceQuestions(),
            'energy_optimization' => $this->getEnergyOptQuestions(),
            'operations_support' => $this->getOperationsQuestions(),
            'apc' => $this->getAPCQuestions(),
            'dynamic_optimization' => $this->getDynamicOptQuestions(),
            'mes' => $this->getMESQuestions(),
            'petroleum_supply_chain' => $this->getPetroleumSCQuestions(),
            'supply_chain' => $this->getSupplyChainQuestions(),
            'apm' => $this->getAPMQuestions(),
            'industrial_data_fabric' => $this->getDataFabricQuestions(),
            'digital_grid_mgmt' => $this->getDigitalGridQuestions(),
        ];

        if (isset($questionSets[$catKey][$level])) {
            return $questionSets[$catKey][$level];
        }
        if (isset($questionSets[$catKey])) {
            // Return beginner as default
            return $questionSets[$catKey]['beginner'] ?? $this->getDefaultQuestions($catKey, $level);
        }
        return $this->getDefaultQuestions($catKey, $level);
    }

    private function getSubsurfaceQuestions() {
        return [
            'beginner' => [
                ['question' => 'What is "porosity" in reservoir engineering?', 'a' => 'Rock strength', 'b' => 'The fraction of rock volume that is void space capable of holding fluids', 'c' => 'The rate of fluid flow', 'd' => 'The depth of the reservoir', 'answer' => 'B', 'explanation' => 'Porosity (phi) is the ratio of pore volume to bulk volume, typically 5-35% for reservoir rocks. It determines storage capacity.', 'difficulty' => 'easy'],
                ['question' => 'Darcy\'s law describes:', 'a' => 'Fluid behavior in pipes', 'b' => 'Single-phase flow through porous media as a function of permeability, pressure gradient, and viscosity', 'c' => 'Heat transfer rates', 'd' => 'Chemical reaction rates', 'answer' => 'B', 'explanation' => 'Darcy\'s law: q = -kA(dP/dx)/mu relates volumetric flow rate to permeability (k), area (A), pressure gradient, and viscosity (mu).', 'difficulty' => 'easy'],
                ['question' => 'What does "PVT" stand for in reservoir fluid analysis?', 'a' => 'Pressure-Volume-Temperature analysis of reservoir fluids', 'b' => 'Potential-Voltage-Time', 'c' => 'Permeability-Viscosity-Transmissibility', 'd' => 'Production-Volume-Target', 'answer' => 'A', 'explanation' => 'PVT analysis characterizes how reservoir fluid properties (density, viscosity, GOR, formation volume factor) change with pressure and temperature.', 'difficulty' => 'easy'],
                ['question' => 'The bubble point pressure is:', 'a' => 'The pressure at which gas first comes out of solution from an undersaturated oil', 'b' => 'The atmospheric pressure', 'c' => 'The highest pressure in the reservoir', 'd' => 'The pressure at the well head', 'answer' => 'A', 'explanation' => 'At bubble point pressure, the first gas bubble forms from the liquid phase. Above this pressure, oil is undersaturated (no free gas).', 'difficulty' => 'medium'],
                ['question' => 'What is "water cut" in production terminology?', 'a' => 'The amount of water removed from a river', 'b' => 'The fraction of water in the total liquid production', 'c' => 'The depth of the water table', 'd' => 'Water injection rate', 'answer' => 'B', 'explanation' => 'Water cut = water production rate / total liquid rate. It increases over field life as the waterfront advances toward producers.', 'difficulty' => 'easy'],
                ['question' => 'In well logging, what does a gamma ray log measure?', 'a' => 'Temperature', 'b' => 'Natural radioactivity of formations, used to distinguish shale from clean sand', 'c' => 'Pressure', 'd' => 'Flow rate', 'answer' => 'B', 'explanation' => 'Gamma ray logs detect natural radioactivity (K, U, Th). Shales have high GR readings; clean sands and carbonates have low GR.', 'difficulty' => 'medium'],
                ['question' => 'What is "permeability" in reservoir rock?', 'a' => 'Rock color', 'b' => 'The ability of rock to transmit fluids, measured in millidarcies', 'c' => 'The age of the rock', 'd' => 'The mineral composition', 'answer' => 'B', 'explanation' => 'Permeability measures how easily fluid flows through rock. Units are darcies (D) or millidarcies (mD). Good reservoir rock has >100 mD.', 'difficulty' => 'easy'],
                ['question' => 'In a black oil model, what are the three phases tracked?', 'a' => 'Solid, liquid, gas', 'b' => 'Oil, water, and gas', 'c' => 'Light oil, heavy oil, water', 'd' => 'Condensate, water, air', 'answer' => 'B', 'explanation' => 'Black oil models track three phases (oil, water, gas) with three components (oil, water, gas) and allow gas dissolution in oil via Rs.', 'difficulty' => 'medium'],
                ['question' => 'What is "skin factor" in well performance?', 'a' => 'Human skin analysis', 'b' => 'A dimensionless measure of additional pressure drop near the wellbore due to formation damage or stimulation', 'c' => 'Pipe coating thickness', 'd' => 'Casing grade', 'answer' => 'B', 'explanation' => 'Positive skin indicates formation damage (reduced near-wellbore permeability); negative skin indicates stimulation (hydraulic fracture or acid treatment).', 'difficulty' => 'medium'],
                ['question' => 'What is the "drainage area" of a well?', 'a' => 'The area where rainwater collects', 'b' => 'The reservoir area from which the well draws fluid', 'c' => 'The wellpad area', 'd' => 'The area of the casing', 'answer' => 'B', 'explanation' => 'Drainage area is the reservoir region contributing flow to a well, determined by well spacing, reservoir boundaries, and interference with neighboring wells.', 'difficulty' => 'easy'],
            ],
            'intermediate' => [
                ['question' => 'In reservoir simulation, what is "upscaling"?', 'a' => 'Increasing production', 'b' => 'Converting fine-scale geological model properties to coarser simulation grid cells while preserving flow behavior', 'c' => 'Making the model bigger', 'd' => 'Increasing pressure', 'answer' => 'B', 'explanation' => 'Upscaling averages fine-scale properties (porosity, permeability) to coarser grids, balancing simulation accuracy with computational cost.', 'difficulty' => 'medium'],
                ['question' => 'What is "history matching" in reservoir simulation?', 'a' => 'Matching historical dates', 'b' => 'Adjusting model parameters until simulated results match observed production and pressure data', 'c' => 'Comparing old reports', 'd' => 'Dating rock samples', 'answer' => 'B', 'explanation' => 'History matching calibrates the reservoir model by adjusting uncertain parameters (permeability, aquifer strength, faults) to match historical production data.', 'difficulty' => 'easy'],
                ['question' => 'In decline curve analysis, the Arps hyperbolic equation uses which parameter?', 'a' => 'Only initial rate qi', 'b' => 'Initial rate qi, initial decline rate Di, and b-factor (decline exponent)', 'c' => 'Only time', 'd' => 'Only pressure', 'answer' => 'B', 'explanation' => 'Arps hyperbolic: q(t) = qi / (1 + b*Di*t)^(1/b). The b-factor (0-1) describes how the decline rate changes with time.', 'difficulty' => 'medium'],
                ['question' => 'What is "relative permeability" (kr)?', 'a' => 'Permeability compared to a reference rock', 'b' => 'The fraction of absolute permeability available to a specific phase in multiphase flow', 'c' => 'Permeability at relative humidity', 'd' => 'The ratio of two different rock permeabilities', 'answer' => 'B', 'explanation' => 'Relative permeability (0-1) describes how each phase\'s effective permeability depends on saturation. kro + krw + krg does not necessarily equal 1.', 'difficulty' => 'medium'],
                ['question' => 'In well test analysis, the "Horner plot" is used for:', 'a' => 'Plotting insect populations', 'b' => 'Analyzing pressure buildup data to determine permeability and skin factor', 'c' => 'Production forecasting', 'd' => 'Equipment sizing', 'answer' => 'B', 'explanation' => 'The Horner plot (pressure vs log((tp+dt)/dt)) gives a straight line whose slope yields permeability and whose intercept gives skin factor.', 'difficulty' => 'hard'],
                ['question' => 'What is "aquifer influx" in reservoir engineering?', 'a' => 'Flooding a building', 'b' => 'Water flow from a connected aquifer into the reservoir as reservoir pressure declines', 'c' => 'Drilling through an aquifer', 'd' => 'Water injection', 'answer' => 'B', 'explanation' => 'Aquifer influx provides pressure support as the aquifer expands in response to reservoir pressure decline, modeled using Van Everdingen-Hurst or Fetkovich methods.', 'difficulty' => 'medium'],
                ['question' => 'In material balance, the "drive index" tells us:', 'a' => 'How far to drive to the well', 'b' => 'The relative contribution of each drive mechanism (fluid expansion, gas cap, water influx, compaction)', 'c' => 'The drilling speed', 'd' => 'Vehicle fuel efficiency', 'answer' => 'B', 'explanation' => 'Drive indices quantify how much each mechanism contributes to hydrocarbon recovery. They sum to 1.0 for a consistent material balance.', 'difficulty' => 'hard'],
                ['question' => 'What is a "type curve" in well testing?', 'a' => 'A printed form', 'b' => 'A dimensionless pressure/derivative plot used to match actual well test data for parameter estimation', 'c' => 'A classification of wells', 'd' => 'A drill bit type', 'answer' => 'B', 'explanation' => 'Type curve matching overlays actual pressure data on dimensionless type curves to determine permeability-thickness product and wellbore storage coefficient.', 'difficulty' => 'hard'],
                ['question' => 'In production optimization, what is "artificial lift"?', 'a' => 'An elevator', 'b' => 'Methods (ESP, gas lift, rod pump, etc.) to supplement natural reservoir energy for fluid production', 'c' => 'A helicopter operation', 'd' => 'A mathematical technique', 'answer' => 'B', 'explanation' => 'Artificial lift provides additional energy to lift fluids to surface when reservoir pressure is insufficient for natural flow.', 'difficulty' => 'easy'],
                ['question' => 'What is "capillary pressure" in multiphase flow?', 'a' => 'Pressure in capillary tubes only', 'b' => 'The pressure difference across a curved fluid interface, determining fluid distribution in pore spaces', 'c' => 'Blood pressure in capillaries', 'd' => 'Atmospheric pressure at sea level', 'answer' => 'B', 'explanation' => 'Capillary pressure (Pc = 2*sigma*cos(theta)/r) controls fluid distribution in pores and determines the transition zone height above the free water level.', 'difficulty' => 'medium'],
            ],
            'advanced' => $this->getGenericAdvancedQuestions('Subsurface Science', ['EOR modeling', 'compositional simulation', 'uncertainty analysis', 'integrated asset modeling']),
            'expert' => $this->getGenericExpertQuestions('Subsurface Science', ['machine learning', 'digital twin', 'smart completions', 'field development planning']),
        ];
    }

    private function getEnergyOptQuestions() {
        return [
            'beginner' => [
                ['question' => 'What is "pinch analysis" used for?', 'a' => 'Analyzing pipe fittings', 'b' => 'Identifying the minimum energy requirement for a process by analyzing heat exchange opportunities', 'c' => 'Measuring force', 'd' => 'Testing materials', 'answer' => 'B', 'explanation' => 'Pinch analysis determines the thermodynamic minimum heating and cooling requirements, and the pinch point where heat cannot be transferred.', 'difficulty' => 'easy'],
                ['question' => 'The "pinch point" divides the process into:', 'a' => 'Left and right sides', 'b' => 'A heat sink (above pinch, needs heating only) and heat source (below pinch, needs cooling only)', 'c' => 'Two equal halves', 'd' => 'Hot and cold fluids', 'answer' => 'B', 'explanation' => 'Above the pinch is a heat sink (deficit) needing hot utility. Below the pinch is a heat source (surplus) needing cold utility.', 'difficulty' => 'medium'],
                ['question' => 'What is Delta-Tmin in pinch analysis?', 'a' => 'The minimum time interval', 'b' => 'The minimum allowable temperature approach between hot and cold streams in a heat exchanger', 'c' => 'The minimum temperature in the process', 'd' => 'The average temperature', 'answer' => 'B', 'explanation' => 'Delta-Tmin is the economic trade-off between energy cost (lower DTmin = less utility) and capital cost (lower DTmin = more exchanger area).', 'difficulty' => 'easy'],
                ['question' => 'What is a "composite curve"?', 'a' => 'A curved pipe', 'b' => 'A plot of cumulative enthalpy vs temperature for all hot (or cold) streams combined', 'c' => 'A graph of production rates', 'd' => 'A cost curve', 'answer' => 'B', 'explanation' => 'Composite curves combine all hot streams into one curve and all cold streams into another, plotted on T-H diagram to visualize heat recovery.', 'difficulty' => 'medium'],
                ['question' => 'What is "utility targeting"?', 'a' => 'Choosing utility companies', 'b' => 'Determining the minimum hot and cold utility requirements before designing the heat exchanger network', 'c' => 'Setting utility prices', 'd' => 'Metering utility usage', 'answer' => 'B', 'explanation' => 'Utility targeting calculates the thermodynamic minimum external heating and cooling before committing to a network design.', 'difficulty' => 'easy'],
                ['question' => 'What is the "grand composite curve" (GCC)?', 'a' => 'A large curved pipe', 'b' => 'A plot of net heat deficit/surplus vs shifted temperature, showing utility placement opportunities', 'c' => 'A financial chart', 'd' => 'A quality control chart', 'answer' => 'B', 'explanation' => 'The GCC plots net heat flow vs shifted temperature, revealing opportunities for utility level optimization and process changes.', 'difficulty' => 'medium'],
                ['question' => 'What is the primary energy source in a typical refinery?', 'a' => 'Solar power', 'b' => 'Fuel gas/fired heaters and steam from boilers', 'c' => 'Wind power', 'd' => 'Battery storage', 'answer' => 'B', 'explanation' => 'Refineries primarily use fired heaters (furnaces) burning fuel gas and steam from gas-fired boilers for process heating.', 'difficulty' => 'easy'],
                ['question' => 'In energy auditing, what is "specific energy consumption"?', 'a' => 'Total energy used', 'b' => 'Energy consumed per unit of product output (e.g., GJ per tonne of product)', 'c' => 'Peak demand', 'd' => 'Minimum energy', 'answer' => 'B', 'explanation' => 'Specific energy consumption normalizes energy use to production, enabling fair comparison across time periods and between facilities.', 'difficulty' => 'easy'],
                ['question' => 'What are the three rules of pinch analysis?', 'a' => 'Build, test, deploy', 'b' => 'Do not transfer heat across the pinch, do not use cold utility above the pinch, do not use hot utility below the pinch', 'c' => 'Heat, cool, mix', 'd' => 'Plan, execute, review', 'answer' => 'B', 'explanation' => 'Violating any of these rules increases total utility consumption above the thermodynamic minimum target.', 'difficulty' => 'medium'],
                ['question' => 'What is CHP (Combined Heat and Power)?', 'a' => 'A chemical process', 'b' => 'Simultaneous generation of electricity and useful heat from a single fuel source', 'c' => 'A type of pump', 'd' => 'A safety system', 'answer' => 'B', 'explanation' => 'CHP (cogeneration) captures waste heat from power generation for process heating, achieving 70-90% total efficiency vs 30-40% for separate generation.', 'difficulty' => 'easy'],
            ],
            'intermediate' => $this->getGenericIntermediateQuestions('Energy Optimization', ['heat integration', 'steam systems', 'cogeneration', 'pinch analysis']),
            'advanced' => $this->getGenericAdvancedQuestions('Energy Optimization', ['total site integration', 'hydrogen pinch', 'water pinch', 'carbon footprint']),
            'expert' => $this->getGenericExpertQuestions('Energy Optimization', ['energy transition', 'net-zero design', 'carbon capture', 'renewable integration']),
        ];
    }

    private function getAPCQuestions() {
        return [
            'beginner' => [
                ['question' => 'In DMC (Dynamic Matrix Control), the prediction horizon should be:', 'a' => 'Equal to the model length', 'b' => 'Shorter than the control horizon', 'c' => 'At least as long as the settling time of the process', 'd' => 'Always 1 step', 'answer' => 'C', 'explanation' => 'The prediction horizon must capture the full process dynamics (settling time) to properly predict future behavior and calculate optimal moves.', 'difficulty' => 'medium'],
                ['question' => 'What is the primary benefit of Model Predictive Control (MPC) over PID?', 'a' => 'Lower software cost', 'b' => 'Handles multivariable interactions, constraints, and optimizes multiple variables simultaneously', 'c' => 'Simpler to implement', 'd' => 'Uses less computing power', 'answer' => 'B', 'explanation' => 'MPC handles MIMO (multi-input multi-output) interactions, respects constraints explicitly, and optimizes within the feasible operating region.', 'difficulty' => 'easy'],
                ['question' => 'What is a "step response model" in APC?', 'a' => 'A dance model', 'b' => 'The time-series of output changes after a step change in input, characterizing process dynamics', 'c' => 'A fitness model', 'd' => 'A manufacturing model', 'answer' => 'B', 'explanation' => 'Step response models capture process gain, time delay, and dynamics by recording output responses to step changes in manipulated variables.', 'difficulty' => 'easy'],
                ['question' => 'What is "model identification" (plant test) in APC?', 'a' => 'Identifying the plant location', 'b' => 'Exciting the process with planned input moves to generate data for building dynamic models', 'c' => 'A security check', 'd' => 'A maintenance procedure', 'answer' => 'B', 'explanation' => 'Model identification involves making systematic step changes (GBN or PRBS signals) in MVs and recording CV responses to build dynamic models.', 'difficulty' => 'medium'],
                ['question' => 'In APC terminology, what is a "CV"?', 'a' => 'Curriculum Vitae', 'b' => 'Controlled Variable - a process output the controller regulates', 'c' => 'Control Valve', 'd' => 'Current Volume', 'answer' => 'B', 'explanation' => 'CV (Controlled Variable) is a process output (e.g., temperature, purity) that the MPC controller drives to target or within limits.', 'difficulty' => 'easy'],
                ['question' => 'What is an "MV" in APC?', 'a' => 'Motor Vehicle', 'b' => 'Manipulated Variable - a process input the controller adjusts', 'c' => 'Maximum Value', 'd' => 'Mean Variance', 'answer' => 'B', 'explanation' => 'MV (Manipulated Variable) is a process input (e.g., valve position, setpoint) that the MPC adjusts to control CVs.', 'difficulty' => 'easy'],
                ['question' => 'What is a "DV" (Disturbance Variable) in APC?', 'a' => 'A type of vehicle', 'b' => 'A measured input that affects CVs but cannot be manipulated by the controller', 'c' => 'A software variable', 'd' => 'Digital voltage', 'answer' => 'B', 'explanation' => 'DVs (feed rate changes, ambient temperature) are measured disturbances that the controller uses in a feedforward manner to improve control.', 'difficulty' => 'medium'],
                ['question' => 'What is "move suppression" in MPC tuning?', 'a' => 'Preventing physical movement', 'b' => 'A tuning parameter that penalizes large MV moves to avoid aggressive control action', 'c' => 'A security feature', 'd' => 'Stopping the controller', 'answer' => 'B', 'explanation' => 'Move suppression increases the penalty on MV move size in the objective function, resulting in smoother, less aggressive control action.', 'difficulty' => 'medium'],
                ['question' => 'What is the typical ROI payback period for an APC project?', 'a' => '10-20 years', 'b' => '3-12 months', 'c' => '5-10 years', 'd' => 'Never', 'answer' => 'B', 'explanation' => 'APC projects typically achieve payback in 3-12 months through reduced energy, increased throughput, and tighter quality control.', 'difficulty' => 'medium'],
                ['question' => 'What is an "inferential sensor" (soft sensor) in APC?', 'a' => 'A physical temperature sensor', 'b' => 'A mathematical model that estimates a difficult-to-measure property from easier measurements', 'c' => 'A motion detector', 'd' => 'A camera system', 'answer' => 'B', 'explanation' => 'Inferential sensors estimate properties (e.g., product purity, octane number) from available measurements (temperatures, pressures, flows) using correlation models.', 'difficulty' => 'medium'],
            ],
            'intermediate' => $this->getGenericIntermediateQuestions('APC', ['step testing', 'model identification', 'controller tuning', 'benefits tracking']),
            'advanced' => $this->getGenericAdvancedQuestions('APC', ['nonlinear MPC', 'constraint handling', 'APC-RTO integration', 'multivariable control']),
            'expert' => $this->getGenericExpertQuestions('APC', ['enterprise deployment', 'adaptive MPC', 'ML soft sensors', 'self-tuning controllers']),
        ];
    }

    private function getMESQuestions() {
        return [
            'beginner' => [
                ['question' => 'A historian samples analog data using:', 'a' => 'Exception-based compression', 'b' => 'Fixed-rate only', 'c' => 'Random intervals', 'd' => 'Manual entry', 'answer' => 'A', 'explanation' => 'Data historians use exception-based (deadband) compression to efficiently store only significant changes, dramatically reducing storage requirements.', 'difficulty' => 'easy'],
                ['question' => 'What does ISA-95 define?', 'a' => 'Instrument calibration procedures', 'b' => 'The interface between enterprise (ERP) and manufacturing operations management systems', 'c' => 'Safety instrumented system design', 'd' => 'Piping standards', 'answer' => 'B', 'explanation' => 'ISA-95 (IEC 62264) provides models and terminology for integrating enterprise (Level 4) and control systems (Levels 0-3).', 'difficulty' => 'easy'],
                ['question' => 'What does ISA-88 specifically address?', 'a' => 'Continuous process control', 'b' => 'Batch control - defining equipment, recipes, and procedures for batch manufacturing', 'c' => 'Network security', 'd' => 'Instrument wiring', 'answer' => 'B', 'explanation' => 'ISA-88 (IEC 61512) defines batch control models: physical model, procedural model, and recipe management for batch process automation.', 'difficulty' => 'medium'],
                ['question' => 'What is OEE in manufacturing?', 'a' => 'Overall Equipment Effectiveness = Availability x Performance x Quality', 'b' => 'Operational Energy Efficiency', 'c' => 'Organizational Excellence Evaluation', 'd' => 'Online Equipment Exchange', 'answer' => 'A', 'explanation' => 'OEE measures manufacturing productivity: Availability (uptime), Performance (speed), Quality (good parts). World-class OEE is ~85%.', 'difficulty' => 'easy'],
                ['question' => 'In MES, what is "genealogy tracking"?', 'a' => 'Family tree research', 'b' => 'Tracking the complete history of materials, equipment, and processes used to make each product batch', 'c' => 'HR records', 'd' => 'Equipment history', 'answer' => 'B', 'explanation' => 'Genealogy (or track and trace) records all raw materials, intermediate products, equipment, and conditions used for each batch - critical for recalls.', 'difficulty' => 'medium'],
                ['question' => 'What is the Purdue Reference Model in ISA-95?', 'a' => 'A university campus map', 'b' => 'A hierarchical model with 5 levels from physical process (Level 0) to enterprise (Level 4)', 'c' => 'A chemical formula', 'd' => 'A project management model', 'answer' => 'B', 'explanation' => 'The Purdue model defines: L0-Process, L1-Sensors/Actuators, L2-Control, L3-Manufacturing Operations, L4-Business Planning.', 'difficulty' => 'medium'],
                ['question' => 'What is "downtime tracking" in MES?', 'a' => 'Tracking employee breaks', 'b' => 'Recording and categorizing all periods when equipment is not producing, with reason codes', 'c' => 'Monitoring internet outages', 'd' => 'Scheduling vacations', 'answer' => 'B', 'explanation' => 'Downtime tracking captures every non-productive period with reason codes (breakdown, changeover, no material) for Pareto analysis and improvement.', 'difficulty' => 'easy'],
                ['question' => 'In a batch record, what is a "unit procedure"?', 'a' => 'A training manual', 'b' => 'A sequence of operations carried out on a specific process cell to produce a batch', 'c' => 'A unit conversion table', 'd' => 'A maintenance procedure', 'answer' => 'B', 'explanation' => 'Per ISA-88, a unit procedure is the highest procedural element executed on a single unit (process cell), containing operations and phases.', 'difficulty' => 'hard'],
                ['question' => 'What is "recipe management" in ISA-88?', 'a' => 'Cooking instructions', 'b' => 'Managing the hierarchy of recipes (general, site, master, control) that define how to make products', 'c' => 'A database system', 'd' => 'Chemical formula management', 'answer' => 'B', 'explanation' => 'ISA-88 defines a recipe hierarchy from abstract (general recipe) to specific (control recipe) to manage product and process variability.', 'difficulty' => 'medium'],
                ['question' => 'What is "tag" in the context of a data historian?', 'a' => 'A clothing label', 'b' => 'A unique identifier for a data point (sensor, calculated value) stored in the historian', 'c' => 'A game', 'd' => 'A price tag', 'answer' => 'B', 'explanation' => 'A historian tag represents a single data point (e.g., TI-101 temperature) with its configuration (scan rate, compression, engineering units).', 'difficulty' => 'easy'],
            ],
            'intermediate' => $this->getGenericIntermediateQuestions('MES', ['batch management', 'historian configuration', 'OEE analysis', 'ERP integration']),
            'advanced' => $this->getGenericAdvancedQuestions('MES', ['compression algorithms', 'EBR implementation', 'ISA-95 integration', 'analytics dashboards']),
            'expert' => $this->getGenericExpertQuestions('MES', ['digital transformation', 'Industry 4.0', 'real-time quality', 'regulatory compliance']),
        ];
    }

    // Generate reasonable intermediate/advanced/expert questions for categories not fully defined
    private function getGenericIntermediateQuestions($catName, $topics) {
        return [
            ['question' => "In {$catName}, what is the primary purpose of benchmarking?", 'a' => 'Marketing', 'b' => "Comparing current performance against best practices to identify improvement opportunities", 'c' => 'Regulatory compliance only', 'd' => 'Cost reduction only', 'answer' => 'B', 'explanation' => "Benchmarking identifies performance gaps by comparing against industry best practices, guiding prioritization of improvement efforts.", 'difficulty' => 'easy'],
            ['question' => "What is the role of KPIs in {$catName} applications?", 'a' => 'Key Performance Indicators measure and track specific performance metrics against targets', 'b' => 'They are only for financial reporting', 'c' => 'They replace detailed analysis', 'd' => 'They are optional extras', 'answer' => 'A', 'explanation' => 'KPIs provide quantitative measures of performance, enabling objective tracking, comparison, and data-driven decision making.', 'difficulty' => 'easy'],
            ['question' => "In {$topics[0]}, what is the recommended first step?", 'a' => 'Immediately implement changes', 'b' => 'Establish baseline measurements and document current state', 'c' => 'Purchase new software', 'd' => 'Hire consultants', 'answer' => 'B', 'explanation' => 'Establishing an accurate baseline is essential before any optimization to measure improvement and justify investment.', 'difficulty' => 'medium'],
            ['question' => "For {$topics[1]} implementation, what is the critical success factor?", 'a' => 'Budget only', 'b' => 'Accurate data quality and proper configuration', 'c' => 'Software brand', 'd' => 'Number of licenses', 'answer' => 'B', 'explanation' => 'Data quality and proper configuration are the foundation of successful implementation. Poor data leads to unreliable results regardless of software.', 'difficulty' => 'medium'],
            ['question' => "What is the typical ROI timeframe for {$catName} projects?", 'a' => '1 week', 'b' => '6-18 months depending on scope and complexity', 'c' => '10+ years', 'd' => 'Never', 'answer' => 'B', 'explanation' => "Most {$catName} projects achieve ROI within 6-18 months through operational improvements, reduced downtime, and better decision-making.", 'difficulty' => 'medium'],
            ['question' => "In {$topics[2]}, what does validation involve?", 'a' => 'Only checking software version', 'b' => 'Verifying that models and configurations produce correct results against known data', 'c' => 'Signing documents', 'd' => 'Running any test', 'answer' => 'B', 'explanation' => 'Validation ensures models accurately represent reality by comparing outputs against measured plant data or known analytical solutions.', 'difficulty' => 'medium'],
            ['question' => "What is change management in the context of {$catName} implementation?", 'a' => 'Changing passwords', 'b' => 'A structured approach to transitioning people, processes, and technology to the new system', 'c' => 'Replacing equipment', 'd' => 'Updating documentation only', 'answer' => 'B', 'explanation' => 'Change management addresses the human factors of technology adoption: training, communication, stakeholder engagement, and workflow adaptation.', 'difficulty' => 'easy'],
            ['question' => "In {$topics[3]} practice, what is continuous improvement?", 'a' => 'Replacing everything annually', 'b' => 'Ongoing effort to optimize processes, using data and feedback to make incremental improvements', 'c' => 'Working longer hours', 'd' => 'Buying newer equipment', 'answer' => 'B', 'explanation' => 'Continuous improvement (PDCA cycle) uses data-driven analysis to identify and implement incremental process enhancements systematically.', 'difficulty' => 'easy'],
            ['question' => "What is the purpose of a gap analysis in {$catName}?", 'a' => 'Finding physical gaps in equipment', 'b' => 'Comparing current capabilities with desired state to identify areas requiring development', 'c' => 'Measuring distances', 'd' => 'A financial report', 'answer' => 'B', 'explanation' => 'Gap analysis identifies the difference between current and desired performance, forming the basis for improvement roadmap and resource planning.', 'difficulty' => 'easy'],
            ['question' => "What role does training play in {$catName} system success?", 'a' => 'It is optional', 'b' => 'Critical - user competency directly impacts system utilization and ROI realization', 'c' => 'Only needed for IT staff', 'd' => 'Only during initial deployment', 'answer' => 'B', 'explanation' => 'Training is critical for adoption. Systems fail to deliver value when users lack competency to utilize capabilities effectively.', 'difficulty' => 'easy'],
        ];
    }

    private function getGenericAdvancedQuestions($catName, $topics) {
        return [
            ['question' => "In advanced {$catName}, what is the role of machine learning?", 'a' => 'Replacing all engineers', 'b' => 'Augmenting decision-making by finding patterns in large datasets that traditional methods miss', 'c' => 'Only for image recognition', 'd' => 'Marketing automation', 'answer' => 'B', 'explanation' => 'ML augments engineering judgment by discovering complex patterns and correlations in operational data, enabling predictive and prescriptive analytics.', 'difficulty' => 'medium'],
            ['question' => "What is the concept of a digital twin in {$catName}?", 'a' => 'A backup computer', 'b' => 'A virtual replica of a physical system that updates in real-time for monitoring, prediction, and optimization', 'c' => 'A screenshot', 'd' => 'A duplicate database', 'answer' => 'B', 'explanation' => 'A digital twin is a living virtual model synchronized with real-time data, enabling what-if analysis, prediction, and optimization.', 'difficulty' => 'easy'],
            ['question' => "In {$topics[0]}, what is the primary challenge for implementation?", 'a' => 'Software cost only', 'b' => 'Data quality, model accuracy, and integration with existing systems and workflows', 'c' => 'Internet speed', 'd' => 'Office space', 'answer' => 'B', 'explanation' => 'Implementation challenges are primarily around data quality, model accuracy validation, and seamless integration with existing operational workflows.', 'difficulty' => 'medium'],
            ['question' => "For advanced {$topics[1]}, what optimization technique is commonly used?", 'a' => 'Trial and error only', 'b' => 'Mathematical programming (LP, NLP, MILP) combined with heuristic methods', 'c' => 'Random search', 'd' => 'Manual calculation', 'answer' => 'B', 'explanation' => 'Mathematical programming provides rigorous optimal solutions, while heuristics handle combinatorial complexity and practical constraints.', 'difficulty' => 'hard'],
            ['question' => "What is edge computing in the context of {$catName}?", 'a' => 'Computing on the edge of a cliff', 'b' => 'Processing data near the source (at the plant) rather than sending everything to the cloud', 'c' => 'Using old computers', 'd' => 'A networking protocol', 'answer' => 'B', 'explanation' => 'Edge computing processes data locally for low-latency decisions while sending aggregated data to cloud for analytics, combining responsiveness with scalability.', 'difficulty' => 'medium'],
            ['question' => "In {$topics[2]}, what is the importance of scalability?", 'a' => 'Making things bigger', 'b' => 'Ensuring the solution can grow from pilot to enterprise-wide without redesign', 'c' => 'Using larger fonts', 'd' => 'Hiring more people', 'answer' => 'B', 'explanation' => 'Scalable architectures allow starting with a pilot and expanding to enterprise deployment without fundamental re-architecture.', 'difficulty' => 'medium'],
            ['question' => "What is prescriptive analytics in {$catName}?", 'a' => 'Medical prescriptions', 'b' => 'Analytics that not only predicts outcomes but recommends specific actions to achieve desired results', 'c' => 'Describing past events', 'd' => 'Basic reporting', 'answer' => 'B', 'explanation' => 'Prescriptive analytics goes beyond prediction (what will happen) to recommendation (what should be done), combining optimization with prediction.', 'difficulty' => 'medium'],
            ['question' => "In advanced {$topics[3]}, what is the role of simulation?", 'a' => 'Entertainment only', 'b' => 'Testing scenarios and strategies in a safe virtual environment before real-world implementation', 'c' => 'Replacing real operations', 'd' => 'Only for training', 'answer' => 'B', 'explanation' => 'Simulation enables risk-free testing of new strategies, operating conditions, and designs before committing to costly real-world changes.', 'difficulty' => 'easy'],
            ['question' => "What is API-first design in modern {$catName} systems?", 'a' => 'API petroleum standards', 'b' => 'Designing systems with well-defined interfaces (APIs) first, enabling flexible integration with other systems', 'c' => 'Using APIs for authentication only', 'd' => 'A programming language', 'answer' => 'B', 'explanation' => 'API-first design ensures systems can communicate and integrate with other enterprise applications, enabling data flow and workflow automation.', 'difficulty' => 'medium'],
            ['question' => "What is the role of cybersecurity in industrial {$catName} systems?", 'a' => 'Only IT department concern', 'b' => 'Protecting operational technology from cyber threats while maintaining safety and availability', 'c' => 'Optional for old plants', 'd' => 'Only for internet-connected systems', 'answer' => 'B', 'explanation' => 'OT cybersecurity (IEC 62443) protects industrial systems from threats that could impact safety, production, and environmental compliance.', 'difficulty' => 'medium'],
        ];
    }

    private function getGenericExpertQuestions($catName, $topics) {
        return [
            ['question' => "In enterprise {$catName} strategy, what is the most important success factor?", 'a' => 'Technology selection only', 'b' => 'Executive sponsorship, clear business case, and alignment with organizational strategy', 'c' => 'Software features', 'd' => 'Number of vendors', 'answer' => 'B', 'explanation' => 'Enterprise programs succeed with strong executive sponsorship, compelling business case, and strategic alignment. Technology alone does not ensure success.', 'difficulty' => 'medium'],
            ['question' => "What is the concept of Industry 4.0 in {$catName}?", 'a' => 'The fourth building in an industrial park', 'b' => 'The fourth industrial revolution: cyber-physical systems, IoT, cloud computing, and AI transforming manufacturing', 'c' => 'A version number', 'd' => 'A regulatory standard', 'answer' => 'B', 'explanation' => 'Industry 4.0 represents the convergence of physical and digital systems, enabling autonomous optimization and new business models.', 'difficulty' => 'easy'],
            ['question' => "In {$topics[0]} leadership, what is the role of a technology roadmap?", 'a' => 'A GPS navigation tool', 'b' => 'A strategic plan mapping technology evolution, investments, and capabilities over 3-5 years', 'c' => 'A single project plan', 'd' => 'A vendor selection document', 'answer' => 'B', 'explanation' => 'Technology roadmaps align technology investments with business strategy, ensuring coordinated evolution of capabilities and value delivery.', 'difficulty' => 'medium'],
            ['question' => "What is the concept of value realization in {$catName} programs?", 'a' => 'Selling assets', 'b' => 'Systematically measuring and tracking the actual business benefits delivered by the program', 'c' => 'Reducing headcount', 'd' => 'A financial accounting method', 'answer' => 'B', 'explanation' => 'Value realization ensures promised benefits are actually achieved through systematic tracking, accountability, and corrective action.', 'difficulty' => 'medium'],
            ['question' => "In {$topics[1]} implementation, what is the recommended approach?", 'a' => 'Big bang implementation', 'b' => 'Phased approach: pilot on a representative unit, validate value, then scale systematically', 'c' => 'Wait for perfect technology', 'd' => 'Implement everything simultaneously', 'answer' => 'B', 'explanation' => 'Phased implementation reduces risk, builds organizational capability, and demonstrates value before committing to enterprise-wide deployment.', 'difficulty' => 'easy'],
            ['question' => "What is the role of data governance in enterprise {$catName}?", 'a' => 'Government data regulations only', 'b' => 'Ensuring data quality, ownership, security, and lifecycle management across the organization', 'c' => 'IT department policy', 'd' => 'Data backup procedures', 'answer' => 'B', 'explanation' => 'Data governance establishes policies, standards, and accountability for data management, ensuring reliable data for decision-making.', 'difficulty' => 'medium'],
            ['question' => "In {$topics[2]}, what is the build vs buy decision framework?", 'a' => 'Always build custom', 'b' => 'Evaluate total cost of ownership, time-to-value, maintainability, and competitive differentiation', 'c' => 'Always buy commercial', 'd' => 'Choose the cheapest option', 'answer' => 'B', 'explanation' => 'Build vs buy decisions balance customization needs against TCO, implementation speed, ongoing maintenance burden, and competitive advantage.', 'difficulty' => 'medium'],
            ['question' => "What is organizational change readiness assessment for {$catName} transformation?", 'a' => 'A dress code review', 'b' => 'Evaluating the organization capacity and willingness to adopt new technologies and processes', 'c' => 'A financial audit', 'd' => 'A facility inspection', 'answer' => 'B', 'explanation' => 'Change readiness assessment identifies organizational barriers, capability gaps, and cultural factors that could impede technology adoption.', 'difficulty' => 'medium'],
            ['question' => "In leading {$topics[3]} programs, what metrics matter most to executives?", 'a' => 'Number of software features', 'b' => 'Business impact: EBITDA improvement, safety incidents, production volume, reliability', 'c' => 'IT metrics only', 'd' => 'Number of reports generated', 'answer' => 'B', 'explanation' => 'Executives focus on business outcomes (profitability, safety, reliability) rather than technical metrics. Translating technical achievements to business value is essential.', 'difficulty' => 'easy'],
            ['question' => "What is the future trend of AI in {$catName}?", 'a' => 'Replacing all human workers', 'b' => 'Augmenting human expertise with autonomous optimization, predictive insights, and decision support', 'c' => 'Only chatbots', 'd' => 'No impact expected', 'answer' => 'B', 'explanation' => 'AI will augment (not replace) domain expertise, enabling faster analysis, predictive maintenance, autonomous optimization, and enhanced decision-making.', 'difficulty' => 'easy'],
        ];
    }

    private function getOperationsQuestions() { return $this->getSubsurfaceQuestions(); }
    private function getDynamicOptQuestions() { return $this->getEnergyOptQuestions(); }
    private function getPetroleumSCQuestions() { return $this->getEnergyOptQuestions(); }
    private function getSupplyChainQuestions() { return $this->getEnergyOptQuestions(); }
    private function getAPMQuestions() { return $this->getMESQuestions(); }
    private function getDataFabricQuestions() { return $this->getMESQuestions(); }
    private function getDigitalGridQuestions() { return $this->getEnergyOptQuestions(); }

    private function getDefaultQuestions($catKey, $level) {
        $cats = $this->getSeedCategories();
        $catName = $cats[$catKey]['name'] ?? ucwords(str_replace('_', ' ', $catKey));
        $modules = $cats[$catKey]['modules'] ?? ['Module A', 'Module B', 'Module C', 'Module D'];

        return [
            ['question' => "What is the primary purpose of {$catName}?", 'a' => 'Financial reporting', 'b' => "Optimizing {$modules[0]} and related engineering processes", 'c' => 'Human resources management', 'd' => 'Marketing automation', 'answer' => 'B', 'explanation' => "{$catName} focuses on engineering optimization and operational excellence in its domain.", 'difficulty' => 'easy'],
            ['question' => "In {$catName}, what is the role of {$modules[0]}?", 'a' => 'It is optional', 'b' => 'A core module providing fundamental capabilities for the discipline', 'c' => 'Only for reporting', 'd' => 'Only for training', 'answer' => 'B', 'explanation' => "{$modules[0]} is a foundational module providing essential capabilities within {$catName}.", 'difficulty' => 'easy'],
            ['question' => "Which industry standard is most relevant to {$catName}?", 'a' => 'No standards apply', 'b' => 'Industry-specific standards and best practices guide implementation', 'c' => 'Only financial standards', 'd' => 'Fashion standards', 'answer' => 'B', 'explanation' => "{$catName} follows established industry standards to ensure quality, safety, and interoperability.", 'difficulty' => 'medium'],
            ['question' => "What data is critical for {$modules[1]} analysis?", 'a' => 'No data needed', 'b' => 'Accurate, validated process data from sensors and laboratory measurements', 'c' => 'Only financial data', 'd' => 'Only weather data', 'answer' => 'B', 'explanation' => "Accurate process data is the foundation for reliable {$modules[1]} analysis and decision-making.", 'difficulty' => 'medium'],
            ['question' => "What is the key benefit of integrating {$modules[2]} with other systems?", 'a' => 'No benefit', 'b' => 'Holistic view of operations enabling better optimization across the value chain', 'c' => 'Reduced software licenses', 'd' => 'Simpler interfaces', 'answer' => 'B', 'explanation' => "Integration enables data flow and coordinated optimization across systems, breaking down information silos.", 'difficulty' => 'medium'],
            ['question' => "In {$catName}, what is the role of visualization?", 'a' => 'Only for presentations', 'b' => 'Enabling rapid comprehension of complex data for decision-making', 'c' => 'Decorative purposes', 'd' => 'Replacing analysis', 'answer' => 'B', 'explanation' => "Effective visualization transforms complex data into actionable insights, enabling faster and better decisions.", 'difficulty' => 'easy'],
            ['question' => "What skills are needed for {$catName} at the {$level} level?", 'a' => 'No technical skills', 'b' => 'Domain knowledge, analytical skills, and software proficiency', 'c' => 'Only software skills', 'd' => 'Only management skills', 'answer' => 'B', 'explanation' => "Effective use of {$catName} requires a combination of engineering domain knowledge, analytical capability, and tool proficiency.", 'difficulty' => 'easy'],
            ['question' => "How does {$modules[3]} contribute to operational excellence?", 'a' => 'It does not', 'b' => 'By providing data-driven insights for continuous improvement of operations', 'c' => 'By reducing headcount', 'd' => 'By eliminating all manual work', 'answer' => 'B', 'explanation' => "{$modules[3]} enables data-driven decision making and continuous improvement in operational performance.", 'difficulty' => 'medium'],
            ['question' => "What is the relationship between {$catName} and digital transformation?", 'a' => 'They are unrelated', 'b' => "{$catName} is a key enabler of digital transformation in industrial operations", 'c' => 'Digital transformation replaces {$catName}', 'd' => 'Only IT is involved in digital transformation', 'answer' => 'B', 'explanation' => "{$catName} provides critical capabilities for digital transformation by digitizing and optimizing engineering workflows.", 'difficulty' => 'medium'],
            ['question' => "What is the future direction of {$catName}?", 'a' => 'It will become obsolete', 'b' => 'Integration of AI/ML, cloud computing, and IoT for autonomous optimization', 'c' => 'No changes expected', 'd' => 'Return to manual methods', 'answer' => 'B', 'explanation' => "The future of {$catName} involves AI-driven insights, cloud-enabled collaboration, and IoT connectivity for real-time optimization.", 'difficulty' => 'easy'],
        ];
    }
}
