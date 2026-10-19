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
            $table->foreignId('reviewed_by_admin_id')->nullable()->after('proposal_file_path')->constrained('admins')->nullOnDelete();
            $table->text('approval_note')->nullable()->after('reviewed_by_admin_id');
            $table->timestamp('approval_processed_at')->nullable()->after('approval_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by_admin_id');
            $table->dropColumn(['approval_note', 'approval_processed_at']);
        });
    }
};
