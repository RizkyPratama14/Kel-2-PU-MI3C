@extends('layouts.app')
@section('title', $camera->nama)

@section('content')
<section class="section">
    <div class="container detail">
        <img src="{{ asset('images/'.$camera->foto) }}" alt="{{ $camera->nama }}"
             onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'">
        <div>
            <h2>{{ $camera->nama }}</h2>
            <p class="merek">{{ $camera->merek }} · {{ ucfirst($camera->status) }}</p>
            <p class="harga" style="font-size:22px">Rp {{ number_format($camera->harga_per_hari, 0, ',', '.') }} / hari</p>
            <div style="margin-bottom:24px; line-height: 1.7; color: var(--color-text-muted); white-space: pre-line;">{!! nl2br(e($camera->deskripsi)) !!}</div>
            <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px;">
                <a href="{{ route('cek') }}" class="btn">📅 Cek Ketersediaan</a>
                <a class="btn btn-wa" target="_blank" rel="noopener"
                   href="https://wa.me/6283181125461?text={{ urlencode('Halo Digicam Padang, saya mau tanya sewa kamera '.$camera->nama) }}">
                   💬 Tanya via WhatsApp
                </a>
                <a href="{{ route('price') }}" class="btn btn-outline">📋 Lihat Price List Lengkap</a>
            </div>
            <p style="margin-top:20px"><a href="{{ route('katalog') }}" style="color: var(--color-text-muted); text-decoration: none;">← Kembali ke katalog</a></p>
        </div>
    </div>
</section>
@endsection