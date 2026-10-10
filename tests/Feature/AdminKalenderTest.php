<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Kegiatan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminKalenderTest extends TestCase
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

    public function test_guest_cannot_access_kalender(): void
    {
        $response = $this->get('/admin/kalender');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_view_kalender_page(): void
    {
        Kegiatan::create([
            'nama_kegiatan' => 'Sekolah Pasar Modal (SPM) Level 1 & 2 - GI BEI UNIB',
            'deskripsi' => 'Edukasi SPM',
            'jenis_kegiatan' => 'SPM',
            'tanggal' => now()->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '15:30',
            'lokasi' => 'Gedung B Ruang Seminar GI UNIB Bengkulu',
            'kuota_peserta' => 245,
            'status' => 'dipublikasikan',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get('/admin/kalender');
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Kalender/Index')
            ->has('calendarWeeks')
            ->has('eventsClient')
            ->has('eventsByDate')
        );
    }

    public function test_admin_can_navigate_month_and_filter_status(): void
    {
        Kegiatan::create([
            'nama_kegiatan' => 'Workshop Syariah Maret',
            'deskripsi' => 'Kegiatan bulan depan',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => '2026-03-15',
            'waktu_mulai' => '10:00',
            'lokasi' => 'Aula FEB',
            'status' => 'menunggu_approval',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get('/admin/kalender?year=2026&month=3&status=approval');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Kalender/Index')
            ->where('currentMonthName', 'Maret 2026')
        );
    }

    public function test_kalender_initializes_client_side_reactive_events(): void
    {
        Kegiatan::create([
            'nama_kegiatan' => 'Simulasi Saham Digital',
            'deskripsi' => 'Simulasi trading online',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Lab Komputer',
            'status' => 'dipublikasikan',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get('/admin/kalender');
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Kalender/Index')
            ->has('eventsClient.0')
            ->where('eventsClient.0.nama_kegiatan', 'Simulasi Saham Digital')
        );
    }
}
