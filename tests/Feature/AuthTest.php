<?php

namespace Tests\Feature;

use App\Mail\ActivationLinkMail;
use App\Mail\ResetPasswordMail;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
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
        $response->assertRedirect('/login');
    }

    public function test_user_can_register(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad@example.com',
            'no_telepon' => '081122334455',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/verify-email');
        $user = User::where('email', 'ahmad@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);
        $this->assertAuthenticatedAs($user, 'web');

        Mail::assertSent(ActivationLinkMail::class, function ($mail) use ($user) {
            return $mail->hasTo('ahmad@example.com')
                && str_contains($mail->activationUrl, "/verify-email/{$user->id}/");
        });
    }

    public function test_unverified_user_cannot_access_dashboard_and_is_redirected_to_verify_email(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user, 'web')->get('/dashboard');
        $response->assertRedirect('/verify-email');
    }

    public function test_user_can_activate_account_via_signed_link(): void
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        $response = $this->get($url);

        $response->assertRedirect('/dashboard');
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_user_cannot_activate_account_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->getKey(),
                'hash' => 'invalid-hash',
            ]
        );

        $response = $this->get($url);

        $response->assertRedirect('/login');
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_user_can_resend_activation_link(): void
    {
        Mail::fake();

        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user, 'web')->post('/email/verification-notification');

        $response->assertSessionHas('status');

        Mail::assertSent(ActivationLinkMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email)
                && str_contains($mail->activationUrl, "/verify-email/{$user->id}/");
        });
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

    public function test_admin_can_login_via_unified_login_page(): void
    {
        $admin = Admin::create([
            'name' => 'Admin Unified',
            'email' => 'admin_unified@test.com',
            'password' => Hash::make('password123'),
            'jabatan' => 'Staff',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin_unified@test.com',
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
        $response->assertRedirect('/login');
        $this->assertGuest('admin');
    }

    public function test_user_can_view_forgot_password_page(): void
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
    }

    public function test_user_can_request_password_reset_link(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'reset@test.com']);

        $response = $this->post('/forgot-password', [
            'email' => 'reset@test.com',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'reset@test.com',
        ]);

        Mail::assertSent(ResetPasswordMail::class, function ($mail) {
            return $mail->hasTo('reset@test.com') && str_contains($mail->resetUrl, '/reset-password/');
        });
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

    public function test_user_cannot_reset_password_with_invalid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'invalid_token@test.com',
            'password' => Hash::make('oldpassword'),
        ]);

        DB::table('password_reset_tokens')->insert([
            'email' => 'invalid_token@test.com',
            'token' => Hash::make('real-secret-token'),
            'created_at' => now(),
        ]);

        $response = $this->post('/reset-password', [
            'token' => 'wrong-token-12345',
            'email' => 'invalid_token@test.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check('oldpassword', $user->fresh()->password));
    }

    public function test_admin_can_also_reset_password_via_forgot_password_flow(): void
    {
        Mail::fake();

        $admin = Admin::create([
            'name' => 'Admin Reset',
            'email' => 'admin_reset@bei.co.id',
            'password' => Hash::make('oldadminpass'),
            'jabatan' => 'Staff Edukasi',
        ]);

        $this->post('/forgot-password', ['email' => 'admin_reset@bei.co.id']);

        Mail::assertSent(ResetPasswordMail::class, fn ($mail) => $mail->hasTo('admin_reset@bei.co.id'));

        $response = $this->post('/reset-password', [
            'token' => 'sample-token',
            'email' => 'admin_reset@bei.co.id',
            'password' => 'newadminpass999',
            'password_confirmation' => 'newadminpass999',
        ]);

        $response->assertRedirect('/login');
        $this->assertTrue(Hash::check('newadminpass999', $admin->fresh()->password));
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

    public function test_captcha_endpoint_returns_svg_image(): void
    {
        $response = $this->get('/captcha/login');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $this->assertStringContainsString('<svg', $response->getContent());
        $this->assertNotEmpty(session('captcha_login'));
    }

    public function test_login_rate_limiting_locks_out_for_10_minutes_after_3_failed_attempts(): void
    {
        Cache::flush();

        $email = 'locked_user@test.com';

        // Attempt 1: fail
        $res1 = $this->post('/login', [
            'email' => $email,
            'password' => 'wrongpassword1',
            'captcha' => 'test-captcha',
        ]);
        $res1->assertSessionHasErrors('email');

        // Attempt 2: fail
        $res2 = $this->post('/login', [
            'email' => $email,
            'password' => 'wrongpassword2',
            'captcha' => 'test-captcha',
        ]);
        $res2->assertSessionHasErrors('email');

        // Attempt 3: fail triggers 10 minutes lockout
        $res3 = $this->post('/login', [
            'email' => $email,
            'password' => 'wrongpassword3',
            'captcha' => 'test-captcha',
        ]);
        $res3->assertSessionHasErrors('email');
        $errorMsg = $res3->getSession()->get('errors')->first('email');
        $this->assertStringContainsString('10 menit', $errorMsg);

        // Attempt 4 while locked: blocked immediately
        $res4 = $this->post('/login', [
            'email' => $email,
            'password' => 'wrongpassword4',
            'captcha' => 'test-captcha',
        ]);
        $res4->assertSessionHasErrors('email');
        $this->assertStringContainsString('dibatasi sementara', $res4->getSession()->get('errors')->first('email'));
    }

    public function test_progressive_lockout_escalates_to_30_minutes_then_1_hour(): void
    {
        Cache::flush();

        $email = 'escalate_user@test.com';

        // Level 1: 3 failed attempts -> 10m
        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', [
                'email' => $email,
                'password' => "wrong_{$i}",
                'captcha' => 'test-captcha',
            ]);
        }

        // Simulate lockout expiration
        $lockKey = 'throttle_lock_'.md5("login|{$email}|127.0.0.1");
        Cache::forget($lockKey);

        // Level 2: 3 more failed attempts -> 30m
        for ($i = 0; $i < 3; $i++) {
            $res = $this->post('/login', [
                'email' => $email,
                'password' => "wrong_lvl2_{$i}",
                'captcha' => 'test-captcha',
            ]);
        }
        $res->assertSessionHasErrors('email');
        $this->assertStringContainsString('30 menit', $res->getSession()->get('errors')->first('email'));

        // Simulate lockout expiration
        Cache::forget($lockKey);

        // Level 3: 3 more failed attempts -> 1 jam
        for ($i = 0; $i < 3; $i++) {
            $res = $this->post('/login', [
                'email' => $email,
                'password' => "wrong_lvl3_{$i}",
                'captcha' => 'test-captcha',
            ]);
        }
        $res->assertSessionHasErrors('email');
        $this->assertStringContainsString('1 jam', $res->getSession()->get('errors')->first('email'));
    }

    public function test_forgot_password_is_throttled_after_3_failed_attempts(): void
    {
        Cache::flush();

        $email = 'unknown_user_test@mail.com';

        for ($i = 0; $i < 3; $i++) {
            $res = $this->post('/forgot-password', [
                'email' => $email,
                'captcha' => 'test-captcha',
            ]);
        }

        $res->assertSessionHasErrors('email');
        $this->assertStringContainsString('10 menit', session('errors')->first('email'));
    }
}
