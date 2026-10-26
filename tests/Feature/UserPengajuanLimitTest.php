<?php

namespace Tests\Feature;

use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPengajuanLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_submit_proposal_when_daily_limit_reached(): void
    {
        $user = User::factory()->create();
        $targetDate = now()->addDays(3)->format('Y-m-d');

        // Create 4 existing activities on the target date
        for ($i = 1; $i <= 4; $i++) {
            Kegiatan::create([
                'nama_kegiatan' => "Kegiatan {$i}",
                'deskripsi' => 'Deskripsi',
                'jenis_kegiatan' => 'SPM',
                'tanggal' => $targetDate,
                'waktu_mulai' => '09:00',
                'lokasi' => 'BEI',
                'kuota_peserta' => 50,
                'status' => 'menunggu_approval',
            ]);
        }

        $payload = [
            'nama_kegiatan' => 'Proposal Melebihi Kuota',
            'deskripsi' => 'Pengajuan ke-5',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => $targetDate,
            'waktu_mulai' => '10:00',
            'lokasi' => 'Aula',
            'kuota_peserta' => 30,
        ];

        $response = $this->actingAs($user, 'web')->post('/kegiatan/ajukan', $payload);

        $response->assertSessionHasErrors(['tanggal']);
        $response->assertSessionHas('error', 'Pengajuan pada tanggal tersebut sudah penuh (maksimal 4 kegiatan). Silakan pilih tanggal lain.');
        $this->assertDatabaseMissing('kegiatan', ['nama_kegiatan' => 'Proposal Melebihi Kuota']);
    }

    public function test_user_can_submit_proposal_when_under_limit(): void
    {
        $user = User::factory()->create();
        $targetDate = now()->addDays(4)->format('Y-m-d');

        for ($i = 1; $i <= 3; $i++) {
            Kegiatan::create([
                'nama_kegiatan' => "Kegiatan {$i}",
                'deskripsi' => 'Deskripsi',
                'jenis_kegiatan' => 'SPM',
                'tanggal' => $targetDate,
                'waktu_mulai' => '09:00',
                'lokasi' => 'BEI',
                'kuota_peserta' => 50,
                'status' => 'menunggu_approval',
            ]);
        }

        $payload = [
            'nama_kegiatan' => 'Proposal Ke-4',
            'deskripsi' => 'Pengajuan slot terakhir',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => $targetDate,
            'waktu_mulai' => '10:00',
            'lokasi' => 'Aula',
            'kuota_peserta' => 30,
        ];

        $response = $this->actingAs($user, 'web')->post('/kegiatan/ajukan', $payload);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('kegiatan', ['nama_kegiatan' => 'Proposal Ke-4']);
    }

    public function test_cancelled_or_rejected_activities_do_not_count_towards_limit(): void
    {
        $user = User::factory()->create();
        $targetDate = now()->addDays(5)->format('Y-m-d');

        // 3 active + 2 cancelled/rejected
        for ($i = 1; $i <= 3; $i++) {
            Kegiatan::create([
                'nama_kegiatan' => "Kegiatan Aktif {$i}",
                'deskripsi' => 'Deskripsi',
                'jenis_kegiatan' => 'SPM',
                'tanggal' => $targetDate,
                'waktu_mulai' => '09:00',
                'lokasi' => 'BEI',
                'kuota_peserta' => 50,
                'status' => 'menunggu_approval',
            ]);
        }

        Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan Dibatalkan',
            'deskripsi' => 'Batal',
            'jenis_kegiatan' => 'SPM',
            'tanggal' => $targetDate,
            'waktu_mulai' => '09:00',
            'lokasi' => 'BEI',
            'kuota_peserta' => 50,
            'status' => 'dibatalkan',
        ]);

        Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan Ditolak',
            'deskripsi' => 'Tolak',
            'jenis_kegiatan' => 'SPM',
            'tanggal' => $targetDate,
            'waktu_mulai' => '09:00',
            'lokasi' => 'BEI',
            'kuota_peserta' => 50,
            'status' => 'ditolak',
        ]);

        $payload = [
            'nama_kegiatan' => 'Proposal Lolos Karena Ada Batal',
            'deskripsi' => 'Harus berhasil karena yang aktif baru 3',
            'jenis_kegiatan' => 'Workshop',
            'tanggal' => $targetDate,
            'waktu_mulai' => '10:00',
            'lokasi' => 'Aula',
            'kuota_peserta' => 30,
        ];

        $response = $this->actingAs($user, 'web')->post('/kegiatan/ajukan', $payload);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('kegiatan', ['nama_kegiatan' => 'Proposal Lolos Karena Ada Batal']);
    }
}
