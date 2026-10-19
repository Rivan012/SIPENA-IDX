<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Kegiatan;
use App\Models\User;
use App\Support\ActivityDescriptionSanitizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ActivityDescriptionTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    private User $maker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'Admin Editor',
            'email' => 'admin-editor@example.com',
            'password' => Hash::make('password123'),
            'jabatan' => 'Admin',
        ]);

        $this->maker = User::create([
            'name' => 'Maker Editor',
            'email' => 'maker-editor@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
    }

    public function test_sanitizer_keeps_allowed_formatting_and_removes_unsafe_markup(): void
    {
        Storage::fake('local');
        $sanitizer = app(ActivityDescriptionSanitizer::class);

        $clean = $sanitizer->sanitize('<p>Materi <strong>penting</strong></p><script>alert(1)</script><a href="javascript:alert(2)">tautan</a><img src="data:image/png;base64,AAAA" onerror="alert(3)">');

        $this->assertStringContainsString('<strong>penting</strong>', $clean);
        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringNotContainsString('data:image', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
    }

    public function test_maker_proposal_preserves_allowed_formatting_and_removes_unsafe_html(): void
    {
        $response = $this->actingAs($this->maker, 'web')->post('/kegiatan/ajukan', [
            'nama_kegiatan' => 'Workshop Rich Text',
            'deskripsi' => '<p>Materi <strong>pasar modal</strong></p><script>alert(1)</script><p><img src="https://evil.example/banner.png" onerror="alert(2)"></p>',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(7)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
        ]);

        $response->assertRedirect('/kegiatan');
        $description = Kegiatan::where('nama_kegiatan', 'Workshop Rich Text')->value('deskripsi');

        $this->assertStringContainsString('<strong>pasar modal</strong>', $description);
        $this->assertStringNotContainsString('<script', $description);
        $this->assertStringNotContainsString('onerror', $description);
        $this->assertStringNotContainsString('evil.example', $description);
    }

    public function test_legacy_description_is_sanitized_before_being_sent_to_the_detail_page(): void
    {
        $rawDescription = '<p>Legacy <strong>description</strong></p><script>alert(1)</script>';
        $kegiatan = Kegiatan::create([
            'user_id' => $this->maker->id,
            'nama_kegiatan' => 'Kegiatan Legacy HTML',
            'deskripsi' => $rawDescription,
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(7)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'status' => 'menunggu_approval',
        ]);

        $response = $this->actingAs($this->maker, 'web')->get("/kegiatan/{$kegiatan->id}");

        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->where('kegiatan.deskripsi_html', app(ActivityDescriptionSanitizer::class)->sanitize($rawDescription))
        );
    }

    public function test_admin_activity_description_is_sanitized_before_persistence(): void
    {
        $this->actingAs($this->admin, 'admin')->post('/admin/kegiatan', [
            'nama_kegiatan' => 'Kegiatan Admin Rich Text',
            'deskripsi' => '<p>Agenda <em>edukasi</em></p><script>alert(1)</script>',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(7)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'status' => 'dipublikasikan',
        ])->assertRedirect('/admin/kegiatan');

        $description = Kegiatan::where('nama_kegiatan', 'Kegiatan Admin Rich Text')->value('deskripsi');

        $this->assertStringContainsString('<em>edukasi</em>', $description);
        $this->assertStringNotContainsString('<script', $description);
    }

    public function test_uploaded_description_image_is_private_until_used_by_a_public_activity(): void
    {
        Storage::fake('local');

        $uploadResponse = $this->actingAs($this->maker, 'web')->postJson('/description-images/users', [
            'image' => UploadedFile::fake()->image('banner.png'),
        ]);

        $uploadResponse->assertCreated();
        $source = $uploadResponse->json('src');
        $storagePath = ltrim(parse_url($source, PHP_URL_PATH), '/');
        Storage::disk('local')->assertExists($storagePath);
        $this->assertSame($source, app(ActivityDescriptionSanitizer::class)->firstImageUrl("<p><img src=\"{$source}\" alt=\"Banner\"></p>"));

        $this->actingAs($this->maker, 'web')->get($source)->assertOk();

        $otherUser = User::factory()->create();
        $this->actingAs($otherUser, 'web')->get($source)->assertForbidden();

        Kegiatan::create([
            'user_id' => $this->maker->id,
            'nama_kegiatan' => 'Kegiatan dengan Banner',
            'deskripsi' => "<p>Deskripsi</p><p><img src=\"{$source}\" alt=\"Banner kegiatan\"></p>",
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(7)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'status' => 'dipublikasikan',
        ]);

        $this->actingAs($otherUser, 'web')->get($source)->assertOk();
    }

    public function test_admin_description_image_is_private_until_used_by_a_public_activity(): void
    {
        Storage::fake('local');

        $uploadResponse = $this->actingAs($this->admin, 'admin')->postJson('/admin/description-images/admins', [
            'image' => UploadedFile::fake()->image('plan-banner.jpg'),
        ]);

        $uploadResponse->assertCreated();
        $source = $uploadResponse->json('src');
        $storagePath = ltrim(parse_url($source, PHP_URL_PATH), '/');
        Storage::disk('local')->assertExists($storagePath);

        $this->actingAs($this->admin, 'admin')->get($source)->assertOk();
        $this->actingAs($this->maker, 'web')->get($source)->assertForbidden();

        Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan dari Rencana',
            'deskripsi' => "<p>Deskripsi rencana</p><p><img src=\"{$source}\" alt=\"Banner kegiatan\"></p>",
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(7)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'status' => 'dipublikasikan',
        ]);

        $this->actingAs($this->maker, 'web')->get($source)->assertOk();
    }

    public function test_image_upload_rejects_unsupported_formats_and_oversized_files(): void
    {
        $this->actingAs($this->maker, 'web')->postJson('/description-images/users', [
            'image' => UploadedFile::fake()->createWithContent('payload.svg', '<svg onload="alert(1)"></svg>'),
        ])->assertStatus(422)->assertJsonValidationErrors('image');

        $this->actingAs($this->maker, 'web')->postJson('/description-images/users', [
            'image' => UploadedFile::fake()->image('too-large.png')->size(5121),
        ])->assertStatus(422)->assertJsonValidationErrors('image');
    }
}
