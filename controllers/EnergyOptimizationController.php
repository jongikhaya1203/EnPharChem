<?php
/**
 * EnPharChem - Energy Optimization Controller
 * Energy optimization and analysis modules
 */

class EnergyOptimizationController extends BaseController {

    private $categorySlug = 'energy-optimization';

    public function index() {
        $category = $this->db->fetch(
            "SELECT * FROM module_categories WHERE slug = ?",
            [$this->categorySlug]
        );

        $modules = $this->getModulesByCategory($this->categorySlug);

        $this->view('modules/category', [
            'pageTitle' => 'Energy Optimization',
            'category' => $category,
            'modules' => $modules,
        ]);
    }

    public function module() {
        $slug = $_GET['slug'] ?? '';
        if (empty($slug)) {
            $this->redirect('energy-optimization');
        }

        $module = $this->getModule($slug);
        if (!$module) {
            $this->redirect('energy-optimization');
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
