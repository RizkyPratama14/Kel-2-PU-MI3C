@extends('layouts.app')
@section('title', 'Aturan Sewa - Digicam Padang')

@section('content')
<section class="section">
    <div class="container">
        <h2>Aturan Sewa</h2>
        <p class="sub">Harap membaca dan memahami syarat serta aturan sewa sebelum melakukan pemesanan di @digicampadang_.</p>

        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-bottom: 36px;">
            <a href="https://instagram.com/digicampadang_" target="_blank" rel="noopener" class="btn btn-outline" style="font-size: 0.88rem;">
                📷 Instagram: @digicampadang_
            </a>
            <a href="https://wa.me/6283181125461" target="_blank" rel="noopener" class="btn btn-wa" style="font-size: 0.88rem;">
                💬 WhatsApp: +62 831-8112-5461
            </a>
        </div>

        <ol class="rules">
            <li>
                <strong>1. Waktu Penyewaan</strong>
                Penyewaan berlaku sesuai durasi yang dipilih, yaitu 3 jam, 6 jam, 12 jam, 24 jam, atau lebih sesuai kesepakatan dan paket yang tersedia.
            </li>

            <li>
                <strong>2. Keterlambatan Pengembalian</strong>
                Toleransi keterlambatan adalah 30 menit. Apabila melewati batas toleransi, penyewa akan dikenakan denda keterlambatan:
                <br><span style="color: var(--color-gold); font-weight: 600;">⏱️ Tiap 60 menit: Denda Rp 15.000.</span>
            </li>

            <li>
                <strong>3. Penambahan Durasi Sewa</strong>
                Apabila ingin menambah durasi sewa, harap konfirmasi terlebih dahulu dan akan dikenakan biaya sebesar <strong>Rp 10.000 per jam</strong> (berlaku kelipatan, selama jadwal unit masih memungkinkan).
            </li>

            <li>
                <strong>4. Pembayaran & Penguncian Jadwal</strong>
                Pembayaran dilakukan dengan DP sebesar <strong>50%</strong> terlebih dahulu untuk mengunci tanggal dan jam sewa kamera.
            </li>

            <li>
                <strong>5. Jaminan Penyewaan</strong>
                Penyewa wajib menitipkan identitas asli yang masih berlaku (KTP atau KTM) sebagai jaminan selama masa penyewaan.
            </li>

            <li>
                <strong>6. Tanggung Jawab Penyewa</strong>
                Penyewa bertanggung jawab penuh atas kamera selama masa sewa. Jika terjadi kerusakan, lecet berat, atau kehilangan komponen/unit, penyewa wajib mengganti sesuai nilai kerugian yang timbul.
            </li>

            <li>
                <strong>7. Kelengkapan Unit Kamera</strong>
                Setiap kamera yang disewa sudah termasuk paket lengkap:
                <ul>
                    <li>Unit Kamera</li>
                    <li>Memory Card / SD Card</li>
                    <li>Pouch / Tas Pelindung Kamera</li>
                    <li>Baterai & Charger Baterai Kamera</li>
                    <li>Card Reader (OTG) untuk transfer file foto</li>
                </ul>
                <p style="margin-top: 8px; color: var(--color-text-muted); font-size: 0.88rem;">
                    Harap mengembalikan seluruh perlengkapan dalam kondisi bersih, lengkap, dan berfungsi normal.
                </p>
            </li>

            <li>
                <strong>8. Kebijakan Pembatalan Sewa</strong>
                Apabila terjadi pembatalan sepihak dari penyewa, uang muka (DP) yang telah dibayarkan tidak dapat dikembalikan (hangus).
            </li>

            <li>
                <strong>9. Reschedule / Perubahan Jadwal</strong>
                Perubahan jadwal sewa (reschedule) diperbolehkan dengan konfirmasi paling lambat <strong>H-1</strong> sebelum waktu sewa, disesuaikan dengan ketersediaan unit.
            </li>
        </ol>

        <div class="info-banner" style="margin-top: 40px;">
            <span class="info-banner-icon">💬</span>
            <div class="info-banner-content">
                <p><strong>Ada pertanyaan mengenai aturan sewa?</strong></p>
                <p>Hubungi admin kami di WhatsApp <a href="https://wa.me/6283181125461" target="_blank" rel="noopener">+62 831-8112-5461</a> atau DM Instagram <a href="https://instagram.com/digicampadang_" target="_blank" rel="noopener">@digicampadang_</a>.</p>
            </div>
        </div>
    </div>
</section>
@endsection