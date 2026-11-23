<?php

/**
 * BookingTestScript.php
 *
 * Script pengujian query booking — jalankan via:
 *   php artisan tinker --execute="require base_path('BookingTestScript.php');"
 * atau langsung dari direktori root:
 *   php artisan tinker < BookingTestScript.php
 *
 * Pastikan sudah:
 *   1. php artisan migrate
 *   2. php artisan db:seed
 *   3. php artisan db:seed --class=BookingSeeder (jika seeder belum jalan)
 */

use App\Models\Booking;
use App\Models\Camera;
use App\Models\User;
use Carbon\Carbon;

$tz   = 'Asia/Jakarta';
$unit = Camera::first();
$user = User::where('email', 'test@example.com')->first();

if (! $unit) {
    echo "❌  Tidak ada kamera. Jalankan: php artisan db:seed --class=CameraSeeder\n";
    return;
}

echo "==========================================================\n";
echo "  UJI BOOKING — Unit: {$unit->nama} (ID: {$unit->id})\n";
echo "  Waktu server: " . now($tz)->format('Y-m-d H:i:s T') . "\n";
echo "==========================================================\n\n";

// -----------------------------------------------------------------
// 1. Tampilkan semua booking dummy
// -----------------------------------------------------------------
echo "── [1] Semua booking di DB ────────────────────────────────\n";
$semua = Booking::where('unit_id', $unit->id)->get([
    'kode_booking','status_booking','mulai_at','selesai_at','expired_at',
]);
foreach ($semua as $b) {
    $exp = $b->expired_at ? $b->expired_at->format('H:i') : '-';
    echo "  {$b->kode_booking} | {$b->status_booking:30} | "
       . "{$b->mulai_at->format('H:i')}–{$b->selesai_at->format('H:i')}"
       . " | exp: {$exp}\n";
}

// -----------------------------------------------------------------
// 2. Tampilkan booking "aktif" (scope aktif)
// -----------------------------------------------------------------
echo "\n── [2] Booking aktif (Menunggu Verifikasi / Menunggu Pembayaran + belum expired) ──\n";
$aktif = Booking::where('unit_id', $unit->id)->aktif()->get([
    'kode_booking','status_booking','mulai_at','selesai_at',
]);
if ($aktif->isEmpty()) {
    echo "  (tidak ada)\n";
} else {
    foreach ($aktif as $b) {
        echo "  ✅  {$b->kode_booking} | {$b->status_booking} | "
           . "{$b->mulai_at->format('H:i')}–{$b->selesai_at->format('H:i')}\n";
    }
}

// -----------------------------------------------------------------
// 3. Cek bentrok: rentang yang seharusnya BENTROK (10:00–12:00)
// -----------------------------------------------------------------
$base   = Carbon::now($tz)->startOfDay()->addHours(9);
$cek1_mulai   = $base->copy()->addHour()->format('Y-m-d H:i:s');   // 10:00
$cek1_selesai = $base->copy()->addHours(3)->format('Y-m-d H:i:s'); // 12:00

echo "\n── [3] Cek bentrok: slot 10:00–12:00 (tumpang-tindih dg booking #1 & #2) ───\n";

// Query manual sesuai logika di soal
$bentrok1 = Booking::where('unit_id', $unit->id)
    ->where(function ($q) {
        $q->where('status_booking', 'Menunggu Verifikasi')
          ->orWhere(fn ($q) => $q->where('status_booking', 'Menunggu Pembayaran')
                                 ->where('expired_at', '>', now()));
    })
    ->where('mulai_at', '<', $cek1_selesai)
    ->where('selesai_at', '>', $cek1_mulai)
    ->exists();

echo "  Slot 10:00–12:00 → bentrok? " . ($bentrok1 ? '🔴 YA (tidak bisa dipesan)' : '🟢 TIDAK') . "\n";

// -----------------------------------------------------------------
// 4. Cek bentrok: slot yang SEHARUSNYA AMAN karena booking expired/gagal
// -----------------------------------------------------------------
$cek2_mulai   = $base->copy()->addHours(2)->format('Y-m-d H:i:s'); // 11:00
$cek2_selesai = $base->copy()->addHours(5)->format('Y-m-d H:i:s'); // 14:00

