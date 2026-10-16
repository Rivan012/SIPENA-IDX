<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\DetailKegiatan;
use App\Models\Kegiatan;
use App\Models\PengajuanKegiatan;
use App\Models\RencanaKegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class AdminApprovalTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::create([
            'name' => 'Admin Reviewer',
            'email' => 'reviewer@bei.co.id',
            'password' => Hash::make('password123'),
            'jabatan' => 'Kepala Perwakilan BEI',
        ]);
    }

    public function test_guest_cannot_access_approval_page(): void
    {
        $response = $this->get('/admin/pengajuan');
        $response->assertRedirect('/login');

        $response = $this->get('/admin/approval');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_view_approval_index(): void
    {
        $rencana = RencanaKegiatan::create([
            'admin_id' => $this->admin->id,
            'nama_kegiatan' => 'Edukasi Saham Pemula',
            'jenis_kegiatan' => 'SPM',
            'tanggal_rencana' => '2025-05-15',
            'status' => 'diajukan',
        ]);

        DetailKegiatan::create([
            'rencana_kegiatan_id' => $rencana->id,
            'deskripsi' => 'Pengenalan pasar modal',
            'tanggal' => '2025-05-15',
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula UNIB',
            'anggaran' => 15000000,
        ]);

        $pengajuan = PengajuanKegiatan::create([
            'rencana_kegiatan_id' => $rencana->id,
            'admin_id' => $this->admin->id,
            'nomor_pengajuan' => 'PROP-20250515-001',
            'tanggal_pengajuan' => '2025-05-01',
            'status' => 'diajukan',
            'catatan' => 'Mohon persetujuan anggaran',
        ]);

        $maker = User::factory()->create();
        Kegiatan::create([
            'user_id' => $maker->id,
            'nama_kegiatan' => 'Proposal Maker Workshop',
            'deskripsi' => 'Materi proposal maker',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(10)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI Bengkulu',
            'status' => 'menunggu_approval',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get('/admin/pengajuan');
        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Admin/Pengajuan/Index')
            ->has('approvalItems.data', 2)
            ->where('approvalItems.total', 2)
            ->where('approvalItems.data.0.source', 'maker')
            ->where('approvalItems.data.0.nama_kegiatan', 'Proposal Maker Workshop')
            ->where('approvalItems.data.0.id', 'maker:1')
            ->where('approvalItems.data.1.source', 'anggaran')
            ->where('approvalItems.data.1.identifier', 'PROP-20250515-001')
            ->where('approvalItems.data.1.id', 'anggaran:1')
        );
    }

    public function test_admin_approval_queue_includes_maker_proposals(): void
    {
        $maker = User::factory()->create();
        Kegiatan::create([
            'user_id' => $maker->id,
            'nama_kegiatan' => 'Proposal Maker untuk Review',
            'deskripsi' => 'Materi proposal',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(10)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI Bengkulu',
            'mitra' => $maker->name,
            'status' => 'menunggu_approval',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get('/admin/approval');

        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Admin/Pengajuan/Index')
            ->has('approvalItems.data', 1)
            ->where('approvalItems.data.0.source', 'maker')
            ->where('approvalItems.data.0.nama_kegiatan', 'Proposal Maker untuk Review')
            ->where('approvalItems.data.0.submitter', $maker->name)
        );
    }

    public function test_approval_search_and_status_filter_apply_to_both_sources(): void
    {
        $maker = User::factory()->create();
        Kegiatan::create([
            'user_id' => $maker->id,
            'nama_kegiatan' => 'Workshop Maker Dicari',
            'deskripsi' => 'Proposal maker',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(10)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI Bengkulu',
            'status' => 'menunggu_approval',
        ]);

        $rencana = RencanaKegiatan::create([
            'admin_id' => $this->admin->id,
            'nama_kegiatan' => 'Workshop Anggaran Dicari',
            'jenis_kegiatan' => 'Workshop',
            'tanggal_rencana' => now()->addDays(11)->format('Y-m-d'),
            'status' => 'diajukan',
        ]);
        PengajuanKegiatan::create([
            'rencana_kegiatan_id' => $rencana->id,
            'admin_id' => $this->admin->id,
            'nomor_pengajuan' => 'ANG-SEARCH-001',
            'tanggal_pengajuan' => now()->toDateString(),
            'status' => 'diajukan',
        ]);

        $makerResponse = $this->actingAs($this->admin, 'admin')->get('/admin/approval?search=Maker&status=menunggu');
        $makerResponse->assertInertia(fn (AssertableInertia $page) => $page
            ->has('approvalItems.data', 1)
            ->where('approvalItems.data.0.source', 'maker')
        );

        $budgetResponse = $this->actingAs($this->admin, 'admin')->get('/admin/approval?search=Anggaran&status=menunggu');
        $budgetResponse->assertInertia(fn (AssertableInertia $page) => $page
            ->has('approvalItems.data', 1)
            ->where('approvalItems.data.0.source', 'anggaran')
        );
    }

    public function test_approval_pagination_spans_both_sources(): void
    {
        $maker = User::factory()->create();

        foreach (range(1, 6) as $index) {
            Kegiatan::create([
                'user_id' => $maker->id,
                'nama_kegiatan' => "Maker Activity {$index}",
                'deskripsi' => 'Proposal maker',
                'jenis_kegiatan' => 'Workshop',
                'tanggal' => now()->addDays(10)->format('Y-m-d'),
                'waktu_mulai' => '09:00',
                'lokasi' => 'Aula BEI Bengkulu',
                'status' => 'menunggu_approval',
            ]);

            $rencana = RencanaKegiatan::create([
                'admin_id' => $this->admin->id,
                'nama_kegiatan' => "Budget Activity {$index}",
                'jenis_kegiatan' => 'SPM',
                'tanggal_rencana' => now()->addDays(11)->format('Y-m-d'),
                'status' => 'diajukan',
            ]);
            PengajuanKegiatan::create([
                'rencana_kegiatan_id' => $rencana->id,
                'admin_id' => $this->admin->id,
                'nomor_pengajuan' => sprintf('PAG-%03d', $index),
                'tanggal_pengajuan' => now()->toDateString(),
                'status' => 'diajukan',
            ]);
        }

        $firstPage = $this->actingAs($this->admin, 'admin')->get('/admin/approval');
        $firstPage->assertInertia(fn (AssertableInertia $page) => $page
            ->has('approvalItems.data', 10)
            ->where('approvalItems.total', 12)
        );

        $secondPage = $this->actingAs($this->admin, 'admin')->get('/admin/approval?page=2');
        $secondPage->assertInertia(fn (AssertableInertia $page) => $page
            ->has('approvalItems.data', 2)
            ->where('approvalItems.total', 12)
        );
    }

    public function test_admin_can_request_revision_and_maker_can_resubmit(): void
    {
        $maker = User::factory()->create();
        $proposal = Kegiatan::create([
            'user_id' => $maker->id,
            'nama_kegiatan' => 'Proposal Butuh Revisi',
            'deskripsi' => 'Deskripsi awal',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(10)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI Bengkulu',
            'status' => 'menunggu_approval',
        ]);

        $this->actingAs($this->admin, 'admin')
            ->patch("/admin/approval/kegiatan/{$proposal->id}/status", [
                'status' => 'revisi',
                'catatan' => 'Mohon lengkapi target peserta dan materi.',
            ])
            ->assertSessionHas('success');

        $proposal->refresh();
        $this->assertSame('revisi', $proposal->status);
        $this->assertSame('Mohon lengkapi target peserta dan materi.', $proposal->approval_note);
        $this->assertSame($this->admin->id, $proposal->reviewed_by_admin_id);

        $makerPage = $this->actingAs($maker, 'web')->get('/kegiatan');
        $makerPage->assertInertia(fn (AssertableInertia $page) => $page
            ->component('User/KegiatanSaya')
            ->where('kegiatanList.data.0.status', 'revisi')
            ->where('kegiatanList.data.0.approval_note', 'Mohon lengkapi target peserta dan materi.')
            ->where('kegiatanList.data.0.reviewed_by_admin.name', $this->admin->name)
        );

        $this->actingAs($maker, 'web')->put("/kegiatan/{$proposal->id}", [
            'nama_kegiatan' => 'Proposal Butuh Revisi',
            'deskripsi' => 'Deskripsi sudah dilengkapi',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(11)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI Bengkulu',
        ])->assertRedirect("/kegiatan/{$proposal->id}");

        $proposal->refresh();
        $this->assertSame('menunggu_approval', $proposal->status);
        $this->assertNull($proposal->approval_note);
        $this->assertNull($proposal->reviewed_by_admin_id);
    }

    public function test_admin_must_add_a_note_to_reject_maker_proposal(): void
    {
        $maker = User::factory()->create();
        $proposal = Kegiatan::create([
            'user_id' => $maker->id,
            'nama_kegiatan' => 'Proposal Ditolak',
            'deskripsi' => 'Deskripsi proposal',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(10)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI Bengkulu',
            'status' => 'menunggu_approval',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->patch("/admin/approval/kegiatan/{$proposal->id}/status", ['status' => 'ditolak']);

        $response->assertSessionHasErrors('catatan');
        $this->assertSame('menunggu_approval', $proposal->fresh()->status);
    }

    public function test_admin_can_view_approval_detail(): void
    {
        $rencana = RencanaKegiatan::create([
            'admin_id' => $this->admin->id,
            'nama_kegiatan' => 'Workshop Investasi Syariah',
            'jenis_kegiatan' => 'Workshop',
            'tanggal_rencana' => '2025-06-10',
            'status' => 'diajukan',
        ]);

        $pengajuan = PengajuanKegiatan::create([
            'rencana_kegiatan_id' => $rencana->id,
            'admin_id' => $this->admin->id,
            'nomor_pengajuan' => 'PROP-20250610-002',
            'tanggal_pengajuan' => '2025-05-20',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get("/admin/pengajuan/{$pengajuan->id}");
        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Admin/Pengajuan/Show')
            ->where('pengajuan.nomor_pengajuan', 'PROP-20250610-002')
        );
    }

    public function test_admin_can_approve_proposal_and_auto_create_kegiatan(): void
    {
        $rencana = RencanaKegiatan::create([
            'admin_id' => $this->admin->id,
            'nama_kegiatan' => 'Seminar Pasar Modal Nasional',
            'jenis_kegiatan' => 'Sosialisasi',
            'tanggal_rencana' => '2025-07-20',
            'status' => 'diajukan',
        ]);

        DetailKegiatan::create([
            'rencana_kegiatan_id' => $rencana->id,
            'deskripsi' => 'Seminar akbar',
            'tanggal' => '2025-07-20',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '13:00',
            'lokasi' => 'Gedung Serba Guna',
            'target_peserta' => 200,
            'anggaran' => 20000000,
        ]);

        $pengajuan = PengajuanKegiatan::create([
            'rencana_kegiatan_id' => $rencana->id,
            'admin_id' => $this->admin->id,
            'nomor_pengajuan' => 'PROP-20250720-003',
            'tanggal_pengajuan' => '2025-06-01',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->patch("/admin/pengajuan/{$pengajuan->id}/status", [
                'status' => 'disetujui',
                'catatan' => 'Disetujui sesuai pagu RKAP.',
                'terbitkan_kegiatan' => 1,
            ]);

        $response->assertSessionHas('success');
        $this->assertEquals('disetujui', $pengajuan->fresh()->status);
        $this->assertEquals('disetujui', $rencana->fresh()->status);

        $this->assertDatabaseHas('kegiatan', [
            'pengajuan_kegiatan_id' => $pengajuan->id,
            'nama_kegiatan' => 'Seminar Pasar Modal Nasional',
            'status' => 'dipublikasikan',
        ]);
    }

    public function test_admin_can_request_revision(): void
    {
        $rencana = RencanaKegiatan::create([
            'admin_id' => $this->admin->id,
            'nama_kegiatan' => 'Webinar Perlu Revisi',
            'jenis_kegiatan' => 'Webinar',
            'tanggal_rencana' => '2025-08-10',
            'status' => 'diajukan',
        ]);

        $pengajuan = PengajuanKegiatan::create([
            'rencana_kegiatan_id' => $rencana->id,
            'admin_id' => $this->admin->id,
            'nomor_pengajuan' => 'PROP-20250810-004',
            'tanggal_pengajuan' => '2025-07-01',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->patch("/admin/pengajuan/{$pengajuan->id}/status", [
                'status' => 'revisi',
                'catatan' => 'Kurangi anggaran konsumsi.',
            ]);

        $response->assertSessionHas('success');
        $this->assertEquals('revisi', $pengajuan->fresh()->status);
        $this->assertEquals('Kurangi anggaran konsumsi.', $pengajuan->fresh()->catatan);
    }

    public function test_admin_can_export_approval_csv(): void
    {
        $maker = User::factory()->create();
        Kegiatan::create([
            'user_id' => $maker->id,
            'nama_kegiatan' => 'CSV Maker Proposal',
            'deskripsi' => 'Proposal untuk ekspor',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(10)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI Bengkulu',
            'status' => 'menunggu_approval',
        ]);

        $rencana = RencanaKegiatan::create([
            'admin_id' => $this->admin->id,
            'nama_kegiatan' => 'CSV Budget Proposal',
            'jenis_kegiatan' => 'SPM',
            'tanggal_rencana' => now()->addDays(10)->format('Y-m-d'),
            'status' => 'diajukan',
        ]);
        PengajuanKegiatan::create([
            'rencana_kegiatan_id' => $rencana->id,
            'admin_id' => $this->admin->id,
            'nomor_pengajuan' => 'CSV-001',
            'tanggal_pengajuan' => now()->toDateString(),
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/pengajuan/export');

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
        $this->assertStringContainsString('CSV Maker Proposal', $response->streamedContent());
        $this->assertStringContainsString('CSV Budget Proposal', $response->streamedContent());

        $filteredResponse = $this->actingAs($this->admin, 'admin')
            ->get('/admin/pengajuan/export?search=CSV Maker');
        $filteredCsv = $filteredResponse->streamedContent();
        $this->assertStringContainsString('CSV Maker Proposal', $filteredCsv);
        $this->assertStringNotContainsString('CSV Budget Proposal', $filteredCsv);
    }
}
