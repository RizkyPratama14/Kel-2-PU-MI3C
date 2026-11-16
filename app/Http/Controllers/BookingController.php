<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Camera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    // -------------------------------------------------------------------------
    // Konstanta status
    // -------------------------------------------------------------------------
    private const STATUS_MENUNGGU_VERIFIKASI = 'Menunggu Verifikasi';
    private const STATUS_MENUNGGU_PEMBAYARAN = 'Menunggu Pembayaran';

    // -------------------------------------------------------------------------
    // GET /bookings/unit/{camera}?mulai=...&selesai=...
    // Cek ketersediaan + status unit berdasarkan ID kamera dan rentang waktu
    // -------------------------------------------------------------------------
    public function cekKetersediaan(Request $request, Camera $camera)
    {
        $request->validate([
            'mulai'   => ['required', 'date'],
            'selesai' => ['required', 'date', 'after:mulai'],
        ]);

        $unitId  = $camera->id;
        $mulai   = $request->mulai;
        $selesai = $request->selesai;

        // ----- Query bentrok (sesuai logika di soal) -----
        $adaBentrok = Booking::where('unit_id', $unitId)
            ->where(function ($q) {
                $q->where('status_booking', self::STATUS_MENUNGGU_VERIFIKASI)
                  ->orWhere(fn ($q) => $q
                      ->where('status_booking', self::STATUS_MENUNGGU_PEMBAYARAN)
                      ->where('expired_at', '>', now())
                  );
            })
            ->where('mulai_at', '<', $selesai)
            ->where('selesai_at', '>', $mulai)
            ->exists();

        return response()->json([
            'unit'        => [
                'id'    => $camera->id,
                'nama'  => $camera->nama,
                'merek' => $camera->merek,
            ],
            'tersedia'    => ! $adaBentrok,
            'mulai'       => $mulai,
            'selesai'     => $selesai,
            'server_time' => now('Asia/Jakarta')->toIso8601String(),
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /bookings
    // Simpan booking — user_id dari sesi Auth, waktu dari server (Asia/Jakarta)
    // -------------------------------------------------------------------------
    public function store(Request $request)
    {
        $request->validate([
            'unit_id'       => ['required', 'exists:cameras,id'],
            'mulai_at'      => ['required', 'date', 'after:now'],
            'selesai_at'    => ['required', 'date', 'after:mulai_at'],
            'lokasi_pickup' => ['required', 'string', 'max:255'],
        ]);

        $unitId  = $request->unit_id;
        $mulai   = $request->mulai_at;
        $selesai = $request->selesai_at;

        // ── Pengecekan bentrok ──────────────────────────────────────────────
        $adaBentrok = Booking::where('unit_id', $unitId)
            ->where(function ($q) {
                $q->where('status_booking', self::STATUS_MENUNGGU_VERIFIKASI)
                  ->orWhere(fn ($q) => $q
                      ->where('status_booking', self::STATUS_MENUNGGU_PEMBAYARAN)
                      ->where('expired_at', '>', now())
                  );
            })
            ->where('mulai_at', '<', $selesai)
            ->where('selesai_at', '>', $mulai)
            ->exists();

        if ($adaBentrok) {
            return response()->json([
                'message' => 'Jadwal yang dipilih sudah dipesan oleh pengguna lain.',
            ], 422);
        }

        // ── Hitung durasi ───────────────────────────────────────────────────
        $mulaiCarbon   = \Carbon\Carbon::parse($mulai, 'Asia/Jakarta');
        $selesaiCarbon = \Carbon\Carbon::parse($selesai, 'Asia/Jakarta');
        $durasiJam     = (int) $mulaiCarbon->diffInHours($selesaiCarbon);

        // ── Simpan dalam satu transaksi DB ──────────────────────────────────
        $booking = DB::transaction(function () use (
            $request, $unitId, $mulai, $selesai, $durasiJam
        ) {
            return Booking::create([
                // ✅ Kode unik di-generate server
                'kode_booking'      => Booking::generateKode(),

                // ✅ user_id dari sesi login — BUKAN dari input request
                'user_id'           => Auth::id(),

                'unit_id'           => $unitId,

                // ✅ Waktu dikirim klien, disimpan oleh server (zona Asia/Jakarta)
                'mulai_at'          => \Carbon\Carbon::parse($mulai, 'Asia/Jakarta'),
                'selesai_at'        => \Carbon\Carbon::parse($selesai, 'Asia/Jakarta'),
                'durasi_jam'        => $durasiJam,

                'lokasi_pickup'     => $request->lokasi_pickup,
                'status_booking'    => self::STATUS_MENUNGGU_VERIFIKASI,
                'status_pembayaran' => 'Belum Bayar',
                'payment_ref'       => null,
                'expired_at'        => null,

                // ✅ created_at diisi otomatis oleh Eloquent dengan timezone server
            ]);
        });

        return response()->json([
            'message'      => 'Booking berhasil dibuat dan menunggu verifikasi.',
            'booking'      => $booking,
            'server_time'  => now('Asia/Jakarta')->toIso8601String(),
        ], 201);
    }

    // -------------------------------------------------------------------------
    // GET /bookings/aktif?unit_id=X&mulai=...&selesai=...
    // Ambil daftar booking aktif pada kamera tertentu dalam rentang waktu
    // -------------------------------------------------------------------------
    public function aktifByUnit(Request $request)
    {
        $request->validate([
            'unit_id' => ['required', 'exists:cameras,id'],
            'mulai'   => ['required', 'date'],
            'selesai' => ['required', 'date', 'after:mulai'],
        ]);

        $bookings = Booking::with('user:id,name,email')
            ->where('unit_id', $request->unit_id)
            // scope aktif → Menunggu Verifikasi OR (Menunggu Pembayaran + belum expired)
            ->aktif()
            // filter hanya yang tumpang-tindih dengan rentang yang diminta
            ->where('mulai_at', '<', $request->selesai)
            ->where('selesai_at', '>', $request->mulai)
            ->orderBy('mulai_at')
            ->get();

        return response()->json([
            'unit_id'     => (int) $request->unit_id,
            'total_aktif' => $bookings->count(),
            'bookings'    => $bookings,
            'server_time' => now('Asia/Jakarta')->toIso8601String(),
        ]);
    }
}
