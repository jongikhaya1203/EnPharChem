<?php
/**
 * EnPharChem - Licensing Portal Controller
 * Manages licenses, module grants, requests, and reporting.
 */

class LicensingController extends BaseController {

    /**
     * Main licensing dashboard
     */
    public function index() {
        $this->requireRole(['superuser', 'admin']);

        // Stats
        $totalLicenses = $this->db->fetch("SELECT COUNT(*) as cnt FROM licenses")['cnt'] ?? 0;
        $activeLicenses = $this->db->fetch("SELECT COUNT(*) as cnt FROM licenses WHERE status = 'active'")['cnt'] ?? 0;
        $suspendedLicenses = $this->db->fetch("SELECT COUNT(*) as cnt FROM licenses WHERE status = 'suspended'")['cnt'] ?? 0;
        $expiredLicenses = $this->db->fetch("SELECT COUNT(*) as cnt FROM licenses WHERE status = 'expired'")['cnt'] ?? 0;
        $pendingRequests = $this->db->fetch("SELECT COUNT(*) as cnt FROM license_requests WHERE status = 'pending'")['cnt'] ?? 0;

        // All licenses with user info
        $licenses = $this->db->fetchAll(
            "SELECT l.*, u.username, u.email,
                    iu.username as issued_by_name,
                    (SELECT COUNT(*) FROM license_modules lm WHERE lm.license_id = l.id AND lm.status = 'granted') as granted_modules,
                    (SELECT COUNT(*) FROM modules WHERE is_active = 1) as total_modules
             FROM licenses l
             LEFT JOIN users u ON l.user_id = u.id
             LEFT JOIN users iu ON l.issued_by = iu.id
             ORDER BY l.created_at DESC"
        ) ?: [];

        // Pending requests
        $requests = $this->db->fetchAll(
            "SELECT lr.*, u.username, u.email, m.name as module_name, mc.name as category_name
             FROM license_requests lr
             LEFT JOIN users u ON lr.user_id = u.id
             LEFT JOIN modules m ON lr.module_id = m.id
             LEFT JOIN module_categories mc ON m.category_id = mc.id
             WHERE lr.status = 'pending'
             ORDER BY lr.created_at DESC"
        ) ?: [];

        // All users for the issue form
        $users = $this->db->fetchAll("SELECT id, username, email FROM users ORDER BY username") ?: [];

        $this->view('control-panel/licensing', [
            'pageTitle' => 'Licensing Portal',
            'totalLicenses' => $totalLicenses,
            'activeLicenses' => $activeLicenses,
            'suspendedLicenses' => $suspendedLicenses,
            'expiredLicenses' => $expiredLicenses,
            'pendingRequests' => $pendingRequests,
            'licenses' => $licenses,
            'requests' => $requests,
            'users' => $users,
        ]);
    }

    /**
     * Issue a new license
     */
    public function issueLicense() {
        $this->requireRole(['superuser', 'admin']);

        if ($this->isPost()) {
            $licenseKey = 'EP-' . strtoupper(substr(md5(uniqid()), 0, 4)) . '-' . strtoupper(substr(md5(time()), 0, 4)) . '-' . strtoupper(substr(md5(rand()), 0, 4));

            $licenseId = $this->db->insert('licenses', [
                'license_key' => $licenseKey,
                'user_id' => $_POST['user_id'] ?: null,
                'license_type' => $_POST['license_type'] ?? 'trial',
                'status' => 'active',
                'issued_date' => date('Y-m-d'),
                'expiry_date' => !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null,
                'max_modules' => (int)($_POST['max_modules'] ?? 5),
                'max_users' => (int)($_POST['max_users'] ?? 1),
                'issued_by' => $this->user['id'],
                'notes' => $_POST['notes'] ?? '',
            ]);

            $this->logAudit($licenseId, 'license_issued', "License {$licenseKey} issued. Type: " . ($_POST['license_type'] ?? 'trial'));

            $this->redirect('control-panel/licensing?msg=issued');
        }

        $this->redirect('control-panel/licensing');
    }

