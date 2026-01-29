<?php

class Router
{
    private array $routes = [];
    private Response $response;
    private string $apiPrefix = '/api';

    public function __construct()
    {
        $this->response = new Response();
    }

    public function addRoute(string $method, string $path, callable $handler): void
    {
        $path = rtrim($this->apiPrefix . $path, '/');
        if (empty($path)) {
            $path = $this->apiPrefix;
        }
        $this->routes[strtoupper($method)][$path] = $handler;
    }

    public function get(string $path, callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, callable $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }
    
    public function patch(string $path, callable $handler): void
    {
        $this->addRoute('PATCH', $path, $handler);
    }

    public function delete(string $path, callable $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    public function dispatch(Request $request): void
    {
        $method = $request->getMethod();
        $uri = rtrim($request->getUri(), '/');
        if (empty($uri)) {
            $uri = '/';
        }

        $routeFound = false;

        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $path => $handler) {
                $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
                if (preg_match('#^' . $pattern . '$#', $uri, $matches)) {
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    $routeFound = true;
                    // Pass request, response, and params to the handler
                    call_user_func_array($handler, [$request, new Response(), $params]);
                    break;
                }
            }
        }

        if (!$routeFound) {
            $this->response->error('Route Not Found', 404);
        }
    }
}
