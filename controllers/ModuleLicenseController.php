<?php
/**
 * EnPharChem - Module License Manager Controller
 * Manages which modules/categories have licenses granted (bypass license required badge)
 */

class ModuleLicenseController extends BaseController {

    public function index() {
        // Ensure license_waived column exists
        try {
            $this->db->query("ALTER TABLE modules ADD COLUMN license_waived TINYINT(1) DEFAULT 0");
        } catch (Exception $e) {}

        if ($this->isPost()) {
            $action = $_POST['action'] ?? '';

            if ($action === 'grant_module') {
                $moduleId = (int)($_POST['module_id'] ?? 0);
                if ($moduleId) {
                    $this->db->update('modules', ['license_waived' => 1], 'id = ?', [$moduleId]);
                }
            } elseif ($action === 'revoke_module') {
                $moduleId = (int)($_POST['module_id'] ?? 0);
                if ($moduleId) {
                    $this->db->update('modules', ['license_waived' => 0], 'id = ?', [$moduleId]);
                }
            } elseif ($action === 'grant_category') {
                $categoryId = (int)($_POST['category_id'] ?? 0);
                if ($categoryId) {
                    $this->db->update('modules', ['license_waived' => 1], 'category_id = ?', [$categoryId]);
                }
            } elseif ($action === 'revoke_category') {
                $categoryId = (int)($_POST['category_id'] ?? 0);
                if ($categoryId) {
                    $this->db->update('modules', ['license_waived' => 0], 'category_id = ?', [$categoryId]);
                }
            } elseif ($action === 'grant_all') {
                $this->db->query("UPDATE modules SET license_waived = 1");
            } elseif ($action === 'revoke_all') {
                $this->db->query("UPDATE modules SET license_waived = 0");
            }

            $this->redirect('control-panel/module-licenses?msg=updated');
            return;
        }

        // Load all categories with their modules
        $categories = $this->db->fetchAll(
            "SELECT mc.*, COUNT(m.id) as total_modules,
                    SUM(CASE WHEN m.license_waived = 1 THEN 1 ELSE 0 END) as waived_count
             FROM module_categories mc
             LEFT JOIN modules m ON m.category_id = mc.id AND m.is_active = 1
             WHERE mc.is_active = 1
             GROUP BY mc.id
             ORDER BY mc.sort_order"
        ) ?: [];

        $modules = $this->db->fetchAll(
            "SELECT m.*, mc.name as category_name, mc.slug as category_slug
             FROM modules m
             JOIN module_categories mc ON m.category_id = mc.id
             WHERE m.is_active = 1
             ORDER BY mc.sort_order, m.sort_order"
        ) ?: [];

        // Group modules by category_id
        $modulesByCategory = [];
        foreach ($modules as $m) {
            $modulesByCategory[$m['category_id']][] = $m;
        }

        $stats = [
            'total_modules' => count($modules),
            'granted' => count(array_filter($modules, fn($m) => !empty($m['license_waived']))),
            'required' => count(array_filter($modules, fn($m) => empty($m['license_waived']))),
            'categories' => count($categories),
        ];

        $this->view('control-panel/module-licenses', [
            'pageTitle' => 'Module License Manager',
            'categories' => $categories,
            'modulesByCategory' => $modulesByCategory,
            'stats' => $stats,
        ]);
    }
}
