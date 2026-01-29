<?php

// The router instance is passed by reference from bootstrap.php
/**
 * @var Router $router
 */

require_once __DIR__ . '/DraftsController.php';

$controller = new DraftsController();

$router->get('/drafts', function (Request $request, Response $response) use ($controller) {
    $controller->getAll($request, $response);
});

$router->get('/drafts/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->getById($request, $response, $params);
});

$router->post('/drafts', function (Request $request, Response $response) use ($controller) {
    $controller->create($request, $response);
});

$router->put('/drafts/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->update($request, $response, $params);
});

$router->patch('/drafts/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->partialUpdate($request, $response, $params);
});

$router->delete('/drafts/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->delete($request, $response, $params);
});

