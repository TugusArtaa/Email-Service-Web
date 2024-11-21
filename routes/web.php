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

Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login')->middleware('guest');

Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();

    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');

Route::post('/login', [AuthenticationController::class, 'authenticate']);

Route::get('/', [EmailLogController::class, 'index'])->middleware('auth')->name('dashboard');

Route::prefix('integrasi')->group(function () {
    Route::get('/', function(){
        return Inertia::render('Integrasi');
    })->name('integrasi.index');
    Route::post('/send', [EmailQueueController::class, 'sendEmails']);
    Route::delete('/delete', [EmailLogController::class, 'deleteAll'])->name('application.delete');
    Route::delete('/delete-date', [EmailLogController::class, 'bulkDelete'])->name('application.deleteDate');

})->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

Route::prefix('application')->group(function () {
    Route::get('/', [ApplicationController::class, 'index'])->name('application.index');
    Route::post('/', [ApplicationController::class, 'store'])->name('application.index');
    Route::delete('/delete', [ApplicationController::class, 'delete'])->name('application.delete');
    Route::post('/{application}/regenerate-secret', [ApplicationController::class, 'regenerateSecret'])->name('application.delete');
})->middleware('auth');