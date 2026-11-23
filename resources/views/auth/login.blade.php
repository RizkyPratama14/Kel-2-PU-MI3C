@extends('layouts.app')
@section('title', 'Masuk - Digicam Padang')

@section('content')
<div class="auth-section">
    <div class="auth-card">
        <div style="text-align: center; margin-bottom: 14px;">
            <img src="{{ asset('images/logo-grent.png') }}" alt="GRENT Logo" style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,127,162,0.4); box-shadow: 0 4px 12px rgba(255,127,162,0.25);">
        </div>
        <h2>Masuk ke Akun Anda</h2>
        <p class="auth-subtitle">Silakan masukkan email dan password untuk melanjutkan.</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="nama@email.com">
                @error('email')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                    <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password', this)" aria-label="Lihat Password" title="Tampilkan / Sembunyikan Password">
                        <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                        <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember" style="margin: 0; cursor: pointer; font-weight: normal; color: var(--color-text-muted);">Ingat saya</label>
            </div>

            <button type="submit" class="btn">Masuk</button>
        </form>

        <div class="auth-footer">
            Belum punya akun? <a href="{{ route('register') }}" style="color: var(--color-accent); text-decoration: none;">Daftar Sekarang</a>
        </div>
    </div>
</div>
@endsection
