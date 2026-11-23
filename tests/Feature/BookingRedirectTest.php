<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Camera;
use App\Models\User;
use Database\Seeders\CameraSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Task T15 & T16: Test redirect WhatsApp dan penanganan error penyimpanan.
 */
class BookingRedirectTest extends TestCase
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
     * T15: Setelah booking berhasil disimpan, redirect ke URL WhatsApp.
     */
    public function test_successful_booking_redirects_to_whatsapp(): void
    {
        $response = $this->actingAs($this->user)->post('/bookings', [
            'unit_id'           => $this->camera->id,
            'mulai_at'          => now()->addDays(1)->toDateTimeString(),
            'selesai_at'        => now()->addDays(1)->addHours(4)->toDateTimeString(),
            'lokasi_pickup'     => 'Kampus B Lantai 2',
            'metode_pembayaran' => 'transfer_bank',
        ]);

        // Harus redirect ke wa.me
        $response->assertRedirect();
        $redirectUrl = $response->headers->get('Location');
        $this->assertStringStartsWith('https://wa.me/6283181125461', $redirectUrl);
    }

    /**
     * T15: Pesan WhatsApp berisi data booking dari database.
     */
    public function test_whatsapp_message_contains_booking_data(): void
    {
        $response = $this->actingAs($this->user)->post('/bookings', [
            'unit_id'           => $this->camera->id,
            'mulai_at'          => now()->addDays(1)->toDateTimeString(),
            'selesai_at'        => now()->addDays(1)->addHours(4)->toDateTimeString(),
            'lokasi_pickup'     => 'Kampus C',
            'metode_pembayaran' => 'e_wallet',
        ]);

        $redirectUrl = urldecode($response->headers->get('Location'));

        // Verifikasi pesan mengandung data booking
        $this->assertStringContainsString($this->user->name, $redirectUrl);
        $this->assertStringContainsString($this->camera->nama, $redirectUrl);
        $this->assertStringContainsString('Kampus C', $redirectUrl);
        $this->assertStringContainsString('Menunggu Verifikasi', $redirectUrl);
    }

    /**
     * T15: Booking tersimpan di database dengan status "Menunggu Verifikasi".
     */
    public function test_booking_saved_with_menunggu_verifikasi_status(): void
    {
        $this->actingAs($this->user)->post('/bookings', [
            'unit_id'           => $this->camera->id,
            'mulai_at'          => now()->addDays(2)->toDateTimeString(),
            'selesai_at'        => now()->addDays(2)->addHours(4)->toDateTimeString(),
            'lokasi_pickup'     => 'Toko Kamera',
            'metode_pembayaran' => 'cod',
        ]);

        $this->assertDatabaseHas('bookings', [
            'unit_id'           => $this->camera->id,
            'user_id'           => $this->user->id,
            'lokasi_pickup'     => 'Toko Kamera',
            'status_booking'    => 'Menunggu Verifikasi',
            'metode_pembayaran' => 'cod',
        ]);
    }

    /**
     * T16: Redirect tidak berjalan jika ada bentrok (simpan gagal logis).
     */
    public function test_redirect_does_not_happen_when_conflict_exists(): void
    {
        // Buat booking yang sudah ada
        Booking::create([
            'kode_booking'      => Booking::generateKode(),
            'user_id'           => $this->user->id,
            'unit_id'           => $this->camera->id,
            'mulai_at'          => now()->addDays(3)->toDateTimeString(),
            'selesai_at'        => now()->addDays(3)->addHours(4)->toDateTimeString(),
            'durasi_jam'        => 4,
            'lokasi_pickup'     => 'Existing',
            'status_booking'    => 'Menunggu Verifikasi',
            'status_pembayaran' => 'Belum Bayar',
        ]);

        // Coba booking pada waktu yang sama → harus gagal
        $response = $this->actingAs($this->user)->post('/bookings', [
            'unit_id'           => $this->camera->id,
            'mulai_at'          => now()->addDays(3)->addHours(1)->toDateTimeString(),
            'selesai_at'        => now()->addDays(3)->addHours(3)->toDateTimeString(),
            'lokasi_pickup'     => 'Kampus D',
            'metode_pembayaran' => 'transfer_bank',
        ]);

        // Tidak redirect ke WhatsApp
        $redirectUrl = $response->headers->get('Location');
        if ($redirectUrl) {
            $this->assertStringNotContainsString('wa.me', $redirectUrl);
        }

        // Ada error message
        $response->assertSessionHasErrors(['unit_id']);
    }

    /**
     * T16: Pesan error ditampilkan ke halaman view jika simpan gagal.
     */
    public function test_error_message_shown_when_save_fails(): void
    {
        // Buat booking bentrok
        Booking::create([
            'kode_booking'      => Booking::generateKode(),
            'user_id'           => $this->user->id,
            'unit_id'           => $this->camera->id,
            'mulai_at'          => now()->addDays(5)->toDateTimeString(),
            'selesai_at'        => now()->addDays(5)->addHours(6)->toDateTimeString(),
            'durasi_jam'        => 6,
            'lokasi_pickup'     => 'Existing',
            'status_booking'    => 'Menunggu Verifikasi',
            'status_pembayaran' => 'Belum Bayar',
        ]);

        $response = $this->actingAs($this->user)->post('/bookings', [
            'unit_id'           => $this->camera->id,
            'mulai_at'          => now()->addDays(5)->addHours(2)->toDateTimeString(),
            'selesai_at'        => now()->addDays(5)->addHours(4)->toDateTimeString(),
            'lokasi_pickup'     => 'Kampus E',
            'metode_pembayaran' => 'transfer_bank',
        ]);

        // Harus ada error di session
        $response->assertSessionHasErrors();
    }
}
