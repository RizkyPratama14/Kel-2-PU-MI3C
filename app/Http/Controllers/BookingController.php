<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Camera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        // ----- Query bentrok & status ketersediaan (T3) -----
        $status = Booking::cekStatus($unitId, $mulai, $selesai);
        $tersedia = ($status === 'Tersedia');

        return response()->json([
            'unit'        => [
                'id'    => $camera->id,
                'nama'  => $camera->nama,
                'merek' => $camera->merek,
            ],
            'status'      => $status,
            'tersedia'    => $tersedia,
            'mulai'       => $mulai,
            'selesai'     => $selesai,
            'server_time' => now('Asia/Jakarta')->toIso8601String(),
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /bookings
    // Simpan booking — user_id dari sesi Auth, waktu dari server (Asia/Jakarta)
    // T15: Redirect ke WhatsApp setelah booking berhasil disimpan
    // T16: Jika gagal simpan, tampilkan pesan error ke view
    // -------------------------------------------------------------------------
    public function store(StoreBookingRequest $request)
    {
        $unitId  = $request->unit_id;
        $mulai   = $request->mulai_at;
        $selesai = $request->selesai_at;

        // ── Pengecekan bentrok ──────────────────────────────────────────────
        if (Booking::adaBentrok($unitId, $mulai, $selesai)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Jadwal yang dipilih sudah dipesan oleh pengguna lain.',
                ], 422);
            }

            return back()->withInput()->withErrors([
                'unit_id' => 'Jadwal yang dipilih sudah dipesan oleh pengguna lain.',
            ]);
        }

        // ── Hitung durasi ───────────────────────────────────────────────────
        $mulaiCarbon   = \Carbon\Carbon::parse($mulai, 'Asia/Jakarta');
        $selesaiCarbon = \Carbon\Carbon::parse($selesai, 'Asia/Jakarta');
        $durasiJam     = (int) $mulaiCarbon->diffInHours($selesaiCarbon);

        // ── T16: Simpan dalam transaksi DB dengan penanganan error ──────────
        try {
            $booking = DB::transaction(function () use (
                $request, $unitId, $mulai, $selesai, $durasiJam
            ) {
                return Booking::create([
                    'kode_booking'      => Booking::generateKode(),
                    'user_id'           => Auth::id(),
                    'unit_id'           => $unitId,
                    'mulai_at'          => \Carbon\Carbon::parse($mulai, 'Asia/Jakarta'),
                    'selesai_at'        => \Carbon\Carbon::parse($selesai, 'Asia/Jakarta'),
                    'durasi_jam'        => $durasiJam,
                    'lokasi_pickup'     => $request->lokasi_pickup,
                    'status_booking'    => self::STATUS_MENUNGGU_VERIFIKASI,
                    'status_pembayaran' => 'Belum Bayar',
                    'metode_pembayaran' => $request->metode_pembayaran,
                    'payment_ref'       => null,
                    'expired_at'        => null,
                ]);
            });
        } catch (\Throwable $e) {
            // T16: Jika penyimpanan gagal, redirect TIDAK berjalan
            Log::error('Gagal menyimpan booking: ' . $e->getMessage());

            return back()->withInput()->withErrors([
                'simpan' => 'Terjadi kesalahan saat menyimpan data booking. Silakan coba lagi.',
            ]);
        }

        // ── T15: Redirect ke WhatsApp HANYA jika status = "Menunggu Verifikasi" ──
        if ($booking->status_booking === self::STATUS_MENUNGGU_VERIFIKASI) {
            $camera = Camera::find($booking->unit_id);
            $user   = Auth::user();

            $pesan = "Halo Admin Digicam Padang,\n"
                   . "Saya ingin konfirmasi booking kamera.\n\n"
                   . "📋 *Detail Booking:*\n"
                   . "Kode: {$booking->kode_booking}\n"
                   . "Nama: {$user->name}\n"
                   . "Kamera: {$camera->nama} ({$camera->merek})\n"
                   . "Mulai: {$booking->mulai_at->format('d/m/Y H:i')}\n"
                   . "Selesai: {$booking->selesai_at->format('d/m/Y H:i')}\n"
                   . "Durasi: {$booking->durasi_jam} jam\n"
                   . "Lokasi Pickup: {$booking->lokasi_pickup}\n"
                   . "Metode Bayar: {$booking->metode_pembayaran}\n"
                   . "Status: {$booking->status_booking}\n\n"
                   . "Mohon diverifikasi. Terima kasih! 🙏";

            $waUrl = 'https://wa.me/6283181125461?text=' . urlencode($pesan);

            return redirect()->away($waUrl);
        }

        // Fallback jika status bukan "Menunggu Verifikasi"
        return back()->with('success', 'Booking berhasil dibuat.');
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
