<?php
/**
 * EnPharChem Platform - Application Configuration
 */

define('APP_NAME', 'EnPharChem');
define('APP_VERSION', '1.0.0');
define('APP_TAGLINE', 'Energy, Pharmaceutical & Chemical Engineering Platform');
define('APP_URL', 'http://localhost/enpharchem');
define('APP_ROOT', dirname(__DIR__));

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
