<?php
/**
 * EnPharChem - Module Controller
 * Handles module browsing, category listing, and module launching
 */

class ModuleController extends BaseController {

    public function index() {
        $categories = $this->getModuleCategories();

        $this->view('modules/index', [
            'pageTitle' => 'Module Categories',
            'categories' => $categories,
        ]);
    }

    public function category() {
        $slug = $_GET['slug'] ?? '';
        if (empty($slug)) {
            $this->redirect('modules');
        }

        $category = $this->db->fetch(
            "SELECT * FROM module_categories WHERE slug = ? AND is_active = 1",
            [$slug]
        );

        if (!$category) {
            $this->redirect('modules');
        }

        $modules = $this->getModulesByCategory($slug);

        $this->view('modules/category', [
            'pageTitle' => $category['name'],
            'category' => $category,
            'modules' => $modules,
        ]);
    }

    public function view_module() {
        $slug = $_GET['slug'] ?? '';
        if (empty($slug)) {
            $this->redirect('modules');
        }

        $module = $this->getModule($slug);
        if (!$module) {
            $this->redirect('modules');
        }

        $relatedModules = $this->getModulesByCategory($module['category_slug']);

        $this->view('modules/view', [
            'pageTitle' => $module['name'],
            'module' => $module,
            'relatedModules' => $relatedModules,
        ]);
    }

    public function launch() {
        $slug = $_GET['slug'] ?? '';
        $projectId = $_GET['project_id'] ?? '';

        if (empty($slug)) {
            $this->redirect('modules');
        }

        $module = $this->getModule($slug);
        if (!$module) {
            $this->redirect('modules');
        }

        if (empty($projectId)) {
            $projects = $this->db->fetchAll(
                "SELECT * FROM projects WHERE user_id = ? ORDER BY updated_at DESC",
                [$this->user['id']]
            );
            $this->view('modules/select_project', [
                'pageTitle' => 'Select Project - ' . $module['name'],
                'module' => $module,
                'projects' => $projects,
            ]);
            return;
        }

        $project = $this->db->fetch(
            "SELECT * FROM projects WHERE id = ? AND user_id = ?",
            [$projectId, $this->user['id']]
        );

        if (!$project) {
            $this->redirect('modules');
        }

        $this->view('modules/workspace', [
            'pageTitle' => $module['name'] . ' - Workspace',
            'module' => $module,
            'project' => $project,
        ]);
    }
}
