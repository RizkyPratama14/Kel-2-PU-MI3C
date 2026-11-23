<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('home');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '! 👋');
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'Email atau kata sandi salah.']);
    }

    public function showRegister()
    {
        if (Auth::check()) return redirect()->route('home');
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'no_whatsapp'  => ['required', 'string', 'min:10', 'max:20'],
            'password'     => ['required', 'confirmed', Password::min(8)],
        ], [
            'name.required'        => 'Nama lengkap wajib diisi.',
            'email.required'       => 'Alamat email wajib diisi.',
            'email.email'          => 'Format email tidak valid.',
            'email.unique'         => 'Email ini sudah terdaftar. Silakan login.',
            'no_whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'no_whatsapp.min'      => 'Nomor WhatsApp minimal 10 digit.',
            'password.required'    => 'Password wajib diisi.',
            'password.confirmed'   => 'Konfirmasi password tidak cocok.',
            'password.min'         => 'Password minimal 8 karakter.',
        ]);

        $data = [
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ];

        // Aman jika migrasi kolom no_whatsapp belum sempat dijalankan
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'no_whatsapp')) {
            $data['no_whatsapp'] = $validated['no_whatsapp'];
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'role')) {
            $data['role'] = 'penyewa';
        }

        $user = User::create($data);

        Auth::login($user);

        return redirect()->route('home')
            ->with('success', 'Akun berhasil dibuat! Selamat datang, ' . $user->name . '! 🎉');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Anda berhasil keluar. Sampai jumpa! 👋');
    }
}
