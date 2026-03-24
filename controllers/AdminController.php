<?php
/**
 * EnPharChem - Admin Controller
 * Administrative dashboard, user management, module management, and settings
 */

class AdminController extends BaseController {

    public function __construct() {
        parent::__construct();
        $this->requireRole(['admin']);
    }

    public function index() {
        $systemStats = [
            'total_users' => $this->db->fetch(
                "SELECT COUNT(*) as count FROM users"
            )['count'],
            'active_users' => $this->db->fetch(
                "SELECT COUNT(*) as count FROM users WHERE is_active = 1"
            )['count'],
            'total_projects' => $this->db->fetch(
                "SELECT COUNT(*) as count FROM projects"
            )['count'],
            'total_simulations' => $this->db->fetch(
                "SELECT COUNT(*) as count FROM simulations"
            )['count'],
            'running_simulations' => $this->db->fetch(
                "SELECT COUNT(*) as count FROM simulations WHERE status = 'running'"
            )['count'],
            'completed_simulations' => $this->db->fetch(
                "SELECT COUNT(*) as count FROM simulations WHERE status = 'completed'"
            )['count'],
            'total_modules' => $this->db->fetch(
                "SELECT COUNT(*) as count FROM modules"
            )['count'],
            'active_modules' => $this->db->fetch(
                "SELECT COUNT(*) as count FROM modules WHERE is_active = 1"
            )['count'],
        ];

        $recentUsers = $this->db->fetchAll(
            "SELECT id, username, email, first_name, last_name, role, is_active, created_at, last_login
             FROM users ORDER BY created_at DESC LIMIT 10"
        );

        $recentSimulations = $this->db->fetchAll(
            "SELECT s.*, u.username, m.name as module_name, p.name as project_name
             FROM simulations s
             JOIN users u ON s.user_id = u.id
             JOIN modules m ON s.module_id = m.id
             JOIN projects p ON s.project_id = p.id
             ORDER BY s.updated_at DESC LIMIT 10"
        );

        $this->view('admin/index', [
            'pageTitle' => 'Admin Dashboard',
            'systemStats' => $systemStats,
            'recentUsers' => $recentUsers,
            'recentSimulations' => $recentSimulations,
        ]);
    }

    public function users() {
        if ($this->isPost()) {
            $action = $_POST['action'] ?? '';
            $userId = $_POST['user_id'] ?? '';

            if (!empty($userId) && in_array($action, ['enable', 'disable'])) {
                $isActive = ($action === 'enable') ? 1 : 0;
                $this->db->update('users', [
                    'is_active' => $isActive,
                ], 'id = ?', [$userId]);
            }

            $this->redirect('admin/users');
        }

        $role = $this->getParam('role', '');
        $search = $this->getParam('search', '');

        $sql = "SELECT id, username, email, first_name, last_name, company, role,
                       license_type, is_active, created_at, last_login
                FROM users WHERE 1=1";
        $params = [];

        if (!empty($role)) {
            $sql .= " AND role = ?";
            $params[] = $role;
        }

        if (!empty($search)) {
            $sql .= " AND (username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY created_at DESC";

        $users = $this->db->fetchAll($sql, $params);

        $this->view('admin/users', [
            'pageTitle' => 'User Management',
            'users' => $users,
            'filterRole' => $role,
            'searchQuery' => $search,
        ]);
    }

    public function modules() {
        if ($this->isPost()) {
            $action = $_POST['action'] ?? '';
            $moduleId = $_POST['module_id'] ?? '';

            if ($action === 'update_module' && !empty($moduleId)) {
                $updateData = [];
                if (!empty($_POST['module_name'])) $updateData['name'] = $_POST['module_name'];
                if (!empty($_POST['module_version'])) $updateData['version'] = $_POST['module_version'];
                if (!empty($_POST['module_icon'])) $updateData['icon'] = $_POST['module_icon'];
                if (!empty($_POST['license_required'])) $updateData['license_required'] = $_POST['license_required'];
                if (!empty($updateData)) {
                    $this->db->update('modules', $updateData, 'id = ?', [$moduleId]);
                }
            } elseif (!empty($moduleId)) {
                if ($action === 'activate') {
                    $this->db->update('modules', ['is_active' => 1], 'id = ?', [$moduleId]);
                } elseif ($action === 'deactivate') {
                    $this->db->update('modules', ['is_active' => 0], 'id = ?', [$moduleId]);
                }
            }

            $this->redirect('admin/modules');
        }

        $modules = $this->db->fetchAll(
            "SELECT m.*, mc.name as category_name, mc.slug as category_slug,
                    (SELECT COUNT(*) FROM simulations s WHERE s.module_id = m.id) as usage_count
             FROM modules m
             JOIN module_categories mc ON m.category_id = mc.id
             ORDER BY mc.sort_order, m.sort_order"
        );

        $categories = $this->getModuleCategories();

        $this->view('admin/modules', [
            'pageTitle' => 'Module Management',
            'modules' => $modules,
            'categories' => $categories,
        ]);
    }

    public function settings() {
        // Auto-create settings table if missing
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(255) NOT NULL UNIQUE,
                setting_value TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB");
        } catch (Exception $e) {}

        if ($this->isPost()) {
            $settingsKeys = [
                'site_name', 'site_description', 'maintenance_mode',
                'default_license_type', 'max_concurrent_simulations',
                'session_timeout', 'allow_registration',
            ];

            foreach ($settingsKeys as $key) {
                $value = $_POST[$key] ?? null;
                if ($value !== null) {
                    $existing = $this->db->fetch(
                        "SELECT id FROM settings WHERE setting_key = ?",
                        [$key]
                    );
                    if ($existing) {
                        $this->db->update('settings', [
                            'setting_value' => $value,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ], 'setting_key = ?', [$key]);
                    } else {
                        $this->db->insert('settings', [
                            'setting_key' => $key,
                            'setting_value' => $value,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }

            $this->redirect('admin/settings');
        }

        $settingsRows = $this->db->fetchAll("SELECT * FROM settings ORDER BY setting_key");
        $settings = [];
        foreach ($settingsRows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        $this->view('admin/settings', [
            'pageTitle' => 'System Settings',
            'settings' => $settings,
        ]);
    }
}
