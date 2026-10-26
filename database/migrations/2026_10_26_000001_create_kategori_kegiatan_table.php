<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kategori_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->string('keterangan', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed kategori default & kategori yang sudah ada di tabel kegiatan
        $defaults = ['SPM', 'Sosialisasi', 'Workshop', 'Edukasi', 'Webinar'];
        $existing = [];

        if (Schema::hasTable('kegiatan')) {
            $existing = DB::table('kegiatan')
                ->whereNotNull('jenis_kegiatan')
                ->distinct()
                ->pluck('jenis_kegiatan')
                ->all();
        }

        $all = array_values(array_unique(array_filter(array_merge($defaults, $existing))));

        foreach ($all as $item) {
            DB::table('kategori_kegiatan')->insertOrIgnore([
                'nama' => $item,
                'keterangan' => match ($item) {
                    'SPM' => 'Sekolah Pasar Modal',
                    'Edukasi' => 'Edukasi & Literasi Publik',
                    'Sosialisasi' => 'Sosialisasi Pasar Modal',
                    'Workshop' => 'Pelatihan & Workshop Praktis',
                    'Webinar' => 'Seminar / Webinar Online',
                    default => 'Kategori Kegiatan',
                },
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_kegiatan');
    }
};