    /**
     * Manage a single license - view details and handle module grants
     */
    public function manageLicense() {
        $this->requireRole(['superuser', 'admin']);

        $licenseId = (int)($_GET['id'] ?? 0);
        if (!$licenseId) {
            $this->redirect('control-panel/licensing');
            return;
        }

        // Handle POST actions
        if ($this->isPost()) {
            $action = $_POST['action'] ?? '';
            $this->handleManageAction($licenseId, $action);
            $this->redirect('control-panel/licensing/manage?id=' . $licenseId . '&msg=updated');
            return;
        }

        // GET: Load license details
        $license = $this->db->fetch(
            "SELECT l.*, u.username, u.email, iu.username as issued_by_name
             FROM licenses l
             LEFT JOIN users u ON l.user_id = u.id
             LEFT JOIN users iu ON l.issued_by = iu.id
             WHERE l.id = ?",
            [$licenseId]
        );

        if (!$license) {
            $this->redirect('control-panel/licensing');
            return;
        }

        // Get all module grants for this license
        $licenseModules = $this->db->fetchAll(
            "SELECT lm.*, m.name as module_name, m.slug as module_slug, m.description as module_description,
                    mc.name as category_name, mc.id as category_id, mc.slug as category_slug, mc.icon as category_icon,
                    gu.username as granted_by_name
             FROM license_modules lm
             JOIN modules m ON lm.module_id = m.id
             JOIN module_categories mc ON m.category_id = mc.id
             LEFT JOIN users gu ON lm.granted_by = gu.id
             WHERE lm.license_id = ?
             ORDER BY mc.sort_order, m.sort_order",
            [$licenseId]
        ) ?: [];

        // Index module grants by module_id for quick lookup
        $moduleGrants = [];
        foreach ($licenseModules as $lm) {
            $moduleGrants[$lm['module_id']] = $lm;
        }

        // Get all categories with their modules
        $categories = $this->db->fetchAll(
            "SELECT mc.*, COUNT(m.id) as module_count
             FROM module_categories mc
             LEFT JOIN modules m ON m.category_id = mc.id AND m.is_active = 1
             WHERE mc.is_active = 1
             GROUP BY mc.id
             ORDER BY mc.sort_order"
        ) ?: [];

        $allModules = $this->db->fetchAll(
            "SELECT m.*, mc.name as category_name, mc.id as category_id
             FROM modules m
             JOIN module_categories mc ON m.category_id = mc.id
             WHERE m.is_active = 1
             ORDER BY mc.sort_order, m.sort_order"
        ) ?: [];

        // Organize modules by category
        $modulesByCategory = [];
        foreach ($allModules as $mod) {
            $modulesByCategory[$mod['category_id']][] = $mod;
        }

        // Audit log
        $auditLog = $this->db->fetchAll(
            "SELECT la.*, u.username as performed_by_name
             FROM license_audit_log la
             LEFT JOIN users u ON la.performed_by = u.id
             WHERE la.license_id = ?
             ORDER BY la.created_at DESC
             LIMIT 50",
            [$licenseId]
        ) ?: [];

        // Stats for this license
        $grantedCount = 0;
        $deniedCount = 0;
        $pendingCount = 0;
        $revokedCount = 0;
        foreach ($licenseModules as $lm) {
            if ($lm['status'] === 'granted') $grantedCount++;
            elseif ($lm['status'] === 'denied') $deniedCount++;
            elseif ($lm['status'] === 'pending') $pendingCount++;
            elseif ($lm['status'] === 'revoked') $revokedCount++;
        }

        $this->view('control-panel/license-manage', [
            'pageTitle' => 'Manage License - ' . $license['license_key'],
            'license' => $license,
            'licenseModules' => $licenseModules,
            'moduleGrants' => $moduleGrants,
            'categories' => $categories,
            'modulesByCategory' => $modulesByCategory,
            'auditLog' => $auditLog,
            'grantedCount' => $grantedCount,
            'deniedCount' => $deniedCount,
            'pendingCount' => $pendingCount,
            'revokedCount' => $revokedCount,
            'totalModules' => count($allModules),
        ]);
    }

