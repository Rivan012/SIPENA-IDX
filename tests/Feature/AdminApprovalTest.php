<?php

namespace Tests\Feature;

use App\Mail\KegiatanStatusNotificationMail;
use App\Models\Admin;
use App\Models\Aktivitas;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
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
        $response = $this->get('/admin/approval');
        $response->assertRedirect('/login');
    }

    public function test_budget_tables_and_activity_foreign_keys_are_removed(): void
    {
        $this->assertFalse(Schema::hasTable('rencana_kegiatan'));
        $this->assertFalse(Schema::hasTable('detail_kegiatan'));
        $this->assertFalse(Schema::hasTable('pengajuan_kegiatan'));
        $this->assertFalse(Schema::hasColumn('kegiatan', 'rencana_kegiatan_id'));
        $this->assertFalse(Schema::hasColumn('kegiatan', 'pengajuan_kegiatan_id'));
    }

    public function test_budget_routes_are_removed(): void
    {
        $this->get('/admin/anggaran')->assertNotFound();
        $this->get('/admin/rencana-kegiatan')->assertNotFound();
        $this->get('/admin/pengajuan')->assertNotFound();
    }

    public function test_admin_can_view_approval_index(): void
    {
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

        $response = $this->actingAs($this->admin, 'admin')->get('/admin/approval');
        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Admin/Pengajuan/Index')
            ->has('approvalItems.data', 1)
            ->where('approvalItems.total', 1)
            ->where('approvalItems.data.0.nama_kegiatan', 'Proposal Maker Workshop')
            ->where('approvalItems.data.0.id', 1)
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
            ->where('approvalItems.data.0.nama_kegiatan', 'Proposal Maker untuk Review')
            ->where('approvalItems.data.0.submitter', $maker->name)
        );
    }

    public function test_approval_search_and_status_filter_apply_to_maker_proposals(): void
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

        $makerResponse = $this->actingAs($this->admin, 'admin')->get('/admin/approval?search=Maker&status=menunggu');
        $makerResponse->assertInertia(fn (AssertableInertia $page) => $page
            ->has('approvalItems.data', 1)
        );

        $noMatchResponse = $this->actingAs($this->admin, 'admin')->get('/admin/approval?search=Missing&status=menunggu');
        $noMatchResponse->assertInertia(fn (AssertableInertia $page) => $page
            ->has('approvalItems.data', 0)
        );
    }

    public function test_approval_pagination_lists_maker_proposals(): void
    {
        $maker = User::factory()->create();

        foreach (range(1, 12) as $index) {
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
            'kuota_peserta' => 80,
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

    public function test_admin_can_export_approval_pdf(): void
    {
        $maker = User::factory()->create();
        Kegiatan::create([
            'user_id' => $maker->id,
            'nama_kegiatan' => 'PDF Maker Proposal',
            'deskripsi' => 'Proposal untuk ekspor',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(10)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI Bengkulu',
            'status' => 'menunggu_approval',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.approval.export'));

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'application/pdf'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());

        $filteredResponse = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.approval.export', ['search' => 'PDF Maker']));
        $filteredResponse->assertStatus(200);
        $this->assertTrue(str_contains($filteredResponse->headers->get('content-type'), 'application/pdf'));
        $this->assertStringStartsWith('%PDF-', $filteredResponse->getContent());
    }

    public function test_dashboard_keeps_maker_proposals_after_budget_removal(): void
    {
        $maker = User::factory()->create();
        $proposal = Kegiatan::create([
            'user_id' => $maker->id,
            'nama_kegiatan' => 'Proposal Mitra di Dashboard',
            'deskripsi' => 'Proposal edukasi',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(10)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI Bengkulu',
            'status' => 'menunggu_approval',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get('/admin/dashboard');

        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Admin/Dashboard')
            ->where('proposalMenungguReview.0.id', $proposal->id)
            ->where('proposalMenungguReview.0.pengusul', $maker->name)
            ->where('metrics.total_mitra', 1)
        );
    }

    public function test_admin_can_schedule_ongoing_and_finish_approved_proposal(): void
    {
        $maker = User::factory()->create();
        $proposal = Kegiatan::create([
            'user_id' => $maker->id,
            'nama_kegiatan' => 'Proposal Transisi Status',
            'deskripsi' => 'Proposal edukasi',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(5)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI Bengkulu',
            'status' => 'disetujui',
        ]);

        // disetujui -> dipublikasikan (Jadwalkan)
        $this->actingAs($this->admin, 'admin')
            ->patch("/admin/approval/kegiatan/{$proposal->id}/status", [
                'status' => 'dipublikasikan',
            ])
            ->assertSessionHas('success');
        $this->assertSame('dipublikasikan', $proposal->fresh()->status);

        // dipublikasikan -> berlangsung
        $this->actingAs($this->admin, 'admin')
            ->patch("/admin/approval/kegiatan/{$proposal->id}/status", [
                'status' => 'berlangsung',
            ])
            ->assertSessionHas('success');
        $this->assertSame('berlangsung', $proposal->fresh()->status);

        // berlangsung -> selesai
        $this->actingAs($this->admin, 'admin')
            ->patch("/admin/approval/kegiatan/{$proposal->id}/status", [
                'status' => 'selesai',
            ])
            ->assertSessionHas('success');
        $this->assertSame('selesai', $proposal->fresh()->status);
    }

    public function test_admin_status_actions_trigger_dashboard_notifications_and_email(): void
    {
        Mail::fake();

        $maker = User::factory()->create([
            'email' => 'mitra-idx@example.com',
            'name' => 'Mitra Komunitas',
        ]);

        $proposal = Kegiatan::create([
            'user_id' => $maker->id,
            'nama_kegiatan' => 'Workshop Saham Syariah',
            'deskripsi' => 'Edukasi pasar modal syariah',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(7)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Universitas Bengkulu',
            'status' => 'menunggu_approval',
        ]);

        // 1. Setujui
        $this->actingAs($this->admin, 'admin')
            ->patch("/admin/approval/kegiatan/{$proposal->id}/status", [
                'status' => 'disetujui',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $maker->id,
            'judul' => 'Proposal Kegiatan Disetujui',
        ]);
        Mail::assertSent(KegiatanStatusNotificationMail::class, function ($mail) use ($maker) {
            return $mail->hasTo($maker->email) && $mail->status === 'disetujui';
        });

        // 2. Terjadwal (dipublikasikan)
        $this->actingAs($this->admin, 'admin')
            ->patch("/admin/approval/kegiatan/{$proposal->id}/status", [
                'status' => 'dipublikasikan',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $maker->id,
            'judul' => 'Kegiatan Telah Terjadwal',
        ]);
        Mail::assertSent(KegiatanStatusNotificationMail::class, function ($mail) use ($maker) {
            return $mail->hasTo($maker->email) && $mail->status === 'dipublikasikan';
        });

        // 3. Berlangsung
        $this->actingAs($this->admin, 'admin')
            ->patch("/admin/approval/kegiatan/{$proposal->id}/status", [
                'status' => 'berlangsung',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $maker->id,
            'judul' => 'Kegiatan Sedang Berlangsung',
        ]);
        Mail::assertSent(KegiatanStatusNotificationMail::class, function ($mail) use ($maker) {
            return $mail->hasTo($maker->email) && $mail->status === 'berlangsung';
        });

        // 4. Selesai
        $this->actingAs($this->admin, 'admin')
            ->patch("/admin/approval/kegiatan/{$proposal->id}/status", [
                'status' => 'selesai',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('notifikasi', [
            'user_id' => $maker->id,
            'judul' => 'Kegiatan Telah Selesai',
        ]);
        Mail::assertSent(KegiatanStatusNotificationMail::class, function ($mail) use ($maker) {
            return $mail->hasTo($maker->email) && $mail->status === 'selesai';
        });

        // 5. Cek notifikasi muncul di Dashboard Mitra
        $response = $this->actingAs($maker, 'web')->get(route('user.dashboard'));
        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('User/Dashboard')
            ->has('recentNotifications', 4)
            ->where('recentNotifications.0.judul', 'Kegiatan Telah Selesai')
        );
    }

    public function test_admin_can_download_attendance_for_approved_and_ongoing_activities(): void
    {
        $proposal = Kegiatan::create([
            'user_id' => User::factory()->create()->id,
            'nama_kegiatan' => 'Kegiatan Presensi Cepat',
            'deskripsi' => 'Deskripsi',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(5)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.kegiatan.presensi.download', $proposal->id));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_proposal_lifecycle_between_approval_and_kelola_kegiatan(): void
    {
        $maker = User::factory()->create();
        $proposal = Kegiatan::create([
            'user_id' => $maker->id,
            'nama_kegiatan' => 'Proposal Transisi Alur Baru',
            'deskripsi' => 'Deskripsi proposal alur',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(7)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI Bengkulu',
            'status' => 'menunggu_approval',
        ]);

        // 1. Sebelum disetujui: hanya muncul di Approval Proposal, TIDAK di Kelola Kegiatan
        $approvalResponse = $this->actingAs($this->admin, 'admin')->get('/admin/approval');
        $approvalResponse->assertSee('Proposal Transisi Alur Baru');

        $kegiatanResponse = $this->actingAs($this->admin, 'admin')->get('/admin/kegiatan');
        $kegiatanResponse->assertDontSee('Proposal Transisi Alur Baru');

        // 2. Admin menyetujui proposal
        $this->actingAs($this->admin, 'admin')
            ->patch("/admin/approval/kegiatan/{$proposal->id}/status", [
                'status' => 'disetujui',
            ])
            ->assertSessionHas('success');

        // 3. Setelah disetujui: muncul di Kelola Kegiatan, tidak di antrean default Approval
        $kegiatanAfter = $this->actingAs($this->admin, 'admin')->get('/admin/kegiatan');
        $kegiatanAfter->assertSee('Proposal Transisi Alur Baru');

        $approvalAfter = $this->actingAs($this->admin, 'admin')->get('/admin/approval');
        $approvalAfter->assertDontSee('Proposal Transisi Alur Baru');

        // Tersedia jika filter status disetujui dipilih
        $approvalFilter = $this->actingAs($this->admin, 'admin')->get('/admin/approval?status=disetujui');
        $approvalFilter->assertSee('Proposal Transisi Alur Baru');
    }

    public function test_admin_approval_table_supports_sorting(): void
    {
        $maker = User::factory()->create();
        Kegiatan::create([
            'user_id' => $maker->id,
            'nama_kegiatan' => 'Alpha Proposal',
            'deskripsi' => 'Deskripsi',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(2)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'status' => 'menunggu_approval',
        ]);

        Kegiatan::create([
            'user_id' => $maker->id,
            'nama_kegiatan' => 'Omega Proposal',
            'deskripsi' => 'Deskripsi',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(3)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'status' => 'menunggu_approval',
        ]);

        $responseAsc = $this->actingAs($this->admin, 'admin')->get('/admin/approval?sort=nama_kegiatan&direction=asc');
        $responseAsc->assertStatus(200);
        $this->assertSame('Alpha Proposal', $responseAsc->inertiaPage()['props']['approvalItems']['data'][0]['nama_kegiatan']);

        $responseDesc = $this->actingAs($this->admin, 'admin')->get('/admin/approval?sort=nama_kegiatan&direction=desc');
        $responseDesc->assertStatus(200);
        $this->assertSame('Omega Proposal', $responseDesc->inertiaPage()['props']['approvalItems']['data'][0]['nama_kegiatan']);
    }

    public function test_admin_can_view_activity_history(): void
    {
        $maker = User::factory()->create();
        $kegiatan = Kegiatan::create([
            'user_id' => $maker->id,
            'nama_kegiatan' => 'Kegiatan Riwayat Test',
            'deskripsi' => 'Deskripsi',
            'jenis_kegiatan' => 'SPM',
            'tanggal' => now()->addDays(5)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'status' => 'menunggu_approval',
        ]);

        Aktivitas::log('pengajuan_proposal', "Mitra {$maker->name} mengajukan proposal", $kegiatan, null, $maker);

        $response = $this->actingAs($this->admin, 'admin')->get('/admin/aktivitas');
        $response->assertStatus(200);
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Admin/Aktivitas/Index')
            ->has('aktivitas.data')
            ->where('stats.total', fn ($total) => $total >= 1)
        );

        $responseFilter = $this->actingAs($this->admin, 'admin')->get('/admin/aktivitas?role=mitra');
        $responseFilter->assertStatus(200);
    }
}
