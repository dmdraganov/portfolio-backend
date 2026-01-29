<?php

require_once __DIR__ . '/Response.php';

class ErrorHandler
{
    public static function register()
    {
        // Set custom error and exception handlers
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
    }

    public static function handleException(Throwable $exception)
    {
        // Ensure headers are not already sent
        if (headers_sent()) {
            // If headers are sent, we can't send a new response.
            // Log the error and terminate.
            error_log("Unhandled exception after headers sent: " . $exception->getMessage());
            exit;
        }

        $response = new Response();
        $statusCode = 500;
        $message = 'Internal Server Error';

        // Determine status code based on exception type
        if ($exception instanceof InvalidArgumentException) {
            $statusCode = 400; // Bad Request
            $message = $exception->getMessage();
        } elseif ($exception instanceof RuntimeException && $exception->getMessage() === 'Not Found') {
            $statusCode = 404; // Not Found
            $message = $exception->getMessage();
        }
        // Add more custom exception mappings here if needed

        // For all other exceptions, if in development mode, show more details
        // Note: You would need a config setting for environment
        // For now, we keep it simple.
        
        $errorData = ['error' => $message];
        
        // Example of adding debug info (don't do this in production)
        // $errorData['file'] = $exception->getFile();
        // $errorData['line'] = $exception->getLine();

        $response->json($errorData, $statusCode);
    }

    public static function handleError(int $errno, string $errstr, string $errfile, int $errline)
    {
        // This error handler converts all PHP errors into exceptions
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting
            return false;
        }
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}
