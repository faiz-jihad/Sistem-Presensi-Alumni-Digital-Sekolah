<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensi_sessions', function (Blueprint $table) {
            if (!Schema::hasColumn('presensi_sessions', 'class_id')) {
                $table->foreignId('class_id')
                    ->nullable()
                    ->after('school_id')
                    ->constrained('classes')
                    ->nullOnDelete();
            }

            $table->foreignId('schedule_id')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('presensi_sessions', function (Blueprint $table) {
            $table->foreignId('schedule_id')
                ->nullable(false)
                ->change();

            if (Schema::hasColumn('presensi_sessions', 'class_id')) {
                $table->dropConstrainedForeignId('class_id');
            }
        });
    }
};
