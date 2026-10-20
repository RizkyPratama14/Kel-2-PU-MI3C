@extends('layouts.app')
@section('title', 'Masuk - Digicam Padang')

@section('content')
<div class="auth-section">
    <div class="auth-card">
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
                <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••">
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
