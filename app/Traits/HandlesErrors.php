<?php

namespace App\Traits;

use App\Services\ErrorHandlingService;
use Throwable;

trait HandlesErrors
{
    /**
     * Handle exceptions in Livewire components with user feedback
     */
    protected function handleError(Throwable $exception, string $operation = 'operation'): void
    {
        $errorService = app(ErrorHandlingService::class);
        $result = $errorService->handleLivewireError($exception, $operation);

        // Flash error message to session
        session()->flash('error', $result['message']);

        // Emit error event for client-side handling
        $this->dispatch('error-occurred', [
            'message' => $result['message'],
            'code' => $result['error_code'],
        ]);
    }

    /**
     * Try to execute an operation with error handling
     */
    protected function tryOperation(callable $operation, string $operationName = 'operation'): bool
    {
        try {
            $operation();

            return true;
        } catch (Throwable $e) {
            $this->handleError($e, $operationName);

            return false;
        }
    }

    /**
     * Display success message
     */
    protected function showSuccess(string $message): void
    {
        session()->flash('success', $message);

        $this->dispatch('success-occurred', [
            'message' => $message,
        ]);
    }

    /**
     * Display warning message
     */
    protected function showWarning(string $message): void
    {
        session()->flash('warning', $message);

        $this->dispatch('warning-occurred', [
            'message' => $message,
        ]);
    }

    /**
     * Display info message
     */
    protected function showInfo(string $message): void
    {
        session()->flash('info', $message);

        $this->dispatch('info-occurred', [
            'message' => $message,
        ]);
    }

    /**
     * Validate data with error handling
     */
    protected function validateWithErrorHandling(array $rules, array $messages = []): bool
    {
        try {
            $this->validate($rules, $messages);

            return true;
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorService = app(ErrorHandlingService::class);
            $errors = $errorService->formatValidationErrors($e->errors());

            foreach ($errors as $field => $message) {
                $this->addError($field, $message);
            }

            return false;
        } catch (Throwable $e) {
            $this->handleError($e, 'validation');

            return false;
        }
    }

    /**
     * Reset error bag with specific field
     */
    protected function clearError(string $field): void
    {
        $this->resetErrorBag($field);
    }

    /**
     * Reset all errors
     */
    protected function clearAllErrors(): void
    {
        $this->resetErrorBag();
    }
}