    /**
     * Handle manage license POST actions
     */
    private function handleManageAction($licenseId, $action) {
        switch ($action) {
            case 'grant_module':
                $moduleId = (int)($_POST['module_id'] ?? 0);
                if ($moduleId) {
                    // Check if exists
                    $existing = $this->db->fetch(
                        "SELECT id FROM license_modules WHERE license_id = ? AND module_id = ?",
                        [$licenseId, $moduleId]
                    );
                    if ($existing) {
                        $this->db->update('license_modules', [
                            'status' => 'granted',
                            'granted_date' => date('Y-m-d'),
                            'granted_by' => $this->user['id'],
                            'denied_reason' => null,
                        ], 'license_id = ? AND module_id = ?', [$licenseId, $moduleId]);
                    } else {
                        $this->db->insert('license_modules', [
                            'license_id' => $licenseId,
                            'module_id' => $moduleId,
                            'status' => 'granted',
                            'granted_date' => date('Y-m-d'),
                            'granted_by' => $this->user['id'],
                        ]);
                    }
                    $moduleName = $this->db->fetch("SELECT name FROM modules WHERE id = ?", [$moduleId])['name'] ?? '';
                    $this->logAudit($licenseId, 'module_granted', "Module granted: {$moduleName}");
                }
                break;

            case 'deny_module':
                $moduleId = (int)($_POST['module_id'] ?? 0);
                $denyReason = $_POST['deny_reason'] ?? '';
                if ($moduleId) {
                    $existing = $this->db->fetch(
                        "SELECT id FROM license_modules WHERE license_id = ? AND module_id = ?",
                        [$licenseId, $moduleId]
                    );
                    if ($existing) {
                        $this->db->update('license_modules', [
                            'status' => 'denied',
                            'denied_reason' => $denyReason,
                        ], 'license_id = ? AND module_id = ?', [$licenseId, $moduleId]);
                    } else {
                        $this->db->insert('license_modules', [
                            'license_id' => $licenseId,
                            'module_id' => $moduleId,
                            'status' => 'denied',
                            'denied_reason' => $denyReason,
                        ]);
                    }
                    $moduleName = $this->db->fetch("SELECT name FROM modules WHERE id = ?", [$moduleId])['name'] ?? '';
                    $this->logAudit($licenseId, 'module_denied', "Module denied: {$moduleName}. Reason: {$denyReason}");
                }
                break;

            case 'revoke_module':
                $moduleId = (int)($_POST['module_id'] ?? 0);
                if ($moduleId) {
                    $this->db->update('license_modules', [
                        'status' => 'revoked',
                    ], 'license_id = ? AND module_id = ?', [$licenseId, $moduleId]);
                    $moduleName = $this->db->fetch("SELECT name FROM modules WHERE id = ?", [$moduleId])['name'] ?? '';
                    $this->logAudit($licenseId, 'module_revoked', "Module revoked: {$moduleName}");
                }
                break;

            case 'grant_all_category':
                $categoryId = (int)($_POST['category_id'] ?? 0);
                if ($categoryId) {
                    $modules = $this->db->fetchAll(
                        "SELECT id, name FROM modules WHERE category_id = ? AND is_active = 1",
                        [$categoryId]
                    ) ?: [];
                    foreach ($modules as $mod) {
                        $existing = $this->db->fetch(
                            "SELECT id FROM license_modules WHERE license_id = ? AND module_id = ?",
                            [$licenseId, $mod['id']]
                        );
                        if ($existing) {
                            $this->db->update('license_modules', [
                                'status' => 'granted',
                                'granted_date' => date('Y-m-d'),
                                'granted_by' => $this->user['id'],
                                'denied_reason' => null,
                            ], 'license_id = ? AND module_id = ?', [$licenseId, $mod['id']]);
                        } else {
                            $this->db->insert('license_modules', [
                                'license_id' => $licenseId,
                                'module_id' => $mod['id'],
                                'status' => 'granted',
                                'granted_date' => date('Y-m-d'),
                                'granted_by' => $this->user['id'],
                            ]);
                        }
                    }
                    $catName = $this->db->fetch("SELECT name FROM module_categories WHERE id = ?", [$categoryId])['name'] ?? '';
                    $this->logAudit($licenseId, 'category_granted', "All modules granted in category: {$catName} (" . count($modules) . " modules)");
                }
                break;

            case 'deny_all_category':
                $categoryId = (int)($_POST['category_id'] ?? 0);
                $denyReason = $_POST['deny_reason'] ?? 'Category-level denial';
                if ($categoryId) {
                    $modules = $this->db->fetchAll(
                        "SELECT id, name FROM modules WHERE category_id = ? AND is_active = 1",
                        [$categoryId]
                    ) ?: [];
                    foreach ($modules as $mod) {
                        $existing = $this->db->fetch(
                            "SELECT id FROM license_modules WHERE license_id = ? AND module_id = ?",
                            [$licenseId, $mod['id']]
                        );
                        if ($existing) {
                            $this->db->update('license_modules', [
                                'status' => 'denied',
                                'denied_reason' => $denyReason,
                            ], 'license_id = ? AND module_id = ?', [$licenseId, $mod['id']]);
                        } else {
                            $this->db->insert('license_modules', [
                                'license_id' => $licenseId,
                                'module_id' => $mod['id'],
                                'status' => 'denied',
                                'denied_reason' => $denyReason,
                            ]);
                        }
                    }
                    $catName = $this->db->fetch("SELECT name FROM module_categories WHERE id = ?", [$categoryId])['name'] ?? '';
                    $this->logAudit($licenseId, 'category_denied', "All modules denied in category: {$catName}. Reason: {$denyReason}");
                }
                break;

            case 'suspend_license':
                $this->db->update('licenses', ['status' => 'suspended'], 'id = ?', [$licenseId]);
                $this->logAudit($licenseId, 'license_suspended', 'License suspended');
                break;

            case 'activate_license':
                $this->db->update('licenses', ['status' => 'active'], 'id = ?', [$licenseId]);
                $this->logAudit($licenseId, 'license_activated', 'License activated');
                break;

            case 'revoke_license':
                $this->db->update('licenses', ['status' => 'revoked'], 'id = ?', [$licenseId]);
                $this->logAudit($licenseId, 'license_revoked', 'License revoked');
                break;
        }
    }

