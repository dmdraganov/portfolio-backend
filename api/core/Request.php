<?php

class Request
{
    private array $server;
    private array $get;
    private array $post;
    private array $files;
    private ?string $body;

    public function __construct()
    {
        $this->server = $_SERVER;
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->body = file_get_contents('php://input');
    }

    public function getMethod(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    public function getUri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        // Remove query string
        return strtok($uri, '?');
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function getJsonBody(): ?array
    {
        return json_decode($this->body, true);
    }
    
    public function getQueryParams(): array
    {
        return $this->get;
    }

    public function getPostParams(): array
    {
        return $this->post;
    }
    
    public function getFiles(): array
    {
        return $this->files;
    }
    
    public function getHeader(string $name): ?string
    {
        $headerName = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $this->server[$headerName] ?? null;
    }
}
