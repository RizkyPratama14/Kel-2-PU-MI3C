<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Camera;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * BookingSeeder — minimal 5 baris data dummy untuk menguji:
 *   - pengecekan bentrok
 *   - booking aktif vs gagal/dibatalkan
 *   - zona waktu server Asia/Jakarta
 */
class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $tz   = 'Asia/Jakarta';
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        // Ambil kamera pertama sebagai unit uji
        $unit = Camera::first();
        if (! $unit) {
            $this->command->warn('Jalankan CameraSeeder terlebih dahulu.');
            return;
        }
        $unitId = $unit->id;

        // Basis waktu: hari ini 09:00 WIB
        $base = Carbon::now($tz)->startOfDay()->addHours(9);

        // Helper: buat kode unik
        $kode = fn () => 'BK-' . Carbon::now($tz)->format('Ymd') . '-' . strtoupper(Str::random(4));

        // -----------------------------------------------------------------
        // 1. Menunggu Verifikasi → mengunci slot 09:00–13:00 (4 jam)
        //    → booking baru pada rentang ini HARUS bentrok
        // -----------------------------------------------------------------
        Booking::create([
            'kode_booking'      => $kode(),
            'user_id'           => $user->id,
            'unit_id'           => $unitId,
            'mulai_at'          => $base->copy(),                 // 09:00
            'selesai_at'        => $base->copy()->addHours(4),    // 13:00
            'durasi_jam'        => 4,
            'lokasi_pickup'     => 'Kampus A Gedung 1',
            'status_booking'    => 'Menunggu Verifikasi',
            'status_pembayaran' => 'Belum Bayar',
            'payment_ref'       => null,
            'expired_at'        => null,
        ]);

        // -----------------------------------------------------------------
        // 2. Menunggu Pembayaran + expired_at MASA DEPAN (belum kedaluwarsa)
        //    → slot 10:00–14:00 → tumpang-tindih dengan #1 → bentrok
        // -----------------------------------------------------------------
        Booking::create([
            'kode_booking'      => $kode(),
            'user_id'           => $user->id,
            'unit_id'           => $unitId,
            'mulai_at'          => $base->copy()->addHour(),      // 10:00
            'selesai_at'        => $base->copy()->addHours(5),    // 14:00
            'durasi_jam'        => 4,
            'lokasi_pickup'     => 'Kampus B Lobi',
            'status_booking'    => 'Menunggu Pembayaran',
            'status_pembayaran' => 'Belum Bayar',
            'payment_ref'       => 'PAY-' . strtoupper(Str::random(8)),
            'expired_at'        => Carbon::now($tz)->addHours(2), // belum expired
        ]);

        // -----------------------------------------------------------------
        // 3. Menunggu Pembayaran + expired_at LAMPAU (sudah kedaluwarsa)
        //    → slot 11:00–15:00 → TIDAK dianggap aktif → bisa dipesan lagi
        // -----------------------------------------------------------------
        Booking::create([
            'kode_booking'      => $kode(),
            'user_id'           => $user->id,
            'unit_id'           => $unitId,
            'mulai_at'          => $base->copy()->addHours(2),    // 11:00
            'selesai_at'        => $base->copy()->addHours(6),    // 15:00
            'durasi_jam'        => 4,
            'lokasi_pickup'     => 'Kampus C Parkir',
            'status_booking'    => 'Menunggu Pembayaran',
            'status_pembayaran' => 'Belum Bayar',
            'payment_ref'       => 'PAY-' . strtoupper(Str::random(8)),
            'expired_at'        => Carbon::now($tz)->subHour(),   // SUDAH expired
        ]);

        // -----------------------------------------------------------------
        // 4. Status "Gagal" → slot 09:00–12:00 → TIDAK aktif → bisa dipesan
        // -----------------------------------------------------------------
        Booking::create([
            'kode_booking'      => $kode(),
            'user_id'           => $user->id,
            'unit_id'           => $unitId,
            'mulai_at'          => $base->copy(),                 // 09:00
            'selesai_at'        => $base->copy()->addHours(3),    // 12:00
            'durasi_jam'        => 3,
            'lokasi_pickup'     => 'Kampus A Gedung 2',
            'status_booking'    => 'Gagal',
            'status_pembayaran' => 'Gagal',
            'payment_ref'       => 'PAY-' . strtoupper(Str::random(8)),
            'expired_at'        => Carbon::now($tz)->subHours(3),
        ]);

        // -----------------------------------------------------------------
        // 5. Slot BERBEDA: 16:00–20:00 → Menunggu Verifikasi → aktif
        //    → pengecekan slot 09:00–13:00 TIDAK bentrok dengan ini
        // -----------------------------------------------------------------
        Booking::create([
            'kode_booking'      => $kode(),
            'user_id'           => $user->id,
            'unit_id'           => $unitId,
            'mulai_at'          => $base->copy()->addHours(7),    // 16:00
            'selesai_at'        => $base->copy()->addHours(11),   // 20:00
            'durasi_jam'        => 4,
            'lokasi_pickup'     => 'Kampus D Ruang 3',
            'status_booking'    => 'Menunggu Verifikasi',
            'status_pembayaran' => 'Belum Bayar',
            'payment_ref'       => null,
            'expired_at'        => null,
        ]);

        $this->command->info('BookingSeeder selesai: 5 booking dummy berhasil dibuat.');
    }
}
