<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Admin Module: Test middleware, route, dan controller admin.
 */
class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Halaman login admin bisa diakses.
     */
    public function test_admin_login_page_is_accessible(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSee('Login Admin');
    }

    /**
     * Test: Admin bisa login dengan kredensial yang benar.
     */
    public function test_admin_can_login_with_valid_credentials(): void
    {
        $admin = User::factory()->create([
            'email'    => 'admin@digicampadang.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        $response = $this->post('/admin/login', [
            'email'    => 'admin@digicampadang.com',
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    /**
     * Test: Non-admin user tidak bisa login via admin login.
     */
    public function test_non_admin_user_cannot_login_as_admin(): void
    {
        User::factory()->create([
            'email'    => 'user@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'user',
        ]);

        $response = $this->post('/admin/login', [
            'email'    => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Test: Admin login gagal dengan password salah.
     */
    public function test_admin_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email'    => 'admin@digicampadang.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        $response = $this->post('/admin/login', [
            'email'    => 'admin@digicampadang.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Test: Route /admin diproteksi middleware admin.
     */
    public function test_admin_dashboard_requires_admin_role(): void
    {
        // Guest → redirect ke admin login
        $response = $this->get('/admin');
        $response->assertRedirect('/login');

        // User biasa → akses ditolak
        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->get('/admin');
        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Test: Admin bisa mengakses dashboard.
     */
    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Admin');
    }

    /**
     * Test: Admin bisa logout.
     */
    public function test_admin_can_logout(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin);

        $response = $this->post('/admin/logout');
        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    /**
     * Test: Rate limiting pada login admin (throttle:5,1).
     */
    public function test_admin_login_is_rate_limited(): void
    {
        User::factory()->create([
            'email'    => 'admin@digicampadang.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // Lakukan 6 percobaan login gagal
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/admin/login', [
                'email'    => 'admin@digicampadang.com',
                'password' => 'wrongpassword',
            ]);
        }

        // Percobaan ke-6 harus di-throttle (429 Too Many Requests)
        $response->assertStatus(429);
    }
}