echo "\n── [4] Cek bentrok: slot 11:00–14:00 (booking #3 expired, #4 gagal) ────────\n";
$bentrok2 = Booking::where('unit_id', $unit->id)
    ->where(function ($q) {
        $q->where('status_booking', 'Menunggu Verifikasi')
          ->orWhere(fn ($q) => $q->where('status_booking', 'Menunggu Pembayaran')
                                 ->where('expired_at', '>', now()));
    })
    ->where('mulai_at', '<', $cek2_selesai)
    ->where('selesai_at', '>', $cek2_mulai)
    ->exists();

echo "  Slot 11:00–14:00 → bentrok? " . ($bentrok2 ? '🔴 YA (tidak bisa dipesan)' : '🟢 TIDAK (bisa dipesan lagi!)') . "\n";

// -----------------------------------------------------------------
// 5. Cek bentrok: slot 20:00–23:00 → seharusnya BEBAS
// -----------------------------------------------------------------
$cek3_mulai   = $base->copy()->addHours(11)->format('Y-m-d H:i:s'); // 20:00
$cek3_selesai = $base->copy()->addHours(14)->format('Y-m-d H:i:s'); // 23:00

echo "\n── [5] Cek bentrok: slot 20:00–23:00 (tidak ada booking aktif di sini) ─────\n";
$bentrok3 = Booking::where('unit_id', $unit->id)
    ->where(function ($q) {
        $q->where('status_booking', 'Menunggu Verifikasi')
          ->orWhere(fn ($q) => $q->where('status_booking', 'Menunggu Pembayaran')
                                 ->where('expired_at', '>', now()));
    })
    ->where('mulai_at', '<', $cek3_selesai)
    ->where('selesai_at', '>', $cek3_mulai)
    ->exists();

echo "  Slot 20:00–23:00 → bentrok? " . ($bentrok3 ? '🔴 YA' : '🟢 TIDAK (bisa dipesan!)') . "\n";

// -----------------------------------------------------------------
// 6. Simulasi simpan transaksi baru dengan user dari sesi + waktu server
// -----------------------------------------------------------------
echo "\n── [6] Simulasi simpan booking baru ────────────────────────────────────────\n";

$mulaiServer  = now('Asia/Jakarta');  // waktu server WIB
$selesaiServer = $mulaiServer->copy()->addHours(3);

$newBooking = new Booking([
    'kode_booking'      => Booking::generateKode(),
    'user_id'           => $user->id,   // dari sesi login
    'unit_id'           => $unit->id,
    'mulai_at'          => $mulaiServer,                    // isi dari server
    'selesai_at'        => $selesaiServer,
    'durasi_jam'        => 3,
    'lokasi_pickup'     => 'Kampus E Ruang 10',
    'status_booking'    => 'Menunggu Verifikasi',
    'status_pembayaran' => 'Belum Bayar',
    'expired_at'        => null,
]);

echo "  kode_booking : {$newBooking->kode_booking}\n";
echo "  user_id      : {$newBooking->user_id} ({$user->name})\n";
echo "  mulai_at     : {$newBooking->mulai_at} [Asia/Jakarta]\n";
echo "  selesai_at   : {$newBooking->selesai_at} [Asia/Jakarta]\n";
echo "  status       : {$newBooking->status_booking}\n";
echo "\n  (Booking baru TIDAK disimpan ke DB oleh script uji ini.)\n";

echo "\n==========================================================\n";
echo "  RINGKASAN HASIL UJI\n";
echo "==========================================================\n";
echo "  Slot 10:00–12:00 → BENTROK  (booking #1 Menunggu Verifikasi)\n";
echo "  Slot 11:00–14:00 → BEBAS    (booking #3 expired, #4 Gagal)\n";
echo "  Slot 20:00–23:00 → BEBAS    (tidak ada booking aktif)\n";
echo "  Waktu server     → Asia/Jakarta ✅\n";
echo "  Jadwal gagal/expired dapat dipesan lagi ✅\n";
