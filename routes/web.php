<?php

use App\Http\Controllers\ProfileController;
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
    
    Route::get('/projects/{project}', function (App\Models\Project $project) {
        return view('projects.show', compact('project'));
    })->name('projects.show');
    
    Route::get('/projects/{project}/kanban', function (App\Models\Project $project) {
        return view('projects.kanban', compact('project'));
    })->name('projects.kanban');
    
    Route::get('/projects/{project}/timeline', function (App\Models\Project $project) {
        return view('projects.timeline', compact('project'));
    })->name('projects.timeline');
    
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
        return response()->download(storage_path('app/invoices/invoice-' . $invoice->id . '.pdf'));
    })->name('invoices.pdf');
});

require __DIR__.'/auth.php';
