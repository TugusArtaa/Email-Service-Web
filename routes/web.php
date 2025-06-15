<?php

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\EmailQueueController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Middleware\CheckUserLevel;

// Authentication Routes
Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login')->middleware('guest');

Route::post('/login', [AuthenticationController::class, 'authenticate']);

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Dashboard (All Authenticated Users)
Route::middleware('auth')->group(function () {
    Route::get('/', [EmailLogController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Email Integration
    Route::prefix('integrasi')->group(function () {
        Route::get('/', function () {
            return Inertia::render('Integrasi');
        })->name('integrasi.index');
        Route::delete('/delete', [EmailLogController::class, 'deleteAll'])->name('application.delete');
    });

    // Email Log
    Route::get('/email-logs', [EmailLogController::class, 'integrasi']);

    // Email Queue Extract
    Route::post('/email-queue/extract', [EmailQueueController::class, 'extractEmailData']);

    // Application Management
    Route::get('/application', [ApplicationController::class, 'index'])->name('application.index');
    Route::post('/application', [ApplicationController::class, 'store'])->name('application.index');

    // Applications
    Route::prefix('applications')->group(function () {
        Route::get('/', [ApplicationController::class, 'getData']);
        Route::get('/approve', [ApplicationController::class, 'getApproveData']);
        Route::get('/{application}', [ApplicationController::class, 'show']);
        Route::delete('/delete', [ApplicationController::class, 'delete']);
        Route::post('/approve-application', [ApplicationController::class, 'approveApplication']);
        Route::post('/application-status-change', [ApplicationController::class, 'handleApplicationStatusChange']);
    });
});

// Supervisor Only: Approve Section
Route::prefix('approve')
    ->middleware(['auth', CheckUserLevel::class . ':supervisor'])
    ->group(function () {
        Route::get('/', function () {
            return Inertia::render('ApproveApp');
        })->name('approve.index');
    });