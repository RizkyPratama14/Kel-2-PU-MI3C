<?php

namespace Tests\Feature;

use App\Models\Camera;
use App\Models\User;
use Database\Seeders\CameraSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DigicamTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CameraSeeder::class);
    }

    public function test_home_page_renders_with_testimoni_section(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Sewa Kamera Mudah di Padang');
        $response->assertSee('Testimoni Kamera');
        $response->assertSee('Canon EOS M10');
        $response->assertSee('Sony Cybershot W320');
        $response->assertSee('Kodak Pixpro FZ55');
    }

    /**
     * US-1001: Katalog kamera (foto, harga, spesifikasi), pencarian & filter.
     */
    public function test_us_1001_katalog_search_and_filter(): void
    {
        // 1. Render all
        $response = $this->get('/katalog');
        $response->assertStatus(200);
        $response->assertSee('Katalog Kamera');
        $response->assertSee('Canon EOS M10');
        $response->assertSee('Sony Cyber-shot W320');
        $response->assertSee('Kodak Pixpro FZ55');

        // 2. Pencarian by keyword 'Canon'
        $searchResponse = $this->get('/katalog?q=Canon');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Canon EOS M10');
        $searchResponse->assertDontSee('Kodak Pixpro FZ55');

        // 3. Filter by brand 'Sony'
        $filterResponse = $this->get('/katalog?merek=Sony');
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('Sony Cyber-shot W320');
        $filterResponse->assertDontSee('Canon EOS M10');

        // 4. Detail kamera
        $camera = Camera::first();
        $this->assertNotNull($camera);
        $detailResponse = $this->get('/katalog/' . $camera->id);
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee($camera->nama);
        $detailResponse->assertSee($camera->merek);
    }

    /**
     * US-1002: Aturan sewa + tautan Instagram/WhatsApp.
     */
    public function test_us_1002_aturan_sewa_has_instagram_and_whatsapp(): void
    {
        $response = $this->get('/aturan-sewa');
        $response->assertStatus(200);
        $response->assertSee('Aturan Sewa');
        $response->assertSee('Waktu Penyewaan');
        $response->assertSee('Keterlambatan Pengembalian');
        $response->assertSee('Jaminan Penyewaan');
        // Tautan Instagram & WhatsApp (US-1002)
        $response->assertSee('instagram.com/digicampadang_');
        $response->assertSee('wa.me/6283181125461');
    }

    /**
     * US-1008: Register dan login Penyewa; form booking hanya terbuka setelah login.
     */
    public function test_us_1008_auth_and_form_booking_lock(): void
    {
        // 1. Guest visits /cek-ketersediaan -> form is locked
        $guestResponse = $this->get('/cek-ketersediaan');
        $guestResponse->assertStatus(200);
        $guestResponse->assertSee('Form Jadwal Terkunci (US-1008)');
        $guestResponse->assertSee('Masuk ke Akun');

        // Guest attempting POST directly is blocked by auth middleware
        $postGuest = $this->post('/cek-ketersediaan', []);
        $postGuest->assertRedirect('/login');

        // 2. Register new Penyewa
        $email = 'penyewa' . uniqid() . '@example.com';
        $registerResponse = $this->post('/register', [
            'name'                  => 'Rizky Penyewa',
            'email'                 => $email,
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $registerResponse->assertRedirect('/');
        $this->assertAuthenticated();

        // 3. Authenticated user visits /cek-ketersediaan -> form is OPEN
        $authResponse = $this->get('/cek-ketersediaan');
        $authResponse->assertStatus(200);
        $authResponse->assertSee('Rizky Penyewa');
        $authResponse->assertSee('Tanggal Mulai Sewa');
        $authResponse->assertSee('Tanggal Pengembalian');
        $authResponse->assertDontSee('Form Jadwal Terkunci (US-1008)');
    }

    /**
     * US-1003: Form tanggal & jam sewa + validasi tanggal kembali.
     */
    public function test_us_1003_validasi_tanggal_kembali(): void
    {
        $user = User::factory()->create();
        $camera = Camera::first();

        // 1. Gagal jika tanggal kembali sebelum tanggal mulai
        $invalidDateResponse = $this->actingAs($user)->post('/cek-ketersediaan', [
            'camera_id'       => $camera->id,
            'tanggal_mulai'   => date('Y-m-d', strtotime('+2 days')),
            'jam_mulai'       => '10:00',
            'tanggal_kembali' => date('Y-m-d', strtotime('+1 day')),
            'jam_kembali'     => '10:00',
        ]);
        $invalidDateResponse->assertSessionHasErrors(['tanggal_kembali']);

        // 2. Gagal jika tanggal sama tapi jam kembali <= jam mulai
        $invalidTimeResponse = $this->actingAs($user)->post('/cek-ketersediaan', [
            'camera_id'       => $camera->id,
            'tanggal_mulai'   => date('Y-m-d', strtotime('+1 day')),
            'jam_mulai'       => '14:00',
            'tanggal_kembali' => date('Y-m-d', strtotime('+1 day')),
            'jam_kembali'     => '12:00',
        ]);
        $invalidTimeResponse->assertSessionHasErrors(['jam_kembali']);

        // 3. Berhasil jika tanggal & jam kembali valid
        $validResponse = $this->actingAs($user)->post('/cek-ketersediaan', [
            'camera_id'       => $camera->id,
            'tanggal_mulai'   => date('Y-m-d', strtotime('+1 day')),
            'jam_mulai'       => '10:00',
            'tanggal_kembali' => date('Y-m-d', strtotime('+2 days')),
            'jam_kembali'     => '10:00',
        ]);
        $validResponse->assertSessionHasNoErrors();
        $validResponse->assertSessionHas('rencana_sewa');
        $validResponse->assertSessionHas('success', 'Rencana sewa berhasil divalidasi!');
    }

    public function test_price_list_matches_provided_specifications(): void
    {
        $response = $this->get('/price-list');
        $response->assertStatus(200);
        $response->assertSee('Price List');
        // Canon EOS M10 prices
        $response->assertSee('Canon EOS M10');
        $response->assertSee('30.000');
        $response->assertSee('50.000');
        $response->assertSee('65.000');
        $response->assertSee('80.000');
        $response->assertSee('100.000');
        // Sony Cybershot W320 prices
        $response->assertSee('Sony Cybershot W320');
        $response->assertSee('25.000');
        $response->assertSee('70.000');
        $response->assertSee('120.000');
        // Kodak Pixpro FZ55 prices
        $response->assertSee('Kodak Pixpro FZ55');
        $response->assertSee('60.000');
        // Requirements and Contact info from pricelist
        $response->assertSee('DP 50% & KTP', false);
        $response->assertSee('83181125461');
    }
}
