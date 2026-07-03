<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')
                  ->constrained('schools')
                  ->cascadeOnDelete();
            $table->foreignId('class_id')
                  ->constrained('classes')
                  ->cascadeOnDelete();
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->cascadeOnDelete();
            $table->foreignId('teacher_id')
                  ->nullable()
                  ->constrained('teachers')
                  ->nullOnDelete();
            $table->date('date');
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->enum('status', [
                'present',      // Hadir
                'late',         // Terlambat
                'permission',   // Izin
                'sick',         // Sakit
                'absent'        // Alpha
            ]);
            $table->text('note')->nullable();
            $table->string('attachment')->nullable()->comment('File bukti');
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])
                  ->nullable();
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'date']);
            $table->index(['student_id', 'date']);
            $table->index(['class_id', 'date']);
            $table->index('status');
            $table->index('verification_status');
            $table->unique(
                ['student_id', 'date'],
                'unique_attendance_per_student_per_day'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};