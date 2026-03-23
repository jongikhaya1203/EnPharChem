<?php
/**
 * EnPharChem - Control Panel Controller
 * Manages Active Directory, CMS, Marketing, Training, and Data Management
 */

class ControlPanelController extends BaseController {

    /**
     * Main control panel dashboard
     */
    public function index() {
        $stats = [
            'total_users' => $this->safeCount('users'),
            'ad_users' => $this->safeCount('ad_users'),
            'cms_pages' => $this->safeCount('cms_pages'),
            'marketing_materials' => $this->safeCount('marketing_materials'),
            'training_courses' => $this->safeCount('training_courses'),
        ];

        $this->view('control-panel/index', [
            'pageTitle' => 'Control Panel',
            'stats' => $stats,
        ]);
    }

    /**
     * Active Directory management
     */
    public function activeDirectory() {
        if ($this->isPost()) {
            $action = $_POST['action'] ?? '';

            if ($action === 'create_group') {
                try {
                    $this->db->insert('ad_groups', [
                        'name' => $_POST['name'] ?? '',
                        'description' => $_POST['description'] ?? '',
                        'group_type' => $_POST['group_type'] ?? 'security',
                        'is_active' => 1,
                    ]);
                } catch (Exception $e) {
                    // Group may already exist
                }
            } elseif ($action === 'create_user') {
                try {
                    $this->db->insert('ad_users', [
                        'username' => $_POST['username'] ?? '',
                        'display_name' => $_POST['display_name'] ?? '',
                        'email' => $_POST['email'] ?? '',
                        'department' => $_POST['department'] ?? '',
                        'title' => $_POST['title'] ?? '',
                        'group_id' => !empty($_POST['group_id']) ? (int)$_POST['group_id'] : null,
                        'phone' => $_POST['phone'] ?? '',
                        'location' => $_POST['location'] ?? '',
                        'account_status' => 'active',
                        'last_sync' => date('Y-m-d H:i:s'),
                        'last_logon' => date('Y-m-d H:i:s'),
                    ]);
                    // Update member count
                    if (!empty($_POST['group_id'])) {
                        $this->db->query(
                            "UPDATE ad_groups SET member_count = (SELECT COUNT(*) FROM ad_users WHERE group_id = ?) WHERE id = ?",
                            [(int)$_POST['group_id'], (int)$_POST['group_id']]
                        );
                    }
                } catch (Exception $e) {
                    // User may already exist
                }
            }

            $this->redirect('control-panel/active-directory');
            return;
        }

        $groups = $this->db->fetchAll("SELECT * FROM ad_groups ORDER BY name") ?: [];
        $users = $this->db->fetchAll(
            "SELECT u.*, g.name as group_name FROM ad_users u LEFT JOIN ad_groups g ON u.group_id = g.id ORDER BY u.display_name"
        ) ?: [];

        $adStats = [
            'total_groups' => count($groups),
            'total_users' => count($users),
            'active_pct' => count($users) > 0
                ? round(count(array_filter($users, fn($u) => $u['account_status'] === 'active')) / count($users) * 100)
                : 0,
            'last_sync' => count($users) > 0 ? max(array_column($users, 'last_sync')) : 'Never',
        ];

        $this->view('control-panel/active-directory', [
            'pageTitle' => 'Active Directory',
            'groups' => $groups,
            'adUsers' => $users,
            'adStats' => $adStats,
        ]);
    }

    /**
     * CMS Pages management
     */
    public function cmsPages() {
        if ($this->isPost()) {
            $action = $_POST['action'] ?? '';

            if ($action === 'create') {
                $title = $_POST['title'] ?? '';
                $slug = $this->slugify($title);
                try {
                    $this->db->insert('cms_pages', [
                        'title' => $title,
                        'slug' => $slug,
                        'content' => $_POST['content'] ?? '',
                        'category' => $_POST['category'] ?? 'general',
                        'status' => $_POST['status'] ?? 'draft',
                        'author_id' => $this->user['id'] ?? null,
                        'meta_description' => $_POST['meta_description'] ?? '',
                        'meta_keywords' => $_POST['meta_keywords'] ?? '',
                    ]);
                } catch (Exception $e) {
                    // Slug conflict or other error
                }
            } elseif ($action === 'delete') {
                $id = (int)($_POST['id'] ?? 0);
                if ($id) {
                    $this->db->delete('cms_pages', 'id = ?', [$id]);
                }
            } elseif ($action === 'toggle_status') {
                $id = (int)($_POST['id'] ?? 0);
                if ($id) {
                    $page = $this->db->fetch("SELECT status FROM cms_pages WHERE id = ?", [$id]);
                    if ($page) {
                        $newStatus = $page['status'] === 'published' ? 'draft' : 'published';
                        $this->db->update('cms_pages', ['status' => $newStatus], 'id = ?', [$id]);
                    }
                }
            }

            $this->redirect('control-panel/cms');
            return;
        }

        $pages = $this->db->fetchAll(
            "SELECT p.*, u.username as author_name FROM cms_pages p LEFT JOIN users u ON p.author_id = u.id ORDER BY p.updated_at DESC"
        ) ?: [];

        $cmsStats = [
            'total' => count($pages),
            'published' => count(array_filter($pages, fn($p) => $p['status'] === 'published')),
            'drafts' => count(array_filter($pages, fn($p) => $p['status'] === 'draft')),
            'total_views' => array_sum(array_column($pages, 'view_count')),
        ];

        $this->view('control-panel/cms', [
            'pageTitle' => 'CMS Pages',
            'pages' => $pages,
            'cmsStats' => $cmsStats,
        ]);
    }

    /**
     * CMS Page Editor
     */
    public function cmsPageEdit() {
        $id = (int)($this->getParam('id', 0));

        if ($this->isPost()) {
            $data = [
                'title' => $_POST['title'] ?? '',
                'slug' => $_POST['slug'] ?? $this->slugify($_POST['title'] ?? ''),
                'content' => $_POST['content'] ?? '',
                'category' => $_POST['category'] ?? 'general',
                'status' => $_POST['status'] ?? 'draft',
                'meta_description' => $_POST['meta_description'] ?? '',
                'meta_keywords' => $_POST['meta_keywords'] ?? '',
                'featured_image' => $_POST['featured_image'] ?? '',
            ];

            if ($id) {
                $this->db->update('cms_pages', $data, 'id = ?', [$id]);
            } else {
                $data['author_id'] = $this->user['id'] ?? null;
                $id = $this->db->insert('cms_pages', $data);
            }

            $this->redirect('control-panel/cms');
            return;
        }

        $page = null;
        if ($id) {
            $page = $this->db->fetch("SELECT * FROM cms_pages WHERE id = ?", [$id]);
        }

        $this->view('control-panel/cms-edit', [
            'pageTitle' => $page ? 'Edit Page' : 'Create Page',
            'page' => $page,
        ]);
    }

    /**
     * Marketing Material management
     */
    public function marketingMaterial() {
        if ($this->isPost()) {
            $action = $_POST['action'] ?? '';

            if ($action === 'create') {
                try {
                    $this->db->insert('marketing_materials', [
                        'title' => $_POST['title'] ?? '',
                        'description' => $_POST['description'] ?? '',
                        'material_type' => $_POST['material_type'] ?? 'brochure',
                        'category' => $_POST['category'] ?? '',
                        'target_audience' => $_POST['target_audience'] ?? '',
                        'file_url' => $_POST['file_url'] ?? '',
                        'status' => $_POST['status'] ?? 'draft',
                        'created_by' => $this->user['id'] ?? null,
                    ]);
                } catch (Exception $e) {
                    // Handle error
                }
            } elseif ($action === 'update') {
                $id = (int)($_POST['id'] ?? 0);
                if ($id) {
                    $this->db->update('marketing_materials', [
                        'title' => $_POST['title'] ?? '',
                        'description' => $_POST['description'] ?? '',
                        'material_type' => $_POST['material_type'] ?? 'brochure',
                        'category' => $_POST['category'] ?? '',
                        'target_audience' => $_POST['target_audience'] ?? '',
                        'file_url' => $_POST['file_url'] ?? '',
                        'status' => $_POST['status'] ?? 'draft',
                    ], 'id = ?', [$id]);
                }
            } elseif ($action === 'delete') {
                $id = (int)($_POST['id'] ?? 0);
                if ($id) {
                    $this->db->delete('marketing_materials', 'id = ?', [$id]);
                }
            } elseif ($action === 'approve') {
                $id = (int)($_POST['id'] ?? 0);
                if ($id) {
                    $this->db->update('marketing_materials', ['status' => 'approved'], 'id = ?', [$id]);
                }
            }

            $this->redirect('control-panel/marketing');
            return;
        }

        $materials = $this->db->fetchAll(
            "SELECT m.*, u.username as creator_name FROM marketing_materials m LEFT JOIN users u ON m.created_by = u.id ORDER BY m.created_at DESC"
        ) ?: [];

        $this->view('control-panel/marketing', [
            'pageTitle' => 'Marketing Material',
            'materials' => $materials,
        ]);
    }