    /**
     * License requests management
     */
    public function requests() {
        $this->requireRole(['superuser', 'admin']);

        if ($this->isPost()) {
            $action = $_POST['action'] ?? '';
            $requestId = (int)($_POST['request_id'] ?? 0);

            if ($requestId) {
                $request = $this->db->fetch("SELECT * FROM license_requests WHERE id = ?", [$requestId]);

                if ($request && $action === 'approve') {
                    // Update request
                    $this->db->update('license_requests', [
                        'status' => 'approved',
                        'reviewed_by' => $this->user['id'],
                        'review_date' => date('Y-m-d H:i:s'),
                        'review_notes' => $_POST['review_notes'] ?? '',
                    ], 'id = ?', [$requestId]);

                    // Create license_module entry if license_id exists
                    if ($request['license_id']) {
                        $existing = $this->db->fetch(
                            "SELECT id FROM license_modules WHERE license_id = ? AND module_id = ?",
                            [$request['license_id'], $request['module_id']]
                        );
                        if ($existing) {
                            $this->db->update('license_modules', [
                                'status' => 'granted',
                                'granted_date' => date('Y-m-d'),
                                'granted_by' => $this->user['id'],
                            ], 'license_id = ? AND module_id = ?', [$request['license_id'], $request['module_id']]);
                        } else {
                            $this->db->insert('license_modules', [
                                'license_id' => $request['license_id'],
                                'module_id' => $request['module_id'],
                                'status' => 'granted',
                                'granted_date' => date('Y-m-d'),
                                'granted_by' => $this->user['id'],
                            ]);
                        }
                        $this->logAudit($request['license_id'], 'request_approved', "Request #{$requestId} approved");
                    }
                } elseif ($request && $action === 'deny') {
                    $this->db->update('license_requests', [
                        'status' => 'denied',
                        'reviewed_by' => $this->user['id'],
                        'review_date' => date('Y-m-d H:i:s'),
                        'review_notes' => $_POST['review_notes'] ?? '',
                    ], 'id = ?', [$requestId]);

                    if ($request['license_id']) {
                        $this->logAudit($request['license_id'], 'request_denied', "Request #{$requestId} denied");
                    }
                }
            }

            $this->redirect('control-panel/licensing/requests?msg=updated');
            return;
        }

        // GET: List all requests
        $requests = $this->db->fetchAll(
            "SELECT lr.*, u.username, u.email, m.name as module_name, mc.name as category_name,
                    l.license_key, ru.username as reviewer_name
             FROM license_requests lr
             LEFT JOIN users u ON lr.user_id = u.id
             LEFT JOIN modules m ON lr.module_id = m.id
             LEFT JOIN module_categories mc ON m.category_id = mc.id
             LEFT JOIN licenses l ON lr.license_id = l.id
             LEFT JOIN users ru ON lr.reviewed_by = ru.id
             ORDER BY FIELD(lr.status, 'pending', 'approved', 'denied', 'cancelled'), lr.created_at DESC"
        ) ?: [];

        $this->view('control-panel/licensing', [
            'pageTitle' => 'License Requests',
            'showRequestsTab' => true,
            'totalLicenses' => $this->db->fetch("SELECT COUNT(*) as cnt FROM licenses")['cnt'] ?? 0,
            'activeLicenses' => $this->db->fetch("SELECT COUNT(*) as cnt FROM licenses WHERE status = 'active'")['cnt'] ?? 0,
            'suspendedLicenses' => $this->db->fetch("SELECT COUNT(*) as cnt FROM licenses WHERE status = 'suspended'")['cnt'] ?? 0,
            'expiredLicenses' => $this->db->fetch("SELECT COUNT(*) as cnt FROM licenses WHERE status = 'expired'")['cnt'] ?? 0,
            'pendingRequests' => $this->db->fetch("SELECT COUNT(*) as cnt FROM license_requests WHERE status = 'pending'")['cnt'] ?? 0,
            'licenses' => $this->db->fetchAll(
                "SELECT l.*, u.username, u.email, iu.username as issued_by_name,
                        (SELECT COUNT(*) FROM license_modules lm WHERE lm.license_id = l.id AND lm.status = 'granted') as granted_modules,
                        (SELECT COUNT(*) FROM modules WHERE is_active = 1) as total_modules
                 FROM licenses l LEFT JOIN users u ON l.user_id = u.id LEFT JOIN users iu ON l.issued_by = iu.id
                 ORDER BY l.created_at DESC"
            ) ?: [],
            'requests' => $requests,
            'users' => $this->db->fetchAll("SELECT id, username, email FROM users ORDER BY username") ?: [],
        ]);
    }

