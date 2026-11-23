<?php

namespace Tests\Feature;

use App\Models\Camera;
use App\Models\User;
use Database\Seeders\CameraSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Task T9: Test validasi StoreBookingRequest.
 *
 * Memastikan:
 * - Simpan ditolak jika kolom wajib kosong
 * - Simpan ditolak jika metode pembayaran belum dipilih atau invalid
 * - Pesan error dalam Bahasa Indonesia
 */
class StoreBookingValidationTest extends TestCase
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
     * Test: Simpan ditolak jika semua kolom wajib kosong.
     */
    public function test_store_rejected_when_all_required_fields_empty(): void
    {
        $response = $this->actingAs($this->user)->post('/bookings', []);

        $response->assertSessionHasErrors([
            'unit_id',
            'mulai_at',
            'selesai_at',
            'lokasi_pickup',
            'metode_pembayaran',
        ]);
    }

    /**
     * Test: Simpan ditolak jika unit_id kosong.
     */
    public function test_store_rejected_when_unit_id_missing(): void
    {
        $response = $this->actingAs($this->user)->post('/bookings', [
            'mulai_at'          => now()->addDays(1)->toDateTimeString(),
            'selesai_at'        => now()->addDays(1)->addHours(4)->toDateTimeString(),
            'lokasi_pickup'     => 'Kampus A',
            'metode_pembayaran' => 'transfer_bank',
        ]);

        $response->assertSessionHasErrors(['unit_id']);
    }

    /**
     * Test: Simpan ditolak jika mulai_at kosong.
     */
    public function test_store_rejected_when_mulai_at_missing(): void
    {
        $response = $this->actingAs($this->user)->post('/bookings', [
            'unit_id'           => $this->camera->id,
            'selesai_at'        => now()->addDays(1)->addHours(4)->toDateTimeString(),
            'lokasi_pickup'     => 'Kampus A',
            'metode_pembayaran' => 'transfer_bank',
        ]);

        $response->assertSessionHasErrors(['mulai_at']);
    }

    /**
     * Test: Simpan ditolak jika selesai_at kosong.
     */
    public function test_store_rejected_when_selesai_at_missing(): void
    {
        $response = $this->actingAs($this->user)->post('/bookings', [
            'unit_id'           => $this->camera->id,
            'mulai_at'          => now()->addDays(1)->toDateTimeString(),
            'lokasi_pickup'     => 'Kampus A',
            'metode_pembayaran' => 'transfer_bank',
        ]);

        $response->assertSessionHasErrors(['selesai_at']);
    }

    /**
     * Test: Simpan ditolak jika lokasi_pickup kosong.
     */
    public function test_store_rejected_when_lokasi_pickup_missing(): void
    {
        $response = $this->actingAs($this->user)->post('/bookings', [
            'unit_id'           => $this->camera->id,
            'mulai_at'          => now()->addDays(1)->toDateTimeString(),
            'selesai_at'        => now()->addDays(1)->addHours(4)->toDateTimeString(),
            'metode_pembayaran' => 'transfer_bank',
        ]);

        $response->assertSessionHasErrors(['lokasi_pickup']);
    }

    /**
     * Test: Simpan ditolak jika metode_pembayaran belum dipilih (kosong).
     */
    public function test_store_rejected_when_metode_pembayaran_missing(): void
    {
        $response = $this->actingAs($this->user)->post('/bookings', [
            'unit_id'       => $this->camera->id,
            'mulai_at'      => now()->addDays(1)->toDateTimeString(),
            'selesai_at'    => now()->addDays(1)->addHours(4)->toDateTimeString(),
            'lokasi_pickup' => 'Kampus A',
        ]);

        $response->assertSessionHasErrors(['metode_pembayaran']);
    }

    /**
     * Test: Simpan ditolak jika metode_pembayaran tidak valid (bukan transfer_bank/e_wallet/cod).
     */
    public function test_store_rejected_when_metode_pembayaran_invalid(): void
    {
        $response = $this->actingAs($this->user)->post('/bookings', [
            'unit_id'           => $this->camera->id,
            'mulai_at'          => now()->addDays(1)->toDateTimeString(),
            'selesai_at'        => now()->addDays(1)->addHours(4)->toDateTimeString(),
            'lokasi_pickup'     => 'Kampus A',
            'metode_pembayaran' => 'bitcoin',  // Invalid!
        ]);

        $response->assertSessionHasErrors(['metode_pembayaran']);
    }

    /**
     * Test: Pesan error dalam Bahasa Indonesia.
     */
    public function test_error_messages_are_in_bahasa_indonesia(): void
    {
        $response = $this->actingAs($this->user)->post('/bookings', []);

        $response->assertSessionHasErrors([
            'unit_id'           => 'Unit kamera wajib dipilih.',
            'mulai_at'          => 'Waktu mulai sewa wajib diisi.',
            'selesai_at'        => 'Waktu selesai sewa wajib diisi.',
            'lokasi_pickup'     => 'Lokasi pengambilan wajib diisi.',
            'metode_pembayaran' => 'Metode pembayaran wajib dipilih.',
        ]);
    }

    /**
     * Test: Pesan error metode pembayaran invalid dalam Bahasa Indonesia.
     */
    public function test_metode_pembayaran_invalid_message_is_in_bahasa(): void
    {
        $response = $this->actingAs($this->user)->post('/bookings', [
            'unit_id'           => $this->camera->id,
            'mulai_at'          => now()->addDays(1)->toDateTimeString(),
            'selesai_at'        => now()->addDays(1)->addHours(4)->toDateTimeString(),
            'lokasi_pickup'     => 'Kampus A',
            'metode_pembayaran' => 'paypal',
        ]);

        $response->assertSessionHasErrors([
            'metode_pembayaran' => 'Metode pembayaran yang dipilih tidak valid. Pilih salah satu: Transfer Bank, E-Wallet, atau COD.',
        ]);
    }

    /**
     * Test: User yang belum login tidak bisa store booking.
     */
    public function test_unauthenticated_user_cannot_store_booking(): void
    {
        $response = $this->post('/bookings', [
            'unit_id'           => $this->camera->id,
            'mulai_at'          => now()->addDays(1)->toDateTimeString(),
            'selesai_at'        => now()->addDays(1)->addHours(4)->toDateTimeString(),
            'lokasi_pickup'     => 'Kampus A',
            'metode_pembayaran' => 'transfer_bank',
        ]);

        $response->assertRedirect('/login');
    }
}
