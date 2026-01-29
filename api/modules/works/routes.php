<?php

/**
 * @var Router $router
 */

require_once __DIR__ . '/WorksController.php';

$controller = new WorksController();

$router->get('/works', function (Request $request, Response $response) use ($controller) {
    $controller->getAll($request, $response);
});
