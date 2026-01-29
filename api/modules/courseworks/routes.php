<?php

/**
 * @var Router $router
 */

require_once __DIR__ . '/CourseworksController.php';

$controller = new CourseworksController();

$router->get('/courseworks', function (Request $request, Response $response) use ($controller) {
    $controller->getAll($request, $response);
});

$router->get('/courseworks/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->getById($request, $response, $params);
});

$router->post('/courseworks', function (Request $request, Response $response) use ($controller) {
    $controller->create($request, $response);
});

$router->put('/courseworks/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->update($request, $response, $params);
});

$router->patch('/courseworks/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->partialUpdate($request, $response, $params);
});

$router->delete('/courseworks/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->delete($request, $response, $params);
});

$router->post('/courseworks/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->uploadFile($request, $response, $params);
});
