<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\DetailKegiatan;
use App\Models\RencanaKegiatan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAnggaranTest extends TestCase
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
            'jabatan' => 'Admin Anggaran',
        ]);
    }

    public function test_guest_cannot_access_anggaran(): void
    {
        $response = $this->get('/admin/anggaran');
        $response->assertRedirect('/login');

        $response = $this->get('/admin/rencana-kegiatan');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_view_anggaran_index_with_kpi_cards(): void
    {
        $rencana = RencanaKegiatan::create([
            'admin_id' => $this->admin->id,
            'nama_kegiatan' => 'Edukasi Saham Pemula',
            'jenis_kegiatan' => 'SPM',
            'tanggal_rencana' => '2025-05-15',
            'status' => 'disetujui',
            'keterangan' => 'Rencana awal',
        ]);

        DetailKegiatan::create([
            'rencana_kegiatan_id' => $rencana->id,
            'deskripsi' => 'Program pengenalan saham',
            'tanggal' => '2025-05-15',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '15:00',
            'lokasi' => 'Aula UNIB',
            'target_peserta' => 150,
            'anggaran' => 12500000,
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get('/admin/anggaran');
        $response->assertStatus(200);
        $response->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('Admin/RencanaKegiatan/Index')
            ->has('rencanaList.data', 1)
            ->where('rencanaList.data.0.nama_kegiatan', 'Edukasi Saham Pemula')
        );
    }

    public function test_admin_can_create_new_rencana_anggaran(): void
    {
        $payload = [
            'nama_kegiatan' => 'Workshop Technical Analysis 2025',
            'jenis_kegiatan' => 'Workshop',
            'tanggal_rencana' => '2025-06-20',
            'lokasi' => 'Hotel Santika Bengkulu',
            'waktu_mulai' => '08:30',
            'waktu_selesai' => '16:00',
            'target_peserta' => 80,
            'anggaran' => 25000000,
            'deskripsi' => 'Pelatihan intensif indikator teknikal',
            'keterangan' => 'Didanai pos edukasi',
            'status' => 'draft',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post('/admin/rencana-kegiatan', $payload);

        $response->assertRedirect(route('admin.rencana-kegiatan.index'));
        $this->assertDatabaseHas('rencana_kegiatan', [
            'nama_kegiatan' => 'Workshop Technical Analysis 2025',
            'status' => 'draft',
        ]);
        $this->assertDatabaseHas('detail_kegiatan', [
            'lokasi' => 'Hotel Santika Bengkulu',
            'anggaran' => 25000000,
        ]);
    }

    public function test_admin_can_update_rencana_anggaran(): void
    {
        $rencana = RencanaKegiatan::create([
            'admin_id' => $this->admin->id,
            'nama_kegiatan' => 'Kegiatan Lama',
            'jenis_kegiatan' => 'Webinar',
            'tanggal_rencana' => '2025-07-10',
            'status' => 'draft',
        ]);

        DetailKegiatan::create([
            'rencana_kegiatan_id' => $rencana->id,
            'deskripsi' => 'Deskripsi lama',
            'tanggal' => '2025-07-10',
            'waktu_mulai' => '09:00',
            'lokasi' => 'Online',
            'anggaran' => 5000000,
        ]);

        $updatePayload = [
            'nama_kegiatan' => 'Kegiatan Baru Diperbarui',
            'jenis_kegiatan' => 'Webinar',
            'tanggal_rencana' => '2025-07-12',
            'lokasi' => 'Zoom Premium',
            'waktu_mulai' => '10:00',
            'waktu_selesai' => '12:00',
            'target_peserta' => 300,
            'anggaran' => 7500000,
            'deskripsi' => 'Deskripsi baru',
            'status' => 'draft',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->put("/admin/rencana-kegiatan/{$rencana->id}", $updatePayload);

        $response->assertRedirect(route('admin.rencana-kegiatan.index'));
        $this->assertDatabaseHas('rencana_kegiatan', [
            'id' => $rencana->id,
            'nama_kegiatan' => 'Kegiatan Baru Diperbarui',
        ]);
        $this->assertDatabaseHas('detail_kegiatan', [
            'rencana_kegiatan_id' => $rencana->id,
            'anggaran' => 7500000,
        ]);
    }

    public function test_admin_can_submit_draft_to_review(): void
    {
        $rencana = RencanaKegiatan::create([
            'admin_id' => $this->admin->id,
            'nama_kegiatan' => 'Draft SPM Siap Review',
            'jenis_kegiatan' => 'SPM',
            'tanggal_rencana' => '2025-08-01',
            'status' => 'draft',
        ]);

        DetailKegiatan::create([
            'rencana_kegiatan_id' => $rencana->id,
            'deskripsi' => 'SPM Draft',
            'tanggal' => '2025-08-01',
            'waktu_mulai' => '09:00',
            'lokasi' => 'UNIB',
            'anggaran' => 10000000,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post("/admin/rencana-kegiatan/{$rencana->id}/ajukan");

        $response->assertSessionHas('success');
        $this->assertEquals('diajukan', $rencana->fresh()->status);
        $this->assertDatabaseHas('pengajuan_kegiatan', [
            'rencana_kegiatan_id' => $rencana->id,
            'status' => 'diajukan',
        ]);
    }

    public function test_admin_can_delete_rencana_anggaran(): void
    {
        $rencana = RencanaKegiatan::create([
            'admin_id' => $this->admin->id,
            'nama_kegiatan' => 'Rencana Mau Dihapus',
            'jenis_kegiatan' => 'Sosialisasi',
            'tanggal_rencana' => '2025-09-01',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete("/admin/rencana-kegiatan/{$rencana->id}");

        $response->assertRedirect(route('admin.rencana-kegiatan.index'));
        $this->assertDatabaseMissing('rencana_kegiatan', ['id' => $rencana->id]);
    }

    public function test_admin_can_export_csv(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/rencana-kegiatan/export');

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
    }
}
