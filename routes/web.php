<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Time Tracking Routes
    Route::get('/time-tracking', function () {
        return view('time-tracking.index');
    })->name('time-tracking.index');

    Route::get('/time-tracking/calendar', function () {
        return view('time-tracking.calendar');
    })->name('time-tracking.calendar');

    Route::get('/time-tracking/bulk-edit', function () {
        return view('time-tracking.bulk-edit');
    })->name('time-tracking.bulk-edit');

    // Projects Routes
    Route::get('/projects', function () {
        return view('projects.index');
    })->name('projects.index');

    Route::get('/projects/timeline', function () {
        return view('projects.timeline-all');
    })->name('projects.timeline-all');

    Route::get('/projects/{project}', function (App\Models\Project $project) {
        return view('projects.show', compact('project'));
    })->name('projects.show');

    Route::get('/projects/{project}/kanban', function (App\Models\Project $project) {
        return view('projects.kanban', compact('project'));
    })->name('projects.kanban');

    Route::get('/projects/{project}/timeline', function (App\Models\Project $project) {
        return view('projects.timeline', compact('project'));
    })->name('projects.timeline');

    // Clients Routes
    Route::get('/clients', function () {
        return view('clients.index');
    })->name('clients.index');

    Route::get('/clients/create', function () {
        return view('clients.create');
    })->name('clients.create');

    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');

    Route::get('/clients/{client}', function (App\Models\Client $client) {
        return view('clients.show', compact('client'));
    })->name('clients.show');

    // Invoicing Routes
    Route::get('/invoices', function () {
        return view('invoices.index');
    })->name('invoices.index');

    Route::get('/invoices/create', function () {
        return view('invoices.create');
    })->name('invoices.create');

    Route::get('/invoices/{invoice}', function (App\Models\Invoice $invoice) {
        return view('invoices.show', compact('invoice'));
    })->name('invoices.show');

    Route::get('/invoices/{invoice}/pdf', function (App\Models\Invoice $invoice) {
        $pdf = PDF::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download('invoice-'.$invoice->invoice_number.'.pdf');
    })->name('invoices.pdf');

    // Invoice Templates (Recurring Invoices) Routes
    Route::get('/invoice-templates', function () {
        return view('invoice-templates.index');
    })->name('invoice-templates.index');

    Route::get('/invoice-templates/create', function () {
        return view('invoice-templates.create');
    })->name('invoice-templates.create');

    Route::get('/invoice-templates/{template}/edit', function (App\Models\InvoiceTemplate $template) {
        return view('invoice-templates.edit', compact('template'));
    })->name('invoice-templates.edit');

    // Expenses Routes
    Route::get('/expenses', function () {
        return view('expenses.index');
    })->name('expenses.index');

    Route::get('/expenses/create', function () {
        return view('expenses.create');
    })->name('expenses.create');

    Route::get('/expenses/{expense}/edit', function (App\Models\Expense $expense) {
        return view('expenses.edit', compact('expense'));
    })->name('expenses.edit');
});

require __DIR__.'/auth.php';
