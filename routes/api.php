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
    Route::post('/send', [EmailQueueController::class, 'sendEmails']);
    Route::get('/extract/{id}', [EmailQueueController::class, 'extractEmailData']);
    Route::get('/extract', [EmailQueueController::class, 'extractAllEmailData']);
});

//Route untuk mengakses controller EmailLogController
Route::prefix('email-logs')->group(function () {
    Route::get('/', [EmailLogController::class, 'index']);
});

//Route untuk mengakses controller ApplicationController
Route::prefix('applications')->group(function () {
    Route::post('/', [ApplicationController::class, 'store']);
    Route::get('/', [ApplicationController::class, 'index']);
    Route::get('/{application}', [ApplicationController::class, 'show']);
    Route::post('/{application}/regenerate-secret', [ApplicationController::class, 'regenerateSecret']);
});