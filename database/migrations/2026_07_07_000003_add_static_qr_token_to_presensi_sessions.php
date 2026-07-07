<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensi_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('presensi_sessions', 'qr_token')) {
                $table->string('qr_token', 64)
                    ->nullable()
                    ->after('attendance_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('presensi_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('presensi_sessions', 'qr_token')) {
                $table->dropColumn('qr_token');
            }
        });
    }
};
