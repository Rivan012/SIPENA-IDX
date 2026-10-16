<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminKegiatanTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::create([
            'name' => 'Admin BEI',
            'email' => 'admin@bei.co.id',
            'password' => Hash::make('password123'),
            'jabatan' => 'Admin Pusat',
        ]);
    }

    public function test_guest_cannot_access_kegiatan(): void
    {
        $response = $this->get('/admin/kegiatan');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_view_kegiatan_index(): void
    {
        Kegiatan::create([
            'nama_kegiatan' => 'SPM Level 1 Bengkulu',
            'deskripsi' => 'Sekolah Pasar Modal',
            'jenis_kegiatan' => 'SPM',
            'tanggal' => now()->addDays(5)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'GI BEI UNIB',
            'mitra' => 'PT BEI',
            'status' => 'dipublikasikan',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get('/admin/kegiatan');
        $response->assertStatus(200);
        $response->assertSee('SPM Level 1 Bengkulu');
        $response->assertSee('GI BEI UNIB');
        $response->assertSee('PT BEI');
        $response->assertInertia(fn ($page) => $page
            ->where('kegiatan.data.0.status', 'dipublikasikan')
            ->where('kegiatan.data.0.status_label', 'Terjadwal')
            ->where('kegiatan.data.0.status_class', 'bg-sky-50 text-sky-700')
        );
    }

    public function test_admin_kegiatan_lists_approved_maker_proposals_and_legacy_activities(): void
    {
        $maker = User::factory()->create();
        $statuses = [
            'menunggu_approval' => false,
            'diproses' => false,
            'revisi' => false,
            'ditolak' => false,
            'dibatalkan' => false,
            'disetujui' => true,
            'dipublikasikan' => true,
            'berlangsung' => true,
            'selesai' => true,
        ];

        foreach ($statuses as $status => $isVisible) {
            Kegiatan::create([
                'user_id' => $maker->id,
                'nama_kegiatan' => "Proposal {$status}",
                'deskripsi' => 'Proposal maker untuk uji status',
                'jenis_kegiatan' => 'Workshop',
                'tanggal' => now()->addDays(5)->format('Y-m-d'),
                'waktu_mulai' => '09:00',
                'lokasi' => 'Aula BEI',
                'status' => $status,
            ]);
        }

        Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan Legacy Menunggu',
            'deskripsi' => 'Kegiatan dari alur lama',
            'jenis_kegiatan' => 'SPM',
            'tanggal' => now()->addDays(5)->format('Y-m-d'),
            'waktu_mulai' => '10:00',
            'lokasi' => 'Galeri Investasi',
            'status' => 'menunggu_approval',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get('/admin/kegiatan');

        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Kegiatan/Index')
            ->has('kegiatan.data', 5)
            ->where('stats.total', 5)
            ->where('stats.berjalan', 1)
            ->where('stats.selesai', 1)
            ->where('stats.menunggu', 1)
        );

        foreach (['disetujui', 'dipublikasikan', 'berlangsung', 'selesai'] as $status) {
            $response->assertSee("Proposal {$status}");
        }

        foreach (['menunggu_approval', 'diproses', 'revisi', 'ditolak', 'dibatalkan'] as $status) {
            $response->assertDontSee("Proposal {$status}");
        }

        $response->assertSee('Kegiatan Legacy Menunggu');
    }

    public function test_admin_kegiatan_csv_excludes_unapproved_maker_proposals(): void
    {
        $maker = User::factory()->create();

        foreach (['menunggu_approval', 'disetujui'] as $status) {
            Kegiatan::create([
                'user_id' => $maker->id,
                'nama_kegiatan' => "CSV {$status}",
                'deskripsi' => 'Proposal untuk ekspor CSV',
                'jenis_kegiatan' => 'Workshop',
                'tanggal' => now()->addDays(5)->format('Y-m-d'),
                'waktu_mulai' => '09:00',
                'lokasi' => 'Aula BEI',
                'status' => $status,
            ]);
        }

        Kegiatan::create([
            'nama_kegiatan' => 'CSV Legacy Menunggu',
            'deskripsi' => 'Kegiatan dari alur lama',
            'jenis_kegiatan' => 'SPM',
            'tanggal' => now()->addDays(5)->format('Y-m-d'),
            'waktu_mulai' => '10:00',
            'lokasi' => 'Galeri Investasi',
            'status' => 'menunggu_approval',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get('/admin/kegiatan?export=csv');
        $csv = $response->streamedContent();

        $this->assertStringContainsString('CSV disetujui', $csv);
        $this->assertStringContainsString('CSV Legacy Menunggu', $csv);
        $this->assertStringNotContainsString('CSV menunggu_approval', $csv);
    }

    public function test_admin_can_view_create_page(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get('/admin/kegiatan/create');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Admin/Kegiatan/Create'));
    }

    public function test_admin_can_store_kegiatan(): void
    {
        $payload = [
            'nama_kegiatan' => 'Workshop Saham Syariah',
            'deskripsi' => 'Pengenalan instrumen efek syariah',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(10)->format('Y-m-d'),
            'waktu_mulai' => '13:30',
            'waktu_selesai' => '16:00',
            'lokasi' => 'Aula FEB UMB',
            'mitra' => 'Mandiri Sekuritas',
            'kuota_peserta' => 100,
            'status' => 'dipublikasikan',
        ];

        $response = $this->actingAs($this->admin, 'admin')->post('/admin/kegiatan', $payload);
        $response->assertRedirect('/admin/kegiatan');
        $this->assertDatabaseHas('kegiatan', [
            'nama_kegiatan' => 'Workshop Saham Syariah',
            'lokasi' => 'Aula FEB UMB',
            'mitra' => 'Mandiri Sekuritas',
            'status' => 'dipublikasikan',
        ]);
    }

    public function test_admin_can_view_show_kegiatan(): void
    {
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Sosialisasi Cerdas Investasi',
            'deskripsi' => 'Edukasi masyarakat umum',
            'jenis_kegiatan' => 'Sosialisasi',
            'tanggal' => now()->addDays(3)->format('Y-m-d'),
            'waktu_mulai' => '10:00',
            'lokasi' => 'Zoom Webinar',
            'status' => 'menunggu_approval',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get("/admin/kegiatan/{$kegiatan->id}");
        $response->assertStatus(200);
        $response->assertSee('Sosialisasi Cerdas Investasi');
        $response->assertSee('Zoom Webinar');
    }

    public function test_admin_can_view_edit_and_update_kegiatan(): void
    {
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Training of Trainers (ToT)',
            'deskripsi' => 'Pelatihan Duta BEI',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(7)->format('Y-m-d'),
            'waktu_mulai' => '08:30',
            'lokasi' => 'KP BEI Bengkulu',
            'status' => 'menunggu_approval',
        ]);

        $editResponse = $this->actingAs($this->admin, 'admin')->get("/admin/kegiatan/{$kegiatan->id}/edit");
        $editResponse->assertStatus(200);
        $editResponse->assertInertia(fn ($page) => $page->component('Admin/Kegiatan/Edit'));

        $updateResponse = $this->actingAs($this->admin, 'admin')->put("/admin/kegiatan/{$kegiatan->id}", [
            'nama_kegiatan' => 'Training of Trainers (ToT) Revisi',
            'deskripsi' => 'Pelatihan Duta BEI Terupdate',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->addDays(8)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'KP BEI Bengkulu Lt 2',
            'status' => 'disetujui',
        ]);

        $updateResponse->assertRedirect("/admin/kegiatan/{$kegiatan->id}");
        $this->assertDatabaseHas('kegiatan', [
            'id' => $kegiatan->id,
            'nama_kegiatan' => 'Training of Trainers (ToT) Revisi',
            'lokasi' => 'KP BEI Bengkulu Lt 2',
            'status' => 'disetujui',
        ]);
    }

    public function test_admin_can_delete_kegiatan(): void
    {
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan Batal',
            'deskripsi' => 'Batal diadakan',
            'jenis_kegiatan' => 'Seminar',
            'tanggal' => now()->addDays(2)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Hotel Santika',
            'status' => 'dibatalkan',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->delete("/admin/kegiatan/{$kegiatan->id}");
        $response->assertRedirect('/admin/kegiatan');
        $this->assertDatabaseMissing('kegiatan', ['id' => $kegiatan->id]);
    }

    public function test_admin_can_export_csv(): void
    {
        Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan Ekspor',
            'deskripsi' => 'Data untuk tes ekspor',
            'jenis_kegiatan' => 'SPM',
            'tanggal' => now()->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Bengkulu',
            'status' => 'dipublikasikan',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get('/admin/kegiatan?export=csv');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
