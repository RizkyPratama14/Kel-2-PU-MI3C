<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CameraController extends Controller
{
    /**
     * Tampilkan katalog kamera dengan pencarian & filter (US-1001).
     */
    public function index(Request $request)
    {
        $query = Camera::query();

        if ($request->filled('q')) {
            $query->where('nama', 'like', '%'.$request->q.'%');
        }
        if ($request->filled('merek')) {
            $query->where('merek', $request->merek);
        }

        return view('katalog.index', [
            'cameras' => $query->get(),
            'mereks'  => Camera::select('merek')->distinct()->pluck('merek'),
        ]);
    }

    /**
     * Tampilkan detail kamera (US-1001).
     */
    public function show(Camera $camera)
    {
        return view('katalog.tampil', compact('camera'));
    }

    /**
     * Halaman pemilihan tanggal & jam rencana sewa (US-1003 & US-1008).
     * Daftar kamera hardcode sesuai 3 unit resmi @digicampadang_.
     */
    public function checkAvailability(Request $request)
    {
        // Hardcode 3 kamera resmi — tidak ambil dari DB agar selalu konsisten
        $cameras = collect([
            (object)['id' => 1, 'nama' => 'Canon EOS M10',       'merek' => 'Canon', 'harga_per_hari' => 100000],
            (object)['id' => 2, 'nama' => 'Sony Cyber-shot W320', 'merek' => 'Sony',  'harga_per_hari' => 70000],
            (object)['id' => 3, 'nama' => 'Kodak Pixpro FZ55',    'merek' => 'Kodak', 'harga_per_hari' => 80000],
        ]);

        $selectedCameraId = $request->query('camera_id');

        return view('ketersediaan', compact('cameras', 'selectedCameraId'));
    }

    /**
     * Validasi form tanggal & jam sewa + tanggal kembali (US-1003).
     */
    public function validateSchedule(Request $request)
    {
        // Daftar kamera resmi (hardcode, ID 1-3)
        $daftarKamera = [
            1 => (object)['id' => 1, 'nama' => 'Canon EOS M10',       'merek' => 'Canon'],
            2 => (object)['id' => 2, 'nama' => 'Sony Cyber-shot W320', 'merek' => 'Sony'],
            3 => (object)['id' => 3, 'nama' => 'Kodak Pixpro FZ55',    'merek' => 'Kodak'],
        ];

        $validated = $request->validate([
            'camera_id'       => ['required', 'in:1,2,3'],
            'tanggal_mulai'   => ['required', 'date', 'after_or_equal:today'],
            'jam_mulai'       => ['required'],
            'tanggal_kembali' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'jam_kembali'     => ['required'],
        ], [
            'camera_id.required'             => 'Silakan pilih kamera yang ingin disewa.',
            'camera_id.in'                   => 'Kamera yang dipilih tidak valid.',
            'tanggal_mulai.required'         => 'Tanggal mulai sewa wajib diisi.',
            'tanggal_mulai.after_or_equal'   => 'Tanggal sewa tidak boleh menggunakan tanggal yang sudah lewat.',
            'jam_mulai.required'             => 'Jam mulai sewa wajib diisi.',
            'tanggal_kembali.required'       => 'Tanggal pengembalian wajib diisi.',
            'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal mulai sewa.',
            'jam_kembali.required'           => 'Jam kembali sewa wajib diisi.',
        ]);

        // Validasi jam kembali bila pada tanggal yang sama
        if ($validated['tanggal_mulai'] === $validated['tanggal_kembali']) {
            if ($validated['jam_kembali'] <= $validated['jam_mulai']) {
                return back()->withInput()->withErrors([
                    'jam_kembali' => 'Jam pengembalian harus setelah jam mulai sewa pada hari yang sama.',
                ]);
            }
        }

        $camId  = (int) $validated['camera_id'];
        $camera = $daftarKamera[$camId];

        $start = Carbon::parse($validated['tanggal_mulai'] . ' ' . $validated['jam_mulai'], 'Asia/Jakarta');
        $end   = Carbon::parse($validated['tanggal_kembali'] . ' ' . $validated['jam_kembali'], 'Asia/Jakarta');

        // ── US-1004: Cek bentrok real-time khusus unit kamera yang dipilih ──
        // Jika kamera berbeda di waktu yang sama, sistem tetap membolehkan (tidak bentrok).
        if (\App\Models\Booking::adaBentrok($camId, $start, $end)) {
            $saranKamera = match($camId) {
                1 => 'Sony Cyber-shot W320 atau Kodak Pixpro FZ55',
                2 => 'Canon EOS M10 atau Kodak Pixpro FZ55',
                default => 'Canon EOS M10 atau Sony Cyber-shot W320',
            };
            return back()->withInput()->withErrors([
                'camera_id' => "🔴 Kamera {$camera->nama} sudah dipesan pada jadwal ini. Namun unit lain ({$saranKamera}) masih dapat disewa pada jam tersebut. Silakan pilih unit lain atau ganti jam sewa.",
            ]);
        }

        $durasiJam = max(1, $start->diffInHours($end));

        $estimasiHarga = $this->calculateEstimatedPrice($camera->nama, $durasiJam);

        // Simpan rencana ke session, lalu arahkan ke form booking (US-1005)
        session(['rencana_sewa' => [
            'camera'          => $camera,
            'mulai'           => $start->translatedFormat('d F Y, H:i'),
            'kembali'         => $end->translatedFormat('d F Y, H:i'),
            'mulai_at'        => $start->format('Y-m-d H:i:s'),
            'selesai_at'      => $end->format('Y-m-d H:i:s'),
            'durasi_jam'      => $durasiJam,
            'estimasi_harga'  => $estimasiHarga,
            'tanggal_mulai'   => $validated['tanggal_mulai'],
            'jam_mulai'       => $validated['jam_mulai'],
            'tanggal_kembali' => $validated['tanggal_kembali'],
            'jam_kembali'     => $validated['jam_kembali'],
        ]]);

        return redirect()->route('booking.form')
            ->with('success', 'Jadwal valid! Silakan lengkapi form booking.');
    }

    /**
     * Hitung estimasi harga berdasarkan pricelist resmi @digicampadang_.
     */
    private function calculateEstimatedPrice(string $cameraName, int $hours): int
    {
        $nameLower = strtolower($cameraName);

        if (str_contains($nameLower, 'canon')) {
            if ($hours <= 3) return 30000;
            if ($hours <= 6) return 50000;
            if ($hours <= 8) return 65000;
            if ($hours <= 12) return 80000;
            if ($hours <= 24) return 100000;
            $days = ceil($hours / 24);
            return (int) ($days * 100000);
        }

        if (str_contains($nameLower, 'sony')) {
            if ($hours <= 6) return 25000;
            if ($hours <= 12) return 50000;
            if ($hours <= 24) return 70000;
            if ($hours <= 48) return 120000;
            $days = ceil($hours / 24);
            return (int) ($days * 55000);
        }

        if (str_contains($nameLower, 'kodak')) {
            if ($hours <= 6) return 30000;
            if ($hours <= 12) return 60000;
            if ($hours <= 24) return 80000;
            $days = ceil($hours / 24);
            return (int) ($days * 80000);
        }

        $days = max(1, ceil($hours / 24));
        return (int) ($days * 80000);
    }
}
