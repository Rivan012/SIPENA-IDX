<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class AdminPenggunaTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::create([
            'name' => 'Admin Pengguna',
            'email' => 'admin-pengguna@bei.co.id',
            'password' => Hash::make('password123'),
            'jabatan' => 'Admin Monitoring',
        ]);

        $this->user = User::create([
            'name' => 'Peserta Ujian',
            'email' => 'peserta@example.com',
            'password' => Hash::make('password123'),
            'no_telepon' => '081234567890',
        ]);
    }

    public function test_guest_cannot_access_monitoring_and_user_kegiatan(): void
    {
        $response = $this->get('/admin/pengguna');
        $response->assertRedirect('/login');

        $response = $this->get('/kegiatan');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_view_monitoring_page(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get('/admin/pengguna');
        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Admin/Monitoring/Index')
            ->has('users.data', 1)
            ->where('users.data.0.name', 'Peserta Ujian')
        );
    }

    public function test_admin_can_export_users_csv(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get('/admin/monitoring/export');
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
    }

    public function test_dashboard_shows_maker_proposals_grouped_by_status(): void
    {
        foreach (['menunggu_approval', 'disetujui', 'dipublikasikan', 'berlangsung', 'ditolak', 'dibatalkan', 'selesai'] as $status) {
            Kegiatan::create([
                'user_id' => $this->user->id,
                'nama_kegiatan' => "Kegiatan {$status}",
                'deskripsi' => 'Deskripsi kegiatan',
                'jenis_kegiatan' => 'Workshop',
                'tanggal' => now()->addDays(5)->format('Y-m-d'),
                'waktu_mulai' => '09:00',
                'lokasi' => 'GI BEI UNIB',
                'status' => $status,
            ]);
        }

        Kegiatan::create([
            'user_id' => User::factory()->create()->id,
            'nama_kegiatan' => 'Proposal Maker Lain',
            'deskripsi' => 'Bukan proposal user ini',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(5)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'GI BEI UNIB',
            'status' => 'menunggu_approval',
        ]);

        $response = $this->actingAs($this->user, 'web')->get('/dashboard');

        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('User/Dashboard')
            ->where('proposedCount', 1)
            ->where('otherStatusCount', 5)
            ->where('completedCount', 1)
        );
    }

    public function test_user_can_view_owned_proposals_and_dashboard(): void
    {
        $kegiatan = Kegiatan::create([
            'user_id' => $this->user->id,
            'nama_kegiatan' => 'SPM Batch Terbuka',
            'deskripsi' => 'Deskripsi kegiatan SPM',
            'jenis_kegiatan' => 'SPM',
            'tanggal' => now()->addDays(5)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'GI BEI UNIB',
            'kuota_peserta' => 50,
            'status' => 'dipublikasikan',
        ]);

        $response = $this->actingAs($this->user, 'web')->get('/kegiatan');
        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('User/KegiatanSaya')
            ->has('kegiatanList.data', 1)
            ->where('kegiatanList.data.0.nama_kegiatan', 'SPM Batch Terbuka')
        );

        $legacyResponse = $this->actingAs($this->user, 'web')->get('/kegiatan-saya');
        $legacyResponse->assertInertia(fn (AssertableInertia $page) => $page
            ->component('User/KegiatanSaya')
            ->has('kegiatanList.data', 1)
        );

        $responseDetail = $this->actingAs($this->user, 'web')->get("/kegiatan/{$kegiatan->id}");
        $responseDetail->assertStatus(200);
        $responseDetail->assertInertia(fn (AssertableInertia $page) => $page
            ->component('User/Kegiatan/Show')
            ->where('kegiatan.nama_kegiatan', 'SPM Batch Terbuka')
        );
    }

    public function test_user_can_submit_activity_proposal_as_maker(): void
    {
        $response = $this->actingAs($this->user, 'web')->post('/kegiatan/ajukan', [
            'nama_kegiatan' => 'Workshop Investasi Mandiri',
            'deskripsi' => 'Kegiatan proposal baru oleh maker',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(7)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '12:00',
            'lokasi' => 'Aula BEI Bengkulu',
            'mitra' => 'Peserta Ujian',
            'kuota_peserta' => 80,
        ]);

        $response->assertRedirect('/kegiatan');
        $this->assertDatabaseHas('kegiatan', [
            'nama_kegiatan' => 'Workshop Investasi Mandiri',
            'mitra' => 'Peserta Ujian',
            'status' => 'menunggu_approval',
        ]);

        $catalogResponse = $this->actingAs($this->user, 'web')->get('/kegiatan');
        $catalogResponse->assertStatus(200);
        $catalogResponse->assertSee('Workshop Investasi Mandiri');
    }

    public function test_maker_can_upload_pdf_proposal_and_admin_can_download_it(): void
    {
        Storage::fake('local');

        $response = $this->actingAs($this->user, 'web')->post('/kegiatan/ajukan', [
            'nama_kegiatan' => 'Workshop dengan Dokumen',
            'deskripsi' => 'Proposal disertakan sebagai PDF',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(7)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI Bengkulu',
            'proposal_file' => UploadedFile::fake()->createWithContent('proposal.pdf', '%PDF-1.4 proposal'),
        ]);

        $response->assertRedirect('/kegiatan');
        $proposal = Kegiatan::where('nama_kegiatan', 'Workshop dengan Dokumen')->firstOrFail();
        $this->assertNotNull($proposal->proposal_file_path);
        Storage::disk('local')->assertExists($proposal->proposal_file_path);

        $userDownload = $this->actingAs($this->user, 'web')->get("/kegiatan/{$proposal->id}/proposal");
        $userDownload->assertDownload("proposal-{$proposal->id}.pdf");

        $otherMaker = User::factory()->create();
        $this->actingAs($otherMaker, 'web')->get("/kegiatan/{$proposal->id}/proposal")->assertForbidden();

        $adminDownload = $this->actingAs($this->admin, 'admin')->get("/admin/kegiatan/{$proposal->id}/proposal");
        $adminDownload->assertDownload("proposal-{$proposal->id}.pdf");
    }

    public function test_maker_can_replace_an_existing_proposal_pdf(): void
    {
        Storage::fake('local');
        $oldPath = 'proposals/old-proposal.pdf';
        Storage::disk('local')->put($oldPath, '%PDF-1.4 old proposal');

        $proposal = Kegiatan::create([
            'user_id' => $this->user->id,
            'nama_kegiatan' => 'Proposal Revisi PDF',
            'deskripsi' => 'Dokumen diperbarui',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(9)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI Bengkulu',
            'status' => 'menunggu_approval',
            'proposal_file_path' => $oldPath,
        ]);

        $response = $this->actingAs($this->user, 'web')->post("/kegiatan/{$proposal->id}", [
            '_method' => 'put',
            'nama_kegiatan' => 'Proposal Revisi PDF',
            'deskripsi' => 'Dokumen diperbarui',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(10)->format('Y-m-d'),
            'waktu_mulai' => '10:00',
            'lokasi' => 'Aula BEI Bengkulu',
            'proposal_file' => UploadedFile::fake()->createWithContent('proposal-baru.pdf', '%PDF-1.4 new proposal'),
        ]);

        $response->assertRedirect("/kegiatan/{$proposal->id}");
        $proposal->refresh();
        $this->assertNotSame($oldPath, $proposal->proposal_file_path);
        Storage::disk('local')->assertMissing($oldPath);
        Storage::disk('local')->assertExists($proposal->proposal_file_path);
    }

    public function test_maker_cannot_upload_non_pdf_proposal(): void
    {
        Storage::fake('local');

        $response = $this->actingAs($this->user, 'web')->post('/kegiatan/ajukan', [
            'nama_kegiatan' => 'Workshop Tanpa PDF',
            'deskripsi' => 'File harus ditolak',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(7)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI Bengkulu',
            'proposal_file' => UploadedFile::fake()->createWithContent('proposal.txt', '%PDF-1.4 still not a .pdf file'),
        ]);

        $response->assertSessionHasErrors('proposal_file');
        $this->assertDatabaseMissing('kegiatan', ['nama_kegiatan' => 'Workshop Tanpa PDF']);
    }

    public function test_user_can_update_and_delete_own_activity_proposal(): void
    {
        $proposal = Kegiatan::create([
            'user_id' => $this->user->id,
            'nama_kegiatan' => 'Proposal Awal',
            'deskripsi' => 'Deskripsi awal',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(9)->format('Y-m-d'),
            'waktu_mulai' => '08:00',
            'lokasi' => 'Rumah Komunitas',
            'mitra' => 'Maker Test',
            'status' => 'menunggu_approval',
        ]);

        $editResponse = $this->actingAs($this->user, 'web')->get("/kegiatan/{$proposal->id}/edit");
        $editResponse->assertStatus(200);

        $updateResponse = $this->actingAs($this->user, 'web')->put("/kegiatan/{$proposal->id}", [
            'nama_kegiatan' => 'Proposal Revisi',
            'deskripsi' => 'Deskripsi revisi',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(10)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '11:00',
            'lokasi' => 'Seminar Hall',
            'mitra' => 'Maker Test',
            'kuota_peserta' => 40,
        ]);

        $updateResponse->assertRedirect("/kegiatan/{$proposal->id}");
        $this->assertDatabaseHas('kegiatan', [
            'id' => $proposal->id,
            'nama_kegiatan' => 'Proposal Revisi',
            'lokasi' => 'Seminar Hall',
        ]);

        $deleteResponse = $this->actingAs($this->user, 'web')->delete("/kegiatan/{$proposal->id}");
        $deleteResponse->assertRedirect('/kegiatan-saya');
        $this->assertDatabaseMissing('kegiatan', ['id' => $proposal->id]);
    }

    public function test_user_can_register_and_cancel_kegiatan(): void
    {
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Edukasi Saham Pemula',
            'deskripsi' => 'Belajar saham',
            'jenis_kegiatan' => 'Edukasi',
            'tanggal' => now()->addDays(3)->format('Y-m-d'),
            'waktu_mulai' => '10:00',
            'lokasi' => 'Aula UNIB',
            'kuota_peserta' => 100,
            'status' => 'dipublikasikan',
        ]);

        // Daftar
        $responseDaftar = $this->actingAs($this->user, 'web')
            ->post("/kegiatan/{$kegiatan->id}/daftar");
        $responseDaftar->assertSessionHas('success');
        $this->assertTrue($kegiatan->fresh()->peserta()->where('user_id', $this->user->id)->exists());

        // Proposal page stays separate from participant registration records.
        $responseSaya = $this->actingAs($this->user, 'web')->get('/kegiatan-saya');
        $responseSaya->assertInertia(fn (AssertableInertia $page) => $page
            ->component('User/KegiatanSaya')
            ->has('kegiatanList.data', 0)
        );

        // Batalkan
        $responseBatal = $this->actingAs($this->user, 'web')
            ->delete("/kegiatan/{$kegiatan->id}/batal");
        $responseBatal->assertSessionHas('success');
        $this->assertFalse($kegiatan->fresh()->peserta()->where('user_id', $this->user->id)->exists());
    }
}
