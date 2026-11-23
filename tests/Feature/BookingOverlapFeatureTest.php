<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Camera;
use App\Models\User;
use Database\Seeders\CameraSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Task T3 (Feature): Test integrasi overlap booking dengan database.
 *
 * Memastikan scopeBentrokDengan dan adaBentrok bekerja benar dengan DB.
 */
class BookingOverlapFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Camera $camera;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CameraSeeder::class);
        $this->camera = Camera::first();
        $this->user = User::factory()->create();
    }

    /**
     * Helper untuk membuat booking aktif di database.
     */
    private function createBooking(string $mulai, string $selesai, string $status = 'Menunggu Verifikasi'): Booking
    {
        return Booking::create([
            'kode_booking'      => Booking::generateKode(),
            'user_id'           => $this->user->id,
            'unit_id'           => $this->camera->id,
            'mulai_at'          => $mulai,
            'selesai_at'        => $selesai,
            'durasi_jam'        => 4,
            'lokasi_pickup'     => 'Test Location',
            'status_booking'    => $status,
            'status_pembayaran' => 'Belum Bayar',
        ]);
    }

    /**
     * Booking yang overlap dengan booking aktif harus terdeteksi.
     */
    public function test_overlapping_booking_is_detected_in_database(): void
    {
        // Buat booking aktif: 10:00 - 14:00
        $this->createBooking('2026-12-15 10:00:00', '2026-12-15 14:00:00');

        // Cek rentang 12:00 - 16:00 → harus bentrok
        $result = Booking::adaBentrok($this->camera->id, '2026-12-15 12:00:00', '2026-12-15 16:00:00');
        $this->assertTrue($result, 'Booking yang overlap harus terdeteksi di database.');
    }

    /**
     * ✅ Test kritis: Booking berdampingan presisi TIDAK bentrok di database.
     *
     * Booking A selesai 12:00, Booking B mulai 12:00 → TIDAK bentrok.
     */
    public function test_adjacent_bookings_are_not_conflicting_in_database(): void
    {
        // Buat booking aktif: 08:00 - 12:00
        $this->createBooking('2026-12-15 08:00:00', '2026-12-15 12:00:00');

        // Cek rentang 12:00 - 16:00 → TIDAK boleh bentrok
        $result = Booking::adaBentrok($this->camera->id, '2026-12-15 12:00:00', '2026-12-15 16:00:00');
        $this->assertFalse($result, 'Booking berdampingan presisi TIDAK boleh bentrok.');
    }

    /**
     * Booking yang sudah "Dibatalkan" atau "Gagal" tidak menghalangi booking baru.
     */
    public function test_cancelled_booking_does_not_block(): void
    {
        $this->createBooking('2026-12-15 10:00:00', '2026-12-15 14:00:00', 'Dibatalkan');

        $result = Booking::adaBentrok($this->camera->id, '2026-12-15 10:00:00', '2026-12-15 14:00:00');
        $this->assertFalse($result, 'Booking yang dibatalkan tidak boleh menghalangi booking baru.');
    }

    /**
     * Booking pada unit yang berbeda tidak bentrok.
     */
    public function test_different_unit_does_not_conflict(): void
    {
        $this->createBooking('2026-12-15 10:00:00', '2026-12-15 14:00:00');

        $otherCamera = Camera::where('id', '!=', $this->camera->id)->first();
        if ($otherCamera) {
            $result = Booking::adaBentrok($otherCamera->id, '2026-12-15 10:00:00', '2026-12-15 14:00:00');
            $this->assertFalse($result, 'Booking pada unit berbeda tidak boleh bentrok.');
        } else {
            $this->markTestSkipped('Hanya 1 kamera di database.');
        }
    }

    /**
     * Cek status "Tersedia" saat slot kosong.
     */
    public function test_status_tersedia_when_no_bookings(): void
    {
        $service = new \App\Services\BookingAvailabilityService();
        $status = $service->cekStatus($this->camera->id, '2026-12-20 08:00:00', '2026-12-20 12:00:00');

        $this->assertEquals('Tersedia', $status);
    }

    /**
     * Cek status "Tersewa" saat slot ada booking aktif.
     */
    public function test_status_tersewa_when_slot_booked(): void
    {
        $this->createBooking('2026-12-20 08:00:00', '2026-12-20 12:00:00');

        $service = new \App\Services\BookingAvailabilityService();
        $status = $service->cekStatus($this->camera->id, '2026-12-20 09:00:00', '2026-12-20 11:00:00');

        $this->assertEquals('Tersewa', $status);
    }
}
