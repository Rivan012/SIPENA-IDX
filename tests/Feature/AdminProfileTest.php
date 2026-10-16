<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'Admin Profil',
            'email' => 'admin-profile@example.com',
            'password' => Hash::make('password123'),
            'jabatan' => 'Admin Bengkulu',
        ]);
    }

    public function test_admin_can_update_profile_and_upload_a_private_photo(): void
    {
        Storage::fake('local');

        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.profil.edit'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Profil/Edit')
                ->where('admin.email', 'admin-profile@example.com')
            );

        $response = $this->actingAs($this->admin, 'admin')->put(route('admin.profil.update'), [
            'name' => 'Admin Diperbarui',
            'email' => 'admin-updated@example.com',
            'no_telepon' => '081234567890',
            'jabatan' => 'Admin Cabang',
            'avatar' => UploadedFile::fake()->image('admin-profile.webp'),
        ]);

        $response->assertRedirect(route('admin.profil.edit'));
        $this->admin->refresh();
        $this->assertSame('Admin Diperbarui', $this->admin->name);
        $this->assertStringStartsWith("profile-photos/admins/{$this->admin->id}/", $this->admin->profile_photo_path);
        Storage::disk('local')->assertExists($this->admin->profile_photo_path);

        $photoUrl = route('profile-photos.show', [
            'ownerType' => 'admins',
            'ownerId' => $this->admin->id,
            'filename' => basename($this->admin->profile_photo_path),
        ]);

        $this->actingAs($this->admin, 'admin')->get($photoUrl)->assertOk();
    }

    public function test_guest_cannot_access_admin_profile_settings(): void
    {
        $this->get(route('admin.profil.edit'))->assertRedirect(route('login'));
    }

    public function test_admin_cannot_view_another_accounts_profile_photo(): void
    {
        Storage::fake('local');
        $otherAdmin = Admin::create([
            'name' => 'Admin Lain',
            'email' => 'admin-other@example.com',
            'password' => Hash::make('password123'),
            'jabatan' => 'Admin',
        ]);
        $filename = '123e4567-e89b-12d3-a456-426614174000.png';
        Storage::disk('local')->put("profile-photos/admins/{$otherAdmin->id}/{$filename}", 'image-bytes');

        $response = $this->actingAs($this->admin, 'admin')->get(route('profile-photos.show', [
            'ownerType' => 'admins',
            'ownerId' => $otherAdmin->id,
            'filename' => $filename,
        ]));

        $response->assertNotFound();
    }
}
