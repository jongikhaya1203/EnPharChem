<?php
/**
 * EnPharChem - Concurrent FEED Controller
 * Front-End Engineering Design modules
 */

class ConcurrentFeedController extends BaseController {

    private $categorySlug = 'concurrent-feed';

    public function index() {
        $category = $this->db->fetch(
            "SELECT * FROM module_categories WHERE slug = ?",
            [$this->categorySlug]
        );

        $modules = $this->getModulesByCategory($this->categorySlug);

        $this->view('modules/category', [
            'pageTitle' => 'Concurrent FEED',
            'category' => $category,
            'modules' => $modules,
        ]);
    }

    public function module() {
        $slug = $_GET['slug'] ?? '';
        if (empty($slug)) {
            $this->redirect('concurrent-feed');
        }

        $module = $this->getModule($slug);
        if (!$module) {
            $this->redirect('concurrent-feed');
        }

        $projects = $this->db->fetchAll(
            "SELECT * FROM projects WHERE user_id = ? ORDER BY updated_at DESC",
            [$this->user['id']]
        );

        $this->view('modules/workspace', [
            'pageTitle' => $module['name'],
            'module' => $module,
            'projects' => $projects,
        ]);
    }
}
