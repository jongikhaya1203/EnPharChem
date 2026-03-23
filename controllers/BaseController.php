<?php
/**
 * EnPharChem - Base Controller
 */

class BaseController {
    protected $db;
    protected $user;

    public function __construct() {
        $this->db = Database::getInstance();
        if (isset($_SESSION['user_id'])) {
            $this->user = $this->db->fetch(
                "SELECT * FROM users WHERE id = ?",
                [$_SESSION['user_id']]
            );
        }
    }

    protected function view($view, $data = []) {
        $data['user'] = $this->user;
        $data['appName'] = APP_NAME;
        $data['appVersion'] = APP_VERSION;
        extract($data);
        ob_start();
        include VIEWS_PATH . '/' . $view . '.php';
        $content = ob_get_clean();
        include VIEWS_PATH . '/layouts/main.php';
    }

    protected function viewWithoutLayout($view, $data = []) {
        $data['user'] = $this->user;
        extract($data);
        include VIEWS_PATH . '/' . $view . '.php';
    }

    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($path) {
        header('Location: ' . APP_URL . '/' . $path);
        exit;
    }

    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function getParam($key, $default = null) {
        return $_GET[$key] ?? $_POST[$key] ?? $default;
    }

    protected function requireRole($roles) {
        if (!is_array($roles)) $roles = [$roles];
        if (!in_array($this->user['role'], $roles)) {
            http_response_code(403);
            include VIEWS_PATH . '/errors/403.php';
            exit;
        }
    }

    protected function getModuleCategories() {
        return $this->db->fetchAll(
            "SELECT mc.*, COUNT(m.id) as module_count
             FROM module_categories mc
             LEFT JOIN modules m ON m.category_id = mc.id AND m.is_active = 1
             WHERE mc.is_active = 1
             GROUP BY mc.id
             ORDER BY mc.sort_order"
        );
    }

    protected function getModulesByCategory($categorySlug) {
        return $this->db->fetchAll(
            "SELECT m.* FROM modules m
             JOIN module_categories mc ON m.category_id = mc.id
             WHERE mc.slug = ? AND m.is_active = 1
             ORDER BY m.sort_order",
            [$categorySlug]
        );
    }

    protected function getModule($slug) {
        return $this->db->fetch(
            "SELECT m.*, mc.name as category_name, mc.slug as category_slug
             FROM modules m
             JOIN module_categories mc ON m.category_id = mc.id
             WHERE m.slug = ?",
            [$slug]
        );
    }
}
