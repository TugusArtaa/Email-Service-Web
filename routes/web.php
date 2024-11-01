<?php

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;

Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login')->middleware('guest');

Route::get('/logout', function (Request $request) {
    Auth::logout();
 
    $request->session()->invalidate();
 
    $request->session()->regenerateToken();
 
    return redirect('/');
})->name('logout');

Route::post('/login', [AuthenticationController::class, 'authenticate']);

Route::get('/', function () {
    return Inertia::render('Dashboard');
})->middleware('auth');