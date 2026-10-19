@extends('layouts.app')
@section('title', 'Beranda - Digicam Padang')

@section('content')
<section class="hero">
    <div class="container">
        <h1>Sewa Kamera Mudah di Padang</h1>
        <p>Pilih kamera favoritmu, cek ketersediaan, lalu pesan lewat WhatsApp. Proses mudah, harga terjangkau!</p>
        <div class="hero-btns">
            <a href="{{ route('katalog') }}" class="btn">📷 Lihat Katalog</a>
            <a href="{{ route('cek') }}" class="btn btn-outline">📅 Cek Ketersediaan</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2>Tentang Digicam Padang</h2>
        <p class="sub">Isi dengan latar belakang usaha dari Bu Cinta.</p>
    </div>
</section>
@endsection