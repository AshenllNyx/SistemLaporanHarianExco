<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
    // Authentication routes: simple login form and actions
Route::get('/login', [\App\Http\Controllers\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [\App\Http\Controllers\LoginController::class, 'logout'])->name('logout');