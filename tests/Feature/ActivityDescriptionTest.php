<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\DetailKegiatan;
use App\Models\Kegiatan;
use App\Models\PengajuanKegiatan;
use App\Models\RencanaKegiatan;
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

    public function test_planning_description_is_sanitized_and_remains_safe_when_approved(): void
    {
        $rawDescription = '<p>Rencana <strong>edukasi</strong></p><script>alert(1)</script>';

        $this->actingAs($this->admin, 'admin')->post('/admin/rencana-kegiatan', [
            'nama_kegiatan' => 'Rencana Rich Text',
            'jenis_kegiatan' => 'Workshop',
            'tanggal_rencana' => now()->addDays(10)->format('Y-m-d'),
            'lokasi' => 'Aula BEI',
            'waktu_mulai' => '09:00',
            'target_peserta' => 50,
            'anggaran' => 10000000,
            'deskripsi' => $rawDescription,
            'status' => 'diajukan',
        ])->assertRedirect('/admin/rencana-kegiatan');

        $rencana = RencanaKegiatan::where('nama_kegiatan', 'Rencana Rich Text')->firstOrFail();
        $detail = DetailKegiatan::where('rencana_kegiatan_id', $rencana->id)->firstOrFail();
        $this->assertStringContainsString('<strong>edukasi</strong>', $detail->deskripsi);
        $this->assertStringNotContainsString('<script', $detail->deskripsi);

        $planDetail = $this->actingAs($this->admin, 'admin')->get("/admin/rencana-kegiatan/{$rencana->id}");
        $planDetail->assertInertia(fn (AssertableInertia $page) => $page
            ->where('detail.deskripsi_html', app(ActivityDescriptionSanitizer::class)->sanitize($detail->deskripsi))
        );

        $pengajuan = PengajuanKegiatan::where('rencana_kegiatan_id', $rencana->id)->firstOrFail();
        $approvalDetail = $this->actingAs($this->admin, 'admin')->get("/admin/pengajuan/{$pengajuan->id}");
        $approvalDetail->assertInertia(fn (AssertableInertia $page) => $page
            ->where('detail.deskripsi_html', app(ActivityDescriptionSanitizer::class)->sanitize($rawDescription))
        );

        $this->actingAs($this->admin, 'admin')
            ->patch("/admin/pengajuan/{$pengajuan->id}/status", [
                'status' => 'disetujui',
                'catatan' => 'Disetujui',
                'terbitkan_kegiatan' => true,
            ])
            ->assertSessionHas('success');

        $publishedActivity = Kegiatan::where('pengajuan_kegiatan_id', $pengajuan->id)->firstOrFail();
        $this->assertStringContainsString('<strong>edukasi</strong>', $publishedActivity->deskripsi);
        $this->assertStringNotContainsString('<script', $publishedActivity->deskripsi);
    }

    public function test_detail_kegiatan_endpoint_sanitizes_description(): void
    {
        $rencana = RencanaKegiatan::create([
            'admin_id' => $this->admin->id,
            'nama_kegiatan' => 'Rencana Detail Sanitasi',
            'jenis_kegiatan' => 'Workshop',
            'tanggal_rencana' => now()->addDays(10)->format('Y-m-d'),
            'status' => 'draft',
        ]);

        $this->actingAs($this->admin, 'admin')->post("/admin/rencana-kegiatan/{$rencana->id}/detail", [
            'deskripsi' => '<p>Detail <em>format</em></p><script>alert(1)</script>',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(10)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'target_peserta' => 20,
            'anggaran' => 1000000,
        ])->assertSessionHas('success');

        $detail = DetailKegiatan::where('rencana_kegiatan_id', $rencana->id)->firstOrFail();
        $this->assertStringContainsString('<em>format</em>', $detail->deskripsi);
        $this->assertStringNotContainsString('<script', $detail->deskripsi);
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
