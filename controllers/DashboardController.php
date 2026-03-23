<?php
/**
 * EnPharChem - Dashboard Controller
 */

class DashboardController extends BaseController {

    public function index() {
        $categories = $this->getModuleCategories();

        $stats = [
            'total_projects' => $this->db->fetch(
                "SELECT COUNT(*) as count FROM projects WHERE user_id = ?",
                [$this->user['id']]
            )['count'],
            'active_simulations' => $this->db->fetch(
                "SELECT COUNT(*) as count FROM simulations WHERE user_id = ? AND status IN ('running','draft')",
                [$this->user['id']]
            )['count'],
            'completed_simulations' => $this->db->fetch(
                "SELECT COUNT(*) as count FROM simulations WHERE user_id = ? AND status = 'completed'",
                [$this->user['id']]
            )['count'],
            'total_modules' => $this->db->fetch(
                "SELECT COUNT(*) as count FROM modules WHERE is_active = 1"
            )['count'],
            'available_modules' => $this->db->fetch(
                "SELECT COUNT(*) as count FROM modules WHERE is_active = 1"
            )['count'],
        ];

        $recentProjects = $this->db->fetchAll(
            "SELECT * FROM projects WHERE user_id = ? ORDER BY updated_at DESC LIMIT 5",
            [$this->user['id']]
        );

        $recentSimulations = $this->db->fetchAll(
            "SELECT s.*, m.name as module_name, p.name as project_name
             FROM simulations s
             JOIN modules m ON s.module_id = m.id
             JOIN projects p ON s.project_id = p.id
             WHERE s.user_id = ?
             ORDER BY s.updated_at DESC LIMIT 10",
            [$this->user['id']]
        );

        $this->view('dashboard/index', [
            'pageTitle' => 'Dashboard',
            'categories' => $categories,
            'stats' => $stats,
            'recentProjects' => $recentProjects,
            'recentSimulations' => $recentSimulations,
        ]);
    }
}
