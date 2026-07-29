<?php
/**
 * EnPharChem - Flowsheet Process Simulator Controller
 * ---------------------------------------------------
 * Flagship process-simulation tool. Delivers the interactive flowsheet
 * workspace (index) and the server-side compute endpoint (run) that drives
 * the FlowsheetEngine. The same code path serves both the cloud deployment
 * (server compute) and on-premise XAMPP installs.
 */

require_once APP_ROOT . '/lib/FlowsheetEngine.php';

class FlowsheetController extends BaseController
{
    /** Master switch — on-prem/offline builds can set this false via config. */
    const ENFORCE_LICENSE = true;
    const MODULE_SLUG = 'flowsheet-simulator';

    public function index()
    {
        $state = $this->licenseState();
        $this->view('flowsheet/index', [
            'pageTitle'     => 'Flowsheet Simulator',
            'licenseState'  => $state,
        ]);
    }

    /** JSON: return the component thermophysical database for the palette. */
    public function components()
    {
        $engine = new FlowsheetEngine();
        $this->json(['ok' => true, 'components' => $engine->components()]);
    }

    /** JSON: solve a posted flowsheet and return stream/unit results. */
    public function run()
    {
        if (!$this->isPost()) {
            $this->json(['ok' => false, 'error' => 'POST required'], 405);
        }

        // --- LICENSE ENFORCEMENT: block compute for unlicensed users ---------
        $state = $this->licenseState();
        if (!$state['allowed']) {
            $this->auditDenied($state);
            $m = $state['module'] ?? [];
            $this->json([
                'ok'            => false,
                'needs_license' => true,
                'error'         => 'A license is required to run the Flowsheet Simulator.',
                'module'        => [
                    'id'   => $m['id'] ?? null,
                    'name' => $m['name'] ?? 'Flowsheet Simulator',
                    'tier' => $m['license_required'] ?? 'professional',
                ],
            ], 403);
        }

        $raw = file_get_contents('php://input');
        $fs  = json_decode($raw, true);
        if (!is_array($fs) || empty($fs['units'])) {
            $this->json(['ok' => false, 'error' => 'Invalid or empty flowsheet payload.'], 400);
        }

        try {
            $model  = ($fs['model'] ?? 'peng-robinson') === 'ideal' ? 'ideal' : 'peng-robinson';
            $engine = new FlowsheetEngine($model);
            $result = $engine->run($fs);
            $pkg = $engine->model() === 'peng-robinson'
                 ? 'Peng-Robinson EOS (rigorous VLE)'
                 : 'Ideal / Raoult (Clausius-Clapeyron)';
            $result['solved_at'] = date('Y-m-d H:i:s');
            $result['property_package'] = $pkg;
            $result['engine']    = 'EnPharChem Flowsheet Engine v1.1 · ' . $pkg . ' · sequential-modular';
            $this->json($result);
        } catch (Throwable $e) {
            $this->json(['ok' => false, 'error' => 'Solver error: ' . $e->getMessage()], 500);
        }
    }

    /** JSON: built-in example flowsheets. */
    public function examples()
    {
        $this->json(['ok' => true, 'examples' => $this->exampleLibrary()]);
    }

