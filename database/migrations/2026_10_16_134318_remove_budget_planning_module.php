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
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pengajuan_kegiatan_id');
            $table->dropConstrainedForeignId('rencana_kegiatan_id');
        });

        Schema::dropIfExists('pengajuan_kegiatan');
        Schema::dropIfExists('detail_kegiatan');
        Schema::dropIfExists('rencana_kegiatan');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('rencana_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('nama_kegiatan');
            $table->string('jenis_kegiatan');
            $table->date('tanggal_rencana');
            $table->string('status')->default('draft');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

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

        Schema::create('pengajuan_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rencana_kegiatan_id')->constrained('rencana_kegiatan')->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('nomor_pengajuan')->nullable()->unique();
            $table->date('tanggal_pengajuan');
            $table->string('status')->default('diajukan');
            $table->text('catatan')->nullable();
            $table->text('catatan_revisi')->nullable();
            $table->timestamp('diproses_at')->nullable();
            $table->timestamps();
        });

        Schema::table('kegiatan', function (Blueprint $table) {
            $table->foreignId('rencana_kegiatan_id')->nullable()->constrained('rencana_kegiatan');
            $table->foreignId('pengajuan_kegiatan_id')->nullable()->constrained('pengajuan_kegiatan');
        });
    }
};
