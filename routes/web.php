<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\DormController;

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
    Route::get('/homepage', [LaporanController::class, 'homepage'])->name('homepage');
    
    // Admin Homepage - Report Tracking
    Route::get('/homepage-admin', [LaporanController::class, 'homepageAdmin'])->name('homepage.admin');

    // Profile Management
    Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::get('/profile/edit-admin', [UserController::class, 'editProfileAdmin'])->name('profile.editAdmin');
    Route::put('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');

    // Dorms
    Route::get('/dorms', [DormController::class, 'index'])->name('dorms.index');
    Route::get('/dorms/create', [DormController::class, 'create'])->name('dorms.create');
    Route::post('/dorms', [DormController::class, 'store'])->name('dorms.store');

    // Senarai User (admin only)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/{no_ic}/approve', [UserController::class, 'approve'])->name('users.approve');
    Route::get('/users/{no_ic}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{no_ic}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{no_ic}', [UserController::class, 'destroy'])->name('users.destroy');

    //search laporan
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

    /*
    |--------------------------------------------------------------------------
    | LAPORAN HARIAN EXCO — ROUTES
    |--------------------------------------------------------------------------
    |
    | Step 1  : Borang laporan dorm
    | Step 2  : Soalan buat laporan disiplin Yes/No
    | Step 3  : Borang laporan disiplin
    | Step 4  : Soalan buat laporan kerosakan Yes/No
    | Step 5  : Borang laporan kerosakan
    | Step 6  : Review laporan
    | Step 7  : Submit laporan
    |
    */

    // ➤ STEP 1 — Papar borang laporan dorm
    Route::get('/laporan/create', [LaporanController::class, 'create'])
        ->name('laporan.create');

    // ➤ STEP 1 — Simpan laporan dorm (draf)
    Route::post('/laporan/store-dorm', [LaporanController::class, 'storeDorm'])
        ->name('laporan.storeDorm');

    // ========================================
    // DISIPLIN
    // ========================================

    // ➤ STEP 2 — Soalan Disiplin? (YA / TIDAK)
    Route::get('/laporan/disiplin/{id}', [LaporanController::class, 'soalanDisiplin'])
        ->name('laporan.disiplin.soalan');

    // ➤ STEP 3 — Borang Laporan Disiplin (jika YA)
    Route::get('/laporan/disiplin/create/{id}', [LaporanController::class, 'createDisiplin'])
        ->name('laporan.disiplin.create');

    // ➤ STEP 3 — Simpan laporan disiplin
    Route::post('/laporan/disiplin/store', [LaporanController::class, 'storeDisiplin'])
        ->name('laporan.disiplin.store');

    // ========================================
    // KEROSAKAN
    // ========================================

    // ➤ STEP 4 — Soalan Kerosakan? (YA / TIDAK)
    Route::get('/laporan/kerosakan/{id}', [LaporanController::class, 'soalanKerosakan'])
        ->name('laporan.kerosakan.soalan');

    // ➤ STEP 5 — Borang Laporan Kerosakan (jika YA)
    Route::get('/laporan/kerosakan/create/{id}', [LaporanController::class, 'createKerosakan'])
        ->name('laporan.kerosakan.create');

    // ➤ STEP 5 — Simpan laporan kerosakan
    Route::post('/laporan/kerosakan/store', [LaporanController::class, 'storeKerosakan'])
        ->name('laporan.kerosakan.store');

    // ========================================
    // REVIEW & SUBMIT
    // ========================================

    // ➤ STEP 6 — Review semua laporan
    Route::get('/laporan/review/{id}', [LaporanController::class, 'review'])
        ->name('laporan.review');

    // ➤ DELETE — Hapus laporan (admin)
    Route::delete('/laporan/{laporan}', [LaporanController::class, 'destroy'])
        ->name('laporan.destroy');

    // ➤ STEP 7 — Submit laporan
    Route::post('/laporan/{laporan}/submit', [LaporanController::class, 'submit'])
        ->name('laporan.submit');

    // review admin
    Route::get('/laporan/review-admin/{id}', [LaporanController::class, 'reviewAdmin'])
        ->name('laporan.reviewAdmin');

    // ➤ PENGESAHAN — Admin confirm laporan
    Route::post('/laporan/{laporan}/pengesahan', [LaporanController::class, 'pengesahan'])
        ->name('laporan.pengesahan');

    // ➤ HANTAR SEMULA — Admin mark laporan for resubmission
    Route::post('/laporan/{laporan}/hantar-semula', [LaporanController::class, 'hantarSemula'])
        ->name('laporan.hantarSemula');   

    // ========================================
    // PELAJAR SAKIT
    // ========================================

    // ➤ STEP 6 — Soalan Pelajar Sakit? (YA / TIDAK)
    Route::get('/laporan/pelajar-sakit/{id}', [LaporanController::class, 'soalanPelajarSakit'])
        ->name('laporan.pelajarsakit.soalan');

    // ➤ STEP 7 — Borang Laporan Pelajar Sakit (jika YA)
    Route::get('/laporan/pelajar-sakit/create/{id}', [LaporanController::class, 'createPelajarSakit'])
        ->name('laporan.pelajarsakit.create');

    // ➤ STEP 7 — Simpan laporan pelajar sakit
    Route::post('/laporan/pelajar-sakit/store', [LaporanController::class, 'storePelajarSakit'])
        ->name('laporan.pelajarsakit.store');

    // ========================================
    // DEWAN MAKAN
    // ========================================

    // ➤ STEP 8 — Soalan Dewan Makan? (YA / TIDAK)
    Route::get('/laporan/dewan-makan/{id}', [LaporanController::class, 'soalanDewanMakan'])
        ->name('laporan.dewanmakan.soalan');

    // ➤ STEP 9 — Borang Laporan Dewan Makan (jika YA)
    Route::get('/laporan/dewan-makan/create/{id}', [LaporanController::class, 'createDewanMakan'])
        ->name('laporan.dewanmakan.create');

    // ➤ STEP 9 — Simpan laporan dewan makan
    Route::post('/laporan/dewan-makan/store', [LaporanController::class, 'storeDewanMakan'])
        ->name('laporan.dewanmakan.store');
});