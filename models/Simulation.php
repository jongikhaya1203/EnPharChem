<?php
/**
 * EnPharChem - Simulation Model
 */

class Simulation {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find($id) {
        return $this->db->fetch(
            "SELECT s.*, m.name as module_name, m.slug as module_slug,
                    p.name as project_name, mc.name as category_name
             FROM simulations s
             JOIN modules m ON s.module_id = m.id
             JOIN module_categories mc ON m.category_id = mc.id
             JOIN projects p ON s.project_id = p.id
             WHERE s.id = ?",
            [$id]
        );
    }

    public function getByUser($userId, $limit = 50) {
        return $this->db->fetchAll(
            "SELECT s.*, m.name as module_name, p.name as project_name
             FROM simulations s
             JOIN modules m ON s.module_id = m.id
             JOIN projects p ON s.project_id = p.id
             WHERE s.user_id = ? ORDER BY s.updated_at DESC LIMIT ?",
            [$userId, $limit]
        );
    }

    public function create($data) {
        return $this->db->insert('simulations', $data);
    }

    public function update($id, $data) {
        return $this->db->update('simulations', $data, 'id = ?', [$id]);
    }

    public function delete($id) {
        return $this->db->delete('simulations', 'id = ?', [$id]);
    }

    public function run($id) {
        $sim = $this->find($id);
        if (!$sim) return false;

        $this->update($id, [
            'status' => 'running',
            'started_at' => date('Y-m-d H:i:s'),
        ]);

        // Generate mock simulation results
        $results = $this->generateResults($sim);

        $this->update($id, [
            'status' => 'completed',
            'output_data' => json_encode($results),
            'convergence_status' => 'converged',
            'iterations' => rand(15, 150),
            'execution_time' => round(rand(100, 5000) / 100, 2),
            'completed_at' => date('Y-m-d H:i:s'),
        ]);

        return $results;
    }

    private function generateResults($sim) {
        return [
            'temperature_profile' => $this->generateProfile(8, 200, 450, 'Temperature (°C)'),
            'pressure_profile' => $this->generateProfile(8, 10, 50, 'Pressure (bar)'),
            'composition' => [
                'components' => ['Methane', 'Ethane', 'Propane', 'Butane', 'Pentane', 'CO2', 'H2S', 'Water'],
                'feed' => [0.70, 0.10, 0.08, 0.04, 0.02, 0.03, 0.01, 0.02],
                'product' => [0.85, 0.06, 0.04, 0.02, 0.01, 0.01, 0.005, 0.005],
            ],
            'energy_balance' => [
                'heat_input' => round(rand(5000, 15000) / 10, 1) . ' kW',
                'heat_output' => round(rand(4000, 14000) / 10, 1) . ' kW',
                'work_input' => round(rand(100, 3000) / 10, 1) . ' kW',
                'efficiency' => round(rand(850, 960) / 10, 1) . '%',
            ],
            'mass_balance' => [
                'feed_rate' => round(rand(1000, 10000) / 10, 1) . ' kg/h',
                'product_rate' => round(rand(800, 9000) / 10, 1) . ' kg/h',
                'waste_rate' => round(rand(50, 500) / 10, 1) . ' kg/h',
                'balance_error' => round(rand(1, 50) / 1000, 4) . '%',
            ],
            'convergence' => [
                'method' => 'Newton-Raphson',
                'tolerance' => 1e-6,
                'iterations' => rand(15, 80),
                'final_error' => round(rand(1, 100) / 1e8, 10),
            ],
        ];
    }

    private function generateProfile($points, $min, $max, $label) {
        $data = [];
        for ($i = 0; $i < $points; $i++) {
            $data[] = round($min + ($max - $min) * ($i / ($points - 1)) + rand(-20, 20), 1);
        }
        return ['label' => $label, 'data' => $data];
    }

    public function getCount($userId = null) {
        if ($userId) {
            return $this->db->fetch("SELECT COUNT(*) as count FROM simulations WHERE user_id = ?", [$userId])['count'];
        }
        return $this->db->fetch("SELECT COUNT(*) as count FROM simulations")['count'];
    }
}
