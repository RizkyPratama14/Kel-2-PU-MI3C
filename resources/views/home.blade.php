@extends('layouts.app')
@section('title', 'Beranda - Digicam Padang')

@section('content')
<section class="hero">
    <div class="container">
        <h1>Sewa Kamera Mudah di Padang</h1>
        <p>Pilih kamera favoritmu, cek ketersediaan, lalu pesan dengan Proses mudah, harga terjangkau!</p>
        <div class="hero-btns">
            <a href="{{ route('katalog') }}" class="btn">📷 Lihat Katalog</a>
            <a href="{{ route('cek') }}" class="btn btn-outline">📅 Cek Ketersediaan</a>
        </div>
    </div>
</section>

<section class="section testimoni-section" style="margin-top:0;">
    <div class="container">
        <h2>Testimoni Kamera</h2>
        <p class="sub">Hasil foto dari pelanggan kami yang puas dengan kamera sewaan @digicampadang_</p>

        <div class="testimoni-tabs">
            <button class="testimoni-tab active" data-target="canon">Canon EOS M10</button>
            <button class="testimoni-tab" data-target="sony">Sony Cybershot W320</button>
            <button class="testimoni-tab" data-target="kodak">Kodak Pixpro FZ55</button>
        </div>

        {{-- Canon EOS M10 --}}
        <div class="testimoni-panel testimoni-grid active" id="panel-canon">
            <img src="{{ asset('images/testimoni/canon-eos-m10/IMG_6003.JPG.jpeg') }}" alt="Testimoni Canon EOS M10" loading="lazy" onclick="openLightbox(this)">
            <img src="{{ asset('images/testimoni/canon-eos-m10/IMG_6062.JPG.jpeg') }}" alt="Testimoni Canon EOS M10" loading="lazy" onclick="openLightbox(this)">
            <img src="{{ asset('images/testimoni/canon-eos-m10/IMG_6609.JPG.jpeg') }}" alt="Testimoni Canon EOS M10" loading="lazy" onclick="openLightbox(this)">
            <img src="{{ asset('images/testimoni/canon-eos-m10/IMG_6618.JPG.jpeg') }}" alt="Testimoni Canon EOS M10" loading="lazy" onclick="openLightbox(this)">
            <img src="{{ asset('images/testimoni/canon-eos-m10/IMG_7506.JPG.jpeg') }}" alt="Testimoni Canon EOS M10" loading="lazy" onclick="openLightbox(this)">
            <img src="{{ asset('images/testimoni/canon-eos-m10/IMG_7627.JPG.jpeg') }}" alt="Testimoni Canon EOS M10" loading="lazy" onclick="openLightbox(this)">
        </div>

        {{-- Sony Cybershot W320 --}}
        <div class="testimoni-panel testimoni-grid" id="panel-sony">
            <img src="{{ asset('images/testimoni/sony-cybershot-w320/DSC02670.JPG.jpeg') }}" alt="Testimoni Sony Cybershot W320" loading="lazy" onclick="openLightbox(this)">
            <img src="{{ asset('images/testimoni/sony-cybershot-w320/IMG_1271.JPG.jpeg') }}" alt="Testimoni Sony Cybershot W320" loading="lazy" onclick="openLightbox(this)">
            <img src="{{ asset('images/testimoni/sony-cybershot-w320/IMG_1897.JPG.jpeg') }}" alt="Testimoni Sony Cybershot W320" loading="lazy" onclick="openLightbox(this)">
            <img src="{{ asset('images/testimoni/sony-cybershot-w320/IMG_2662.JPG.jpeg') }}" alt="Testimoni Sony Cybershot W320" loading="lazy" onclick="openLightbox(this)">
            <img src="{{ asset('images/testimoni/sony-cybershot-w320/IMG_3292.JPG.jpeg') }}" alt="Testimoni Sony Cybershot W320" loading="lazy" onclick="openLightbox(this)">
            <img src="{{ asset('images/testimoni/sony-cybershot-w320/IMG_5381.JPG.jpeg') }}" alt="Testimoni Sony Cybershot W320" loading="lazy" onclick="openLightbox(this)">
            <img src="{{ asset('images/testimoni/sony-cybershot-w320/IMG_5522.JPG.jpeg') }}" alt="Testimoni Sony Cybershot W320" loading="lazy" onclick="openLightbox(this)">
        </div>

        {{-- Kodak Pixpro FZ55 --}}
        <div class="testimoni-panel testimoni-grid" id="panel-kodak">
            <img src="{{ asset('images/testimoni/kodak-pixpro-fz55/WhatsApp-Image-2026-10-18-at-12.56.21-(1).jpeg') }}" alt="Testimoni Kodak Pixpro FZ55" loading="lazy" onclick="openLightbox(this)">
            <img src="{{ asset('images/testimoni/kodak-pixpro-fz55/WhatsApp-Image-2026-10-18-at-12.56.21-(2).jpeg') }}" alt="Testimoni Kodak Pixpro FZ55" loading="lazy" onclick="openLightbox(this)">
            <img src="{{ asset('images/testimoni/kodak-pixpro-fz55/WhatsApp-Image-2026-10-18-at-12.56.21.jpeg') }}" alt="Testimoni Kodak Pixpro FZ55" loading="lazy" onclick="openLightbox(this)">
            <img src="{{ asset('images/testimoni/kodak-pixpro-fz55/WhatsApp-Image-2026-10-18-at-12.56.22-(1).jpeg') }}" alt="Testimoni Kodak Pixpro FZ55" loading="lazy" onclick="openLightbox(this)">
            <img src="{{ asset('images/testimoni/kodak-pixpro-fz55/WhatsApp-Image-2026-10-18-at-12.56.22.jpeg') }}" alt="Testimoni Kodak Pixpro FZ55" loading="lazy" onclick="openLightbox(this)">
        </div>
    </div>
</section>

<section class="section" style="margin-top:0; padding-top:0;">
    <div class="container">
        <h2>Tentang Digicam Padang</h2>
        <p class="sub">
            Digicam Padang adalah jasa penyewaan kamera digital terpercaya di kota Padang.
            Kami menyediakan berbagai pilihan kamera dengan harga terjangkau dan proses yang mudah.
            Hubungi kami melalui WhatsApp untuk pemesanan.
        </p>
        <div style="text-align:center;">
            <a href="https://wa.me/6283181125461?text={{ urlencode('Halo, saya tertarik untuk sewa kamera di Digicam Padang') }}" class="btn btn-wa" target="_blank" rel="noopener">
                💬 Hubungi via WhatsApp
            </a>
        </div>
    </div>
</section>

{{-- Lightbox --}}
<div class="lightbox" id="lightbox" onclick="closeLightbox(event)">
    <button class="lightbox-close" onclick="closeLightbox(event)">&times;</button>
    <img id="lightbox-img" src="" alt="Preview">
</div>

<script>
// Testimoni tabs
document.querySelectorAll('.testimoni-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.testimoni-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.testimoni-panel').forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById('panel-' + tab.dataset.target).classList.add('active');
    });
});

// Lightbox
function openLightbox(img) {
    const lb = document.getElementById('lightbox');
    document.getElementById('lightbox-img').src = img.src;
    lb.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeLightbox(e) {
    if (e.target === document.getElementById('lightbox') || e.target.classList.contains('lightbox-close')) {
        document.getElementById('lightbox').classList.remove('open');
        document.body.style.overflow = '';
    }
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.getElementById('lightbox').classList.remove('open');
        document.body.style.overflow = '';
    }
});
</script>
@endsection