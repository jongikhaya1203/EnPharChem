<?php
/**
 * EnPharChem - API Controller
 * JSON API endpoints for AJAX requests and external integrations
 */

class ApiController extends BaseController {

    public function simulations() {
        $projectId = $this->getParam('project_id');
        $status = $this->getParam('status');

        $sql = "SELECT s.id, s.name, s.status, s.created_at, s.updated_at,
                       m.name as module_name, m.slug as module_slug,
                       p.name as project_name
                FROM simulations s
                JOIN modules m ON s.module_id = m.id
                JOIN projects p ON s.project_id = p.id
                WHERE s.user_id = ?";
        $params = [$this->user['id']];

        if (!empty($projectId)) {
            $sql .= " AND s.project_id = ?";
            $params[] = $projectId;
        }

        if (!empty($status)) {
            $sql .= " AND s.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY s.updated_at DESC";

        $limit = intval($this->getParam('limit', 50));
        $offset = intval($this->getParam('offset', 0));
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $simulations = $this->db->fetchAll($sql, $params);

        $this->json([
            'success' => true,
            'data' => $simulations,
            'meta' => [
                'limit' => $limit,
                'offset' => $offset,
            ],
        ]);
    }

    public function components() {
        $query = trim($this->getParam('q', ''));
        $category = $this->getParam('category', '');

        if (strlen($query) < 2) {
            $this->json([
                'success' => true,
                'data' => [],
                'message' => 'Query must be at least 2 characters.',
            ]);
            return;
        }

        $sql = "SELECT * FROM chemical_components WHERE
                (name LIKE ? OR formula LIKE ? OR cas_number LIKE ?)";
        $params = ["%{$query}%", "%{$query}%", "%{$query}%"];

        if (!empty($category)) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }

        $sql .= " ORDER BY name LIMIT 50";

        $components = $this->db->fetchAll($sql, $params);

        $this->json([
            'success' => true,
            'data' => $components,
            'count' => count($components),
        ]);
    }

    public function flowsheet() {
        $simulationId = $this->getParam('simulation_id');

        if (empty($simulationId)) {
            $this->json(['success' => false, 'error' => 'Simulation ID is required.'], 400);
            return;
        }

        $simulation = $this->db->fetch(
            "SELECT * FROM simulations WHERE id = ? AND user_id = ?",
            [$simulationId, $this->user['id']]
        );

        if (!$simulation) {
            $this->json(['success' => false, 'error' => 'Simulation not found.'], 404);
            return;
        }

        if ($this->isPost()) {
            // Save flowsheet data
            $flowsheetJson = file_get_contents('php://input');
            $flowsheetData = json_decode($flowsheetJson, true);

            if ($flowsheetData === null && json_last_error() !== JSON_ERROR_NONE) {
                $this->json(['success' => false, 'error' => 'Invalid JSON data.'], 400);
                return;
            }

            $this->db->update('simulations', [
                'input_data' => json_encode($flowsheetData),
                'updated_at' => date('Y-m-d H:i:s'),
            ], 'id = ? AND user_id = ?', [$simulationId, $this->user['id']]);

            $this->json([
                'success' => true,
                'message' => 'Flowsheet saved successfully.',
            ]);
        } else {
            // Load flowsheet data
            $inputData = json_decode($simulation['input_data'], true) ?: [];

            $this->json([
                'success' => true,
                'data' => $inputData,
            ]);
        }
    }

    public function dashboardStats() {
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
        ];

        $recentActivity = $this->db->fetchAll(
            "SELECT s.name, s.status, s.updated_at, m.name as module_name, p.name as project_name
             FROM simulations s
             JOIN modules m ON s.module_id = m.id
             JOIN projects p ON s.project_id = p.id
             WHERE s.user_id = ?
             ORDER BY s.updated_at DESC LIMIT 10",
            [$this->user['id']]
        );

        $this->json([
            'success' => true,
            'stats' => $stats,
            'recentActivity' => $recentActivity,
        ]);
    }
}
