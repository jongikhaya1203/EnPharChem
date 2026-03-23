<?php
/**
 * EnPharChem - Petroleum Supply Chain Controller
 * Petroleum supply chain planning and scheduling modules
 */

class PetroleumSCController extends BaseController {

    private $categorySlug = 'petroleum-supply-chain';

    public function index() {
        $category = $this->db->fetch(
            "SELECT * FROM module_categories WHERE slug = ?",
            [$this->categorySlug]
        );

        $modules = $this->getModulesByCategory($this->categorySlug);

        $this->view('modules/category', [
            'pageTitle' => 'Petroleum Supply Chain',
            'category' => $category,
            'modules' => $modules,
        ]);
    }

    public function module() {
        $slug = $_GET['slug'] ?? '';
        if (empty($slug)) {
            $this->redirect('petroleum-supply-chain');
        }

        $module = $this->getModule($slug);
        if (!$module) {
            $this->redirect('petroleum-supply-chain');
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
