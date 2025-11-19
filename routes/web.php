<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

// Authentication routes: simple login form and actions
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// Registration routes
Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [LoginController::class, 'register'])->name('register.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/homepage', function () {
    return view('homepage');
})->name('homepage');