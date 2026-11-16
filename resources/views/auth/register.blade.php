@extends('layouts.app')
@section('title', 'Daftar Akun - Digicam Padang')

@section('content')
<div class="auth-section">
    <div class="auth-card">
        <h2>Buat Akun Baru</h2>
        <p class="auth-subtitle">Daftarkan akun untuk kemudahan pemesanan sewa kamera.</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama Anda">
                @error('name')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="email">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="nama@email.com">
                @error('email')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter">
                @error('password')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password">
            </div>

            <button type="submit" class="btn">Daftar Akun</button>
        </form>

        <div class="auth-footer">
            Sudah memiliki akun? <a href="{{ route('login') }}" style="color: var(--color-accent); text-decoration: none;">Masuk di Sini</a>
        </div>
    </div>
</div>
@endsection
