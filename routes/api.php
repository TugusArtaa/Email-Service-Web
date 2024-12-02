<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\EmailQueueController;
use App\Http\Controllers\EmailLogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Route untuk mengakses controller EmailQueueController
Route::prefix('email-queue')->group(function () {
    Route::post('/sendExcel', [EmailQueueController::class, 'sendEmailsFromExcel']);
    Route::post('/send', [EmailQueueController::class, 'sendEmails']);
    Route::post('/retry', [EmailQueueController::class, 'retryEmails']);
    Route::post('/extract', [EmailQueueController::class, 'extractEmailData']);
    //Route::get('/extract', [EmailQueueController::class, 'extractAllEmailData']);
})->middleware('auth:sanctum');

//Route untuk mengakses controller EmailLogController
Route::prefix('email-logs')->group(function () {
    Route::get('/', [EmailLogController::class, 'integrasi']);
    Route::delete('/delete-id', [EmailLogController::class, 'deleteAll']);
    Route::delete('/delete-date', [EmailLogController::class, 'bulkDelete']);
})->middleware('auth:sanctum');

//Route untuk mengakses controller ApplicationController
Route::prefix('applications')->group(function () {
    Route::post('/', [ApplicationController::class, 'store']);
    Route::get('/', [ApplicationController::class, 'getData']);
    Route::delete('/delete', [ApplicationController::class, 'delete']);
    Route::get('/{application}', [ApplicationController::class, 'show']);
    Route::post('/approve-application', [ApplicationController::class, 'approveApplication']); //untuk melakukan approve saat requestRegenerateSecretKey dan addApplication
    Route::post('/application-status-change', [ApplicationController::class, 'handleApplicationStatusChange']); //untuk mengubah status aplikasi
})->middleware('auth:sanctum');
