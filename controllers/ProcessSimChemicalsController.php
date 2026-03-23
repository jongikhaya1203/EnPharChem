<?php
/**
 * EnPharChem - Process Simulation for Chemicals Controller
 * Handles chemical process simulation modules (benchmarked against Aspen Plus)
 */

class ProcessSimChemicalsController extends BaseController {

    private $categorySlug = 'process-sim-chemicals';

    public function index() {
        $category = $this->db->fetch(
            "SELECT * FROM module_categories WHERE slug = ?",
            [$this->categorySlug]
        );

        $modules = $this->getModulesByCategory($this->categorySlug);

        $this->view('modules/category', [
            'pageTitle' => 'Process Simulation for Chemicals',
            'category' => $category,
            'modules' => $modules,
        ]);
    }

    public function plus() {
        $module = $this->db->fetch(
            "SELECT m.*, mc.name as category_name, mc.slug as category_slug
             FROM modules m
             JOIN module_categories mc ON m.category_id = mc.id
             WHERE mc.slug = ? AND m.slug LIKE '%plus%'",
            [$this->categorySlug]
        );

        $projects = $this->db->fetchAll(
            "SELECT * FROM projects WHERE user_id = ? ORDER BY updated_at DESC",
            [$this->user['id']]
        );

        $recentSimulations = $this->db->fetchAll(
            "SELECT s.*, p.name as project_name
             FROM simulations s
             JOIN projects p ON s.project_id = p.id
             WHERE s.user_id = ? AND s.module_id = ?
             ORDER BY s.updated_at DESC LIMIT 10",
            [$this->user['id'], $module['id'] ?? 0]
        );

        $this->view('modules/workspace', [
            'pageTitle' => 'EnPharChem Plus',
            'module' => $module,
            'projects' => $projects,
            'recentSimulations' => $recentSimulations,
        ]);
    }

    public function module() {
        $slug = $_GET['slug'] ?? '';
        if (empty($slug)) {
            $this->redirect('process-sim-chemicals');
        }

        $module = $this->getModule($slug);
        if (!$module) {
            $this->redirect('process-sim-chemicals');
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
