<?php

/**
 * @var Router $router
 */

require_once __DIR__ . '/PagesController.php';

$controller = new PagesController();

$router->get('/pages', function (Request $request, Response $response) use ($controller) {
    $controller->getAll($request, $response);
});

$router->get('/pages/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->getById($request, $response, $params);
});

$router->post('/pages', function (Request $request, Response $response) use ($controller) {
    $controller->create($request, $response);
});

$router->put('/pages/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->update($request, $response, $params);
});

$router->patch('/pages/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->partialUpdate($request, $response, $params);
});

$router->delete('/pages/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->delete($request, $response, $params);
});
