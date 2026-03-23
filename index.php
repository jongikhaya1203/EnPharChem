<?php
/**
 * EnPharChem Platform - Main Entry Point
 * Energy, Pharmaceutical and Chemical Engineering Software
 * Benchmarked against AspenTech EPC Software
 */

session_start();

// Load configuration
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

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

// Route matching
if (isset($routes[$path])) {
    $route = $routes[$path];
    $controllerName = $route['controller'];
    $action = $route['action'];

    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $action)) {
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
