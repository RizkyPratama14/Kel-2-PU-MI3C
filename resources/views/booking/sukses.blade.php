@extends('layouts.app')
@section('title', 'Booking Berhasil - Digicam Padang')

@section('content')
<section class="section">
    <div class="container">
        <div class="sukses-card">

            {{-- Ikon sukses animasi --}}
            <div class="sukses-icon-wrap">
                <div class="sukses-icon">✅</div>
            </div>

            <h2 class="sukses-title">Pemesanan Berhasil Dikirim!</h2>
            <p class="sukses-sub">
                Booking kamu sudah tercatat. Silakan hubungi admin via WhatsApp untuk konfirmasi
                dan instruksi pembayaran DP 50%.
            </p>

            {{-- Kode Booking --}}
            <div class="kode-booking-box">
                <span class="kode-label">Kode Booking</span>
                <span class="kode-value">{{ $booking->kode_booking }}</span>
                <button class="kode-copy" onclick="copyKode('{{ $booking->kode_booking }}')">📋 Salin</button>
            </div>

            {{-- Ringkasan --}}
            <div class="sukses-ringkasan">
                <div class="sukses-row">
                    <span>📷 Kamera</span>
                    <strong>{{ $kamera->nama ?? ($booking->unit->nama ?? 'Unit Kamera') }} ({{ $kamera->merek ?? ($booking->unit->merek ?? '') }})</strong>
                </div>
                <div class="sukses-row">
                    <span>👤 Penyewa</span>
                    <strong>{{ $booking->user->name }}</strong>
                </div>
                <div class="sukses-row">
                    <span>📅 Mulai Sewa</span>
                    <strong>{{ $booking->mulai_at->setTimezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB</strong>
                </div>
                <div class="sukses-row">
                    <span>🔄 Pengembalian</span>
                    <strong>{{ $booking->selesai_at->setTimezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB</strong>
                </div>
                <div class="sukses-row">
                    <span>⏱️ Durasi</span>
                    <strong>{{ $booking->durasi_jam }} Jam</strong>
                </div>
                <div class="sukses-row">
                    <span>📍 Lokasi Pickup</span>
                    <strong>{{ $booking->lokasi_pickup }}</strong>
                </div>
                <div class="sukses-row">
                    <span>💳 Metode Bayar</span>
                    <strong>{{ ucwords(str_replace('_', ' ', $booking->metode_pembayaran ?? '-')) }}</strong>
                </div>
                <div class="sukses-row sukses-row-total">
                    <span>💰 Estimasi DP (50%)</span>
                    <strong>Rp {{ number_format($booking->estimasi_harga / 2, 0, ',', '.') }}</strong>
                </div>
                <div class="sukses-row">
                    <span>🕐 Dicatat pada</span>
                    <strong>{{ $booking->created_at->setTimezone('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB</strong>
                </div>
            </div>

            {{-- Status badge --}}
            <div class="status-badge-row">
                <span class="status-badge menunggu">⏳ Menunggu Verifikasi Admin</span>
            </div>

            {{-- Tombol WA (US-1006) --}}
            <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="btn btn-wa btn-wa-big">
                💬 Konfirmasi & Hubungi Admin via WhatsApp
            </a>

            <div class="sukses-info-box">
                <strong>Langkah selanjutnya:</strong>
                <ol>
                    <li>Klik tombol di atas untuk mengirim pesan konfirmasi ke admin</li>
                    <li>Tunggu admin memverifikasi ketersediaan unit</li>
                    <li>Bayar DP 50% sesuai instruksi admin</li>
                    <li>Ambil kamera sesuai lokasi dan jadwal yang disepakati</li>
                </ol>
            </div>

            <a href="{{ route('home') }}" class="btn btn-outline" style="margin-top: 16px;">← Kembali ke Beranda</a>
        </div>
    </div>
</section>

<script>
function copyKode(kode) {
    navigator.clipboard.writeText(kode).then(() => {
        const btn = document.querySelector('.kode-copy');
        btn.textContent = '✅ Tersalin!';
        setTimeout(() => btn.textContent = '📋 Salin', 2000);
    });
}
</script>
@endsection
