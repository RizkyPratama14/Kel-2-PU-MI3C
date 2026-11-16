<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

/**
 * Task T3: Service untuk perbandingan status ketersediaan unit (kamera).
 *
 * Menggunakan aturan overlap:
 *   Dua rentang [A, B) dan [C, D) dianggap bentrok jika mulai_A < selesai_B && selesai_A > mulai_B
 *
 * Rentang yang berdampingan secara presisi (misal selesai 12:00 dan mulai 12:00)
 * TIDAK dianggap bentrok karena kondisi menggunakan < dan > (bukan <= dan >=).
 */
class BookingAvailabilityService
{
    /**
     * Status yang dikembalikan.
     */
    public const STATUS_TERSEDIA = 'Tersedia';
    public const STATUS_TERSEWA  = 'Tersewa';

    /**
     * Tentukan status unit pada rentang waktu tertentu.
     *
     * @param  int     $unitId   ID kamera/unit
     * @param  string  $mulai    Waktu mulai (ISO / datetime string)
     * @param  string  $selesai  Waktu selesai (ISO / datetime string)
     * @return string  'Tersedia' atau 'Tersewa'
     */
    public function cekStatus(int $unitId, string $mulai, string $selesai): string
    {
        $bentrok = $this->adaOverlap($unitId, $mulai, $selesai);

        return $bentrok ? self::STATUS_TERSEWA : self::STATUS_TERSEDIA;
    }

    /**
     * Cek apakah ada overlap antara rentang yang diminta dengan booking aktif.
     *
     * Overlap terjadi jika: mulai_A < selesai_B && selesai_A > mulai_B
     * Sehingga rentang berdampingan presisi (selesai=12:00, mulai=12:00) TIDAK overlap.
     *
     * @param  int     $unitId
     * @param  string  $mulai
     * @param  string  $selesai
     * @return bool
     */
    public function adaOverlap(int $unitId, string $mulai, string $selesai): bool
    {
        return Booking::bentrokDengan($unitId, $mulai, $selesai)->exists();
    }

    /**
     * Cek apakah dua rentang waktu mentah overlap (tanpa query DB).
     *
     * Berguna untuk unit testing murni tanpa database.
     *
     * @param  string  $mulaiA    Mulai rentang A
     * @param  string  $selesaiA  Selesai rentang A
     * @param  string  $mulaiB    Mulai rentang B
     * @param  string  $selesaiB  Selesai rentang B
     * @return bool
     */
    public static function isOverlap(string $mulaiA, string $selesaiA, string $mulaiB, string $selesaiB): bool
    {
        $startA = Carbon::parse($mulaiA);
        $endA   = Carbon::parse($selesaiA);
        $startB = Carbon::parse($mulaiB);
        $endB   = Carbon::parse($selesaiB);

        // Dua rentang [A, B) dan [C, D) tumpang-tindih jika A < D && B > C
        return $startA->lt($endB) && $endA->gt($startB);
    }
}
