<?php
/**
 * EnPharChem Platform - Main Entry Point
 * Energy, Pharmaceutical and Chemical Engineering Software
 * Benchmarked against AspenTech EPC Software
 */

// Load configuration first so the session can be hardened with SESSION_LIFETIME
// and the correct cookie flags before it is started.
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

// --- Session hardening -------------------------------------------------------
// Only mark the cookie Secure when the request is actually over HTTPS, so plain
// local/XAMPP HTTP installs keep working.
$httpsOn = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
    || (($_SERVER['SERVER_PORT'] ?? null) == 443)
    || (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
ini_set('session.use_strict_mode', '1');   // reject attacker-supplied session ids
ini_set('session.use_only_cookies', '1');
ini_set('session.gc_maxlifetime', (string) SESSION_LIFETIME);
session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path'     => '/',
    'httponly' => true,                      // not reachable from JavaScript
    'secure'   => $httpsOn,
    'samesite' => 'Lax',                     // blocks cross-site cookie sending
]);
session_start();

// Absolute session lifetime: expire sessions older than SESSION_LIFETIME.
if (isset($_SESSION['created_at']) && (time() - $_SESSION['created_at']) > SESSION_LIFETIME) {
    $_SESSION = [];
    session_destroy();
    session_start();
}
if (!isset($_SESSION['created_at'])) {
    $_SESSION['created_at'] = time();
}

// Autoload controllers and models
spl_autoload_register(function ($class) {
    $paths = [
        CONTROLLERS_PATH . '/' . $class . '.php',
        MODELS_PATH . '/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

require_once __DIR__ . '/lib/Csrf.php';

// Load routes
$routes = require_once __DIR__ . '/config/routes.php';

// Parse request URI
$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/enpharchem/';
$path = str_replace($basePath, '', parse_url($requestUri, PHP_URL_PATH));
$path = trim($path, '/');

// Check authentication (skip for login/register)
$publicRoutes = ['login', 'register'];
if (!in_array($path, $publicRoutes) && !isset($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/login');
    exit;
}

// --- CSRF: single choke point for every state-changing request ---------------
// Enforced here rather than per handler so a new POST action is protected by
// default. Runs after the auth check above, so a POST on an expired session
// lands on the login page instead of an unexplained block page. GET/HEAD are
// not gated — actions that mutate state must use POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Csrf::check()) {
    http_response_code(403);
    if (Csrf::clientWantsJson()) {
        header('Content-Type: application/json');
        echo json_encode([
            'ok'      => false,
            'success' => false,
            'error'   => 'CSRF token missing or invalid. Reload the page and try again.',
        ]);
    } else {
        include VIEWS_PATH . '/errors/csrf.php';
    }
    exit;
}

// Route matching
if (isset($routes[$path])) {
    $route = $routes[$path];
    $controllerName = $route['controller'];
    $action = $route['action'];

    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        // method_exists() also matches BaseController's protected helpers
        // (view, json, redirect, ...), so a route action colliding with one
        // would fatal on invocation instead of 404ing. Only dispatch to
        // genuinely public actions.
        if (method_exists($controller, $action)
            && (new ReflectionMethod($controller, $action))->isPublic()) {
            $controller->$action();
        } else {
            http_response_code(404);
            include VIEWS_PATH . '/errors/404.php';
        }
    } else {
        http_response_code(404);
        include VIEWS_PATH . '/errors/404.php';
    }
} else {
    http_response_code(404);
    include VIEWS_PATH . '/errors/404.php';
}
