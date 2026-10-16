<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class UserSidebarNavigationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::create([
            'name' => 'Peserta Tes Navigasi',
            'email' => 'peserta-nav@example.com',
            'password' => Hash::make('password123'),
            'no_telepon' => '081234567899',
        ]);
    }

    public function test_user_layout_renders_sidebar_navigation(): void
    {
        $response = $this->actingAs($this->user, 'web')->get(route('user.dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('User/Dashboard')
            ->has('auth.user')
            ->where('auth.user.name', 'Peserta Tes Navigasi')
        );
    }

    public function test_user_can_access_and_update_profile(): void
    {
        $response = $this->actingAs($this->user, 'web')->get(route('user.profil.edit'));
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('User/Profil/Edit')
            ->where('user.email', 'peserta-nav@example.com')
        );

        $responseUpdate = $this->actingAs($this->user, 'web')->put(route('user.profil.update'), [
            'name' => 'Peserta Update',
            'email' => 'peserta-updated@example.com',
            'no_telepon' => '0899999999',
        ]);

        $responseUpdate->assertRedirect(route('user.profil.edit'));
        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => 'Peserta Update',
            'email' => 'peserta-updated@example.com',
        ]);
    }

    public function test_user_can_upload_and_view_their_private_profile_photo(): void
    {
        Storage::fake('local');

        $response = $this->actingAs($this->user, 'web')->put(route('user.profil.update'), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'no_telepon' => $this->user->no_telepon,
            'avatar' => UploadedFile::fake()->image('profile.png'),
        ]);

        $response->assertRedirect(route('user.profil.edit'));
        $this->user->refresh();
        $this->assertStringStartsWith("profile-photos/users/{$this->user->id}/", $this->user->profile_photo_path);
        Storage::disk('local')->assertExists($this->user->profile_photo_path);

        $photoUrl = route('profile-photos.show', [
            'ownerType' => 'users',
            'ownerId' => $this->user->id,
            'filename' => basename($this->user->profile_photo_path),
        ]);

        $this->get($photoUrl)->assertOk();

        $otherUser = User::factory()->create();
        $this->actingAs($otherUser, 'web')->get($photoUrl)->assertNotFound();
    }

    public function test_user_cannot_upload_an_unsupported_profile_photo(): void
    {
        Storage::fake('local');

        $response = $this->actingAs($this->user, 'web')->put(route('user.profil.update'), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'no_telepon' => $this->user->no_telepon,
            'avatar' => UploadedFile::fake()->create('profile.svg', 1, 'image/svg+xml'),
        ]);

        $response->assertSessionHasErrors('avatar');
        $this->assertNull($this->user->fresh()->profile_photo_path);
    }

    public function test_user_can_access_laporan_and_notifikasi(): void
    {
        $responseLaporan = $this->actingAs($this->user, 'web')->get(route('user.laporan.index'));
        $responseLaporan->assertStatus(200);
        $responseLaporan->assertInertia(fn (Assert $page) => $page
            ->component('User/Laporan/Index')
            ->has('laporanList')
        );

        $responseNotif = $this->actingAs($this->user, 'web')->get(route('user.notifikasi.index'));
        $responseNotif->assertStatus(200);
        $responseNotif->assertInertia(fn (Assert $page) => $page
            ->component('User/Notifikasi/Index')
            ->has('notifikasi')
        );
    }
}
