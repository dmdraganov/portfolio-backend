<?php

// This is a router script for the PHP built-in web server.

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// 1. Serve existing files directly from directories like /files
// Example: /files/labs/lab1.pdf
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // Let the server handle the file
}

// 2. Route all API calls to the API's entry point
if (strpos($uri, '/api/') === 0) {
    require_once __DIR__ . '/api/index.php';
    return;
}

// 3. For any other request, return a 404
http_response_code(404);
echo "404 Not Found: The requested resource {$uri} was not found on this server.";