    /**
     * Training Material management
     */
    public function trainingMaterial() {
        if ($this->isPost()) {
            $action = $_POST['action'] ?? '';

            if ($action === 'create_course') {
                try {
                    $this->db->insert('training_courses', [
                        'title' => $_POST['title'] ?? '',
                        'description' => $_POST['description'] ?? '',
                        'category' => $_POST['category'] ?? 'general',
                        'level' => $_POST['level'] ?? 'beginner',
                        'duration_hours' => (float)($_POST['duration_hours'] ?? 0),
                        'instructor' => $_POST['instructor'] ?? '',
                        'prerequisites' => $_POST['prerequisites'] ?? '',
                        'status' => 'draft',
                    ]);
                } catch (Exception $e) {
                    // Handle error
                }
            } elseif ($action === 'create_lesson') {
                try {
                    $this->db->insert('training_lessons', [
                        'course_id' => (int)($_POST['course_id'] ?? 0),
                        'title' => $_POST['title'] ?? '',
                        'description' => $_POST['description'] ?? '',
                        'lesson_type' => $_POST['lesson_type'] ?? 'document',
                        'duration_minutes' => (int)($_POST['duration_minutes'] ?? 30),
                        'lesson_order' => (int)($_POST['lesson_order'] ?? 0),
                        'status' => 'draft',
                    ]);
                } catch (Exception $e) {
                    // Handle error
                }
            } elseif ($action === 'delete_course') {
                $id = (int)($_POST['id'] ?? 0);
                if ($id) {
                    $this->db->delete('training_courses', 'id = ?', [$id]);
                }
            } elseif ($action === 'toggle_status') {
                $id = (int)($_POST['id'] ?? 0);
                if ($id) {
                    $course = $this->db->fetch("SELECT status FROM training_courses WHERE id = ?", [$id]);
                    if ($course) {
                        $newStatus = $course['status'] === 'active' ? 'draft' : 'active';
                        $this->db->update('training_courses', ['status' => $newStatus], 'id = ?', [$id]);
                    }
                }
            }

            $this->redirect('control-panel/training');
            return;
        }

        $courses = $this->db->fetchAll("SELECT * FROM training_courses ORDER BY created_at DESC") ?: [];
        $lessons = $this->db->fetchAll(
            "SELECT l.*, c.title as course_title FROM training_lessons l JOIN training_courses c ON l.course_id = c.id ORDER BY c.title, l.lesson_order"
        ) ?: [];

        // Get lesson counts per course
        $lessonCounts = $this->db->fetchAll("SELECT course_id, COUNT(*) as cnt FROM training_lessons GROUP BY course_id") ?: [];
        $lessonCountMap = [];
        foreach ($lessonCounts as $lc) {
            $lessonCountMap[$lc['course_id']] = $lc['cnt'];
        }

        $trainingStats = [
            'total_courses' => count($courses),
            'active_courses' => count(array_filter($courses, fn($c) => $c['status'] === 'active')),
            'total_lessons' => count($lessons),
            'total_enrollments' => array_sum(array_column($courses, 'enrollment_count')),
        ];

        $this->view('control-panel/training', [
            'pageTitle' => 'Training Material',
            'courses' => $courses,
            'lessons' => $lessons,
            'lessonCountMap' => $lessonCountMap,
            'trainingStats' => $trainingStats,
        ]);
    }

    /**
     * Data Management page
     */
    public function dataManagement() {
        $tables = [
            'users', 'projects', 'simulations', 'modules', 'module_categories',
            'ad_groups', 'ad_users', 'cms_pages', 'marketing_materials',
            'training_courses', 'training_lessons', 'chemical_components',
            'cost_estimates', 'assets', 'heat_exchangers'
        ];

        $tableStats = [];
        foreach ($tables as $table) {
            try {
                $row = $this->db->fetch("SELECT COUNT(*) as cnt FROM {$table}");
                $tableStats[] = ['name' => $table, 'count' => $row['cnt'], 'status' => 'ok'];
            } catch (Exception $e) {
                $tableStats[] = ['name' => $table, 'count' => 0, 'status' => 'missing'];
            }
        }

        $msg = $_GET['msg'] ?? null;

        $this->view('control-panel/data-management', [
            'pageTitle' => 'Data Management',
            'tableStats' => $tableStats,
            'msg' => $msg,
        ]);
    }

