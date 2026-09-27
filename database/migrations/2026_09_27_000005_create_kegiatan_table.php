<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_kegiatan_id')->nullable()->constrained('pengajuan_kegiatan')->nullOnDelete();
            $table->foreignId('rencana_kegiatan_id')->nullable()->constrained('rencana_kegiatan')->nullOnDelete();
            $table->string('nama_kegiatan');
            $table->text('deskripsi');
            $table->string('jenis_kegiatan');
            $table->date('tanggal');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai')->nullable();
            $table->string('lokasi');
            $table->unsignedInteger('kuota_peserta')->nullable();
            $table->string('status')->default('dipublikasikan');
            $table->string('google_calendar_event_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan');
    }
};
