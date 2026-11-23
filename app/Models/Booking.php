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
     * Generate kode booking unik: BK-YYYYMMDD-XXXX
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