    /**
     * Load comprehensive sample data
     */
    public function loadSampleData() {
        if (!$this->isPost()) {
            $this->redirect('control-panel/data-management');
            return;
        }

        $loaded = [];

        // ── Sample Users ──
        $sampleUsers = [
            ['username' => 'engineer1', 'email' => 'engineer1@enpharchem.com', 'first_name' => 'James', 'last_name' => 'Mitchell', 'role' => 'engineer', 'department' => 'Process Engineering', 'company' => 'EnPharChem Technologies', 'license_type' => 'professional'],
            ['username' => 'engineer2', 'email' => 'engineer2@enpharchem.com', 'first_name' => 'Sarah', 'last_name' => 'Chen', 'role' => 'engineer', 'department' => 'Chemical Engineering', 'company' => 'EnPharChem Technologies', 'license_type' => 'enterprise'],
            ['username' => 'engineer3', 'email' => 'engineer3@enpharchem.com', 'first_name' => 'Rajesh', 'last_name' => 'Patel', 'role' => 'operator', 'department' => 'Operations', 'company' => 'EnPharChem Technologies', 'license_type' => 'standard'],
            ['username' => 'engineer4', 'email' => 'engineer4@enpharchem.com', 'first_name' => 'Maria', 'last_name' => 'Rodriguez', 'role' => 'engineer', 'department' => 'R&D', 'company' => 'EnPharChem Technologies', 'license_type' => 'enterprise'],
            ['username' => 'engineer5', 'email' => 'engineer5@enpharchem.com', 'first_name' => 'David', 'last_name' => 'Anderson', 'role' => 'viewer', 'department' => 'Management', 'company' => 'EnPharChem Technologies', 'license_type' => 'trial'],
        ];

        $passwordHash = password_hash('demo123', PASSWORD_DEFAULT);
        $userIds = [];
        foreach ($sampleUsers as $u) {
            try {
                $exists = $this->db->fetch("SELECT id FROM users WHERE username = ?", [$u['username']]);
                if ($exists) {
                    $userIds[] = $exists['id'];
                    continue;
                }
                $u['password_hash'] = $passwordHash;
                $u['is_active'] = 1;
                $uid = $this->db->insert('users', $u);
                $userIds[] = $uid;
                $loaded[] = "User: {$u['username']}";
            } catch (Exception $e) { /* skip */ }
        }

        // ── Sample Projects ──
        $firstUserId = $userIds[0] ?? 1;
        $sampleProjects = [
            ['name' => 'LNG Liquefaction Plant Optimization', 'description' => 'Optimize the mixed refrigerant cycle for a 5 MTPA LNG plant. Target: reduce specific energy consumption below 280 kWh/ton.', 'category' => 'energy', 'status' => 'active'],
            ['name' => 'Ethylene Cracker Revamp Study', 'description' => 'Evaluate furnace coil modifications and quench system upgrades for 15% capacity increase on the 800 KTA ethylene plant.', 'category' => 'chemicals', 'status' => 'active'],
            ['name' => 'Pharmaceutical API Crystallization', 'description' => 'Develop cooling crystallization model for ibuprofen API. Target crystal size distribution: 200-400 microns.', 'category' => 'pharma', 'status' => 'draft'],
            ['name' => 'Offshore Gas Processing FEED', 'description' => 'Front-End Engineering Design for 500 MMSCFD offshore gas processing platform with NGL recovery.', 'category' => 'energy', 'status' => 'active'],
            ['name' => 'Ammonia Plant Heat Integration', 'description' => 'Pinch analysis and heat exchanger network optimization for a 2000 TPD ammonia synthesis plant.', 'category' => 'chemicals', 'status' => 'completed'],
            ['name' => 'Refinery Crude Unit Simulation', 'description' => 'Steady-state and dynamic simulation of atmospheric and vacuum distillation columns processing 150,000 BPD Arabian Light crude.', 'category' => 'energy', 'status' => 'active'],
            ['name' => 'Polyethylene Reactor Modeling', 'description' => 'Fluidized bed reactor model for LLDPE production. Ziegler-Natta catalyst kinetics with multiple site types.', 'category' => 'chemicals', 'status' => 'draft'],
            ['name' => 'Solar Thermal Power Grid Integration', 'description' => 'Dynamic modeling of 100 MW concentrated solar power plant with molten salt storage for grid stability analysis.', 'category' => 'grid', 'status' => 'active'],
        ];

        $projectIds = [];
        foreach ($sampleProjects as $idx => $p) {
            try {
                $exists = $this->db->fetch("SELECT id FROM projects WHERE name = ?", [$p['name']]);
                if ($exists) {
                    $projectIds[] = $exists['id'];
                    continue;
                }
                $p['user_id'] = $userIds[$idx % count($userIds)] ?? $firstUserId;
                $pid = $this->db->insert('projects', $p);
                $projectIds[] = $pid;
                $loaded[] = "Project: {$p['name']}";
            } catch (Exception $e) { /* skip */ }
        }

        // ── Sample Simulations ──
        $moduleRow = $this->db->fetch("SELECT id FROM modules LIMIT 1");
        $defaultModuleId = $moduleRow ? $moduleRow['id'] : 1;

        $sampleSimulations = [
            ['name' => 'MR Cycle Base Case', 'description' => 'C3MR cycle simulation at design conditions. Feed gas: 48% CH4, 8% C2H6, 4% C3H8, 2% nC4, 38% CO2.', 'simulation_type' => 'steady_state', 'status' => 'completed', 'convergence_status' => 'converged', 'iterations' => 42, 'execution_time' => 12.5],
            ['name' => 'Ethylene Furnace Profile', 'description' => 'Thermal cracking simulation of naphtha feed at 850C COT. SRT model with coke formation kinetics.', 'simulation_type' => 'steady_state', 'status' => 'completed', 'convergence_status' => 'converged', 'iterations' => 28, 'execution_time' => 8.3],
            ['name' => 'Crude Column Dynamic Startup', 'description' => 'Dynamic simulation of atmospheric column startup sequence. Ramp from cold to steady state over 12 hours.', 'simulation_type' => 'dynamic', 'status' => 'running', 'convergence_status' => 'in_progress', 'iterations' => 1500, 'execution_time' => 340.0],
            ['name' => 'API Crystallizer Batch Cooling', 'description' => 'Batch crystallization with linear cooling from 70C to 5C over 4 hours. Population balance model.', 'simulation_type' => 'batch', 'status' => 'completed', 'convergence_status' => 'converged', 'iterations' => 85, 'execution_time' => 25.7],
            ['name' => 'NGL Recovery Turboexpander', 'description' => 'Gas subcooled process with turboexpander for C3+ recovery >98%. Feed: 950 MMSCFD lean gas.', 'simulation_type' => 'steady_state', 'status' => 'completed', 'convergence_status' => 'converged', 'iterations' => 35, 'execution_time' => 9.8],
            ['name' => 'Heat Exchanger Network Synthesis', 'description' => 'Pinch analysis at delta-Tmin = 10C. 15 hot streams, 12 cold streams. Target: minimum utility cost.', 'simulation_type' => 'optimization', 'status' => 'completed', 'convergence_status' => 'converged', 'iterations' => 200, 'execution_time' => 45.2],
            ['name' => 'Ammonia Converter Dynamic', 'description' => 'Three-bed radial-flow converter dynamic model. Fe3O4 catalyst with poison deactivation.', 'simulation_type' => 'dynamic', 'status' => 'draft', 'convergence_status' => null, 'iterations' => 0, 'execution_time' => null],
            ['name' => 'LLDPE Reactor Steady State', 'description' => 'Fluidized bed at 85C, 20 bar. Ethylene/1-hexene copolymerization. Multi-site kinetics.', 'simulation_type' => 'steady_state', 'status' => 'failed', 'convergence_status' => 'diverged', 'iterations' => 500, 'execution_time' => 120.0],
            ['name' => 'Vacuum Column Revamp Case', 'description' => 'Evaluate structured packing replacement in vacuum column. Target: 15% capacity increase at 25 mmHg top pressure.', 'simulation_type' => 'steady_state', 'status' => 'completed', 'convergence_status' => 'converged', 'iterations' => 38, 'execution_time' => 11.2],
            ['name' => 'CSP Plant Transient Response', 'description' => 'Solar field transient during cloud passage. Molten salt loop with 8-hour thermal storage at 565C.', 'simulation_type' => 'dynamic', 'status' => 'running', 'convergence_status' => 'in_progress', 'iterations' => 800, 'execution_time' => 180.5],
            ['name' => 'Amine Sweetening Unit', 'description' => 'MDEA-based acid gas removal. Feed: 500 MMSCFD with 12% CO2, 200 ppm H2S. Target: pipeline spec gas.', 'simulation_type' => 'steady_state', 'status' => 'completed', 'convergence_status' => 'converged', 'iterations' => 55, 'execution_time' => 15.8],
            ['name' => 'Distillation Column Optimization', 'description' => 'Reflux ratio optimization for debutanizer column. Minimize reboiler duty while maintaining 98% C4 recovery.', 'simulation_type' => 'optimization', 'status' => 'completed', 'convergence_status' => 'converged', 'iterations' => 150, 'execution_time' => 38.4],
            ['name' => 'Compressor Surge Analysis', 'description' => 'Centrifugal compressor map with surge line prediction. 3-stage with intercoolers at 35C.', 'simulation_type' => 'steady_state', 'status' => 'draft', 'convergence_status' => null, 'iterations' => 0, 'execution_time' => null],
            ['name' => 'Wastewater Treatment Plant', 'description' => 'Activated sludge process simulation. BOD reduction from 300 to 10 mg/L. Aeration basin + clarifier.', 'simulation_type' => 'steady_state', 'status' => 'completed', 'convergence_status' => 'converged', 'iterations' => 62, 'execution_time' => 18.9],
            ['name' => 'Flare Header Network', 'description' => 'Flare system hydraulics for emergency depressurization. 15 relief valves, 2500m header network.', 'simulation_type' => 'steady_state', 'status' => 'draft', 'convergence_status' => null, 'iterations' => 0, 'execution_time' => null],
        ];

        foreach ($sampleSimulations as $idx => $s) {
            try {
                $exists = $this->db->fetch("SELECT id FROM simulations WHERE name = ?", [$s['name']]);
                if ($exists) continue;
                $s['project_id'] = $projectIds[$idx % count($projectIds)] ?? ($projectIds[0] ?? 1);
                $s['module_id'] = $defaultModuleId;
                $s['user_id'] = $userIds[$idx % count($userIds)] ?? $firstUserId;
                $this->db->insert('simulations', $s);
                $loaded[] = "Simulation: {$s['name']}";
            } catch (Exception $e) { /* skip */ }
        }

        // ── Sample AD Groups ──
        $adGroups = [
            ['name' => 'Engineering', 'description' => 'Process and chemical engineering team', 'group_type' => 'security'],
            ['name' => 'Operations', 'description' => 'Plant operations and shift supervisors', 'group_type' => 'security'],
            ['name' => 'Management', 'description' => 'Senior leadership and project managers', 'group_type' => 'security'],
            ['name' => 'Safety', 'description' => 'HSE and safety engineering team', 'group_type' => 'security'],
            ['name' => 'IT', 'description' => 'Information technology and infrastructure', 'group_type' => 'security'],
            ['name' => 'Research', 'description' => 'R&D and technology development', 'group_type' => 'organizational'],
            ['name' => 'Quality', 'description' => 'Quality assurance and control', 'group_type' => 'organizational'],
            ['name' => 'Maintenance', 'description' => 'Mechanical and electrical maintenance', 'group_type' => 'security'],
            ['name' => 'Supply Chain', 'description' => 'Procurement and logistics', 'group_type' => 'distribution'],
            ['name' => 'Training', 'description' => 'Training and development team', 'group_type' => 'organizational'],
        ];

        $groupIds = [];
        foreach ($adGroups as $g) {
            try {
                $exists = $this->db->fetch("SELECT id FROM ad_groups WHERE name = ?", [$g['name']]);
                if ($exists) {
                    $groupIds[] = $exists['id'];
                    continue;
                }
                $g['is_active'] = 1;
                $g['member_count'] = 0;
                $gid = $this->db->insert('ad_groups', $g);
                $groupIds[] = $gid;
                $loaded[] = "AD Group: {$g['name']}";
            } catch (Exception $e) { /* skip */ }
        }

        // ── Sample AD Users ──
        $adUsers = [
            ['username' => 'jmitchell', 'display_name' => 'James Mitchell', 'email' => 'j.mitchell@enpharchem.com', 'department' => 'Process Engineering', 'title' => 'Senior Process Engineer', 'phone' => '+1-713-555-0101', 'location' => 'Houston, TX'],
            ['username' => 'schen', 'display_name' => 'Sarah Chen', 'email' => 's.chen@enpharchem.com', 'department' => 'Chemical Engineering', 'title' => 'Lead Chemical Engineer', 'phone' => '+1-713-555-0102', 'location' => 'Houston, TX'],
            ['username' => 'rpatel', 'display_name' => 'Rajesh Patel', 'email' => 'r.patel@enpharchem.com', 'department' => 'Operations', 'title' => 'Shift Supervisor', 'phone' => '+1-713-555-0103', 'location' => 'Baytown, TX'],
            ['username' => 'mrodriguez', 'display_name' => 'Maria Rodriguez', 'email' => 'm.rodriguez@enpharchem.com', 'department' => 'R&D', 'title' => 'Research Scientist', 'phone' => '+1-713-555-0104', 'location' => 'Houston, TX'],
            ['username' => 'danderson', 'display_name' => 'David Anderson', 'email' => 'd.anderson@enpharchem.com', 'department' => 'Management', 'title' => 'VP Engineering', 'phone' => '+1-713-555-0105', 'location' => 'Houston, TX'],
            ['username' => 'lwilson', 'display_name' => 'Lisa Wilson', 'email' => 'l.wilson@enpharchem.com', 'department' => 'Safety', 'title' => 'HSE Manager', 'phone' => '+1-713-555-0106', 'location' => 'Houston, TX'],
            ['username' => 'tkumar', 'display_name' => 'Tanvi Kumar', 'email' => 't.kumar@enpharchem.com', 'department' => 'IT', 'title' => 'Systems Administrator', 'phone' => '+1-713-555-0107', 'location' => 'Houston, TX'],
            ['username' => 'bjohnson', 'display_name' => 'Brian Johnson', 'email' => 'b.johnson@enpharchem.com', 'department' => 'Maintenance', 'title' => 'Maintenance Planner', 'phone' => '+1-713-555-0108', 'location' => 'Baytown, TX'],
            ['username' => 'azhang', 'display_name' => 'Angela Zhang', 'email' => 'a.zhang@enpharchem.com', 'department' => 'Quality', 'title' => 'QA/QC Engineer', 'phone' => '+1-713-555-0109', 'location' => 'Houston, TX'],
            ['username' => 'mthompson', 'display_name' => 'Michael Thompson', 'email' => 'm.thompson@enpharchem.com', 'department' => 'Supply Chain', 'title' => 'Procurement Manager', 'phone' => '+1-713-555-0110', 'location' => 'Houston, TX'],
            ['username' => 'jgarcia', 'display_name' => 'Juan Garcia', 'email' => 'j.garcia@enpharchem.com', 'department' => 'Process Engineering', 'title' => 'Process Engineer', 'phone' => '+1-713-555-0111', 'location' => 'Deer Park, TX'],
            ['username' => 'kbrown', 'display_name' => 'Karen Brown', 'email' => 'k.brown@enpharchem.com', 'department' => 'Chemical Engineering', 'title' => 'Chemical Engineer', 'phone' => '+1-713-555-0112', 'location' => 'Houston, TX'],
            ['username' => 'rsingh', 'display_name' => 'Ravi Singh', 'email' => 'r.singh@enpharchem.com', 'department' => 'Operations', 'title' => 'Control Room Operator', 'phone' => '+1-713-555-0113', 'location' => 'Baytown, TX'],
            ['username' => 'ekim', 'display_name' => 'Emily Kim', 'email' => 'e.kim@enpharchem.com', 'department' => 'R&D', 'title' => 'Catalyst Researcher', 'phone' => '+1-713-555-0114', 'location' => 'Houston, TX'],
            ['username' => 'twalker', 'display_name' => 'Thomas Walker', 'email' => 't.walker@enpharchem.com', 'department' => 'Management', 'title' => 'Project Manager', 'phone' => '+1-713-555-0115', 'location' => 'Houston, TX'],
            ['username' => 'nlee', 'display_name' => 'Nancy Lee', 'email' => 'n.lee@enpharchem.com', 'department' => 'Training', 'title' => 'Training Coordinator', 'phone' => '+1-713-555-0116', 'location' => 'Houston, TX'],
            ['username' => 'pmartin', 'display_name' => 'Paul Martin', 'email' => 'p.martin@enpharchem.com', 'department' => 'Maintenance', 'title' => 'Reliability Engineer', 'phone' => '+1-713-555-0117', 'location' => 'Baytown, TX'],
            ['username' => 'acooper', 'display_name' => 'Amy Cooper', 'email' => 'a.cooper@enpharchem.com', 'department' => 'Safety', 'title' => 'Process Safety Engineer', 'phone' => '+1-713-555-0118', 'location' => 'Houston, TX'],
            ['username' => 'dwong', 'display_name' => 'Derek Wong', 'email' => 'd.wong@enpharchem.com', 'department' => 'IT', 'title' => 'Data Engineer', 'phone' => '+1-713-555-0119', 'location' => 'Houston, TX'],
            ['username' => 'smorris', 'display_name' => 'Stephanie Morris', 'email' => 's.morris@enpharchem.com', 'department' => 'Quality', 'title' => 'Lab Technician', 'phone' => '+1-713-555-0120', 'location' => 'Deer Park, TX'],
        ];

        foreach ($adUsers as $idx => $au) {
            try {
                $exists = $this->db->fetch("SELECT id FROM ad_users WHERE username = ?", [$au['username']]);
                if ($exists) continue;
                $au['group_id'] = $groupIds[$idx % count($groupIds)] ?? ($groupIds[0] ?? null);
                $au['account_status'] = 'active';
                $au['last_sync'] = date('Y-m-d H:i:s', strtotime("-" . rand(0, 48) . " hours"));
                $au['last_logon'] = date('Y-m-d H:i:s', strtotime("-" . rand(0, 72) . " hours"));
                $this->db->insert('ad_users', $au);
                $loaded[] = "AD User: {$au['display_name']}";
            } catch (Exception $e) { /* skip */ }
        }

        // Update group member counts
        try {
            $this->db->query("UPDATE ad_groups SET member_count = (SELECT COUNT(*) FROM ad_users WHERE ad_users.group_id = ad_groups.id)");
        } catch (Exception $e) { /* skip */ }

        // ── Sample CMS Pages ──
        $cmsPages = [
            ['title' => 'About EnPharChem', 'slug' => 'about', 'category' => 'general', 'status' => 'published', 'content' => 'EnPharChem Technologies is a global leader in engineering simulation and optimization software for the energy, pharmaceutical, and chemical industries. Our integrated platform delivers world-class process simulation, advanced process control, manufacturing execution, supply chain optimization, and asset performance management solutions.', 'meta_description' => 'Learn about EnPharChem Technologies and our engineering software platform.'],
            ['title' => 'Product Overview', 'slug' => 'products', 'category' => 'product', 'status' => 'published', 'content' => 'Our comprehensive suite includes Process Simulation for energy and chemicals, Exchanger Design and Rating, Concurrent FEED engineering, Advanced Process Control, MES, Supply Chain Optimization, Asset Performance Management, and Digital Grid Management. Each module is benchmarked against industry leaders like AspenTech.', 'meta_description' => 'Explore the full range of EnPharChem engineering software products.'],
            ['title' => 'Documentation Hub', 'slug' => 'documentation', 'category' => 'documentation', 'status' => 'published', 'content' => 'Welcome to the EnPharChem documentation hub. Here you will find user guides, API references, tutorials, and technical notes for all platform modules. Use the sidebar navigation to browse by module category.', 'meta_description' => 'EnPharChem platform documentation, user guides, and API references.'],
            ['title' => 'Support Center', 'slug' => 'support', 'category' => 'support', 'status' => 'published', 'content' => 'Our support team is available 24/7 for enterprise customers. Submit tickets through this portal, access knowledge base articles, or schedule a consultation with our application engineers. Standard support hours: 8AM-6PM CST, Monday-Friday.', 'meta_description' => 'Get technical support for EnPharChem platform.'],
            ['title' => 'Contact Us', 'slug' => 'contact', 'category' => 'general', 'status' => 'published', 'content' => 'EnPharChem Technologies Headquarters: 1200 Smith Street, Suite 1500, Houston, TX 77002. Email: info@enpharchem.com. Phone: +1-713-555-0100. Regional offices in London, Singapore, and Abu Dhabi.', 'meta_description' => 'Contact EnPharChem Technologies for sales and support inquiries.'],
            ['title' => 'Terms of Service', 'slug' => 'terms', 'category' => 'legal', 'status' => 'published', 'content' => 'These Terms of Service govern your use of the EnPharChem platform and all associated services. By accessing the platform, you agree to be bound by these terms. Enterprise license agreements supersede these general terms where applicable.', 'meta_description' => 'EnPharChem platform terms of service and legal information.'],
            ['title' => 'Privacy Policy', 'slug' => 'privacy', 'category' => 'legal', 'status' => 'published', 'content' => 'EnPharChem Technologies is committed to protecting your privacy. This policy describes how we collect, use, and safeguard your personal and simulation data. All data is encrypted in transit and at rest using AES-256 encryption.', 'meta_description' => 'EnPharChem privacy policy and data protection information.'],
            ['title' => 'Frequently Asked Questions', 'slug' => 'faq', 'category' => 'support', 'status' => 'published', 'content' => 'Q: What thermodynamic models are supported? A: We support Peng-Robinson, SRK, NRTL, UNIQUAC, Wilson, UNIFAC, SAFT, and CPA equations of state. Q: Can I import Aspen Plus files? A: Yes, we support importing .bkp and .apwz files with full stream and equipment data mapping.', 'meta_description' => 'Common questions about the EnPharChem engineering platform.'],
            ['title' => 'Getting Started Guide', 'slug' => 'getting-started', 'category' => 'documentation', 'status' => 'published', 'content' => 'Welcome to EnPharChem! Follow these steps to get started: 1) Create a new project and select the appropriate engineering domain. 2) Choose a simulation module. 3) Define your process flowsheet with unit operations and streams. 4) Select thermodynamic models and configure component lists. 5) Run the simulation and analyze results.', 'meta_description' => 'Quick start guide for new EnPharChem platform users.'],
            ['title' => 'Release Notes v1.0', 'slug' => 'release-notes-v1', 'category' => 'news', 'status' => 'draft', 'content' => 'EnPharChem Platform v1.0 Release Notes: New features include Process Simulation for Energy and Chemicals, Exchanger Design module with TEMA standards, Concurrent FEED engineering tools, APC integration, MES connectivity, and comprehensive APM dashboards. Performance improvements: 40% faster convergence, 60% reduced memory usage.', 'meta_description' => 'Release notes for EnPharChem Platform version 1.0.'],
        ];

        $authorId = $this->user['id'] ?? ($userIds[0] ?? 1);
        foreach ($cmsPages as $cp) {
            try {
                $exists = $this->db->fetch("SELECT id FROM cms_pages WHERE slug = ?", [$cp['slug']]);
                if ($exists) continue;
                $cp['author_id'] = $authorId;
                $cp['view_count'] = rand(10, 500);
                $this->db->insert('cms_pages', $cp);
                $loaded[] = "CMS Page: {$cp['title']}";
            } catch (Exception $e) { /* skip */ }
        }

        // ── Sample Marketing Materials ──
        $marketingMaterials = [
            ['title' => 'EnPharChem Platform Overview Brochure', 'description' => 'Comprehensive overview of the EnPharChem platform capabilities, including all module categories and key differentiators.', 'material_type' => 'brochure', 'category' => 'General', 'target_audience' => 'C-Level Executives, Engineering Managers', 'status' => 'published', 'file_size' => '4.2 MB'],
            ['title' => 'Process Simulation Technical Whitepaper', 'description' => 'Deep dive into our thermodynamic engine, equation of state implementations, and convergence algorithms benchmarked against Aspen HYSYS.', 'material_type' => 'whitepaper', 'category' => 'Process Simulation', 'target_audience' => 'Process Engineers, Technical Leads', 'status' => 'published', 'file_size' => '8.1 MB'],
            ['title' => 'LNG Plant Optimization Case Study', 'description' => 'How EnPharChem helped a major LNG operator reduce specific energy consumption by 12% through advanced MR cycle optimization.', 'material_type' => 'case_study', 'category' => 'Energy', 'target_audience' => 'Energy Sector Engineers', 'status' => 'approved', 'file_size' => '3.5 MB'],
            ['title' => 'Exchanger Design Module Datasheet', 'description' => 'Technical specifications for the Exchanger Design & Rating module. TEMA compliance, rating modes, and supported geometries.', 'material_type' => 'datasheet', 'category' => 'Heat Transfer', 'target_audience' => 'Mechanical Engineers, Heat Transfer Specialists', 'status' => 'published', 'file_size' => '1.8 MB'],
            ['title' => 'APC Implementation Guide', 'description' => 'Step-by-step guide for deploying Advanced Process Control using the EnPharChem platform. Model identification, controller tuning, and commissioning.', 'material_type' => 'whitepaper', 'category' => 'APC', 'target_audience' => 'Control Engineers', 'status' => 'published', 'file_size' => '6.3 MB'],
            ['title' => 'Refinery Digital Twin Presentation', 'description' => 'Executive presentation on building a digital twin for refinery operations using integrated simulation, MES, and APM modules.', 'material_type' => 'presentation', 'category' => 'Digital Transformation', 'target_audience' => 'CTOs, Digital Transformation Leads', 'status' => 'approved', 'file_size' => '12.5 MB'],
            ['title' => 'Pharmaceutical Manufacturing Case Study', 'description' => 'Batch process optimization for API production achieving 15% yield improvement and 30% cycle time reduction.', 'material_type' => 'case_study', 'category' => 'Pharmaceutical', 'target_audience' => 'Pharma Engineers, Process Chemists', 'status' => 'review', 'file_size' => '4.8 MB'],
            ['title' => 'Supply Chain Optimization Datasheet', 'description' => 'Features and capabilities of the petroleum supply chain planning module. LP/MILP optimization, crude evaluation, and production planning.', 'material_type' => 'datasheet', 'category' => 'Supply Chain', 'target_audience' => 'Planning Engineers, Supply Chain Managers', 'status' => 'published', 'file_size' => '2.1 MB'],
            ['title' => 'Asset Performance Management Brochure', 'description' => 'Overview of APM capabilities including predictive maintenance, reliability analysis, and equipment health monitoring.', 'material_type' => 'brochure', 'category' => 'APM', 'target_audience' => 'Reliability Engineers, Maintenance Managers', 'status' => 'published', 'file_size' => '5.0 MB'],
            ['title' => 'Digital Grid Management Whitepaper', 'description' => 'Technical overview of grid stability analysis, renewable integration modeling, and power system optimization features.', 'material_type' => 'whitepaper', 'category' => 'Grid Management', 'target_audience' => 'Power System Engineers', 'status' => 'draft', 'file_size' => '7.2 MB'],
            ['title' => 'Platform Architecture Infographic', 'description' => 'Visual overview of the EnPharChem platform architecture showing module interconnections and data flow.', 'material_type' => 'infographic', 'category' => 'General', 'target_audience' => 'All Technical Staff', 'status' => 'published', 'file_size' => '2.3 MB'],
            ['title' => 'MES Integration Video Demo', 'description' => 'Video walkthrough of MES module features including batch tracking, genealogy, and OPC connectivity.', 'material_type' => 'video', 'category' => 'MES', 'target_audience' => 'Production Managers, MES Engineers', 'status' => 'approved', 'file_size' => '85.0 MB'],
            ['title' => 'Ethylene Plant Optimization Case Study', 'description' => 'Case study showing 8% throughput increase on a world-scale ethylene plant through integrated simulation and APC.', 'material_type' => 'case_study', 'category' => 'Chemicals', 'target_audience' => 'Petrochemical Engineers', 'status' => 'published', 'file_size' => '4.0 MB'],
            ['title' => 'FEED Engineering Workflow Datasheet', 'description' => 'Concurrent FEED module capabilities for front-end engineering, cost estimation, and project scheduling.', 'material_type' => 'datasheet', 'category' => 'FEED', 'target_audience' => 'Project Engineers, EPC Contractors', 'status' => 'draft', 'file_size' => '1.5 MB'],
            ['title' => 'Industrial Data Fabric Presentation', 'description' => 'Overview of data integration capabilities connecting historian, MES, ERP, and simulation data sources.', 'material_type' => 'presentation', 'category' => 'Data Management', 'target_audience' => 'Data Engineers, IT Managers', 'status' => 'review', 'file_size' => '9.8 MB'],
        ];

        foreach ($marketingMaterials as $mm) {
            try {
                $exists = $this->db->fetch("SELECT id FROM marketing_materials WHERE title = ?", [$mm['title']]);
                if ($exists) continue;
                $mm['created_by'] = $authorId;
                $mm['download_count'] = rand(5, 200);
                $mm['file_url'] = '/assets/marketing/' . $this->slugify($mm['title']) . '.pdf';
                $this->db->insert('marketing_materials', $mm);
                $loaded[] = "Marketing: {$mm['title']}";
            } catch (Exception $e) { /* skip */ }
        }

        // ── Sample Training Courses ──
        $trainingCourses = [
            ['title' => 'Introduction to Process Simulation', 'description' => 'Fundamentals of steady-state process simulation including flowsheet construction, thermodynamic model selection, and convergence strategies.', 'category' => 'process_simulation', 'level' => 'beginner', 'duration_hours' => 16.0, 'instructor' => 'Dr. James Mitchell', 'prerequisites' => 'Basic chemical engineering knowledge', 'enrollment_count' => 45],
            ['title' => 'Advanced Thermodynamic Modeling', 'description' => 'Deep dive into EOS selection, activity coefficient models, electrolyte systems, and polymer thermodynamics.', 'category' => 'process_simulation', 'level' => 'advanced', 'duration_hours' => 24.0, 'instructor' => 'Dr. Sarah Chen', 'prerequisites' => 'Introduction to Process Simulation', 'enrollment_count' => 22],
            ['title' => 'Heat Exchanger Design Fundamentals', 'description' => 'Shell and tube exchanger design per TEMA standards. Thermal rating, mechanical design, and vibration analysis.', 'category' => 'exchanger_design', 'level' => 'intermediate', 'duration_hours' => 20.0, 'instructor' => 'Prof. Robert Williams', 'prerequisites' => 'Basic heat transfer knowledge', 'enrollment_count' => 38],
            ['title' => 'APC Concepts and Implementation', 'description' => 'Model Predictive Control theory, step testing, model identification, controller design, and commissioning best practices.', 'category' => 'apc', 'level' => 'intermediate', 'duration_hours' => 32.0, 'instructor' => 'Dr. Maria Rodriguez', 'prerequisites' => 'Process control fundamentals', 'enrollment_count' => 30],
            ['title' => 'MES for Process Industries', 'description' => 'Manufacturing Execution Systems for batch and continuous processes. ISA-95 integration, batch tracking, and genealogy.', 'category' => 'mes', 'level' => 'beginner', 'duration_hours' => 12.0, 'instructor' => 'Nancy Lee', 'prerequisites' => 'None', 'enrollment_count' => 50],
            ['title' => 'Supply Chain Planning and Optimization', 'description' => 'Petroleum supply chain modeling, crude oil evaluation, LP/MILP optimization, and production planning.', 'category' => 'supply_chain', 'level' => 'advanced', 'duration_hours' => 28.0, 'instructor' => 'Michael Thompson', 'prerequisites' => 'Linear programming basics', 'enrollment_count' => 18],
            ['title' => 'Asset Performance Management', 'description' => 'Predictive maintenance strategies, reliability centered maintenance, equipment health monitoring, and failure mode analysis.', 'category' => 'apm', 'level' => 'intermediate', 'duration_hours' => 18.0, 'instructor' => 'Paul Martin', 'prerequisites' => 'Basic maintenance concepts', 'enrollment_count' => 35],
            ['title' => 'Digital Grid Management Essentials', 'description' => 'Power system modeling, load flow analysis, renewable integration, and grid stability assessment.', 'category' => 'grid_mgmt', 'level' => 'expert', 'duration_hours' => 40.0, 'instructor' => 'Dr. Angela Zhang', 'prerequisites' => 'Power systems engineering', 'enrollment_count' => 12],
        ];

        $courseIds = [];
        foreach ($trainingCourses as $tc) {
            try {
                $exists = $this->db->fetch("SELECT id FROM training_courses WHERE title = ?", [$tc['title']]);
                if ($exists) {
                    $courseIds[] = $exists['id'];
                    continue;
                }
                $tc['status'] = 'active';
                $cid = $this->db->insert('training_courses', $tc);
                $courseIds[] = $cid;
                $loaded[] = "Course: {$tc['title']}";
            } catch (Exception $e) { /* skip */ }
        }

        // ── Sample Training Lessons ──
        $trainingLessons = [
            // Course 1: Intro to Process Simulation
            ['course_idx' => 0, 'title' => 'What is Process Simulation?', 'lesson_order' => 1, 'lesson_type' => 'video', 'duration_minutes' => 45, 'description' => 'Overview of process simulation, history, and applications in the process industries.'],
            ['course_idx' => 0, 'title' => 'Building Your First Flowsheet', 'lesson_order' => 2, 'lesson_type' => 'interactive', 'duration_minutes' => 60, 'description' => 'Hands-on exercise constructing a simple distillation flowsheet.'],
            ['course_idx' => 0, 'title' => 'Thermodynamic Model Selection', 'lesson_order' => 3, 'lesson_type' => 'document', 'duration_minutes' => 40, 'description' => 'Guidelines for selecting appropriate thermodynamic models for different systems.'],
            ['course_idx' => 0, 'title' => 'Convergence Strategies Quiz', 'lesson_order' => 4, 'lesson_type' => 'quiz', 'duration_minutes' => 20, 'description' => 'Assessment on flowsheet convergence methods and troubleshooting.'],
            // Course 2: Advanced Thermo
            ['course_idx' => 1, 'title' => 'Cubic Equations of State', 'lesson_order' => 1, 'lesson_type' => 'video', 'duration_minutes' => 55, 'description' => 'Peng-Robinson and SRK equations: derivation, mixing rules, and binary interaction parameters.'],
            ['course_idx' => 1, 'title' => 'Activity Coefficient Models', 'lesson_order' => 2, 'lesson_type' => 'document', 'duration_minutes' => 50, 'description' => 'NRTL, UNIQUAC, Wilson models for non-ideal liquid mixtures.'],
            ['course_idx' => 1, 'title' => 'Electrolyte Thermodynamics Lab', 'lesson_order' => 3, 'lesson_type' => 'lab', 'duration_minutes' => 90, 'description' => 'Lab exercise modeling an amine gas treating system with electrolyte NRTL.'],
            // Course 3: HX Design
            ['course_idx' => 2, 'title' => 'TEMA Standards Overview', 'lesson_order' => 1, 'lesson_type' => 'video', 'duration_minutes' => 35, 'description' => 'TEMA designations, shell types, tube configurations, and materials of construction.'],
            ['course_idx' => 2, 'title' => 'Thermal Rating Methods', 'lesson_order' => 2, 'lesson_type' => 'document', 'duration_minutes' => 60, 'description' => 'Bell-Delaware method, stream analysis, and correction factors for shell-side heat transfer.'],
            ['course_idx' => 2, 'title' => 'Mechanical Design Calculations', 'lesson_order' => 3, 'lesson_type' => 'lab', 'duration_minutes' => 75, 'description' => 'Tube sheet thickness, shell thickness, expansion joint design per ASME Section VIII.'],
            ['course_idx' => 2, 'title' => 'Vibration Analysis', 'lesson_order' => 4, 'lesson_type' => 'document', 'duration_minutes' => 45, 'description' => 'Flow-induced vibration: vortex shedding, fluid-elastic instability, and acoustic resonance.'],
            // Course 4: APC
            ['course_idx' => 3, 'title' => 'MPC Theory and Algorithms', 'lesson_order' => 1, 'lesson_type' => 'video', 'duration_minutes' => 60, 'description' => 'Model Predictive Control fundamentals: internal model, prediction horizon, and QP optimization.'],
            ['course_idx' => 3, 'title' => 'Plant Step Testing', 'lesson_order' => 2, 'lesson_type' => 'lab', 'duration_minutes' => 120, 'description' => 'Designing and executing step tests for APC model identification. Signal processing and data quality.'],
            ['course_idx' => 3, 'title' => 'Controller Commissioning', 'lesson_order' => 3, 'lesson_type' => 'interactive', 'duration_minutes' => 90, 'description' => 'Tuning APC controllers, setting CV constraints, and optimizing MV moves.'],
            // Course 5: MES
            ['course_idx' => 4, 'title' => 'ISA-95 Integration Model', 'lesson_order' => 1, 'lesson_type' => 'video', 'duration_minutes' => 40, 'description' => 'Understanding the ISA-95 hierarchy and its application to MES implementation.'],
            ['course_idx' => 4, 'title' => 'Batch Record Management', 'lesson_order' => 2, 'lesson_type' => 'document', 'duration_minutes' => 35, 'description' => 'Electronic batch records, recipe management, and regulatory compliance (FDA 21 CFR Part 11).'],
            // Course 6: Supply Chain
            ['course_idx' => 5, 'title' => 'LP Fundamentals for Planning', 'lesson_order' => 1, 'lesson_type' => 'video', 'duration_minutes' => 50, 'description' => 'Linear programming basics applied to refinery planning and crude oil evaluation.'],
            ['course_idx' => 5, 'title' => 'Crude Assay Analysis', 'lesson_order' => 2, 'lesson_type' => 'lab', 'duration_minutes' => 80, 'description' => 'Processing TBP curves, calculating product yields, and evaluating crude blend economics.'],
            ['course_idx' => 5, 'title' => 'Production Scheduling', 'lesson_order' => 3, 'lesson_type' => 'interactive', 'duration_minutes' => 70, 'description' => 'MILP-based scheduling for multi-product plants with tank constraints.'],
            // Course 7: APM
            ['course_idx' => 6, 'title' => 'Reliability Centered Maintenance', 'lesson_order' => 1, 'lesson_type' => 'video', 'duration_minutes' => 45, 'description' => 'RCM methodology, failure modes, and maintenance strategy selection.'],
            ['course_idx' => 6, 'title' => 'Predictive Analytics for Equipment', 'lesson_order' => 2, 'lesson_type' => 'document', 'duration_minutes' => 55, 'description' => 'Machine learning models for remaining useful life prediction and anomaly detection.'],
            ['course_idx' => 6, 'title' => 'APM Dashboard Configuration', 'lesson_order' => 3, 'lesson_type' => 'lab', 'duration_minutes' => 60, 'description' => 'Setting up KPI dashboards, alert thresholds, and equipment health scorecards.'],
            // Course 8: Grid
            ['course_idx' => 7, 'title' => 'Power Flow Analysis', 'lesson_order' => 1, 'lesson_type' => 'video', 'duration_minutes' => 55, 'description' => 'Newton-Raphson load flow, bus voltage regulation, and reactive power management.'],
            ['course_idx' => 7, 'title' => 'Renewable Integration Modeling', 'lesson_order' => 2, 'lesson_type' => 'lab', 'duration_minutes' => 90, 'description' => 'Solar and wind variability modeling, energy storage sizing, and grid frequency response.'],
            ['course_idx' => 7, 'title' => 'Grid Stability Assessment Quiz', 'lesson_order' => 3, 'lesson_type' => 'quiz', 'duration_minutes' => 25, 'description' => 'Assessment covering transient stability, voltage stability, and frequency regulation concepts.'],
        ];

        foreach ($trainingLessons as $tl) {
            try {
                $cidx = $tl['course_idx'];
                unset($tl['course_idx']);
                if (!isset($courseIds[$cidx])) continue;
                $tl['course_id'] = $courseIds[$cidx];
                $exists = $this->db->fetch("SELECT id FROM training_lessons WHERE course_id = ? AND title = ?", [$tl['course_id'], $tl['title']]);
                if ($exists) continue;
                $tl['status'] = 'published';
                $this->db->insert('training_lessons', $tl);
                $loaded[] = "Lesson: {$tl['title']}";
            } catch (Exception $e) { /* skip */ }
        }

        // ── Sample Cost Estimates ──
        $costEstimates = [
            ['name' => 'LNG Plant CAPEX Estimate', 'project_id' => $projectIds[0] ?? 1, 'total_cost' => 4500000000, 'currency' => 'USD'],
            ['name' => 'Ethylene Cracker Revamp', 'project_id' => $projectIds[1] ?? 1, 'total_cost' => 180000000, 'currency' => 'USD'],
            ['name' => 'API Plant Upgrade', 'project_id' => $projectIds[2] ?? 1, 'total_cost' => 25000000, 'currency' => 'USD'],
            ['name' => 'Offshore Platform FEED', 'project_id' => $projectIds[3] ?? 1, 'total_cost' => 2800000000, 'currency' => 'USD'],
            ['name' => 'Ammonia Plant HEN Retrofit', 'project_id' => $projectIds[4] ?? 1, 'total_cost' => 45000000, 'currency' => 'USD'],
        ];

        foreach ($costEstimates as $ce) {
            try {
                $exists = $this->db->fetch("SELECT id FROM cost_estimates WHERE name = ?", [$ce['name']]);
                if ($exists) continue;
                $ce['user_id'] = $firstUserId;
                $ce['estimate_type'] = 'preliminary';
                $ce['accuracy_range'] = '25';
                $this->db->insert('cost_estimates', $ce);
                $loaded[] = "Cost Estimate: {$ce['name']}";
            } catch (Exception $e) { /* skip */ }
        }

        // ── Sample Assets (APM) ──
        $assets = [
            ['name' => 'Centrifugal Compressor K-101', 'asset_type' => 'compressor', 'location' => 'Gas Processing Unit', 'manufacturer' => 'Siemens', 'model_number' => 'STC-SH', 'serial_number' => 'K101-2024-001', 'status' => 'operational'],
            ['name' => 'Shell & Tube Exchanger E-201', 'asset_type' => 'heat_exchanger', 'location' => 'Crude Distillation Unit', 'manufacturer' => 'Alfa Laval', 'model_number' => 'AlfaNova 76', 'serial_number' => 'E201-2023-045', 'status' => 'operational'],
            ['name' => 'Centrifugal Pump P-301A', 'asset_type' => 'pump', 'location' => 'Amine Treating Unit', 'manufacturer' => 'Flowserve', 'model_number' => 'HPX-S 6x4x13', 'serial_number' => 'P301A-2024-012', 'status' => 'operational'],
            ['name' => 'Distillation Column T-401', 'asset_type' => 'column', 'location' => 'Fractionation Area', 'manufacturer' => 'Koch-Glitsch', 'model_number' => 'Custom', 'serial_number' => 'T401-2022-001', 'status' => 'operational'],
            ['name' => 'Fired Heater H-501', 'asset_type' => 'heater', 'location' => 'Reformer Unit', 'manufacturer' => 'John Zink Hamworthy', 'model_number' => 'Ultra-Low NOx', 'serial_number' => 'H501-2023-003', 'status' => 'maintenance'],
            ['name' => 'Gas Turbine GT-601', 'asset_type' => 'turbine', 'location' => 'Power Generation', 'manufacturer' => 'GE', 'model_number' => 'LM6000', 'serial_number' => 'GT601-2024-001', 'status' => 'operational'],
            ['name' => 'Cooling Tower CT-701', 'asset_type' => 'cooling_tower', 'location' => 'Utilities Area', 'manufacturer' => 'SPX Cooling', 'model_number' => 'Marley NC', 'serial_number' => 'CT701-2023-002', 'status' => 'operational'],
            ['name' => 'Air Cooler AC-801', 'asset_type' => 'air_cooler', 'location' => 'NGL Recovery', 'manufacturer' => 'HAMON', 'model_number' => 'Horizontal Forced Draft', 'serial_number' => 'AC801-2024-005', 'status' => 'operational'],
            ['name' => 'Reciprocating Compressor K-901', 'asset_type' => 'compressor', 'location' => 'Hydrogen Unit', 'manufacturer' => 'Ariel', 'model_number' => 'JGK/4', 'serial_number' => 'K901-2023-008', 'status' => 'degraded'],
            ['name' => 'Storage Tank TK-1001', 'asset_type' => 'tank', 'location' => 'Tank Farm', 'manufacturer' => 'CB&I', 'model_number' => 'External Floating Roof', 'serial_number' => 'TK1001-2022-015', 'status' => 'operational'],
        ];

        foreach ($assets as $a) {
            try {
                $exists = $this->db->fetch("SELECT id FROM assets WHERE name = ?", [$a['name']]);
                if ($exists) continue;
                $a['installation_date'] = date('Y-m-d', strtotime('-' . rand(6, 36) . ' months'));
                $this->db->insert('assets', $a);
                $loaded[] = "Asset: {$a['name']}";
            } catch (Exception $e) { /* skip */ }
        }

        // ── Sample Chemical Components ──
        $chemicals = [
            ['name' => 'Methane', 'formula' => 'CH4', 'cas_number' => '74-82-8', 'molecular_weight' => 16.04, 'boiling_point' => -161.5, 'melting_point' => -182.5, 'critical_temperature' => -82.6, 'critical_pressure' => 45.99, 'acentric_factor' => 0.0115],
            ['name' => 'Ethane', 'formula' => 'C2H6', 'cas_number' => '74-84-0', 'molecular_weight' => 30.07, 'boiling_point' => -88.6, 'melting_point' => -183.3, 'critical_temperature' => 32.2, 'critical_pressure' => 48.72, 'acentric_factor' => 0.0995],
            ['name' => 'Propane', 'formula' => 'C3H8', 'cas_number' => '74-98-6', 'molecular_weight' => 44.10, 'boiling_point' => -42.1, 'melting_point' => -187.7, 'critical_temperature' => 96.7, 'critical_pressure' => 42.48, 'acentric_factor' => 0.1523],
            ['name' => 'Water', 'formula' => 'H2O', 'cas_number' => '7732-18-5', 'molecular_weight' => 18.02, 'boiling_point' => 100.0, 'melting_point' => 0.0, 'critical_temperature' => 374.0, 'critical_pressure' => 220.64, 'acentric_factor' => 0.3449],
            ['name' => 'Carbon Dioxide', 'formula' => 'CO2', 'cas_number' => '124-38-9', 'molecular_weight' => 44.01, 'boiling_point' => -78.5, 'melting_point' => -56.6, 'critical_temperature' => 31.0, 'critical_pressure' => 73.77, 'acentric_factor' => 0.2239],
            ['name' => 'Hydrogen Sulfide', 'formula' => 'H2S', 'cas_number' => '7783-06-4', 'molecular_weight' => 34.08, 'boiling_point' => -60.3, 'melting_point' => -85.5, 'critical_temperature' => 100.4, 'critical_pressure' => 89.63, 'acentric_factor' => 0.0942],
            ['name' => 'Nitrogen', 'formula' => 'N2', 'cas_number' => '7727-37-9', 'molecular_weight' => 28.01, 'boiling_point' => -195.8, 'melting_point' => -210.0, 'critical_temperature' => -147.0, 'critical_pressure' => 33.94, 'acentric_factor' => 0.0372],
            ['name' => 'Oxygen', 'formula' => 'O2', 'cas_number' => '7782-44-7', 'molecular_weight' => 32.00, 'boiling_point' => -183.0, 'melting_point' => -218.8, 'critical_temperature' => -118.6, 'critical_pressure' => 50.43, 'acentric_factor' => 0.0222],
            ['name' => 'Hydrogen', 'formula' => 'H2', 'cas_number' => '1333-74-0', 'molecular_weight' => 2.02, 'boiling_point' => -252.9, 'melting_point' => -259.2, 'critical_temperature' => -240.0, 'critical_pressure' => 12.97, 'acentric_factor' => -0.2160],
            ['name' => 'Ethylene', 'formula' => 'C2H4', 'cas_number' => '74-85-1', 'molecular_weight' => 28.05, 'boiling_point' => -103.7, 'melting_point' => -169.2, 'critical_temperature' => 9.2, 'critical_pressure' => 50.41, 'acentric_factor' => 0.0862],
        ];

        foreach ($chemicals as $ch) {
            try {
                $exists = $this->db->fetch("SELECT id FROM chemical_components WHERE name = ?", [$ch['name']]);
                if ($exists) continue;
                $this->db->insert('chemical_components', $ch);
                $loaded[] = "Chemical: {$ch['name']}";
            } catch (Exception $e) { /* skip */ }
        }

        $this->redirect('control-panel/data-management?msg=success&count=' . count($loaded));
    }