    /**
     * Bulk management operations
     */
    public function bulkManage() {
        $this->requireRole(['superuser', 'admin']);

        if (!$this->isPost()) {
            $this->redirect('control-panel/licensing');
            return;
        }

        $action = $_POST['action'] ?? '';
        $licenseId = (int)($_POST['license_id'] ?? 0);

        if (!$licenseId) {
            $this->redirect('control-panel/licensing');
            return;
        }

        switch ($action) {
            case 'grant_all':
                $allModules = $this->db->fetchAll("SELECT id FROM modules WHERE is_active = 1") ?: [];
                foreach ($allModules as $mod) {
                    $existing = $this->db->fetch(
                        "SELECT id FROM license_modules WHERE license_id = ? AND module_id = ?",
                        [$licenseId, $mod['id']]
                    );
                    if ($existing) {
                        $this->db->update('license_modules', [
                            'status' => 'granted',
                            'granted_date' => date('Y-m-d'),
                            'granted_by' => $this->user['id'],
                            'denied_reason' => null,
                        ], 'license_id = ? AND module_id = ?', [$licenseId, $mod['id']]);
                    } else {
                        $this->db->insert('license_modules', [
                            'license_id' => $licenseId,
                            'module_id' => $mod['id'],
                            'status' => 'granted',
                            'granted_date' => date('Y-m-d'),
                            'granted_by' => $this->user['id'],
                        ]);
                    }
                }
                $this->logAudit($licenseId, 'bulk_grant_all', 'All modules granted (' . count($allModules) . ' modules)');
                break;

            case 'revoke_all':
                $this->db->query(
                    "UPDATE license_modules SET status = 'revoked' WHERE license_id = ?",
                    [$licenseId]
                );
                $this->logAudit($licenseId, 'bulk_revoke_all', 'All modules revoked');
                break;

            case 'grant_by_tier':
                $license = $this->db->fetch("SELECT license_type FROM licenses WHERE id = ?", [$licenseId]);
                if ($license) {
                    $limits = [
                        'trial' => 5,
                        'standard' => 40,
                        'professional' => 80,
                        'enterprise' => 99999,
                    ];
                    $limit = $limits[$license['license_type']] ?? 5;

                    $modules = $this->db->fetchAll(
                        "SELECT m.id FROM modules m
                         JOIN module_categories mc ON m.category_id = mc.id
                         WHERE m.is_active = 1
                         ORDER BY mc.sort_order, m.sort_order
                         LIMIT ?",
                        [$limit]
                    ) ?: [];

                    // First revoke all existing
                    $this->db->query(
                        "UPDATE license_modules SET status = 'revoked' WHERE license_id = ?",
                        [$licenseId]
                    );

                    // Then grant the tier-appropriate ones
                    foreach ($modules as $mod) {
                        $existing = $this->db->fetch(
                            "SELECT id FROM license_modules WHERE license_id = ? AND module_id = ?",
                            [$licenseId, $mod['id']]
                        );
                        if ($existing) {
                            $this->db->update('license_modules', [
                                'status' => 'granted',
                                'granted_date' => date('Y-m-d'),
                                'granted_by' => $this->user['id'],
                                'denied_reason' => null,
                            ], 'license_id = ? AND module_id = ?', [$licenseId, $mod['id']]);
                        } else {
                            $this->db->insert('license_modules', [
                                'license_id' => $licenseId,
                                'module_id' => $mod['id'],
                                'status' => 'granted',
                                'granted_date' => date('Y-m-d'),
                                'granted_by' => $this->user['id'],
                            ]);
                        }
                    }
                    $this->logAudit($licenseId, 'bulk_grant_by_tier', "Granted by tier ({$license['license_type']}): " . count($modules) . " modules");
                }
                break;
        }

        $this->redirect('control-panel/licensing/manage?id=' . $licenseId . '&msg=bulk_updated');
    }

