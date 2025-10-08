<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Mask sensitive data in logs
            if ($this->shouldMaskSensitiveData($e)) {
                Log::warning('Exception occurred with sensitive data (masked)', [
                    'exception' => get_class($e),
                    'message' => $this->maskSensitiveData($e->getMessage()),
                ]);
                return false; // Prevent default reporting
            }
        });
    }

    /**
     * Check if exception contains sensitive data that should be masked
     */
    private function shouldMaskSensitiveData(Throwable $e): bool
    {
        $message = $e->getMessage();
        $sensitivePatterns = ['token', 'password', 'secret', 'api_key', 'access_token'];
        
        foreach ($sensitivePatterns as $pattern) {
            if (stripos($message, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Mask sensitive data in error messages
     */
    private function maskSensitiveData(string $message): string
    {
        return preg_replace('/(["\'])([\w\-\.]+@[\w\-\.]+|[a-zA-Z0-9_\-]{20,})(["\'])/', '$1***MASKED***$3', $message);
    }
}