    /** POST: submit a license request for the Flowsheet Simulator module. */
    public function requestLicense()
    {
        if (!$this->isPost()) {
            $this->json(['ok' => false, 'error' => 'POST required'], 405);
        }
        $m = $this->flowsheetModule();
        if (!$m) {
            $this->json(['ok' => false, 'error' => 'Module registry unavailable.'], 400);
        }
        try {
            $existing = $this->db->fetch(
                "SELECT id FROM license_requests
                 WHERE user_id = ? AND module_id = ? AND status = 'pending'",
                [$this->user['id'], $m['id']]
            );
            if ($existing) {
                $this->json(['ok' => true, 'message' => 'A license request is already pending review.']);
            }
            $this->db->insert('license_requests', [
                'user_id'       => $this->user['id'],
                'module_id'     => $m['id'],
                'request_type'  => 'additional_module',
                'status'        => 'pending',
                'justification' => 'Access requested to the Flowsheet Simulator from the tool.',
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
            $this->json(['ok' => true, 'message' => 'License request submitted for administrator review.']);
        } catch (Throwable $e) {
            $this->json(['ok' => false, 'error' => 'Could not submit request: ' . $e->getMessage()], 500);
        }
    }

    // ---- licensing enforcement ------------------------------------------

    /**
     * Resolve (or seed) the module row that represents this tool so it can be
     * governed by the Licensing Portal and Module License Manager.
     */
    private function flowsheetModule()
    {
        try {
            $this->db->query("ALTER TABLE modules ADD COLUMN license_waived TINYINT(1) DEFAULT 0");
        } catch (Throwable $e) { /* column already exists */ }

        try {
            $m = $this->db->fetch("SELECT * FROM modules WHERE slug = ?", [self::MODULE_SLUG]);
            if ($m) return $m;

            $cat = $this->db->fetch("SELECT id FROM module_categories WHERE slug = ?", ['process-sim-chemicals'])
                ?: $this->db->fetch("SELECT id FROM module_categories ORDER BY sort_order LIMIT 1");
            if (!$cat) return null;

            $this->db->insert('modules', [
                'category_id'      => $cat['id'],
                'name'             => 'Flowsheet Simulator',
                'slug'             => self::MODULE_SLUG,
                'description'      => 'Steady-state process flowsheet simulator with Peng-Robinson VLE and departure-function enthalpies.',
                'version'          => '1.1.0',
                'icon'             => 'fa-project-diagram',
                'sort_order'       => 0,
                'is_active'        => 1,
                'license_required' => 'professional',
            ]);
            return $this->db->fetch("SELECT * FROM modules WHERE slug = ?", [self::MODULE_SLUG]);
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Decide whether the current user may run the simulator.
     * Order: enforcement off → admin bypass → waived → active granted license → deny.
     * Fails OPEN when the licensing tables are not present (partial installs).
     * @return array ['allowed'=>bool,'reason'=>string,'module'=>array|null,'license'=>array|null]
     */
    private function licenseState()
    {
        if (!$this->enforcementEnabled()) {
            return ['allowed' => true, 'reason' => 'enforcement-disabled', 'module' => null, 'license' => null];
        }

        $role = $this->user['role'] ?? '';
        if (in_array($role, ['superuser', 'admin'], true)) {
            return ['allowed' => true, 'reason' => 'admin-bypass', 'module' => null, 'license' => null];
        }

        $m = $this->flowsheetModule();
        if (!$m) {
            // Licensing infrastructure not available — do not brick the tool.
            return ['allowed' => true, 'reason' => 'unconfigured', 'module' => null, 'license' => null];
        }

        // Air-gapped on-prem: a valid signed offline license file grants access.
        $offline = $this->offlineGrant($m);
        if ($offline !== null) {
            return $offline;
        }
        if (!empty($m['license_waived'])) {
            return ['allowed' => true, 'reason' => 'waived', 'module' => $m, 'license' => null];
        }

        try {
            $lic = $this->db->fetch(
                "SELECT l.* FROM licenses l
                 JOIN license_modules lm ON lm.license_id = l.id
                 WHERE l.user_id = ? AND lm.module_id = ?
                   AND l.status = 'active' AND lm.status = 'granted'
                   AND (l.expiry_date IS NULL OR l.expiry_date >= CURDATE())
                 ORDER BY l.expiry_date IS NULL DESC, l.expiry_date DESC
                 LIMIT 1",
                [$this->user['id'], $m['id']]
            );
        } catch (Throwable $e) {
            return ['allowed' => true, 'reason' => 'unconfigured', 'module' => $m, 'license' => null];
        }

        if ($lic) {
            return ['allowed' => true, 'reason' => 'licensed', 'module' => $m, 'license' => $lic];
        }
        return ['allowed' => false, 'reason' => 'unlicensed', 'module' => $m, 'license' => null];
    }

    /** Whether compute-time enforcement is active (config/env driven). */
    private function enforcementEnabled()
    {
        if (defined('FLOWSHEET_LICENSE_ENFORCE')) return (bool) FLOWSHEET_LICENSE_ENFORCE;
        return self::ENFORCE_LICENSE;
    }

    /**
     * Offline (air-gapped) license: verify a vendor-signed license file and,
     * if it grants this module and is unexpired, allow. Returns a state array
     * on grant, or null to fall through to the online DB check.
     */
    private function offlineGrant($m)
    {
        $file   = defined('LICENSE_FILE') ? LICENSE_FILE : (getenv('LICENSE_FILE') ?: '');
        $pubkey = defined('LICENSE_PUBKEY') ? LICENSE_PUBKEY : (APP_ROOT . '/deploy/keys/vendor_public.pem');
        if (!$file || !is_file($file) || !is_file($pubkey)) return null;

        require_once APP_ROOT . '/lib/OfflineLicense.php';
        $payload = OfflineLicense::verify($file, $pubkey);
        if (!$payload) return null;

        $slug = $m['slug'] ?? self::MODULE_SLUG;
        if (!OfflineLicense::grants($payload, $slug)) return null;

        return [
            'allowed' => true,
            'reason'  => 'offline-license',
            'module'  => $m,
            'license' => [
                'type'     => 'offline',
                'customer' => $payload['customer'] ?? '',
                'expiry'   => $payload['expiry'] ?? null,
            ],
        ];
    }

    /** Record an unlicensed run attempt in the license audit trail. */
    private function auditDenied($state)
    {
        try {
            $this->db->insert('license_audit_log', [
                'license_id'   => null,
                'action'       => 'flowsheet_run_denied',
                'performed_by' => $this->user['id'] ?? null,
                'details'      => 'Unlicensed Flowsheet Simulator run blocked at compute entry.',
                'ip_address'   => $_SERVER['REMOTE_ADDR'] ?? null,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);
        } catch (Throwable $e) { /* audit table optional */ }
    }

    private function exampleLibrary()
    {
        return [
            'flash' => [
                'name' => 'Two-phase flash separation',
                'units' => [
                    ['id'=>'F1','type'=>'feed','x'=>60,'y'=>140,'params'=>[
                        'components'=>['Propane'=>30,'n-Butane'=>40,'n-Pentane'=>30],'Tc'=>25,'P'=>600]],
                    ['id'=>'H1','type'=>'heater','x'=>280,'y'=>140,'params'=>['mode'=>'outletT','Tc'=>45,'dP'=>50]],
                    ['id'=>'V1','type'=>'flash','x'=>500,'y'=>140,'params'=>['Tc'=>45,'P'=>450]],
                    ['id'=>'P1','type'=>'product','x'=>720,'y'=>70,'params'=>[]],
                    ['id'=>'P2','type'=>'product','x'=>720,'y'=>210,'params'=>[]],
                ],
                'streams' => [
                    ['id'=>'s1','label'=>'Feed','from'=>['unit'=>'F1','port'=>0],'to'=>['unit'=>'H1','port'=>0]],
                    ['id'=>'s2','label'=>'Heated','from'=>['unit'=>'H1','port'=>0],'to'=>['unit'=>'V1','port'=>0]],
                    ['id'=>'s3','label'=>'Vapor','from'=>['unit'=>'V1','port'=>0],'to'=>['unit'=>'P1','port'=>0]],
                    ['id'=>'s4','label'=>'Liquid','from'=>['unit'=>'V1','port'=>1],'to'=>['unit'=>'P2','port'=>0]],
                ],
            ],
            'depropanizer' => [
                'name' => 'Depropanizer (shortcut column)',
                'units' => [
                    ['id'=>'F1','type'=>'feed','x'=>60,'y'=>150,'params'=>[
                        'components'=>['Ethane'=>10,'Propane'=>40,'i-Butane'=>25,'n-Butane'=>25],'Tc'=>40,'P'=>1600]],
                    ['id'=>'C1','type'=>'column','x'=>320,'y'=>150,'params'=>[
                        'lightKey'=>'Propane','heavyKey'=>'i-Butane','recLK'=>0.98,'recHK'=>0.98,'P'=>1500]],
                    ['id'=>'P1','type'=>'product','x'=>600,'y'=>80,'params'=>[]],
                    ['id'=>'P2','type'=>'product','x'=>600,'y'=>220,'params'=>[]],
                ],
                'streams' => [
                    ['id'=>'s1','label'=>'Feed','from'=>['unit'=>'F1','port'=>0],'to'=>['unit'=>'C1','port'=>0]],
                    ['id'=>'s2','label'=>'Distillate','from'=>['unit'=>'C1','port'=>0],'to'=>['unit'=>'P1','port'=>0]],
                    ['id'=>'s3','label'=>'Bottoms','from'=>['unit'=>'C1','port'=>1],'to'=>['unit'=>'P2','port'=>0]],
                ],
            ],
            'reactor' => [
                'name' => 'Conversion reactor + flash',
                'units' => [
                    ['id'=>'F1','type'=>'feed','x'=>50,'y'=>150,'params'=>[
                        'components'=>['Nitrogen'=>25,'Hydrogen'=>75],'Tc'=>200,'P'=>15000]],
                    ['id'=>'R1','type'=>'reactor','x'=>280,'y'=>150,'params'=>[
                        'mode'=>'isothermal','Tc'=>400,'P'=>15000,
                        'reactions'=>[[
                            'key'=>'Nitrogen','conversion'=>0.25,
                            'stoich'=>['Nitrogen'=>-1,'Hydrogen'=>-3,'Ammonia'=>2],
                            'Hrxn'=>-92000]]]],
                    ['id'=>'V1','type'=>'flash','x'=>520,'y'=>150,'params'=>['Tc'=>-20,'P'=>15000]],
                    ['id'=>'P1','type'=>'product','x'=>740,'y'=>90,'params'=>[]],
                    ['id'=>'P2','type'=>'product','x'=>740,'y'=>220,'params'=>[]],
                ],
                'streams' => [
                    ['id'=>'s1','label'=>'Syngas','from'=>['unit'=>'F1','port'=>0],'to'=>['unit'=>'R1','port'=>0]],
                    ['id'=>'s2','label'=>'Reactor out','from'=>['unit'=>'R1','port'=>0],'to'=>['unit'=>'V1','port'=>0]],
                    ['id'=>'s3','label'=>'Recycle gas','from'=>['unit'=>'V1','port'=>0],'to'=>['unit'=>'P1','port'=>0]],
                    ['id'=>'s4','label'=>'Product','from'=>['unit'=>'V1','port'=>1],'to'=>['unit'=>'P2','port'=>0]],
                ],
            ],
        ];
    }
}
