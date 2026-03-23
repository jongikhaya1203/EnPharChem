<?php
/**
 * EnPharChem - Simulation Controller
 * Simulation management, execution, and results viewing
 */

class SimulationController extends BaseController {

    public function index() {
        $simulations = $this->db->fetchAll(
            "SELECT s.*, m.name as module_name, m.slug as module_slug,
                    p.name as project_name
             FROM simulations s
             JOIN modules m ON s.module_id = m.id
             JOIN projects p ON s.project_id = p.id
             WHERE s.user_id = ?
             ORDER BY s.updated_at DESC",
            [$this->user['id']]
        );

        $this->view('simulations/index', [
            'pageTitle' => 'My Simulations',
            'simulations' => $simulations,
        ]);
    }

    public function create() {
        $projectId = $_GET['project_id'] ?? '';
        $moduleId = $_GET['module_id'] ?? '';

        $project = null;
        $module = null;

        if (!empty($projectId)) {
            $project = $this->db->fetch(
                "SELECT * FROM projects WHERE id = ? AND user_id = ?",
                [$projectId, $this->user['id']]
            );
        }

        if (!empty($moduleId)) {
            $module = $this->db->fetch(
                "SELECT * FROM modules WHERE id = ? AND is_active = 1",
                [$moduleId]
            );
        }

        $error = '';

        if ($this->isPost()) {
            $name = trim($_POST['name'] ?? '');
            $postProjectId = $_POST['project_id'] ?? $projectId;
            $postModuleId = $_POST['module_id'] ?? $moduleId;
            $description = trim($_POST['description'] ?? '');
            $inputData = trim($_POST['input_data'] ?? '{}');

            if (empty($name) || empty($postProjectId) || empty($postModuleId)) {
                $error = 'Simulation name, project, and module are required.';
            } else {
                $this->db->insert('simulations', [
                    'user_id' => $this->user['id'],
                    'project_id' => $postProjectId,
                    'module_id' => $postModuleId,
                    'name' => $name,
                    'description' => $description,
                    'status' => 'draft',
                    'input_data' => $inputData,
                    'output_data' => '{}',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $this->redirect('simulations');
            }
        }

        $projects = $this->db->fetchAll(
            "SELECT * FROM projects WHERE user_id = ? ORDER BY name",
            [$this->user['id']]
        );

        $modules = $this->db->fetchAll(
            "SELECT m.*, mc.name as category_name
             FROM modules m
             JOIN module_categories mc ON m.category_id = mc.id
             WHERE m.is_active = 1
             ORDER BY mc.sort_order, m.sort_order"
        );

        $this->view('simulations/create', [
            'pageTitle' => 'Create Simulation',
            'project' => $project,
            'module' => $module,
            'projects' => $projects,
            'modules' => $modules,
            'error' => $error,
        ]);
    }

    public function run() {
        if (!$this->isPost()) {
            $this->redirect('simulations');
        }

        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            $this->redirect('simulations');
        }

        $simulation = $this->db->fetch(
            "SELECT * FROM simulations WHERE id = ? AND user_id = ?",
            [$id, $this->user['id']]
        );

        if (!$simulation) {
            $this->redirect('simulations');
        }

        // Set status to running
        $this->db->update('simulations', [
            'status' => 'running',
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);

        // Generate mock results based on input data
        $inputData = json_decode($simulation['input_data'], true) ?: [];
        $outputData = $this->generateMockResults($inputData);

        // Mark as completed with results
        $this->db->update('simulations', [
            'status' => 'completed',
            'output_data' => json_encode($outputData),
            'updated_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);

        $this->redirect('simulations/results?id=' . $id);
    }

    public function view_simulation() {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            $this->redirect('simulations');
        }

        $simulation = $this->db->fetch(
            "SELECT s.*, m.name as module_name, m.slug as module_slug,
                    mc.name as category_name, p.name as project_name
             FROM simulations s
             JOIN modules m ON s.module_id = m.id
             JOIN module_categories mc ON m.category_id = mc.id
             JOIN projects p ON s.project_id = p.id
             WHERE s.id = ? AND s.user_id = ?",
            [$id, $this->user['id']]
        );

        if (!$simulation) {
            $this->redirect('simulations');
        }

        $flowsheetData = json_decode($simulation['input_data'], true) ?: [];

        $this->view('simulations/view', [
            'pageTitle' => $simulation['name'],
            'simulation' => $simulation,
            'flowsheetData' => $flowsheetData,
        ]);
    }

    public function results() {
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            $this->redirect('simulations');
        }

        $simulation = $this->db->fetch(
            "SELECT s.*, m.name as module_name, m.slug as module_slug,
                    mc.name as category_name, p.name as project_name
             FROM simulations s
             JOIN modules m ON s.module_id = m.id
             JOIN module_categories mc ON m.category_id = mc.id
             JOIN projects p ON s.project_id = p.id
             WHERE s.id = ? AND s.user_id = ?",
            [$id, $this->user['id']]
        );

        if (!$simulation) {
            $this->redirect('simulations');
        }

        $outputData = json_decode($simulation['output_data'], true) ?: [];

        // Prepare chart data for results visualization
        $chartsData = [
            'temperature_profile' => $outputData['temperature_profile'] ?? [],
            'pressure_profile' => $outputData['pressure_profile'] ?? [],
            'composition' => $outputData['composition'] ?? [],
            'energy_balance' => $outputData['energy_balance'] ?? [],
            'convergence' => $outputData['convergence'] ?? [],
        ];

        $this->view('simulations/results', [
            'pageTitle' => 'Results - ' . $simulation['name'],
            'simulation' => $simulation,
            'outputData' => $outputData,
            'chartsData' => $chartsData,
        ]);
    }

    private function generateMockResults($inputData) {
        $temperature = floatval($inputData['temperature'] ?? 350);
        $pressure = floatval($inputData['pressure'] ?? 101.325);

        return [
            'summary' => [
                'convergence_status' => 'converged',
                'iterations' => rand(15, 85),
                'tolerance' => 1.0e-6,
                'execution_time' => round(rand(100, 5000) / 100, 2),
            ],
            'temperature_profile' => array_map(function ($i) use ($temperature) {
                return ['stage' => $i, 'value' => $temperature + rand(-50, 50) + $i * 2];
            }, range(1, 10)),
            'pressure_profile' => array_map(function ($i) use ($pressure) {
                return ['stage' => $i, 'value' => $pressure - $i * rand(1, 5)];
            }, range(1, 10)),
            'composition' => [
                ['component' => 'Methane', 'mole_fraction' => round(rand(1, 40) / 100, 4)],
                ['component' => 'Ethane', 'mole_fraction' => round(rand(1, 25) / 100, 4)],
                ['component' => 'Propane', 'mole_fraction' => round(rand(1, 20) / 100, 4)],
                ['component' => 'n-Butane', 'mole_fraction' => round(rand(1, 15) / 100, 4)],
                ['component' => 'Water', 'mole_fraction' => round(rand(1, 10) / 100, 4)],
            ],
            'energy_balance' => [
                'heat_input' => round(rand(1000, 50000) / 10, 2),
                'heat_output' => round(rand(800, 45000) / 10, 2),
                'work_input' => round(rand(100, 5000) / 10, 2),
                'heat_loss' => round(rand(10, 500) / 10, 2),
            ],
            'convergence' => array_map(function ($i) {
                return ['iteration' => $i, 'error' => pow(10, -1 * $i / 5)];
            }, range(1, 20)),
            'streams' => [
                'feed' => ['temperature' => $temperature, 'pressure' => $pressure, 'flow_rate' => rand(100, 1000)],
                'product' => ['temperature' => $temperature + rand(10, 50), 'pressure' => $pressure - rand(5, 20), 'flow_rate' => rand(80, 900)],
                'waste' => ['temperature' => $temperature - rand(5, 30), 'pressure' => $pressure - rand(1, 10), 'flow_rate' => rand(10, 200)],
            ],
        ];
    }
}
