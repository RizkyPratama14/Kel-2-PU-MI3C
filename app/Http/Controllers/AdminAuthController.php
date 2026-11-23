<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Admin Module: Controller untuk autentikasi admin.
 */
class AdminAuthController extends Controller
{
    /**
     * Menampilkan halaman login admin.
     */
    public function showLogin()
    {
        if (Auth::check() && (Auth::user()->role === 'admin' || (method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin()))) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Memproses autentikasi login admin.
     *
     * Proteksi rate limiting diatur melalui middleware throttle:5,1 di route.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt($credentials)) {
            // Pastikan user yang login benar-benar admin
            $user = Auth::user();
            $isAdmin = $user && ($user->role === 'admin' || (method_exists($user, 'isAdmin') && $user->isAdmin()));

            if (! $isAdmin) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'Akun ini bukan akun admin.']);
            }

            $request->session()->regenerate();

            return redirect()->route('admin.dashboard')
                ->with('success', 'Selamat datang, Admin ' . Auth::user()->name . '!');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau password yang Anda masukkan salah.']);
    }

    /**
     * Memproses logout admin.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Anda berhasil keluar dari panel admin.');
    }
}
