<?php

/**
 * @var Router $router
 */

require_once __DIR__ . '/PracticesController.php';

$controller = new PracticesController();

$router->get('/practices', function (Request $request, Response $response) use ($controller) {
    $controller->getAll($request, $response);
});

$router->get('/practices/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->getById($request, $response, $params);
});

$router->post('/practices', function (Request $request, Response $response) use ($controller) {
    $controller->create($request, $response);
});

$router->put('/practices/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->update($request, $response, $params);
});

$router->patch('/practices/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->partialUpdate($request, $response, $params);
});

$router->delete('/practices/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->delete($request, $response, $params);
});

$router->post('/practices/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->uploadFile($request, $response, $params);
});
