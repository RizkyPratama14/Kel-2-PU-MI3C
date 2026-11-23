<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CameraController;
use Illuminate\Support\Facades\Route;

// ── Halaman Publik ──────────────────────────────────────────────────────────
Route::view('/', 'home')->name('home');
Route::get('/katalog', [CameraController::class, 'index'])->name('katalog');
Route::get('/katalog/{camera}', [CameraController::class, 'show'])->name('katalog.show');
Route::view('/price-list', 'price_list')->name('price');
Route::view('/aturan-sewa', 'aturan')->name('aturan');

<<<<<<< HEAD
// ── Cek Ketersediaan (US-1003 & US-1004) ───────────────────────────────────
Route::get('/cek-ketersediaan', [CameraController::class, 'checkAvailability'])->name('cek');
Route::post('/cek-ketersediaan', [CameraController::class, 'validateSchedule'])
    ->name('cek.validate')
    ->middleware('auth');

// ── Booking (US-1005 & US-1006) ────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/booking/form', [BookingController::class, 'form'])->name('booking.form');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
});

// ── Autentikasi (US-1008) ───────────────────────────────────────────────────
=======
// Booking Routes (T15/T16)
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store')->middleware('auth');

// Auth Routes
>>>>>>> db308a1cbc261e6d38ede29640ef7550ac5bf4f0
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Admin Routes ────────────────────────────────────────────────────────────
Route::prefix('admin')->group(function () {
    // Login admin (tanpa middleware admin, karena belum login)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1');
    });

    // Logout admin
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout')->middleware('auth');

    // Halaman admin yang diproteksi middleware admin
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', fn () => view('admin.dashboard'))->name('admin.dashboard');
    });
});
