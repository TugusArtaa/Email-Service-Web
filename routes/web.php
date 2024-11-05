<?php

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\EmailLogController;
use Illuminate\Support\Facades\Auth;

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

Route::get('/', [EmailLogController::class, 'index'])->middleware('auth');