<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\RabbitMQController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/send-to-queue', [RabbitMQController::class, 'sendToQueue']);

Route::get('/file/{filename}', function ($filename) {
    $path = storage_path('app/public/assets/' . $filename);

    if (!File::exists($path)) {
        return response()->json(['error' => 'File not found.'], 404);
    }

    return response()->file($path);
});

Route::prefix('applications')->group(function () {
    Route::post('/', [ApplicationController::class, 'store']);
    Route::get('/', [ApplicationController::class, 'index']);
    Route::get('/{application}', [ApplicationController::class, 'show']);
    Route::post('/{application}/regenerate-secret', [ApplicationController::class, 'regenerateSecret']);
});
