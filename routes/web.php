<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CameraController;
use App\Models\Camera;
use Illuminate\Support\Facades\Route;

// Halaman Utama
Route::view('/', 'home')->name('home');
Route::get('/katalog', [CameraController::class, 'index'])->name('katalog');
Route::get('/katalog/{camera}', [CameraController::class, 'show'])->name('katalog.show');
Route::get('/price-list', fn () => view('price_list', ['cameras' => Camera::all()]))->name('price');
Route::view('/aturan-sewa', 'aturan')->name('aturan');

// Cek Ketersediaan
Route::get('/cek-ketersediaan', [CameraController::class, 'checkAvailability'])->name('cek');
Route::post('/cek-ketersediaan', [CameraController::class, 'validateSchedule'])->name('cek.validate')->middleware('auth');

// Booking Routes
Route::middleware('auth')->group(function () {
    Route::get('/booking/form', [BookingController::class, 'form'])->name('booking.form');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1');
    });

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout')->middleware('auth');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', fn () => view('admin.dashboard'))->name('admin.dashboard');
    });
});
