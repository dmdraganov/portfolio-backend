<?php

/**
 * @var Router $router
 */

require_once __DIR__ . '/LabsController.php';

$controller = new LabsController();

// --- Standard CRUD routes ---

// NOTE: The order of registration matters for routes with similar paths but different methods.
// The router will match the first one it finds.
// For POST, we have two distinct paths: /labs and /labs/{id}. The router can distinguish them.

$router->get('/labs', function (Request $request, Response $response) use ($controller) {
    $controller->getAll($request, $response);
});

$router->get('/labs/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->getById($request, $response, $params);
});

// This POST is for creating a new lab entry (without a file)
$router->post('/labs', function (Request $request, Response $response) use ($controller) {
    $controller->create($request, $response);
});

$router->put('/labs/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->update($request, $response, $params);
});

$router->patch('/labs/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->partialUpdate($request, $response, $params);
});

$router->delete('/labs/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->delete($request, $response, $params);
});

// --- Custom File Upload Route ---

// This POST is for uploading a file to an existing lab, as per documentation.
$router->post('/labs/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->uploadFile($request, $response, $params);
});
