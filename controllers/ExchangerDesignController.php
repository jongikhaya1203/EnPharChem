<?php
/**
 * EnPharChem - Exchanger Design & Rating Controller
 * Handles heat exchanger design and rating modules
 */

class ExchangerDesignController extends BaseController {

    private $categorySlug = 'exchanger-design';

    public function index() {
        $category = $this->db->fetch(
            "SELECT * FROM module_categories WHERE slug = ?",
            [$this->categorySlug]
        );

        $modules = $this->getModulesByCategory($this->categorySlug);

        $this->view('modules/category', [
            'pageTitle' => 'Exchanger Design & Rating',
            'category' => $category,
            'modules' => $modules,
        ]);
    }

    public function module() {
        $slug = $_GET['slug'] ?? '';
        if (empty($slug)) {
            $this->redirect('exchanger-design');
        }

        $module = $this->getModule($slug);
        if (!$module) {
            $this->redirect('exchanger-design');
        }

        $projects = $this->db->fetchAll(
            "SELECT * FROM projects WHERE user_id = ? ORDER BY updated_at DESC",
            [$this->user['id']]
        );

        $this->view('modules/workspace', [
            'pageTitle' => $module['name'],
            'module' => $module,
            'projects' => $projects,
            'workspaceType' => 'exchanger',
        ]);
    }
}
