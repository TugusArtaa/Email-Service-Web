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

    // Route untuk pengiriman email (send, sendExcel)
    Route::post('/sendExcel', [EmailQueueController::class, 'sendEmailsFromExcel']);
    Route::post('/send', [EmailQueueController::class, 'sendEmails']);

    // Route untuk mengambil detail data log email
    Route::post('/extract', [EmailQueueController::class, 'extractEmailData']);
})->middleware('auth:sanctum');

//Route untuk mengakses controller EmailLogController
Route::prefix('email-logs')->group(function () {

    // Route untuk mengambil data log email
    Route::get('/', [EmailLogController::class, 'integrasi']);
})->middleware('auth:sanctum');

//Route untuk mengakses controller ApplicationController
Route::prefix('applications')->group(function () {
    // Route untuk mengambil data aplikasi, detail aplikasi, dan data aplikasi untuk approval
    Route::get('/', [ApplicationController::class, 'getData']);
    Route::get('/approve', [ApplicationController::class, 'getApproveData']);
    Route::get('/{application}', [ApplicationController::class, 'show']);

    // Route untuk delete aplikasi, approve aplikasi (registrasi dan regenerate secret), dan mengubah status aplikasi
    Route::delete('/delete', [ApplicationController::class, 'delete']);
    Route::post('/approve-application', [ApplicationController::class, 'approveApplication']); 
    Route::post('/application-status-change', [ApplicationController::class, 'handleApplicationStatusChange']);

    Route::post('/application-status-change', [ApplicationController::class, 'handleApplicationStatusChange']);
})->middleware('auth:sanctum');