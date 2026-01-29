<?php

class Auth
{
    private string $password;
    private Response $response;

    public function __construct()
    {
        $authConfigPath = dirname(__DIR__) . '/config/auth.php';
        if (!file_exists($authConfigPath)) {
            throw new Exception("Authentication config file not found.");
        }
        $config = require $authConfigPath;
        // WARNING: Storing passwords in plain text is a significant security risk.
        $this->password = $config['password'];
        $this->response = new Response();
    }

    public function check(Request $request): void
    {
        if ($request->getMethod() === 'OPTIONS') {
            return;
        }

        $pass = $_SERVER['PHP_AUTH_PW'] ?? null;

        if ($pass === null || $pass !== $this->password) {
             $this->sendUnauthorized();
        }
    }

    private function sendUnauthorized(): void
    {
        header('WWW-Authenticate: Basic realm="Portfolio Backend API"');
        $this->response->error('Unauthorized', 401);
    }
}