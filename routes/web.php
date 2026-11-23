<?php

<<<<<<< Updated upstream
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
=======
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
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
>>>>>>> Stashed changes
});
