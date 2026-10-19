<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CameraController;
use App\Models\Camera;
use Illuminate\Support\Facades\Route;

// Halaman Utama
Route::view('/', 'home')->name('home');
Route::get('/katalog', [CameraController::class, 'index'])->name('katalog');
Route::get('/katalog/{camera}', [CameraController::class, 'show'])->name('katalog.show');
Route::get('/price-list', fn () => view('price-list', ['cameras' => Camera::all()]))->name('price');
Route::view('/aturan-sewa', 'aturan')->name('aturan');
Route::get('/cek-ketersediaan', fn () => view('ketersediaan', ['cameras' => Camera::all()]))->name('cek');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
