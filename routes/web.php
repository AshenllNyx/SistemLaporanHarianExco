<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanController;

/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/

// Login
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

// Register
Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [LoginController::class, 'register'])->name('register.attempt');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| PAGES THAT REQUIRE LOGIN (AUTH MIDDLEWARE)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Homepage
    Route::get('/homepage', function () {
        return view('homepage');
    })->name('homepage');

    // Senarai User (admin only)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');


    /*
    |--------------------------------------------------------------------------
    | LAPORAN HARIAN EXCO — ROUTES
    |--------------------------------------------------------------------------
    |
    | Step 1  : Borang laporan dorm
    | Step 2  : Soalan buat laporan disiplin Yes/No
    | Step 3  : Borang laporan disiplin
    | Step 4  : Review laporan
    | Step 5  : Submit laporan
    |
    */

    // ➤ STEP 1 — Papar borang laporan dorm
    Route::get('/laporan/create', [LaporanController::class, 'create'])
        ->name('laporan.create');

    // ➤ STEP 1 — Simpan laporan dorm (draf)
    Route::post('/laporan/store-dorm', [LaporanController::class, 'storeDorm'])
        ->name('laporan.storeDorm');

    // ➤ STEP 2 — Soalan Disiplin? (YA / TIDAK)
    Route::get('/laporan/disiplin/{id}', [LaporanController::class, 'soalanDisiplin'])
        ->name('laporan.disiplin.soalan');

    // ➤ STEP 3 — Borang Laporan Disiplin (jika YA)
    Route::get('/laporan/disiplin/create/{id}', [LaporanController::class, 'createDisiplin'])
        ->name('laporan.disiplin.create');

    // ➤ STEP 3 — Simpan laporan disiplin
    Route::post('/laporan/disiplin/store', [LaporanController::class, 'storeDisiplin'])
        ->name('laporan.disiplin.store');

    // ➤ STEP 4 — Review semua laporan
    Route::get('/laporan/review/{id}', [LaporanController::class, 'review'])
        ->name('laporan.review');

    // ➤ STEP 5 — Submit laporan
    Route::post('/laporan/{laporan}/submit', [LaporanController::class, 'submit'])
        ->name('laporan.submit');
});
