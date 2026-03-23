<?php
/**
 * EnPharChem - Project Model
 */

class Project {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find($id) {
        return $this->db->fetch("SELECT * FROM projects WHERE id = ?", [$id]);
    }

    public function getByUser($userId, $limit = 50) {
        return $this->db->fetchAll(
            "SELECT * FROM projects WHERE user_id = ? ORDER BY updated_at DESC LIMIT ?",
            [$userId, $limit]
        );
    }

    public function create($data) {
        return $this->db->insert('projects', $data);
    }

    public function update($id, $data) {
        return $this->db->update('projects', $data, 'id = ?', [$id]);
    }

    public function delete($id) {
        return $this->db->delete('projects', 'id = ?', [$id]);
    }

    public function getSimulations($projectId) {
        return $this->db->fetchAll(
            "SELECT s.*, m.name as module_name FROM simulations s
             JOIN modules m ON s.module_id = m.id
             WHERE s.project_id = ? ORDER BY s.updated_at DESC",
            [$projectId]
        );
    }

    public function getCount($userId = null) {
        if ($userId) {
            return $this->db->fetch("SELECT COUNT(*) as count FROM projects WHERE user_id = ?", [$userId])['count'];
        }
        return $this->db->fetch("SELECT COUNT(*) as count FROM projects")['count'];
    }
}
