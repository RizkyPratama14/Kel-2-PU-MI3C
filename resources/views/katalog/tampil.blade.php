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
            <p style="margin-bottom:20px">{{ $camera->deskripsi }}</p>
            <a href="{{ route('cek') }}" class="btn">Cek Ketersediaan</a>
            <a class="btn btn-outline" target="_blank"
               href="https://wa.me/62XXXXXXXXXXX?text={{ urlencode('Halo, saya mau tanya sewa kamera '.$camera->nama) }}">Tanya via WhatsApp</a>
            <p style="margin-top:20px"><a href="{{ route('katalog') }}">← Kembali ke katalog</a></p>
        </div>
    </div>
</section>
@endsection