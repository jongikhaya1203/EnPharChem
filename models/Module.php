<?php
/**
 * EnPharChem - Module Model
 */

class Module {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find($id) {
        return $this->db->fetch(
            "SELECT m.*, mc.name as category_name, mc.slug as category_slug
             FROM modules m
             JOIN module_categories mc ON m.category_id = mc.id
             WHERE m.id = ?",
            [$id]
        );
    }

    public function findBySlug($slug) {
        return $this->db->fetch(
            "SELECT m.*, mc.name as category_name, mc.slug as category_slug
             FROM modules m
             JOIN module_categories mc ON m.category_id = mc.id
             WHERE m.slug = ?",
            [$slug]
        );
    }

    public function getByCategory($categorySlug) {
        return $this->db->fetchAll(
            "SELECT m.* FROM modules m
             JOIN module_categories mc ON m.category_id = mc.id
             WHERE mc.slug = ? AND m.is_active = 1
             ORDER BY m.sort_order",
            [$categorySlug]
        );
    }

    public function getCategories() {
        return $this->db->fetchAll(
            "SELECT mc.*, COUNT(m.id) as module_count
             FROM module_categories mc
             LEFT JOIN modules m ON m.category_id = mc.id AND m.is_active = 1
             WHERE mc.is_active = 1
             GROUP BY mc.id
             ORDER BY mc.sort_order"
        );
    }

    public function getCategory($slug) {
        return $this->db->fetch(
            "SELECT * FROM module_categories WHERE slug = ?",
            [$slug]
        );
    }

    public function getAll() {
        return $this->db->fetchAll(
            "SELECT m.*, mc.name as category_name
             FROM modules m
             JOIN module_categories mc ON m.category_id = mc.id
             WHERE m.is_active = 1
             ORDER BY mc.sort_order, m.sort_order"
        );
    }

    public function search($query) {
        $like = "%{$query}%";
        return $this->db->fetchAll(
            "SELECT m.*, mc.name as category_name, mc.slug as category_slug
             FROM modules m
             JOIN module_categories mc ON m.category_id = mc.id
             WHERE m.is_active = 1 AND (m.name LIKE ? OR m.description LIKE ?)
             ORDER BY m.name LIMIT 20",
            [$like, $like]
        );
    }

    public function getCount() {
        return $this->db->fetch("SELECT COUNT(*) as count FROM modules WHERE is_active = 1")['count'];
    }
}
