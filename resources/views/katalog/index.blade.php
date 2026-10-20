@extends('layouts.app')
@section('title', 'Katalog Kamera - Digicam Padang')

@section('content')
<section class="section">
    <div class="container">
        <h2>Katalog Kamera</h2>
        <p class="sub">Jelajahi pilihan kamera digital berkualitas kami. Temukan unit yang pas untuk momen terbaikmu.</p>

        {{-- Form Pencarian dan Filter (US-1001) --}}
        <form method="GET" action="{{ route('katalog') }}" class="filter">
            <input type="text" name="q" placeholder="Cari nama kamera..." value="{{ request('q') }}">
            <select name="merek">
                <option value="">Semua Merek</option>
                @foreach($mereks as $m)
                    <option value="{{ $m }}" @selected(request('merek') == $m)>{{ $m }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn">🔍 Cari</button>
            @if(request('q') || request('merek'))
                <a href="{{ route('katalog') }}" class="btn btn-outline" style="padding: 11px 18px;">Reset</a>
            @endif
        </form>

        {{-- Daftar Kamera --}}
        <div class="grid">
            @forelse($cameras as $c)
            <div class="card">
                <span class="badge {{ $c->status }}">{{ ucfirst($c->status) }}</span>
                <img src="{{ asset('images/'.$c->foto) }}" alt="{{ $c->nama }}"
                     onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'">
                <h3>{{ $c->nama }}</h3>
                <div class="merek">{{ $c->merek }}</div>
                <p class="harga">Rp {{ number_format($c->harga_per_hari, 0, ',', '.') }} <span style="font-size: 0.8rem; font-weight: normal; color: var(--color-text-muted);">/ 24 jam</span></p>
                <div style="padding: 0 20px 14px; font-size: 0.84rem; color: var(--color-text-muted); line-height: 1.5;">
                    {{ Str::limit(explode("\n", $c->deskripsi)[0], 85) }}
                </div>
                <div style="display: flex; gap: 8px; padding: 0 20px 20px;">
                    <a href="{{ route('katalog.show', $c) }}" class="btn btn-outline btn-sm" style="flex: 1; text-align: center;">Detail</a>
                    <a href="{{ route('cek', ['camera_id' => $c->id]) }}" class="btn btn-sm" style="flex: 1; text-align: center;">Pilih Jadwal</a>
                </div>
            </div>
            @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: var(--color-bg-card); border-radius: var(--radius-lg); border: 1px solid var(--color-border);">
                <p style="color: var(--color-text-muted); font-size: 1.1rem; margin-bottom: 16px;">Kamera tidak ditemukan sesuai pencarian Anda.</p>
                <a href="{{ route('katalog') }}" class="btn btn-outline">Lihat Semua Kamera</a>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection