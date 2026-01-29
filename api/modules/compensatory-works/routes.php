<?php

/**
 * @var Router $router
 */

require_once __DIR__ . '/CompensatoryWorksController.php';

$controller = new CompensatoryWorksController();

$router->get('/compensatory-works', function (Request $request, Response $response) use ($controller) {
    $controller->getAll($request, $response);
});

$router->get('/compensatory-works/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->getById($request, $response, $params);
});

$router->post('/compensatory-works', function (Request $request, Response $response) use ($controller) {
    $controller->create($request, $response);
});

$router->put('/compensatory-works/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->update($request, $response, $params);
});

$router->patch('/compensatory-works/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->partialUpdate($request, $response, $params);
});

$router->delete('/compensatory-works/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->delete($request, $response, $params);
});

$router->post('/compensatory-works/{id}', function (Request $request, Response $response, array $params) use ($controller) {
    $controller->uploadFile($request, $response, $params);
});
