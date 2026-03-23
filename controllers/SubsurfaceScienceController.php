<?php
/**
 * EnPharChem - Subsurface Science Controller
 * Subsurface modeling and analysis modules
 */

class SubsurfaceScienceController extends BaseController {

    private $categorySlug = 'subsurface-science';

    public function index() {
        $category = $this->db->fetch(
            "SELECT * FROM module_categories WHERE slug = ?",
            [$this->categorySlug]
        );

        $modules = $this->getModulesByCategory($this->categorySlug);

        $this->view('modules/category', [
            'pageTitle' => 'Subsurface Science & Engineering',
            'category' => $category,
            'modules' => $modules,
        ]);
    }

    public function module() {
        $slug = $_GET['slug'] ?? '';
        if (empty($slug)) {
            $this->redirect('subsurface-science');
        }

        $module = $this->getModule($slug);
        if (!$module) {
            $this->redirect('subsurface-science');
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