    /**
     * Licensing report
     */
    public function generateReport() {
        $this->requireRole(['superuser', 'admin']);

        // Overview stats
        $totalLicenses = $this->db->fetch("SELECT COUNT(*) as cnt FROM licenses")['cnt'] ?? 0;
        $activeLicenses = $this->db->fetch("SELECT COUNT(*) as cnt FROM licenses WHERE status = 'active'")['cnt'] ?? 0;
        $suspendedLicenses = $this->db->fetch("SELECT COUNT(*) as cnt FROM licenses WHERE status = 'suspended'")['cnt'] ?? 0;
        $expiredLicenses = $this->db->fetch("SELECT COUNT(*) as cnt FROM licenses WHERE status = 'expired'")['cnt'] ?? 0;
        $revokedLicenses = $this->db->fetch("SELECT COUNT(*) as cnt FROM licenses WHERE status = 'revoked'")['cnt'] ?? 0;

        // License type distribution
        $typeDistribution = $this->db->fetchAll(
            "SELECT license_type, COUNT(*) as cnt FROM licenses GROUP BY license_type ORDER BY FIELD(license_type, 'trial','standard','professional','enterprise')"
        ) ?: [];

        // Module coverage: how many licenses have each module granted
        $moduleCoverage = $this->db->fetchAll(
            "SELECT m.name as module_name, mc.name as category_name,
                    COUNT(lm.id) as grant_count
             FROM modules m
             JOIN module_categories mc ON m.category_id = mc.id
             LEFT JOIN license_modules lm ON lm.module_id = m.id AND lm.status = 'granted'
             WHERE m.is_active = 1
             GROUP BY m.id
             ORDER BY grant_count DESC, mc.sort_order, m.sort_order"
        ) ?: [];

        // Expiring soon (next 30 days)
        $expiringSoon = $this->db->fetchAll(
            "SELECT l.*, u.username, u.email
             FROM licenses l
             LEFT JOIN users u ON l.user_id = u.id
             WHERE l.status = 'active'
               AND l.expiry_date IS NOT NULL
               AND l.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
             ORDER BY l.expiry_date ASC"
        ) ?: [];

        // Recent audit activity
        $recentActivity = $this->db->fetchAll(
            "SELECT la.*, u.username as performed_by_name, l.license_key
             FROM license_audit_log la
             LEFT JOIN users u ON la.performed_by = u.id
             LEFT JOIN licenses l ON la.license_id = l.id
             ORDER BY la.created_at DESC
             LIMIT 20"
        ) ?: [];

        $this->view('control-panel/licensing-report', [
            'pageTitle' => 'Licensing Report',
            'totalLicenses' => $totalLicenses,
            'activeLicenses' => $activeLicenses,
            'suspendedLicenses' => $suspendedLicenses,
            'expiredLicenses' => $expiredLicenses,
            'revokedLicenses' => $revokedLicenses,
            'typeDistribution' => $typeDistribution,
            'moduleCoverage' => $moduleCoverage,
            'expiringSoon' => $expiringSoon,
            'recentActivity' => $recentActivity,
        ]);
    }

    /**
     * Log an action to the audit log
     */
    private function logAudit($licenseId, $action, $details = '') {
        $this->db->insert('license_audit_log', [
            'license_id' => $licenseId,
            'action' => $action,
            'performed_by' => $this->user['id'] ?? null,
            'details' => $details,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
    }
}
