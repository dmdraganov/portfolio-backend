<?php

class Response
{
    public function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }

    public function error(string $message, int $statusCode = 400): void
    {
        $this->json(['error' => $message], $statusCode);
    }

    public function success(string $message, int $statusCode = 200): void
    {
        $this->json(['message' => $message], $statusCode);
    }
    
    public function noContent(): void
    {
        http_response_code(204);
        exit();
    }
}
