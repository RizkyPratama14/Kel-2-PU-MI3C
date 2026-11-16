@extends('layouts.app')
@section('title', 'Katalog Kamera - Digicam Padang')

@section('content')
<section class="section">
    <div class="container">
        <h2>Katalog Kamera</h2>
        <p class="sub">Jelajahi pilihan kamera digital berkualitas kami. Temukan unit yang pas untuk momen terbaikmu.</p>

        <div style="
            text-align: center;
            padding: 64px 24px;
            background: var(--color-bg-card);
            border-radius: var(--radius-lg);
            border: 1px dashed var(--color-border);
            margin-top: 24px;
        ">
            <div style="font-size: 3.5rem; margin-bottom: 16px;">📷</div>
            <h3 style="margin-bottom: 8px; color: var(--color-text);">Belum Ada Kamera</h3>
            <p style="color: var(--color-text-muted); font-size: 1rem; max-width: 380px; margin: 0 auto;">
                Admin belum menambahkan kamera apapun ke dalam katalog.
                Silakan cek kembali beberapa saat lagi.
            </p>
        </div>
    </div>
</section>
@endsection
