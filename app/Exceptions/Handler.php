<?php

namespace App\Exceptions;

use App\Services\ErrorHandlingService;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
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
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e): SymfonyResponse
    {
        // Handle Livewire component errors
        if ($request->hasHeader('X-Livewire')) {
            return $this->handleLivewireException($request, $e);
        }

        // Handle API errors
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle Livewire-specific exceptions
     */
    protected function handleLivewireException(Request $request, Throwable $e): SymfonyResponse
    {
        $errorService = app(ErrorHandlingService::class);
        $result = $errorService->handleLivewireError($e, 'livewire_request');

        return response()->json([
            'components' => [],
            'effects' => [
                'html' => '',
                'dirty' => [],
                'listeners' => [],
                'redirectTo' => null,
                'returnTo' => null,
            ],
            'serverMemo' => [
                'errors' => [$result['message']],
                'checksum' => '',
                'htmlHash' => '',
                'data' => [],
                'dataMeta' => [],
                'children' => [],
            ],
        ], $result['error_code'] === 'VALIDATION_ERROR' ? 422 : 500);
    }

    /**
     * Handle API-specific exceptions
     */
    protected function handleApiException(Request $request, Throwable $e): SymfonyResponse
    {
        $errorService = app(ErrorHandlingService::class);
        $result = $errorService->handleApiError($e);

        return response()->json($result, $result['status_code']);
    }

    /**
     * Report the exception to logging services
     */
    public function report(Throwable $e): void
    {
        $errorService = app(ErrorHandlingService::class);
        $errorService->handleException($e);

        parent::report($e);
    }
}
