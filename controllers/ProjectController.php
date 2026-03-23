<?php
/**
 * EnPharChem - Project Controller
 * CRUD operations for user projects
 */

class ProjectController extends BaseController {

    public function index() {
        $projects = $this->db->fetchAll(
            "SELECT p.*,
                    (SELECT COUNT(*) FROM simulations s WHERE s.project_id = p.id) as simulation_count
             FROM projects p
             WHERE p.user_id = ?
             ORDER BY p.updated_at DESC",
            [$this->user['id']]
        );

        $this->view('projects/index', [
            'pageTitle' => 'My Projects',
            'projects' => $projects,
        ]);
    }

    public function create() {
        $error = '';

        if ($this->isPost()) {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $type = trim($_POST['type'] ?? 'general');

            if (empty($name)) {
                $error = 'Project name is required.';
            } else {
                $this->db->insert('projects', [
                    'user_id' => $this->user['id'],
                    'name' => $name,
                    'description' => $description,
                    'type' => $type,
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $this->redirect('projects');
            }
        }

        $this->view('projects/create', [
            'pageTitle' => 'Create Project',
            'error' => $error,
        ]);
    }

    public function view_project() {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            $this->redirect('projects');
        }

        $project = $this->db->fetch(
            "SELECT * FROM projects WHERE id = ? AND user_id = ?",
            [$id, $this->user['id']]
        );

        if (!$project) {
            $this->redirect('projects');
        }

        $simulations = $this->db->fetchAll(
            "SELECT s.*, m.name as module_name, m.slug as module_slug
             FROM simulations s
             JOIN modules m ON s.module_id = m.id
             WHERE s.project_id = ?
             ORDER BY s.updated_at DESC",
            [$id]
        );

        $this->view('projects/view', [
            'pageTitle' => $project['name'],
            'project' => $project,
            'simulations' => $simulations,
        ]);
    }

    public function edit() {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            $this->redirect('projects');
        }

        $project = $this->db->fetch(
            "SELECT * FROM projects WHERE id = ? AND user_id = ?",
            [$id, $this->user['id']]
        );

        if (!$project) {
            $this->redirect('projects');
        }

        $error = '';

        if ($this->isPost()) {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $type = trim($_POST['type'] ?? $project['type']);
            $status = trim($_POST['status'] ?? $project['status']);

            if (empty($name)) {
                $error = 'Project name is required.';
            } else {
                $this->db->update('projects', [
                    'name' => $name,
                    'description' => $description,
                    'type' => $type,
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                ], 'id = ? AND user_id = ?', [$id, $this->user['id']]);

                $this->redirect('projects/view?id=' . $id);
            }
        }

        $this->view('projects/edit', [
            'pageTitle' => 'Edit Project - ' . $project['name'],
            'project' => $project,
            'error' => $error,
        ]);
    }

    public function delete() {
        if (!$this->isPost()) {
            $this->redirect('projects');
        }

        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            $this->redirect('projects');
        }

        $project = $this->db->fetch(
            "SELECT * FROM projects WHERE id = ? AND user_id = ?",
            [$id, $this->user['id']]
        );

        if ($project) {
            $this->db->delete('simulations', 'project_id = ? AND user_id = ?', [$id, $this->user['id']]);
            $this->db->delete('projects', 'id = ? AND user_id = ?', [$id, $this->user['id']]);
        }

        $this->redirect('projects');
    }
}
