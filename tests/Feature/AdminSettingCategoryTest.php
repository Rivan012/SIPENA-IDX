<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\KategoriKegiatan;
use App\Models\Kegiatan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSettingCategoryTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'Admin Setting',
            'email' => 'admin-setting@example.com',
            'password' => Hash::make('password123'),
            'jabatan' => 'Admin Bengkulu',
        ]);
    }

    public function test_admin_can_view_setting_page(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.setting.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Setting/Index')
            ->has('kategori')
            ->has('stats')
        );
    }

    public function test_admin_can_store_new_category(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.setting.kategori.store'), [
                'nama' => 'Inkubasi Bisnis',
                'keterangan' => 'Program inkubasi startup dan UMKM binaan IDX',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('kategori_kegiatan', [
            'nama' => 'Inkubasi Bisnis',
            'is_active' => true,
        ]);
    }

    public function test_admin_cannot_store_duplicate_category(): void
    {
        KategoriKegiatan::create([
            'nama' => 'SPM Lanjutan',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.setting.kategori.store'), [
                'nama' => 'SPM Lanjutan',
            ]);

        $response->assertSessionHasErrors('nama');
    }

    public function test_admin_can_toggle_category_status(): void
    {
        $kategori = KategoriKegiatan::create([
            'nama' => 'Webinar Khusus',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.setting.kategori.toggle', $kategori->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('kategori_kegiatan', [
            'id' => $kategori->id,
            'is_active' => false,
        ]);

        // Toggle back
        $this->actingAs($this->admin, 'admin')
            ->patch(route('admin.setting.kategori.toggle', $kategori->id));

        $this->assertDatabaseHas('kategori_kegiatan', [
            'id' => $kategori->id,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_delete_unused_category(): void
    {
        $kategori = KategoriKegiatan::create([
            'nama' => 'Kategori Sementara',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.setting.kategori.destroy', $kategori->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('kategori_kegiatan', [
            'id' => $kategori->id,
        ]);
    }

    public function test_admin_cannot_delete_in_use_category(): void
    {
        $kategori = KategoriKegiatan::firstOrCreate([
            'nama' => 'Kategori Terpakai',
        ], [
            'is_active' => true,
        ]);

        Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan Khusus',
            'deskripsi' => 'Deskripsi SPM',
            'jenis_kegiatan' => 'Kategori Terpakai',
            'tanggal' => now()->toDateString(),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'status' => 'dipublikasikan',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.setting.kategori.destroy', $kategori->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('kategori_kegiatan', [
            'id' => $kategori->id,
        ]);
    }
}
