<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
        $response->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
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

    public function test_user_can_view_kegiatan_and_dashboard(): void
    {
        $kegiatan = Kegiatan::create([
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
        $response->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('User/Kegiatan/Index')
            ->has('kegiatanList.data', 1)
            ->where('kegiatanList.data.0.nama_kegiatan', 'SPM Batch Terbuka')
        );

        $responseDetail = $this->actingAs($this->user, 'web')->get("/kegiatan/{$kegiatan->id}");
        $responseDetail->assertStatus(200);
        $responseDetail->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('User/Kegiatan/Show')
            ->where('kegiatan.nama_kegiatan', 'SPM Batch Terbuka')
        );
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

        // Buka Kegiatan Saya
        $responseSaya = $this->actingAs($this->user, 'web')->get('/kegiatan-saya');
        $responseSaya->assertStatus(200);
        $responseSaya->assertSee('Edukasi Saham Pemula');

        // Batalkan
        $responseBatal = $this->actingAs($this->user, 'web')
            ->delete("/kegiatan/{$kegiatan->id}/batal");
        $responseBatal->assertSessionHas('success');
        $this->assertFalse($kegiatan->fresh()->peserta()->where('user_id', $this->user->id)->exists());
    }
}
