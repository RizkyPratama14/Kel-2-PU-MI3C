<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Booking extends Model
{
    protected $fillable = [
        'kode_booking',
        'user_id',
        'unit_id',
        'mulai_at',
        'selesai_at',
        'durasi_jam',
        'estimasi_harga',
        'lokasi_pickup',
        'catatan',
        'metode_pembayaran',
        'status_booking',
        'status_pembayaran',
        'metode_pembayaran',
        'payment_ref',
        'expired_at',
    ];

    protected $casts = [
        'mulai_at'   => 'datetime',
        'selesai_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    // ── Relasi ──────────────────────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Camera::class, 'unit_id');
    }

    // ── Scopes ──────────────────────────────────────────────────────────────

    /**
     * Scope: booking yang mengunci slot (US-1004).
     * Aktif = "Menunggu Verifikasi"
     *       ATAU "Menunggu Pembayaran" + expired_at > now()
     */
    public function scopeAktif($query)
    {
        return $query->where(function ($q) {
            $q->where('status_booking', 'Menunggu Verifikasi')
              ->orWhere(fn ($q) => $q
                  ->where('status_booking', 'Menunggu Pembayaran')
                  ->where('expired_at', '>', now())
              );
        });
    }

    /**
     * Scope: booking yang tumpang-tindih waktu pada unit tertentu.
     */
    public function scopeBentrokDengan($query, int $unitId, $mulai, $selesai)
    {
        return $query
            ->where('unit_id', $unitId)
            ->aktif()
            ->where('mulai_at', '<', $selesai)
            ->where('selesai_at', '>', $mulai);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Cek apakah slot sudah dipesan (US-1004).
     */
    public static function adaBentrok(int $unitId, $mulai, $selesai): bool
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('bookings')) {
            return false;
        }
        return static::bentrokDengan($unitId, $mulai, $selesai)->exists();
    }

    /**
<<<<<<< HEAD
     * Generate kode booking unik: BK-YYYYMMDD-XXXX
=======
     * Tentukan status ketersediaan unit: "Tersedia" atau "Tersewa".
     *
     * Aturan bentrok (overlap):
     * Dua rentang waktu [A, B) dan [C, D) dianggap bentrok jika:
     *   mulai_A < selesai_B && selesai_A > mulai_B
     *
     * Jika ada bentrok -> "Tersewa", jika tidak ada -> "Tersedia".
     */
    public static function cekStatus(int $unitId, string $mulai, string $selesai): string
    {
        return static::adaBentrok($unitId, $mulai, $selesai) ? 'Tersewa' : 'Tersedia';
    }

    /**
     * Logic perbandingan bentrok (overlap) murni antara dua rentang waktu:
     * Dua rentang dianggap bentrok jika: mulai_A < selesai_B && selesai_A > mulai_B
     *
     * Jadwal berdampingan presisi (misal selesai 12.00 dan mulai 12.00) TIDAK dianggap bentrok.
     */
    public static function isOverlap(string $mulaiA, string $selesaiA, string $mulaiB, string $selesaiB): bool
    {
        $startA = \Carbon\Carbon::parse($mulaiA);
        $endA   = \Carbon\Carbon::parse($selesaiA);
        $startB = \Carbon\Carbon::parse($mulaiB);
        $endB   = \Carbon\Carbon::parse($selesaiB);

        return $startA->lt($endB) && $endA->gt($startB);
    }

    /**
     * Generate kode booking unik.
     * Format: BK-YYYYMMDD-XXXX  (XXXX = 4 huruf/angka acak)
>>>>>>> db308a1cbc261e6d38ede29640ef7550ac5bf4f0
     */
    public static function generateKode(): string
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('bookings')) {
            return 'BK-' . now('Asia/Jakarta')->format('Ymd') . '-' . strtoupper(Str::random(4));
        }

        do {
            $kode = 'BK-' . now('Asia/Jakarta')->format('Ymd') . '-' . strtoupper(Str::random(4));
        } while (static::where('kode_booking', $kode)->exists());

        return $kode;
    }
}