    /**
     * Reset all sample data
     */
    public function resetSampleData() {
        if (!$this->isPost()) {
            $this->redirect('control-panel/data-management');
            return;
        }

        try {
            // Delete sample users (keep admin)
            $this->db->delete('users', "username LIKE 'engineer%'");

            // Truncate control panel tables
            $tablesToTruncate = ['training_lessons', 'training_courses', 'marketing_materials', 'cms_pages', 'ad_users', 'ad_groups'];
            foreach ($tablesToTruncate as $table) {
                try {
                    $this->db->query("DELETE FROM {$table}");
                    $this->db->query("ALTER TABLE {$table} AUTO_INCREMENT = 1");
                } catch (Exception $e) { /* skip */ }
            }

            // Delete sample data from other tables (be careful not to delete seed data)
            try { $this->db->delete('cost_estimates', '1=1'); } catch (Exception $e) {}
            try {
                $this->db->delete('assets', "name LIKE 'Centrifugal%' OR name LIKE 'Shell & Tube%' OR name LIKE 'Centrifugal Pump%' OR name LIKE 'Distillation Column%' OR name LIKE 'Fired Heater%' OR name LIKE 'Gas Turbine%' OR name LIKE 'Cooling Tower%' OR name LIKE 'Air Cooler%' OR name LIKE 'Reciprocating%' OR name LIKE 'Storage Tank%'");
            } catch (Exception $e) {}

            // Delete sample simulations, projects (cascades handle related data)
            try { $this->db->delete('simulations', "name IN ('MR Cycle Base Case','Ethylene Furnace Profile','Crude Column Dynamic Startup','API Crystallizer Batch Cooling','NGL Recovery Turboexpander','Heat Exchanger Network Synthesis','Ammonia Converter Dynamic','LLDPE Reactor Steady State','Vacuum Column Revamp Case','CSP Plant Transient Response','Amine Sweetening Unit','Distillation Column Optimization','Compressor Surge Analysis','Wastewater Treatment Plant','Flare Header Network')"); } catch (Exception $e) {}
            try { $this->db->delete('projects', "name LIKE 'LNG Liquefaction%' OR name LIKE 'Ethylene Cracker%' OR name LIKE 'Pharmaceutical API%' OR name LIKE 'Offshore Gas%' OR name LIKE 'Ammonia Plant%' OR name LIKE 'Refinery Crude%' OR name LIKE 'Polyethylene Reactor%' OR name LIKE 'Solar Thermal%'"); } catch (Exception $e) {}

            // Delete sample chemicals
            try { $this->db->delete('chemical_components', "name IN ('Methane','Ethane','Propane','Water','Carbon Dioxide','Hydrogen Sulfide','Nitrogen','Oxygen','Hydrogen','Ethylene')"); } catch (Exception $e) {}

            $this->redirect('control-panel/data-management?msg=reset');
        } catch (Exception $e) {
            $this->redirect('control-panel/data-management?msg=error');
        }
    }

    // ── Helpers ──

    private function safeCount($table) {
        try {
            $row = $this->db->fetch("SELECT COUNT(*) as cnt FROM {$table}");
            return $row['cnt'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    private function slugify($text) {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9-]/', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        return trim($text, '-');
    }
}
