@extends('layouts.app')
@section('title', 'Daftar Akun - Digicam Padang')

@section('content')
<div class="auth-section">
    <div class="auth-card">
        <div class="auth-logo">📷</div>
        <h2>Buat Akun Baru</h2>
        <p class="auth-subtitle">Daftarkan akun untuk kemudahan pemesanan sewa kamera di Digicam Padang.</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <label for="name">Nama Lengkap <span class="required-mark">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       required autofocus autocomplete="name" placeholder="Nama sesuai KTP">
                @error('name')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="email">Alamat Email <span class="required-mark">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       required autocomplete="email" placeholder="nama@email.com">
                @error('email')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="no_whatsapp">Nomor WhatsApp <span class="required-mark">*</span></label>
                <input type="text" id="no_whatsapp" name="no_whatsapp" value="{{ old('no_whatsapp') }}"
                       required placeholder="Contoh: 08123456789" maxlength="20">
                <div class="field-hint">Digunakan untuk konfirmasi booking via WhatsApp.</div>
                @error('no_whatsapp')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="password">Password <span class="required-mark">*</span></label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password"
                           required autocomplete="new-password" placeholder="Minimal 8 karakter">
                    <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password', this)" aria-label="Lihat Password" title="Tampilkan / Sembunyikan Password">
                        <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                        <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <div class="auth-error">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label for="password_confirmation">Konfirmasi Password <span class="required-mark">*</span></label>
                <div class="password-wrapper">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           required autocomplete="new-password" placeholder="Ulangi password">
                    <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password_confirmation', this)" aria-label="Lihat Password" title="Tampilkan / Sembunyikan Password">
                        <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                            <line x1="1" y1="1" x2="23" y2="23"></line>
                        </svg>
                        <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn" style="margin-top: 8px;">✨ Daftar Sekarang</button>
        </form>

        <div class="auth-footer">
            Sudah memiliki akun? <a href="{{ route('login') }}">Masuk di Sini</a>
        </div>
    </div>
</div>
@endsection
