<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_user_can_view_register_page(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_admin_can_view_login_page(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    public function test_user_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@example.com',
            'no_telepon' => '081122334455',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs(User::where('email', 'ahmad@example.com')->first(), 'web');
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'user@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_admin_can_login(): void
    {
        $admin = Admin::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'jabatan' => 'Staff',
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $response = $this->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest('web');
    }

    public function test_admin_can_logout(): void
    {
        $admin = Admin::create([
            'name' => 'Admin Logout',
            'email' => 'admin_logout@test.com',
            'password' => Hash::make('password123'),
        ]);
        $this->actingAs($admin, 'admin');

        $response = $this->post('/logout');
        $response->assertRedirect('/admin/login');
        $this->assertGuest('admin');
    }

    public function test_user_can_view_forgot_password_page(): void
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
    }

    public function test_user_can_request_password_reset_link(): void
    {
        $user = User::factory()->create(['email' => 'reset@test.com']);

        $response = $this->post('/forgot-password', [
            'email' => 'reset@test.com',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'reset@test.com',
        ]);
    }

    public function test_user_can_view_reset_password_page(): void
    {
        $response = $this->get('/reset-password/sample-token?email=reset@test.com');
        $response->assertStatus(200);
    }

    public function test_user_can_reset_password(): void
    {
        $user = User::factory()->create([
            'email' => 'reset@test.com',
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->post('/reset-password', [
            'token' => 'sample-token',
            'email' => 'reset@test.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect('/login');
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_admin_can_view_dashboard(): void
    {
        $admin = Admin::create([
            'name' => 'Admin Test',
            'email' => 'admin_dash@test.com',
            'password' => Hash::make('password123'),
            'jabatan' => 'Staff',
        ]);
        $response = $this->actingAs($admin, 'admin')->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Admin/Dashboard'));
    }
}
