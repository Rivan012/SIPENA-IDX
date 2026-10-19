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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_kegiatan');
    }
};
