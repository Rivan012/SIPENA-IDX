<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSettingPresensiTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::create([
            'name' => 'Admin Setting Presensi',
            'email' => 'admin-presensi@example.com',
            'password' => Hash::make('password123'),
            'jabatan' => 'Admin IT',
        ]);
    }

    public function test_admin_can_view_presensi_setting_page(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.setting.presensi'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Setting/Presensi')
            ->has('settings.columns')
            ->has('stats')
        );
    }

    public function test_admin_can_update_presensi_template(): void
    {
        Storage::fake('local');

        $payload = [
            'columns' => [
                ['id' => 'name', 'label' => 'NAMA LENGKAP', 'enabled' => true],
                ['id' => 'instansi', 'label' => 'UNIVERSITAS / INSTANSI', 'enabled' => true],
                ['id' => 'signature', 'label' => 'TANDA TANGAN', 'enabled' => true],
            ],
            'orientation' => 'landscape',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.setting.presensi.update'), $payload);

        $response->assertRedirect();
        Storage::disk('local')->assertExists('settings/presensi_template.json');

        $saved = json_decode(Storage::disk('local')->get('settings/presensi_template.json'), true);
        $this->assertSame('landscape', $saved['orientation']);
        $this->assertCount(3, $saved['columns']);
        $this->assertSame('UNIVERSITAS / INSTANSI', $saved['columns'][1]['label']);
    }

    public function test_admin_can_reset_presensi_template(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('settings/presensi_template.json', json_encode(['test' => true]));

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.setting.presensi.reset'));

        $response->assertRedirect();
        Storage::disk('local')->assertMissing('settings/presensi_template.json');
    }

    public function test_guest_cannot_access_presensi_setting(): void
    {
        $response = $this->get(route('admin.setting.presensi'));
        $response->assertRedirect('/login');
    }
}
