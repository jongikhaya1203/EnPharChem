<?php
/**
 * EnPharChem Platform - Application Configuration
 */

define('APP_NAME', 'EnPharChem');
define('APP_VERSION', '1.0.0');
define('APP_TAGLINE', 'Energy, Pharmaceutical & Chemical Engineering Platform');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost/enpharchem');
define('APP_ROOT', dirname(__DIR__));

// ── Deployment / licensing (env-driven; safe defaults for local dev) ──
// FLOWSHEET_LICENSE_ENFORCE: master switch for compute-time license gating.
$__enf = getenv('FLOWSHEET_LICENSE_ENFORCE');
define('FLOWSHEET_LICENSE_ENFORCE',
    $__enf === false ? true : !in_array(strtolower($__enf), ['0', 'false', 'no', 'off'], true));
// Offline (air-gapped) signed-license file + the vendor public key that verifies it.
define('LICENSE_FILE', getenv('LICENSE_FILE') ?: '');
define('LICENSE_PUBKEY', getenv('LICENSE_PUBKEY') ?: (APP_ROOT . '/deploy/keys/vendor_public.pem'));

// Session configuration
define('SESSION_LIFETIME', 3600 * 8); // 8 hours

// Paths
define('VIEWS_PATH', APP_ROOT . '/views');
define('CONTROLLERS_PATH', APP_ROOT . '/controllers');
define('MODELS_PATH', APP_ROOT . '/models');
define('ASSETS_PATH', APP_ROOT . '/assets');

// Company info
define('COMPANY_NAME', 'EnPharChem Technologies');
define('COMPANY_EMAIL', 'info@enpharchem.com');
define('COMPANY_DESCRIPTION', 'EnPharChem is a global asset management software leader providing enterprise solutions for energy, pharmaceutical, and chemical engineering. Our platform delivers process simulation, advanced process control, manufacturing execution, supply chain optimization, asset performance management, and digital grid management solutions.');
