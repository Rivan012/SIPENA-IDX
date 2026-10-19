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
        Schema::create('detail_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rencana_kegiatan_id')->constrained('rencana_kegiatan')->cascadeOnDelete();
            $table->text('deskripsi');
            $table->string('jenis_kegiatan')->nullable();
            $table->date('tanggal');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai')->nullable();
            $table->string('lokasi');
            $table->unsignedInteger('target_peserta')->nullable();
            $table->decimal('anggaran', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_kegiatan');
    }
};
