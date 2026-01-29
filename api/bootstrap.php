<?php

// Environment setup
error_reporting(E_ALL);
ini_set('display_errors', 1); // Should be 0 in production
date_default_timezone_set('UTC');
// mb_internal_encoding('UTF-8');

// --- Core Dependencies ---
require_once __DIR__ . '/core/ErrorHandler.php';
require_once __DIR__ . '/core/Request.php';
require_once __DIR__ . '/core/Response.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Router.php';

// Register the global error and exception handler
ErrorHandler::register();

// --- Configuration ---
$paths = require __DIR__ . '/config/paths.php';

// --- Application Instantiation ---
$request = new Request();
$router = new Router();
$auth = new Auth(); // Auth class for handling authentication

// --- Middleware: Authentication Check ---
// All API endpoints are protected by default.
// The Auth class will handle pre-flight OPTIONS requests.
$auth->check($request);


// --- ROUTE REGISTRATION ---
// Dynamically load routes from all modules
$modulesPath = __DIR__ . '/modules';
$moduleDirs = array_filter(glob($modulesPath . '/*'), 'is_dir');

foreach ($moduleDirs as $moduleDir) {
    $routesFile = $moduleDir . '/routes.php';
    if (file_exists($routesFile)) {
        // The $router variable is available in the scope of this file
        require $routesFile;
    }
}

// Keep the root path as a health check
$router->get('/', function (Request $request, Response $response) {
    $response->json(['message' => 'Portfolio Backend API is running.'], 200);
});


// --- DISPATCH ---
// The router finds the matching route and executes its handler.
// If no route is found, it will send a 404 response.
$router->dispatch($request);
