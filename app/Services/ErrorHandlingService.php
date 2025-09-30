<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

class ErrorHandlingService
{
    /**
     * Handle and log exceptions with context
     */
    public function handleException(Throwable $exception, array $context = []): void
    {
        Log::error($exception->getMessage(), [
            'exception' => $exception,
            'context' => $context,
            'user_id' => auth()->id(),
            'url' => request()->fullUrl(),
            'user_agent' => request()->userAgent(),
            'ip' => request()->ip(),
        ]);
    }

    /**
     * Get user-friendly error message based on exception type
     */
    public function getUserFriendlyMessage(Throwable $exception): string
    {
        return match (get_class($exception)) {
            'Illuminate\Database\QueryException' => 'Database error occurred. Please try again.',
            'Illuminate\Validation\ValidationException' => 'Please check your input and try again.',
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException' => 'The requested resource was not found.',
            'Illuminate\Auth\Access\AuthorizationException' => 'You are not authorized to perform this action.',
            'GuzzleHttp\Exception\ConnectException' => 'Unable to connect to external service. Please try again later.',
            'GuzzleHttp\Exception\RequestException' => 'External service error. Please try again later.',
            default => 'An unexpected error occurred. Please try again.'
        };
    }

    /**
     * Handle Livewire component errors with user feedback
     */
    public function handleLivewireError(Throwable $exception, string $operation = 'operation'): array
    {
        $this->handleException($exception, ['operation' => $operation]);

        return [
            'success' => false,
            'message' => $this->getUserFriendlyMessage($exception),
            'error_code' => $this->getErrorCode($exception),
        ];
    }

    /**
     * Handle API errors with proper status codes
     */
    public function handleApiError(Throwable $exception): array
    {
        $this->handleException($exception);

        $statusCode = $this->getStatusCode($exception);

        return [
            'error' => true,
            'message' => $this->getUserFriendlyMessage($exception),
            'status_code' => $statusCode,
            'error_code' => $this->getErrorCode($exception),
        ];
    }

    /**
     * Get appropriate HTTP status code for exception
     */
    private function getStatusCode(Throwable $exception): int
    {
        return match (get_class($exception)) {
            'Illuminate\Validation\ValidationException' => 422,
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException' => 404,
            'Illuminate\Auth\Access\AuthorizationException' => 403,
            'Illuminate\Database\QueryException' => 500,
            default => 500
        };
    }

    /**
     * Get error code for client-side handling
     */
    private function getErrorCode(Throwable $exception): string
    {
        return match (get_class($exception)) {
            'Illuminate\Database\QueryException' => 'DATABASE_ERROR',
            'Illuminate\Validation\ValidationException' => 'VALIDATION_ERROR',
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException' => 'NOT_FOUND',
            'Illuminate\Auth\Access\AuthorizationException' => 'UNAUTHORIZED',
            'GuzzleHttp\Exception\ConnectException' => 'CONNECTION_ERROR',
            'GuzzleHttp\Exception\RequestException' => 'EXTERNAL_SERVICE_ERROR',
            default => 'UNKNOWN_ERROR'
        };
    }

    /**
     * Format validation errors for display
     */
    public function formatValidationErrors(array $errors): array
    {
        $formatted = [];

        foreach ($errors as $field => $messages) {
            $formatted[$field] = is_array($messages) ? implode(', ', $messages) : $messages;
        }

        return $formatted;
    }

    /**
     * Check if error should be shown to user or just logged
     */
    public function shouldShowToUser(Throwable $exception): bool
    {
        // Don't show sensitive database errors to users
        if ($exception instanceof \Illuminate\Database\QueryException) {
            return false;
        }

        // Don't show internal server errors to users
        if ($exception instanceof \ErrorException) {
            return false;
        }

        return true;
    }

    /**
     * Create notification for user-facing errors
     */
    public function createUserNotification(Throwable $exception, string $type = 'error'): array
    {
        return [
            'type' => $type,
            'title' => 'Error',
            'message' => $this->shouldShowToUser($exception)
                ? $exception->getMessage()
                : $this->getUserFriendlyMessage($exception),
            'timeout' => 5000,
        ];
    }
}
