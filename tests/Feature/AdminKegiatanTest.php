<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Kegiatan;
use App\Models\Peserta;
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
            ->where('stats.all', 5)
            ->where('stats.disetujui', 1)
            ->where('stats.terjadwal', 1)
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

    public function test_admin_can_download_attendance_pdf_in_both_orientations(): void
    {
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Sosialisasi Investasi',
            'deskripsi' => 'Edukasi masyarakat umum',
            'jenis_kegiatan' => 'Sosialisasi',
            'tanggal' => now()->addDays(3)->format('Y-m-d'),
            'waktu_mulai' => '10:00',
            'lokasi' => 'Aula BEI',
            'status' => 'dipublikasikan',
        ]);

        $this->actingAs($this->admin, 'admin');

        foreach ([
            'portrait' => '/MediaBox\s+\[0(?:\.0+)?\s+0(?:\.0+)?\s+595(?:\.\d+)?\s+841(?:\.\d+)?\]/',
            'landscape' => '/MediaBox\s+\[0(?:\.0+)?\s+0(?:\.0+)?\s+841(?:\.\d+)?\s+595(?:\.\d+)?\]/',
        ] as $orientation => $mediaBoxPattern) {
            $response = $this->get(route('admin.kegiatan.presensi.download', [
                'kegiatan' => $kegiatan,
                'orientation' => $orientation,
            ]));

            $response->assertOk();
            $response->assertHeader('Content-Type', 'application/pdf');
            $response->assertHeader('Content-Disposition', "attachment; filename=presensi-{$kegiatan->id}-{$orientation}.pdf");
            $this->assertSame('%PDF', substr($response->getContent(), 0, 4));
            $this->assertTrue(
                preg_match($mediaBoxPattern, $response->getContent()) === 1,
                "The {$orientation} PDF has incorrect page dimensions.",
            );

            if ($orientation === 'landscape') {
                $this->assertSame(
                    1,
                    preg_match_all('/\/Type\s*\/Page\b/', $response->getContent()),
                    'The landscape attendance PDF should fit on one page.',
                );
            }
        }
    }

    public function test_admin_cannot_download_attendance_pdf_for_non_scheduled_activity(): void
    {
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Proposal Menunggu',
            'deskripsi' => 'Belum dijadwalkan',
            'jenis_kegiatan' => 'Sosialisasi',
            'tanggal' => now()->addDays(3)->format('Y-m-d'),
            'waktu_mulai' => '10:00',
            'lokasi' => 'Aula BEI',
            'status' => 'menunggu_approval',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.kegiatan.presensi.download', [
                'kegiatan' => $kegiatan,
                'orientation' => 'portrait',
            ]));

        $response->assertNotFound();
    }

    public function test_guest_cannot_download_attendance_pdf(): void
    {
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Sosialisasi Investasi',
            'deskripsi' => 'Edukasi masyarakat umum',
            'jenis_kegiatan' => 'Sosialisasi',
            'tanggal' => now()->addDays(3)->format('Y-m-d'),
            'waktu_mulai' => '10:00',
            'lokasi' => 'Aula BEI',
            'status' => 'dipublikasikan',
        ]);

        $response = $this->get(route('admin.kegiatan.presensi.download', [
            'kegiatan' => $kegiatan,
            'orientation' => 'portrait',
        ]));

        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_download_attendance_pdf(): void
    {
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Sosialisasi Investasi',
            'deskripsi' => 'Edukasi masyarakat umum',
            'jenis_kegiatan' => 'Sosialisasi',
            'tanggal' => now()->addDays(3)->format('Y-m-d'),
            'waktu_mulai' => '10:00',
            'lokasi' => 'Aula BEI',
            'status' => 'dipublikasikan',
        ]);

        $response = $this->actingAs(User::factory()->create(), 'web')
            ->get(route('admin.kegiatan.presensi.download', [
                'kegiatan' => $kegiatan,
                'orientation' => 'portrait',
            ]));

        $response->assertRedirect('/login');
    }

    public function test_admin_cannot_download_attendance_pdf_with_invalid_orientation(): void
    {
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Sosialisasi Investasi',
            'deskripsi' => 'Edukasi masyarakat umum',
            'jenis_kegiatan' => 'Sosialisasi',
            'tanggal' => now()->addDays(3)->format('Y-m-d'),
            'waktu_mulai' => '10:00',
            'lokasi' => 'Aula BEI',
            'status' => 'dipublikasikan',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.kegiatan.presensi.download', [
                'kegiatan' => $kegiatan,
                'orientation' => 'diagonal',
            ]));

        $response->assertSessionHasErrors('orientation');
    }

    public function test_attendance_template_renders_twenty_five_blank_rows_and_privacy_consent(): void
    {
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Sosialisasi Investasi',
            'deskripsi' => 'Edukasi masyarakat umum',
            'jenis_kegiatan' => 'Sosialisasi',
            'tanggal' => now()->addDays(3)->format('Y-m-d'),
            'waktu_mulai' => '10:00',
            'lokasi' => 'Aula BEI',
            'status' => 'dipublikasikan',
        ]);
        $participant = User::factory()->create(['name' => 'Peserta Kegiatan Ini']);
        $participant->update(['no_telepon' => '081234567890']);
        Peserta::create(['kegiatan_id' => $kegiatan->id, 'user_id' => $participant->id]);

        $html = view('admin.kegiatan.presensi', [
            'kegiatan' => $kegiatan->load('peserta.user'),
            'orientation' => 'portrait',
        ])->render();
        $document = new \DOMDocument;
        $document->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new \DOMXPath($document);
        $letterhead = $xpath->query("//table[@class='letterhead']");
        $heading = $xpath->query("//table[@class='letterhead']//h1");
        $logo = $xpath->query("//table[@class='letterhead']//img[@alt='Logo IDX']");
        $fieldLabels = $xpath->query("//table[@class='event-details']//td[@class='label']");
        $fieldValues = $xpath->query("//table[@class='event-details']//td[@class='field-value']");
        $rows = $xpath->query("//table[contains(concat(' ', normalize-space(@class), ' '), ' roster ')]/tbody/tr");

        $this->assertCount(1, $letterhead);
        $this->assertSame('FORMULIR DAFTAR HADIR PESERTA', trim($heading->item(0)->textContent));
        $this->assertCount(1, $logo);
        $this->assertSame('portrait', $document->getElementsByTagName('body')->item(0)->getAttribute('class'));
        $this->assertStringContainsString('.portrait .number', $html);
        $this->assertStringContainsString('width: 5mm;', $html);
        $this->assertStringContainsString('.portrait .name', $html);
        $this->assertStringContainsString('width: 44mm;', $html);
        $this->assertStringContainsString('.portrait .phone', $html);
        $this->assertStringContainsString('width: 52mm;', $html);
        $this->assertSame(['KP BEI', 'KEGIATAN', 'TANGGAL'], array_map(
            fn ($label): string => trim($label->textContent),
            iterator_to_array($fieldLabels),
        ));
        $this->assertSame([
            'Bengkulu',
            $kegiatan->nama_kegiatan,
            $kegiatan->tanggal->format('d/m/Y'),
        ], array_map(
            fn ($value): string => trim($value->textContent),
            iterator_to_array($fieldValues),
        ));
        $this->assertCount(25, $rows);
        $this->assertFalse(str_contains($html, 'Peserta Kegiatan Ini'));
        $this->assertFalse(str_contains($html, $participant->email));
        $this->assertFalse(str_contains($html, '081234567890'));
        $this->assertStringContainsString('PERLINDUNGAN DATA PRIBADI', $html);
        $this->assertStringContainsString('contactcenter@idx.co.id', $html);

        foreach ($rows as $index => $row) {
            $cells = $xpath->query('./td', $row);

            $this->assertCount(5, $cells);
            $this->assertSame((string) ($index + 1), trim($cells->item(0)->textContent));

            foreach ([1, 2, 3, 4] as $columnIndex) {
                $this->assertSame('', trim($cells->item($columnIndex)->textContent));
            }
        }
    }

    public function test_landscape_attendance_template_renders_fifteen_blank_rows(): void
    {
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Sosialisasi Investasi',
            'deskripsi' => 'Edukasi masyarakat umum',
            'jenis_kegiatan' => 'Sosialisasi',
            'tanggal' => now()->addDays(3)->format('Y-m-d'),
            'waktu_mulai' => '10:00',
            'lokasi' => 'Aula BEI',
            'status' => 'dipublikasikan',
        ]);

        $html = view('admin.kegiatan.presensi', [
            'kegiatan' => $kegiatan,
            'orientation' => 'landscape',
        ])->render();
        $document = new \DOMDocument;
        $document->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new \DOMXPath($document);
        $rows = $xpath->query("//table[contains(concat(' ', normalize-space(@class), ' '), ' roster ')]/tbody/tr");

        $this->assertSame('landscape', $document->getElementsByTagName('body')->item(0)->getAttribute('class'));
        $this->assertCount(15, $rows);

        foreach ($rows as $index => $row) {
            $cells = $xpath->query('./td', $row);

            $this->assertSame((string) ($index + 1), trim($cells->item(0)->textContent));
            foreach ([1, 2, 3, 4] as $columnIndex) {
                $this->assertSame('', trim($cells->item($columnIndex)->textContent));
            }
        }
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

    public function test_admin_can_update_kegiatan_status_directly(): void
    {
        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Kegiatan Lifecycle Test',
            'deskripsi' => 'Tes tombol status',
            'jenis_kegiatan' => 'SPM',
            'tanggal' => now()->addDays(3)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Aula BEI',
            'status' => 'disetujui',
        ]);

        // disetujui -> dipublikasikan
        $this->actingAs($this->admin, 'admin')
            ->patch("/admin/kegiatan/{$kegiatan->id}/status", ['status' => 'dipublikasikan'])
            ->assertSessionHas('success');
        $this->assertSame('dipublikasikan', $kegiatan->fresh()->status);

        // dipublikasikan -> berlangsung
        $this->actingAs($this->admin, 'admin')
            ->patch("/admin/kegiatan/{$kegiatan->id}/status", ['status' => 'berlangsung'])
            ->assertSessionHas('success');
        $this->assertSame('berlangsung', $kegiatan->fresh()->status);

        // berlangsung -> selesai
        $this->actingAs($this->admin, 'admin')
            ->patch("/admin/kegiatan/{$kegiatan->id}/status", ['status' => 'selesai'])
            ->assertSessionHas('success');
        $this->assertSame('selesai', $kegiatan->fresh()->status);
    }

    public function test_admin_cannot_edit_or_delete_approved_kegiatan(): void
    {
        foreach (['disetujui', 'dipublikasikan', 'berlangsung', 'selesai'] as $status) {
            $kegiatan = Kegiatan::create([
                'nama_kegiatan' => "Kegiatan Status {$status}",
                'deskripsi' => 'Deskripsi',
                'jenis_kegiatan' => 'SPM',
                'tanggal' => now()->addDays(4)->format('Y-m-d'),
                'waktu_mulai' => '09:00',
                'lokasi' => 'Aula BEI',
                'status' => $status,
            ]);

            $this->actingAs($this->admin, 'admin')
                ->get("/admin/kegiatan/{$kegiatan->id}/edit")
                ->assertForbidden();

            $this->actingAs($this->admin, 'admin')
                ->put("/admin/kegiatan/{$kegiatan->id}", [
                    'nama_kegiatan' => 'Coba Edit',
                    'deskripsi' => 'Deskripsi coba',
                    'jenis_kegiatan' => 'SPM',
                    'tanggal' => now()->addDays(4)->format('Y-m-d'),
                    'waktu_mulai' => '09:00',
                    'lokasi' => 'Aula BEI',
                    'status' => $status,
                ])
                ->assertForbidden();

            $this->actingAs($this->admin, 'admin')
                ->delete("/admin/kegiatan/{$kegiatan->id}")
                ->assertForbidden();

            $this->assertDatabaseHas('kegiatan', [
                'id' => $kegiatan->id,
                'status' => $status,
            ]);
        }
    }

    public function test_admin_kegiatan_table_supports_sorting(): void
    {
        Kegiatan::create([
            'nama_kegiatan' => 'Alpha Kegiatan',
            'deskripsi' => 'Deskripsi',
            'jenis_kegiatan' => 'SPM',
            'tanggal' => now()->addDays(1)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Bengkulu',
            'status' => 'dipublikasikan',
        ]);

        Kegiatan::create([
            'nama_kegiatan' => 'Omega Kegiatan',
            'deskripsi' => 'Deskripsi',
            'jenis_kegiatan' => 'SPM',
            'tanggal' => now()->addDays(2)->format('Y-m-d'),
            'waktu_mulai' => '09:00',
            'lokasi' => 'Bengkulu',
            'status' => 'dipublikasikan',
        ]);

        $responseAsc = $this->actingAs($this->admin, 'admin')->get('/admin/kegiatan?sort=nama_kegiatan&direction=asc');
        $responseAsc->assertStatus(200);
        $this->assertSame('Alpha Kegiatan', $responseAsc->inertiaPage()['props']['kegiatan']['data'][0]['nama_kegiatan']);

        $responseDesc = $this->actingAs($this->admin, 'admin')->get('/admin/kegiatan?sort=nama_kegiatan&direction=desc');
        $responseDesc->assertStatus(200);
        $this->assertSame('Omega Kegiatan', $responseDesc->inertiaPage()['props']['kegiatan']['data'][0]['nama_kegiatan']);
    }
}
