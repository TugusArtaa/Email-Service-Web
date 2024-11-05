<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\EmailQueueController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/send-to-queue', [EmailQueueController::class, 'sendEmails']);
Route::get('/extract-email/{id}', [EmailQueueController::class, 'extractEmailData']);
Route::get('/extract-email', [EmailQueueController::class, 'extractAllEmailData']);

Route::prefix('applications')->group(function () {
    Route::post('/', [ApplicationController::class, 'store']);
    Route::get('/', [ApplicationController::class, 'index']);
    Route::get('/{application}', [ApplicationController::class, 'show']);
    Route::post('/{application}/regenerate-secret', [ApplicationController::class, 'regenerateSecret']);
});
