<?php
/**
 * EnPharChem - Marketing Documents Controller
 * Generates professional PDF-ready marketing materials
 */

class MarketingDocsController extends BaseController {

    private function getModuleData() {
        $categories = $this->db->fetchAll(
            "SELECT mc.*, COUNT(m.id) as module_count
             FROM module_categories mc
             LEFT JOIN modules m ON m.category_id = mc.id AND m.is_active = 1
             WHERE mc.is_active = 1 GROUP BY mc.id ORDER BY mc.sort_order"
        ) ?: [];

        $allModules = $this->db->fetchAll(
            "SELECT m.*, mc.name as category_name, mc.slug as category_slug, mc.icon as category_icon
             FROM modules m JOIN module_categories mc ON m.category_id = mc.id
             WHERE m.is_active = 1 ORDER BY mc.sort_order, m.sort_order"
        ) ?: [];

        $modulesByCategory = [];
        foreach ($allModules as $mod) {
            $modulesByCategory[$mod['category_name']][] = $mod;
        }

        return ['categories' => $categories, 'modules' => $allModules, 'byCategory' => $modulesByCategory];
    }

    public function installationManual() {
        $data = $this->getModuleData();
        extract($data);
        include VIEWS_PATH . '/marketing-docs/installation-manual.php';
        exit;
    }

    public function securityArchitecture() {
        $data = $this->getModuleData();
        extract($data);
        include VIEWS_PATH . '/marketing-docs/security-architecture.php';
        exit;
    }

    public function systemArchitecture() {
        $data = $this->getModuleData();
        extract($data);
        include VIEWS_PATH . '/marketing-docs/system-architecture.php';
        exit;
    }

    public function productBrochure() {
        $data = $this->getModuleData();
        extract($data);
        include VIEWS_PATH . '/marketing-docs/product-brochure.php';
        exit;
    }

    public function seedMaterials() {
        // Insert marketing material records into the database
        $materials = [
            [
                'title' => 'EnPharChem Installation Manual v1.0',
                'description' => 'Complete installation guide covering system requirements, XAMPP setup, MySQL database configuration, module deployment, and post-installation verification for the EnPharChem platform.',
                'material_type' => 'datasheet',
                'category' => 'Technical Documentation',
                'target_audience' => 'IT Administrators, System Engineers, DevOps Teams',
                'file_url' => '/enpharchem/marketing/installation-manual',
                'status' => 'published',
                'download_count' => 142,
                'created_by' => $this->user['id'],
            ],
            [
                'title' => 'EnPharChem Security Architecture Whitepaper',
                'description' => 'Comprehensive security architecture document covering authentication, authorization, data encryption, network security, RBAC model, audit logging, and compliance frameworks (SOC 2, ISO 27001, NIST).',
                'material_type' => 'whitepaper',
                'category' => 'Security & Compliance',
                'target_audience' => 'CISOs, Security Architects, IT Directors, Compliance Officers',
                'file_url' => '/enpharchem/marketing/security-architecture',
                'status' => 'published',
                'download_count' => 89,
                'created_by' => $this->user['id'],
            ],
            [
                'title' => 'EnPharChem System Architecture Overview',
                'description' => 'Technical system architecture document covering the PHP/MySQL platform stack, MVC framework, 15 module categories, 115+ modules, database schema design, API layer, and integration architecture.',
                'material_type' => 'whitepaper',
                'category' => 'Technical Architecture',
                'target_audience' => 'Solution Architects, CTOs, Engineering Managers, Technical Evaluators',
                'file_url' => '/enpharchem/marketing/system-architecture',
                'status' => 'published',
                'download_count' => 203,
                'created_by' => $this->user['id'],
            ],
            [
                'title' => 'EnPharChem Product Brochure - Complete Module Showcase',
                'description' => 'Full-color marketing brochure showcasing all 115+ modules across 15 categories with detailed screenshots, feature highlights, and competitive advantages vs. AspenTech.',
                'material_type' => 'brochure',
                'category' => 'Marketing',
                'target_audience' => 'Process Engineers, Plant Managers, VP Engineering, Procurement',
                'file_url' => '/enpharchem/marketing/product-brochure',
                'status' => 'published',
                'download_count' => 567,
                'created_by' => $this->user['id'],
            ],
        ];

        $count = 0;
        foreach ($materials as $m) {
            $existing = $this->db->fetch("SELECT id FROM marketing_materials WHERE title = ?", [$m['title']]);
            if (!$existing) {
                try {
                    $this->db->insert('marketing_materials', $m);
                    $count++;
                } catch (Exception $e) {
                    // skip
                }
            }
        }

        $this->redirect('control-panel/marketing?msg=seeded&count=' . $count);
    }
}
