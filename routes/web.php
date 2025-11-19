<?php

use Illuminate\Support\Facades\Route;


    // Authentication routes: simple login form and actions
Route::get('/', [\App\Http\Controllers\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [\App\Http\Controllers\LoginController::class, 'logout'])->name('logout');
// Registration routes
Route::get('/register', [\App\Http\Controllers\LoginController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [\App\Http\Controllers\LoginController::class, 'register'])->name('register.attempt');