<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prayer_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('prayer_type', ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya']);
            $table->date('attendance_date');
            $table->time('scheduled_at');
            $table->timestamp('submitted_at')->nullable();
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'late',
                'missed',
                'expired',
                'cancelled',
            ])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->string('teacher_note', 500)->nullable();
            $table->timestamps();

            $table->unique(
                ['student_id', 'attendance_date', 'prayer_type'],
                'prayer_attendance_student_date_type_unique'
            );
            $table->index(['school_id', 'attendance_date']);
            $table->index(['class_id', 'attendance_date']);
            $table->index(['status', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prayer_attendances');
    }
};
