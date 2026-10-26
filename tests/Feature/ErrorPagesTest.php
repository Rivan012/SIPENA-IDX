<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_visiting_non_existent_page_renders_custom_404_blade_view(): void
    {
        $response = $this->get('/halaman-acak-yang-tidak-pernah-ada-12345');

        $response->assertNotFound();
        $response->assertSee('404');
        $response->assertSee('Halaman Tidak Ditemukan');
        $response->assertSee('SIPENA');
    }

    public function test_inertia_request_to_non_existent_page_renders_inertia_error_component(): void
    {
        $response = $this->withHeaders(['X-Inertia' => 'true'])
            ->get('/halaman-acak-yang-tidak-pernah-ada-12345');

        $response->assertNotFound();
        $response->assertHeader('X-Inertia', 'true');
        $response->assertJson([
            'component' => 'Error',
            'props' => [
                'status' => 404,
            ],
        ]);
    }

    public function test_blade_error_views_exist_and_render_correctly(): void
    {
        $errors = [
            '404' => 'Halaman Tidak Ditemukan',
            '403' => 'Akses Dibatasi',
            '500' => 'Gangguan Server',
            '419' => 'Sesi Halaman Kedaluwarsa',
            '503' => 'Pemeliharaan Sistem',
            '429' => 'Terlalu Banyak Permintaan',
        ];

        foreach ($errors as $code => $title) {
            $view = view("errors.{$code}")->render();
            $this->assertStringContainsString((string) $code, $view);
            $this->assertStringContainsString($title, $view);
            $this->assertStringContainsString('SIPENA', $view);
        }
    }
}
