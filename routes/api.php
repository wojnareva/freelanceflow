<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TimeEntryController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API v1 routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // Clients API
    Route::apiResource('clients', ClientController::class);
    Route::get('clients/{client}/projects', [ClientController::class, 'projects']);
    Route::get('clients/{client}/invoices', [ClientController::class, 'invoices']);
    Route::get('clients/{client}/time-entries', [ClientController::class, 'timeEntries']);

    // Projects API
    Route::apiResource('projects', ProjectController::class);
    Route::get('projects/{project}/tasks', [ProjectController::class, 'tasks']);
    Route::get('projects/{project}/time-entries', [ProjectController::class, 'timeEntries']);
    Route::get('projects/{project}/invoices', [ProjectController::class, 'invoices']);
    Route::get('projects/{project}/expenses', [ProjectController::class, 'expenses']);
    Route::post('projects/{project}/archive', [ProjectController::class, 'archive']);
    Route::post('projects/{project}/restore', [ProjectController::class, 'restore']);

    // Time Entries API
    Route::apiResource('time-entries', TimeEntryController::class);
    Route::post('time-entries/start', [TimeEntryController::class, 'start']);
    Route::post('time-entries/{timeEntry}/stop', [TimeEntryController::class, 'stop']);
    Route::get('time-entries/running', [TimeEntryController::class, 'running']);
    Route::post('time-entries/bulk', [TimeEntryController::class, 'bulk']);

    // Invoices API
    Route::apiResource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/send', [InvoiceController::class, 'send']);
    Route::post('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid']);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf']);
    Route::post('invoices/from-time-entries', [InvoiceController::class, 'createFromTimeEntries']);

    // Expenses API
    Route::apiResource('expenses', ExpenseController::class);
    Route::post('expenses/{expense}/mark-billable', [ExpenseController::class, 'markBillable']);
    Route::post('expenses/{expense}/mark-billed', [ExpenseController::class, 'markBilled']);

    // Reports API
    Route::prefix('reports')->group(function () {
        Route::get('overview', [\App\Http\Controllers\Api\ReportController::class, 'overview']);
        Route::get('revenue', [\App\Http\Controllers\Api\ReportController::class, 'revenue']);
        Route::get('expenses', [\App\Http\Controllers\Api\ReportController::class, 'expenses']);
        Route::get('time-tracking', [\App\Http\Controllers\Api\ReportController::class, 'timeTracking']);
        Route::get('profitability', [\App\Http\Controllers\Api\ReportController::class, 'profitability']);
    });

    // Webhooks management API
    Route::apiResource('webhooks', WebhookController::class);
    Route::post('webhooks/{webhook}/test', [WebhookController::class, 'test']);
});

// Public webhook endpoints (no authentication required)
Route::prefix('webhooks')->group(function () {
    Route::post('payment/{webhook_id}', [\App\Http\Controllers\WebhookHandlerController::class, 'handlePayment']);
    Route::post('expense/{webhook_id}', [\App\Http\Controllers\WebhookHandlerController::class, 'handleExpense']);
});