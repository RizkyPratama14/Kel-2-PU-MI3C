
@extends('layouts.app')
@section('title', 'Aturan Sewa')

@section('content')
<section class="section">
    <div class="container">
        <h2> Aturan Sewa </h2>
        <p class="sub">Harap dibaca sebelum menyewa.</p>

        <p class="sub">
            <a href="https://instagram.com/digicampadang_"
               target="_blank"
               rel="noopener noreferrer">
                @digicampadang_
            </a>
        </p>

        <ol class="rules">
            <li>
                <strong>Waktu Penyewaan</strong><br>
                Penyewaan berlaku sesuai durasi yang dipilih,
                yaitu 6 jam, 12 jam, atau 24 jam, atau lebih
                sesuai kesepakatan.
            </li>

            <li>
                <strong>Keterlambatan Pengembalian</strong><br>
                Toleransi keterlambatan adalah 30 menit.
                Apabila melewati batas toleransi, penyewa akan
                dikenakan denda keterlambatan:
                <br>⏱️ 60 menit: Rp15.000.
            </li>

            <li>
                <strong>Penambahan Durasi Sewa</strong><br>
                Apabila ingin menambah durasi sewa, akan
                dikenakan biaya sebesar Rp10.000 per jam
                dan berlaku kelipatan.
            </li>

            <li>
                <strong>Pembayaran</strong><br>
                Pembayaran dilakukan dengan DP sebesar 50%
                terlebih dahulu untuk mengunci jadwal sewa.
            </li>

            <li>
                <strong>Jaminan Penyewaan</strong><br>
                Penyewa wajib memberikan KTP atau KTM
                sebagai jaminan selama masa penyewaan.
            </li>

            <li>
                <strong>Tanggung Jawab Penyewa</strong><br>
                Penyewa bertanggung jawab atas kamera selama
                masa penyewaan. Jika terjadi kerusakan atau
                kehilangan, penyewa wajib mengganti sesuai
                kerugian yang terjadi.
            </li>

            <li>
                <strong>Kelengkapan Kamera</strong><br>
                Kamera yang disewa sudah termasuk:
                <ul>
                    <li>Kamera</li>
                    <li>Memory card</li>
                    <li>Pouch kamera</li>
                    <li>Charger baterai kamera</li>
                    <li>Card reader</li>
                </ul>
                <p>
                    Harap mengembalikan seluruh perlengkapan
                    dalam kondisi lengkap dan baik.
                </p>
            </li>

            <li>
                <strong>Pembatalan Sewa</strong><br>
                Apabila terjadi pembatalan dari pihak penyewa,
                DP yang telah dibayarkan tidak dapat
                dikembalikan atau hangus.
            </li>

            <li>
                <strong>Reschedule / Perubahan Jadwal</strong><br>
                Jika ingin melakukan perubahan jadwal,
                mohon konfirmasi paling lambat H-1 sebelum
                hari penyewaan.
            </li>
        </ol>

        <p class="sub" style="margin-top:20px; text-align:center;">
            Terima kasih sudah mempercayakan momen kakak
            bersama <strong>@digicampadang_</strong> 
        </p>
    </div>
</section>
@endsection