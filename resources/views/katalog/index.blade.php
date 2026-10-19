@extends('layouts.app')
@section('title', 'Katalog Kamera')

@section('content')
<section class="section">
    <div class="container">
        <h2>Katalog Kamera</h2>
        <p class="sub">Pilih kamera sesuai kebutuhanmu.</p>

        <form method="GET" action="{{ route('katalog') }}" class="filter">
            <input type="text" name="q" placeholder="Cari kamera..." value="{{ request('q') }}">
            <select name="merek">
                <option value="">Semua Merek</option>
                @foreach($mereks as $m)
                    <option value="{{ $m }}" @selected(request('merek') == $m)>{{ $m }}</option>
                @endforeach
            </select>
            <button class="btn">Cari</button>
        </form>

        <div class="grid">
            @forelse($cameras as $c)
            <div class="card">
                <span class="badge {{ $c->status }}">{{ ucfirst($c->status) }}</span>
                <img src="{{ asset('images/'.$c->foto) }}" alt="{{ $c->nama }}"
                     onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'">
                <h3>{{ $c->nama }}</h3>
                <div class="merek">{{ $c->merek }}</div>
                <p class="harga">Rp {{ number_format($c->harga_per_hari, 0, ',', '.') }} / hari</p>
                <a href="{{ route('katalog.show', $c) }}" class="btn">Lihat Detail</a>
            </div>
            @empty
                <p>Kamera tidak ditemukan.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection