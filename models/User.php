<?php
/**
 * EnPharChem - User Model
 */

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find($id) {
        return $this->db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
    }

    public function findByUsername($username) {
        return $this->db->fetch(
            "SELECT * FROM users WHERE username = ? OR email = ?",
            [$username, $username]
        );
    }

    public function getAll($limit = 50, $offset = 0) {
        return $this->db->fetchAll(
            "SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
    }

    public function create($data) {
        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password']);
        return $this->db->insert('users', $data);
    }

    public function update($id, $data) {
        return $this->db->update('users', $data, 'id = ?', [$id]);
    }

    public function delete($id) {
        return $this->db->delete('users', 'id = ?', [$id]);
    }

    public function getCount() {
        return $this->db->fetch("SELECT COUNT(*) as count FROM users")['count'];
    }
}
