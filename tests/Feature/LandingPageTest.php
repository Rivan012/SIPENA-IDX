<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Kegiatan;
use App\Models\Peserta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_receives_upcoming_published_activities_from_database(): void
    {
        $publishedActivity = Kegiatan::create([
            'nama_kegiatan' => 'Sekolah Pasar Modal Dinamis',
            'deskripsi' => 'Edukasi <strong>pasar modal</strong>',
            'jenis_kegiatan' => 'SPM',
            'tanggal' => now()->addDays(5)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '12:00',
            'lokasi' => 'Aula BEI Bengkulu',
            'mitra' => 'Mitra Penguji',
            'kuota_peserta' => 2,
            'status' => 'dipublikasikan',
        ]);
        $participant = User::factory()->create();
        Peserta::create([
            'kegiatan_id' => $publishedActivity->id,
            'user_id' => $participant->id,
        ]);
        $secondParticipant = User::factory()->create();
        Peserta::create([
            'kegiatan_id' => $publishedActivity->id,
            'user_id' => $secondParticipant->id,
        ]);

        Kegiatan::create([
            'nama_kegiatan' => 'Jadwal Lampau',
            'deskripsi' => 'Tidak tampil di landing',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->subDay()->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'status' => 'dipublikasikan',
        ]);
        Kegiatan::create([
            'nama_kegiatan' => 'Proposal Belum Terbit',
            'deskripsi' => 'Tidak tampil di landing',
            'jenis_kegiatan' => 'Sosialisasi',
            'tanggal' => now()->addDays(6)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'status' => 'menunggu_approval',
        ]);
        Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan Selesai',
            'deskripsi' => 'Kegiatan telah dilaksanakan',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->subDay()->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'status' => 'selesai',
        ]);

        $response = $this->get(route('home'));

        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Welcome')
            ->has('agendaItems', 1)
            ->where('agendaItems.0.id', $publishedActivity->id)
            ->where('agendaItems.0.kategori', 'Sekolah Pasar Modal')
            ->where('agendaItems.0.judul', 'Sekolah Pasar Modal Dinamis')
            ->where('agendaItems.0.mitra', 'Mitra Penguji')
            ->where('agendaItems.0.kuotaTotal', 2)
            ->where('agendaItems.0.kuotaTerisi', 2)
            ->where('agendaItems.0.status', 'Kuota Penuh')
            ->where('categories.0', 'Semua')
            ->where('categories.1', 'Sekolah Pasar Modal')
        );

        $admin = Admin::create([
            'name' => 'Admin Dashboard Test',
            'email' => 'landing-dashboard@example.com',
            'password' => Hash::make('password123'),
        ]);

        $dashboardResponse = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

        $dashboardResponse->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Admin/Dashboard')
            ->where('metrics.total_kegiatan', 1)
            ->has('kegiatanTerbaru', 1)
            ->where('kegiatanTerbaru.0.id', $publishedActivity->id)
            ->where('metrics.kegiatan_terlaksana', 1)
        );
    }
}
