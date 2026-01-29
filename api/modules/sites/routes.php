<?php

/**
 * @var Router $router
 */

require_once __DIR__ . '/SitesController.php';

$controller = new SitesController();

// --- Standard CRUD routes for sites ---
$router->get('/sites', function (Request $request, Response $response) use ($controller) {
    $controller->getAll($request, $response);
});

$router->get('/sites/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->getById($request, $response, $params);
});

// POST to /sites is for creating a new site record.
$router->post('/sites', function (Request $request, Response $response) use ($controller) {
    $controller->create($request, $response);
});

$router->put('/sites/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->update($request, $response, $params);
});

$router->patch('/sites/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->partialUpdate($request, $response, $params);
});

$router->delete('/sites/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->delete($request, $response, $params);
});


// --- Custom routes for site content ---

// POST to /sites/{id} is specifically for uploading the site's ZIP archive.
$router->post('/sites/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->uploadZip($request, $response, $params);
});

// --- Routes for managing site page references ---

$router->post('/sites/{id}/references', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->addReference($request, $response, $params);
});

$router->delete('/sites/{id}/references/{fileName}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->deleteReference($request, $response, $params);
});
