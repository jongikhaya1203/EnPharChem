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

    /**
     * Module Catalogue - streams a real generated PDF binary and caches a copy
     * under assets/pdfs/ for direct linking.
     */
    public function moduleCatalogue() {
        require_once APP_ROOT . '/lib/ModuleDocsPdfBuilder.php';
        $data = $this->getModuleData();
        $binary = ModuleDocsPdfBuilder::catalogue(
            $data['categories'], $data['modules'], $this->featureMap($data['modules'])
        );
        $this->streamPdf($binary, 'EnPharChem-Module-Catalogue.pdf', 'module-catalogue.pdf');
    }

    /**
     * How-To Manual - streams a real generated PDF binary and caches a copy
     * under assets/pdfs/ for direct linking.
     */
    public function howToManual() {
        require_once APP_ROOT . '/lib/ModuleDocsPdfBuilder.php';
        $data = $this->getModuleData();
        $binary = ModuleDocsPdfBuilder::manual(
            $data['categories'], $data['modules'], $this->taskMap($data['modules'])
        );
        $this->streamPdf($binary, 'EnPharChem-How-To-Manual.pdf', 'how-to-manual.pdf');
    }

    /** Screen-readable HTML edition of the catalogue. */
    public function moduleCatalogueHtml() {
        require_once APP_ROOT . '/lib/ModuleMockup.php';
        require_once APP_ROOT . '/lib/ModuleDocContent.php';
        $data = $this->getModuleData();
        $data['moduleFeatures'] = $this->featureMap($data['modules']);
        extract($data);
        include VIEWS_PATH . '/marketing-docs/module-catalogue.php';
        exit;
    }

    /** Screen-readable HTML edition of the manual. */
    public function howToManualHtml() {
        require_once APP_ROOT . '/lib/ModuleDocContent.php';
        $data = $this->getModuleData();
        $data['moduleTasks'] = $this->taskMap($data['modules']);
        extract($data);
        include VIEWS_PATH . '/marketing-docs/how-to-manual.php';
        exit;
    }

    /**
     * Business Process Map - streams a real generated PDF mapping each business
     * process to its use cases, the job titles/roles that run it, and a worked
     * example. Content is editorial (lib/BusinessProcessMap.php), not DB-driven.
     */
    public function businessProcessMap() {
        require_once APP_ROOT . '/lib/BusinessProcessPdfBuilder.php';
        $binary = BusinessProcessPdfBuilder::build();
        $this->streamPdf($binary, 'EnPharChem-Business-Process-Map.pdf', 'business-process-map.pdf');
    }

    /** Screen-readable HTML edition of the business process map. */
    public function businessProcessMapHtml() {
        require_once APP_ROOT . '/lib/BusinessProcessMap.php';
        $processes = BusinessProcessMap::processes();
        $roleLegend = BusinessProcessMap::roleLegend();
        include VIEWS_PATH . '/marketing-docs/business-process-map.php';
        exit;
    }

    /**
     * Send a generated PDF to the browser, caching a copy under assets/pdfs/.
     * ?download=1 forces a save dialog instead of inline display.
     */
    private function streamPdf($binary, $downloadName, $cacheName) {
        $outDir = APP_ROOT . '/assets/pdfs';
        if (!is_dir($outDir)) { @mkdir($outDir, 0775, true); }
        @file_put_contents($outDir . '/' . $cacheName, $binary);

        $disposition = (isset($_GET['download']) && $_GET['download'] == '1') ? 'attachment' : 'inline';
        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/pdf');
        header('Content-Length: ' . strlen($binary));
        header('Content-Disposition: ' . $disposition . '; filename="' . $downloadName . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        echo $binary;
        exit;
    }

    /**
     * Feature bullets per module id: seeded values from modules.features when
     * present, otherwise generated on the fly so the document never renders empty.
     */
    private function featureMap($modules) {
        $map = [];
        foreach ($modules as $m) {
            $decoded = json_decode((string)($m['features'] ?? ''), true);
            $map[$m['id']] = (is_array($decoded) && $decoded)
                ? $decoded
                : ModuleDocContent::features($m, $m['category_slug']);
        }
        return $map;
    }

    /**
     * Task walkthroughs per module id, read from module_tasks when seeded and
     * generated otherwise.
     */
    private function taskMap($modules) {
        $seeded = [];
        try {
            $rows = $this->db->fetchAll(
                "SELECT * FROM module_tasks ORDER BY module_id, sort_order, id"
            ) ?: [];
            foreach ($rows as $r) {
                $steps = json_decode((string)$r['steps'], true);
                $seeded[$r['module_id']][] = [
                    'title'     => $r['title'],
                    'objective' => $r['objective'],
                    'inputs'    => $r['inputs'],
                    'outputs'   => $r['outputs'],
                    'steps'     => is_array($steps) ? $steps : [],
                    'tip'       => $r['tip'],
                ];
            }
        } catch (Throwable $e) {
            $seeded = []; // table not migrated yet - fall back to generated content
        }

        $map = [];
        foreach ($modules as $m) {
            $map[$m['id']] = $seeded[$m['id']]
                ?? ModuleDocContent::tasks($m, $m['category_slug'], $m['category_name']);
        }
        return $map;
    }

    /**
     * Generate and persist the documentation content for every active module:
     * feature bullets into modules.features, task walkthroughs into module_tasks.
     */
    public function seedModuleDocs() {
        $this->requireRole(['admin', 'superuser']);
        require_once APP_ROOT . '/lib/ModuleDocContent.php';

        // Ensure the task table exists (mirrors database/module_docs_tables.sql).
        try {
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS module_tasks (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    module_id INT NOT NULL,
                    title VARCHAR(255) NOT NULL,
                    objective VARCHAR(500),
                    inputs VARCHAR(500),
                    outputs VARCHAR(500),
                    steps TEXT,
                    tip VARCHAR(500),
                    sort_order INT DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE KEY uniq_module_task (module_id, title),
                    KEY idx_module (module_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );
        } catch (Throwable $e) {
            $this->redirect('control-panel/marketing?msg=docs_error');
            return;
        }

        $modules = $this->db->fetchAll(
            "SELECT m.*, mc.name as category_name, mc.slug as category_slug
             FROM modules m JOIN module_categories mc ON m.category_id = mc.id
             WHERE m.is_active = 1 ORDER BY mc.sort_order, m.sort_order"
        ) ?: [];

        $modCount = 0;
        $taskCount = 0;
        foreach ($modules as $m) {
            try {
                $features = ModuleDocContent::features($m, $m['category_slug']);
                $this->db->update('modules', ['features' => json_encode($features)], 'id = ?', [$m['id']]);

                $this->db->delete('module_tasks', 'module_id = ?', [$m['id']]);
                $order = 0;
                foreach (ModuleDocContent::tasks($m, $m['category_slug'], $m['category_name']) as $t) {
                    $this->db->insert('module_tasks', [
                        'module_id'  => $m['id'],
                        'title'      => mb_substr($t['title'], 0, 255),
                        'objective'  => mb_substr($t['objective'], 0, 500),
                        'inputs'     => mb_substr($t['inputs'], 0, 500),
                        'outputs'    => mb_substr($t['outputs'], 0, 500),
                        'steps'      => json_encode($t['steps']),
                        'tip'        => mb_substr($t['tip'], 0, 500),
                        'sort_order' => $order++,
                    ]);
                    $taskCount++;
                }
                $modCount++;
            } catch (Throwable $e) {
                // skip this module and continue seeding the rest
            }
        }

        $this->redirect('control-panel/marketing?msg=docs_seeded&modules=' . $modCount . '&tasks=' . $taskCount);
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
                'title' => 'EnPharChem Module Catalogue - All Modules with Interface Views',
                'description' => 'Complete module catalogue covering every active module across all 15 categories, each with an interface illustration, capability bullets, version and licence tier. Grouped by category with a full index.',
                'material_type' => 'brochure',
                'category' => 'Marketing',
                'target_audience' => 'Process Engineers, Plant Managers, VP Engineering, Procurement, Technical Evaluators',
                'file_url' => '/enpharchem/marketing/module-catalogue',
                'status' => 'published',
                'download_count' => 0,
                'created_by' => $this->user['id'],
            ],
            [
                'title' => 'EnPharChem How-To Manual - Tasks by Module',
                'description' => 'Operational how-to manual documenting the tasks performed in every module, each with a numbered walkthrough, required inputs, expected outputs and a How Helper hint. Written against the live module workspace.',
                'material_type' => 'datasheet',
                'category' => 'User Documentation',
                'target_audience' => 'Process Engineers, Operators, Application Engineers, Training Leads',
                'file_url' => '/enpharchem/marketing/how-to-manual',
                'status' => 'published',
                'download_count' => 0,
                'created_by' => $this->user['id'],
            ],
            [
                'title' => 'EnPharChem Business Process Map - Processes, Use Cases & Roles',
                'description' => 'Maps each business process on the platform to the use cases it serves, the job titles and platform roles that carry it out, and a worked example. Includes a platform-role legend and a process summary matrix.',
                'material_type' => 'whitepaper',
                'category' => 'Business & Solution',
                'target_audience' => 'Solution Consultants, Business Analysts, Department Heads, Buyers, Onboarding Leads',
                'file_url' => '/enpharchem/marketing/business-process-map',
                'status' => 'published',
                'download_count' => 0,
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

        // Ensure table exists
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS marketing_materials (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                material_type ENUM('brochure','whitepaper','case_study','datasheet','presentation','video','infographic') NOT NULL DEFAULT 'brochure',
                category VARCHAR(100),
                target_audience VARCHAR(255),
                file_url VARCHAR(500),
                thumbnail_url VARCHAR(500),
                file_size VARCHAR(50),
                status ENUM('draft','review','approved','published') DEFAULT 'draft',
                download_count INT DEFAULT 0,
                created_by INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB");
        } catch (Exception $e) {
            // table may already exist
        }

        $count = 0;
        foreach ($materials as $m) {
            try {
                // Delete existing with same title to allow re-seeding
                $this->db->query("DELETE FROM marketing_materials WHERE title = ?", [$m['title']]);
                $this->db->insert('marketing_materials', $m);
                $count++;
            } catch (Exception $e) {
                // skip on error
            }
        }

        $this->redirect('control-panel/marketing?msg=seeded&count=' . $count);
    }
}
