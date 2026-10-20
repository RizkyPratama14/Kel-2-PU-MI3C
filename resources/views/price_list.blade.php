@extends('layouts.app')
@section('title', 'Price List - Digicam Padang')

@section('content')
<section class="section">
    <div class="container">
        <h2>Price List</h2>
        <p class="sub">Harga sewa kamera @digicampadang_ — Pilih durasi sesuai kebutuhanmu.</p>

        <div class="price-list-grid">
            {{-- Canon EOS M10 --}}
            <div class="price-card">
                <div class="price-card-header">
                    <img src="{{ asset('images/canon-eos-m10.jpg') }}" alt="Canon EOS M10" class="price-card-img"
                         onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'">
                    <div>
                        <h3 class="price-card-name">Canon EOS M10</h3>
                        <div class="price-card-brand">Canon</div>
                    </div>
                </div>
                <div class="price-card-body">
                    <div class="price-row">
                        <span class="price-duration">3 Jam</span>
                        <span class="price-amount">Rp 30.000</span>
                    </div>
                    <div class="price-row">
                        <span class="price-duration">6 Jam</span>
                        <span class="price-amount">Rp 50.000</span>
                    </div>
                    <div class="price-row">
                        <span class="price-duration">8 Jam</span>
                        <span class="price-amount">Rp 65.000</span>
                    </div>
                    <div class="price-row">
                        <span class="price-duration">12 Jam</span>
                        <span class="price-amount">Rp 80.000</span>
                    </div>
                    <div class="price-row">
                        <span class="price-duration">24 Jam</span>
                        <span class="price-amount">Rp 100.000</span>
                    </div>
                </div>
                <div style="padding: 0 24px 20px;">
                    <a href="https://wa.me/6283181125461?text={{ urlencode('Halo Digicam Padang, saya ingin sewa kamera Canon EOS M10') }}" class="btn btn-wa" style="width: 100%; font-size: 0.85rem;" target="_blank" rel="noopener">💬 Sewa Kamera Ini</a>
                </div>
            </div>

            {{-- Sony Cybershot W320 --}}
            <div class="price-card">
                <div class="price-card-header">
                    <img src="{{ asset('images/sony-cybershot-w320.jpg') }}" alt="Sony Cybershot W320" class="price-card-img"
                         onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'">
                    <div>
                        <h3 class="price-card-name">Sony Cybershot W320</h3>
                        <div class="price-card-brand">Sony</div>
                    </div>
                </div>
                <div class="price-card-body">
                    <div class="price-row">
                        <span class="price-duration">6 Jam</span>
                        <span class="price-amount">Rp 25.000</span>
                    </div>
                    <div class="price-row">
                        <span class="price-duration">12 Jam</span>
                        <span class="price-amount">Rp 50.000</span>
                    </div>
                    <div class="price-row">
                        <span class="price-duration">24 Jam</span>
                        <span class="price-amount">Rp 70.000</span>
                    </div>
                    <div class="price-row">
                        <span class="price-duration">48 Jam</span>
                        <span class="price-amount">Rp 120.000</span>
                    </div>
                    <div class="price-row">
                        <span class="price-duration">3 Hari - Lebih</span>
                        <span class="price-amount">Rp 55.000</span>
                    </div>
                </div>
                <div style="padding: 0 24px 20px;">
                    <a href="https://wa.me/6283181125461?text={{ urlencode('Halo Digicam Padang, saya ingin sewa kamera Sony Cybershot W320') }}" class="btn btn-wa" style="width: 100%; font-size: 0.85rem;" target="_blank" rel="noopener">💬 Sewa Kamera Ini</a>
                </div>
            </div>

            {{-- Kodak Pixpro FZ55 --}}
            <div class="price-card">
                <div class="price-card-header">
                    <img src="{{ asset('images/kodak-pixpro-fz55.jpg') }}" alt="Kodak Pixpro FZ55" class="price-card-img"
                         onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'">
                    <div>
                        <h3 class="price-card-name">Kodak Pixpro FZ55</h3>
                        <div class="price-card-brand">Kodak</div>
                    </div>
                </div>
                <div class="price-card-body">
                    <div class="price-row">
                        <span class="price-duration">6 Jam</span>
                        <span class="price-amount">Rp 30.000</span>
                    </div>
                    <div class="price-row">
                        <span class="price-duration">12 Jam</span>
                        <span class="price-amount">Rp 60.000</span>
                    </div>
                    <div class="price-row">
                        <span class="price-duration">24 Jam</span>
                        <span class="price-amount">Rp 80.000</span>
                    </div>
                </div>
                <div style="padding: 0 24px 20px;">
                    <a href="https://wa.me/6283181125461?text={{ urlencode('Halo Digicam Padang, saya ingin sewa kamera Kodak Pixpro FZ55') }}" class="btn btn-wa" style="width: 100%; font-size: 0.85rem;" target="_blank" rel="noopener">💬 Sewa Kamera Ini</a>
                </div>
            </div>
        </div>

        <div class="info-banner">
            <span class="info-banner-icon">📋</span>
            <div class="info-banner-content">
                <p><strong>Syarat:</strong> DP 50% & KTP</p>
                <p><strong>INFO:</strong> <a href="https://wa.me/6283181125461" target="_blank" rel="noopener">+62 831-8112-5461</a></p>
            </div>
        </div>
    </div>
</section>
@endsection