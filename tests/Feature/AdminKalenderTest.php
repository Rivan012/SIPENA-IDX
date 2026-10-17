<?php

namespace Tests\Feature;

use App\Mail\KegiatanReminderMail;
use App\Models\Admin;
use App\Models\Kegiatan;
use App\Models\Peserta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
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
        $response->assertRedirect('/login');
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

    public function test_calendar_weeks_include_sunday_as_the_seventh_day(): void
    {
        Kegiatan::create([
            'nama_kegiatan' => 'Agenda hari Minggu',
            'deskripsi' => 'Kegiatan hari Minggu',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => '2026-02-01',
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'status' => 'dipublikasikan',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get('/admin/kalender?year=2026&month=2');

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Kalender/Index')
            ->where('eventsClient.0.tanggal', '2026-02-01')
            ->where('calendarWeeks.0.0.date_string', '2026-02-01')
            ->where('calendarWeeks.0.0.day', 1)
            ->where('calendarWeeks.0.0.events.0.nama_kegiatan', 'Agenda hari Minggu')
            ->where('calendarWeeks.0.6.date_string', '2026-02-07')
            ->has('calendarWeeks.0', 7)
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
            ->where('filters.view', 'bulan')
        );
    }

    public function test_calendar_event_payload_includes_an_existing_description_image(): void
    {
        Storage::fake('local');
        $imagePath = "description-images/admins/{$this->admin->id}/123e4567-e89b-12d3-a456-426614174000.png";
        $imageUrl = '/'.$imagePath;
        Storage::disk('local')->put($imagePath, 'image-bytes');

        Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan dengan Banner',
            'deskripsi' => "<p>Materi kegiatan</p><p><img src=\"{$imageUrl}\" alt=\"Banner\"></p>",
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'status' => 'dipublikasikan',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get('/admin/kalender');

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Kalender/Index')
            ->where('eventsClient.0.description_banner', $imageUrl)
        );
    }

    public function test_calendar_event_payload_omits_a_description_image_missing_from_storage(): void
    {
        Storage::fake('local');
        $imageUrl = "/description-images/admins/{$this->admin->id}/123e4567-e89b-12d3-a456-426614174002.png";

        Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan dengan Gambar Hilang',
            'deskripsi' => "<p><img src=\"{$imageUrl}\" alt=\"Banner\"></p>",
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'status' => 'dipublikasikan',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->get('/admin/kalender');

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Kalender/Index')
            ->where('eventsClient.0.description_banner', null)
        );
    }

    public function test_admin_can_filter_kalender_by_view_mode(): void
    {
        $responseMinggu = $this->actingAs($this->admin, 'admin')->get('/admin/kalender?view=minggu');
        $responseMinggu->assertStatus(200);
        $responseMinggu->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Kalender/Index')
            ->where('filters.view', 'minggu')
        );

        $responseList = $this->actingAs($this->admin, 'admin')->get('/admin/kalender?view=list');
        $responseList->assertStatus(200);
        $responseList->assertInertia(fn (Assert $page) => $page
            ->component('Admin/Kalender/Index')
            ->where('filters.view', 'list')
        );
    }

    public function test_guest_cannot_send_reminder_email(): void
    {
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Workshop Saham',
            'deskripsi' => 'Deskripsi workshop',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'status' => 'dipublikasikan',
        ]);

        $response = $this->post("/admin/kalender/{$kegiatan->id}/kirim-pengingat", [
            'custom_emails' => 'peserta@example.com',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_admin_can_send_reminder_email_to_activity_proposer(): void
    {
        Mail::fake();

        $userMitra = User::create([
            'name' => 'Budi Santoso (Mitra UNIB)',
            'email' => 'budi@kampus.ac.id',
            'password' => Hash::make('password123'),
        ]);

        $kegiatan = Kegiatan::create([
            'user_id' => $userMitra->id,
            'nama_kegiatan' => 'Sekolah Pasar Modal Level 1',
            'deskripsi' => 'Edukasi SPM',
            'jenis_kegiatan' => 'SPM',
            'tanggal' => now()->addDays(2)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '12:00',
            'lokasi' => 'GI BEI UNIB',
            'status' => 'dipublikasikan',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->post("/admin/kalender/{$kegiatan->id}/kirim-pengingat", [
            'include_pengusul' => true,
            'catatan' => 'Mohon koordinasikan kehadiran peserta 30 menit sebelum acara.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Mail::assertSent(KegiatanReminderMail::class, 1);
        Mail::assertSent(KegiatanReminderMail::class, fn ($mail) => $mail->hasTo('budi@kampus.ac.id') && $mail->customNote === 'Mohon koordinasikan kehadiran peserta 30 menit sebelum acara.');
    }

    public function test_admin_can_send_reminder_email_to_custom_emails(): void
    {
        Mail::fake();

        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Seminar Pasar Modal Syariah',
            'deskripsi' => 'Edukasi Syariah',
            'jenis_kegiatan' => 'Seminar',
            'tanggal' => now()->addDays(3)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'status' => 'dipublikasikan',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->post("/admin/kalender/{$kegiatan->id}/kirim-pengingat", [
            'include_pengusul' => false,
            'custom_emails' => 'mitra1@kampus.ac.id, mitra2@sekuritas.co.id',
            'catatan' => 'Pengingat untuk tim panitia pelaksana.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Mail::assertSent(KegiatanReminderMail::class, 2);
        Mail::assertSent(KegiatanReminderMail::class, fn ($mail) => $mail->hasTo('mitra1@kampus.ac.id'));
        Mail::assertSent(KegiatanReminderMail::class, fn ($mail) => $mail->hasTo('mitra2@sekuritas.co.id'));
    }

    public function test_admin_cannot_send_reminder_email_without_valid_recipients(): void
    {
        Mail::fake();

        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan Tanpa Peserta',
            'deskripsi' => 'Kosong',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => now()->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'status' => 'dipublikasikan',
        ]);

        $response = $this->actingAs($this->admin, 'admin')->post("/admin/kalender/{$kegiatan->id}/kirim-pengingat", [
            'include_pengusul' => true,
            'custom_emails' => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        Mail::assertNothingSent();
    }
}
