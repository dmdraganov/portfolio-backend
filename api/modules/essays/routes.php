<?php

/**
 * @var Router $router
 */

require_once __DIR__ . '/EssaysController.php';

$controller = new EssaysController();

$router->get('/essays', function (Request $request, Response $response) use ($controller) {
    $controller->getAll($request, $response);
});

$router->get('/essays/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->getById($request, $response, $params);
});

$router->post('/essays', function (Request $request, Response $response) use ($controller) {
    $controller->create($request, $response);
});

$router->put('/essays/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->update($request, $response, $params);
});

$router->patch('/essays/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->partialUpdate($request, $response, $params);
});

$router->delete('/essays/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->delete($request, $response, $params);
});

$router->post('/essays/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->uploadFile($request, $response, $params);
});
