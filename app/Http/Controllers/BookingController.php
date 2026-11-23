<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    // -----------------------------------------------------------------------
    // 3 Kamera resmi Digicam Padang (hardcode, sinkron dg CameraController)
    // -----------------------------------------------------------------------
    private static function daftarKamera(): array
    {
        return [
            1 => (object)['id' => 1, 'nama' => 'Canon EOS M10',       'merek' => 'Canon'],
            2 => (object)['id' => 2, 'nama' => 'Sony Cyber-shot W320', 'merek' => 'Sony'],
            3 => (object)['id' => 3, 'nama' => 'Kodak Pixpro FZ55',    'merek' => 'Kodak'],
        ];
    }

    // -----------------------------------------------------------------------
    // GET /booking/form  — Tampilkan form booking (US-1005)
    // Menerima data rencana dari session (diisi oleh CameraController)
    // -----------------------------------------------------------------------
    public function form(Request $request)
    {
        $rencana = session('rencana_sewa');

        if (!$rencana) {
            return redirect()->route('cek')
                ->with('error', 'Silakan isi jadwal sewa terlebih dahulu.');
        }

        // Konversi kamera ke object dengan property yang dibutuhkan view
        $camId = (int) (is_array($rencana['camera'] ?? null) 
            ? ($rencana['camera']['id'] ?? 1) 
            : ($rencana['camera']->id ?? 1));
        $daftar  = self::daftarKamera();
        $camObj  = $daftar[$camId] ?? $daftar[1];

        // Hitung mulai_at & selesai_at dalam format datetime
        $mulaiAt   = Carbon::parse(
            ($rencana['tanggal_mulai'] ?? date('Y-m-d')) . ' ' . ($rencana['jam_mulai'] ?? '08:00'),
            'Asia/Jakarta'
        );
        $selesaiAt = Carbon::parse(
            ($rencana['tanggal_kembali'] ?? date('Y-m-d', strtotime('+1 day'))) . ' ' . ($rencana['jam_kembali'] ?? '08:00'),
            'Asia/Jakarta'
        );

        // US-1004: cek ketersediaan real-time khusus unit_id kamera ini
        $tersedia = !Booking::adaBentrok($camId, $mulaiAt, $selesaiAt);

        $rencanaLengkap = array_merge($rencana, [
            'camera'    => $camObj,
            'mulai_at'  => $mulaiAt->format('Y-m-d H:i:s'),
            'selesai_at'=> $selesaiAt->format('Y-m-d H:i:s'),
        ]);

        return view('booking.form', [
            'rencana'  => $rencanaLengkap,
            'tersedia' => $tersedia,
        ]);
    }

    // -----------------------------------------------------------------------
    // POST /booking  — Simpan booking (US-1005)
    // -----------------------------------------------------------------------
    public function store(Request $request)
    {
        $request->validate([
            'camera_id'          => ['required', 'in:1,2,3'],
            'mulai_at'           => ['required', 'date'],
            'selesai_at'         => ['required', 'date', 'after:mulai_at'],
            'durasi_jam'         => ['required', 'integer', 'min:1'],
            'estimasi_harga'     => ['required', 'integer', 'min:0'],
            'no_whatsapp'        => ['required', 'string', 'max:20'],
            'lokasi_pickup'      => ['required', 'string', 'max:255'],
            'catatan'            => ['nullable', 'string', 'max:500'],
            'metode_pembayaran'  => ['required', 'in:bank_transfer,dana,ovo,gopay'],
            'setuju_aturan'      => ['accepted'],
        ], [
            'camera_id.required'         => 'Kamera belum dipilih.',
            'camera_id.in'               => 'Kamera tidak valid.',
            'mulai_at.required'          => 'Tanggal mulai wajib diisi.',
            'selesai_at.required'        => 'Tanggal selesai wajib diisi.',
            'selesai_at.after'           => 'Tanggal selesai harus setelah tanggal mulai.',
            'durasi_jam.required'        => 'Durasi jam wajib diisi.',
            'no_whatsapp.required'       => 'Nomor WhatsApp wajib diisi untuk konfirmasi.',
            'lokasi_pickup.required'     => 'Lokasi pengambilan wajib diisi.',
            'metode_pembayaran.required' => 'Silakan pilih metode pembayaran.',
            'metode_pembayaran.in'       => 'Metode pembayaran tidak valid.',
            'setuju_aturan.accepted'     => 'Anda harus menyetujui aturan sewa.',
        ]);

        $camId     = (int) $request->camera_id;
        $mulaiAt   = Carbon::parse($request->mulai_at,   'Asia/Jakarta');
        $selesaiAt = Carbon::parse($request->selesai_at, 'Asia/Jakarta');

        // Pastikan tabel bookings sudah ada di MySQL
        if (!\Illuminate\Support\Facades\Schema::hasTable('bookings')) {
            return back()->withInput()->with('error',
                '⚠️ Tabel database "bookings" belum dibuat. Harap jalankan perintah "php artisan migrate" di terminal terlebih dahulu.'
            );
        }

        // ── US-1004: Cek bentrok jadwal real-time khusus unit ini ───────
        if (Booking::adaBentrok($camId, $mulaiAt, $selesaiAt)) {
            $cam = self::daftarKamera()[$camId] ?? null;
            $namaCam = $cam ? $cam->nama : 'kamera ini';
            return back()->withInput()->with('error',
                "🔴 Maaf, unit {$namaCam} sudah dipesan untuk jadwal ini oleh penyewa lain. Anda masih bisa memilih unit kamera lain untuk waktu tersebut."
            );
        }

        // ── US-1005: Simpan booking ─────────────────────────────────────
        $booking = DB::transaction(function () use ($request, $camId, $mulaiAt, $selesaiAt) {
            // Update nomor WA user dari sesi (US-1008 AC5)
            $user = Auth::user();
            if ($request->no_whatsapp !== $user->no_whatsapp) {
                $user->no_whatsapp = $request->no_whatsapp;
                $user->save();
            }

            return Booking::create([
                'kode_booking'      => Booking::generateKode(),
                'user_id'           => Auth::id(),           // dari sesi, bukan input
                'unit_id'           => $camId,
                'mulai_at'          => $mulaiAt,             // waktu server WIB
                'selesai_at'        => $selesaiAt,
                'durasi_jam'        => (int) $request->durasi_jam,
                'estimasi_harga'    => (int) $request->estimasi_harga,
                'lokasi_pickup'     => $request->lokasi_pickup,
                'catatan'           => $request->catatan,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_booking'    => 'Menunggu Verifikasi',
                'status_pembayaran' => 'Belum Bayar',
                'payment_ref'       => null,
                'expired_at'        => now('Asia/Jakarta')->addHours(24),
                // created_at diisi otomatis Eloquent (zona Asia/Jakarta dari config)
            ]);
        });

        // ── US-1006: Siapkan URL WhatsApp konfirmasi ────────────────────
        $cam    = self::daftarKamera()[$camId];
        $waText = implode("\n", [
            '📋 *KONFIRMASI BOOKING DIGICAM PADANG*',
            '',
            "Kode Booking : *{$booking->kode_booking}*",
            "Penyewa      : {$booking->user->name}",
            "No. WA       : {$request->no_whatsapp}",
            "Kamera       : {$cam->nama} ({$cam->merek})",
            "Mulai Sewa   : " . $mulaiAt->translatedFormat('d F Y, H:i') . " WIB",
            "Pengembalian : " . $selesaiAt->translatedFormat('d F Y, H:i') . " WIB",
            "Durasi       : {$booking->durasi_jam} Jam",
            "Lokasi Pickup: {$booking->lokasi_pickup}",
            "Metode DP    : " . ucwords(str_replace('_', ' ', $booking->metode_pembayaran)),
            "Estimasi DP  : Rp " . number_format($booking->estimasi_harga / 2, 0, ',', '.'),
            '',
            'Mohon konfirmasi ketersediaan unit dan instruksi pembayaran DP 50%. Terima kasih!',
        ]);

        $waUrl = 'https://wa.me/6283181125461?text=' . urlencode($waText);

        // Hapus session rencana sewa
        session()->forget('rencana_sewa');

        return view('booking.sukses', [
            'booking' => $booking,
            'kamera'  => $cam,
            'waUrl'   => $waUrl,
        ]);
    }
}
