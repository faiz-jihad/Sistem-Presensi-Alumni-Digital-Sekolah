<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensi_sessions', function (Blueprint $table) {
            $table->foreignId('class_id')
                ->nullable()
                ->after('school_id')
                ->constrained('classes')
                ->nullOnDelete();

            $table->foreignId('schedule_id')
                ->nullable()
                ->change();

            $table->enum('attendance_method', ['manual', 'qr'])
                ->default('manual')
                ->after('status');
        });

        Schema::table('student_attendances', function (Blueprint $table) {
            $table->dropUnique('unique_attendance_per_student_per_day');
            $table->timestamp('scanned_at')->nullable()->after('check_in_time');

            $table->unique(
                ['presensi_session_id', 'student_id'],
                'unique_attendance_per_session_student'
            );
        });
    }

    public function down(): void
    {
        Schema::table('student_attendances', function (Blueprint $table) {
            $table->dropUnique('unique_attendance_per_session_student');
            $table->dropColumn('scanned_at');
            $table->unique(
                ['student_id', 'date'],
                'unique_attendance_per_student_per_day'
            );
        });

        Schema::table('presensi_sessions', function (Blueprint $table) {
            $table->foreignId('schedule_id')
                ->nullable(false)
                ->change();
            $table->dropConstrainedForeignId('class_id');
            $table->dropColumn('attendance_method');
        });
    }
};
