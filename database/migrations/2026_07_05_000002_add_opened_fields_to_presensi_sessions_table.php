<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensi_sessions', function (Blueprint $table) {
            $table->foreignId('opened_by')
                ->nullable()
                ->after('teacher_id')
                ->constrained('users')
                ->nullOnDelete()
                ->comment('User yang membuka sesi');
            $table->timestamp('opened_at')
                ->nullable()
                ->after('opened_by')
                ->comment('Waktu sesi dibuka');
        });
    }

    public function down(): void
    {
        Schema::table('presensi_sessions', function (Blueprint $table) {
            $table->dropForeign(['opened_by']);
            $table->dropColumn(['opened_by', 'opened_at']);
        });
    }
};
