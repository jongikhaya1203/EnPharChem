<?php
/**
 * EnPharChem - Digital Grid Management Controller
 * Digital grid management and optimization modules
 */

class DigitalGridController extends BaseController {

    private $categorySlug = 'digital-grid-mgmt';

    public function index() {
        $category = $this->db->fetch(
            "SELECT * FROM module_categories WHERE slug = ?",
            [$this->categorySlug]
        );

        $modules = $this->getModulesByCategory($this->categorySlug);

        $this->view('modules/category', [
            'pageTitle' => 'Digital Grid Management',
            'category' => $category,
            'modules' => $modules,
        ]);
    }

    public function module() {
        $slug = $_GET['slug'] ?? '';
        if (empty($slug)) {
            $this->redirect('digital-grid-mgmt');
        }

        $module = $this->getModule($slug);
        if (!$module) {
            $this->redirect('digital-grid-mgmt');
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
