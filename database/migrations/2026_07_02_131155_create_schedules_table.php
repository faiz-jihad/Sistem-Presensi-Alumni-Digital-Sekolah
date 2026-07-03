<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')
                  ->constrained('schools')
                  ->cascadeOnDelete();
            $table->foreignId('class_id')
                  ->constrained('classes')
                  ->cascadeOnDelete();
            $table->foreignId('subject_id')
                  ->constrained('subjects')
                  ->cascadeOnDelete();
            $table->foreignId('teacher_id')
                  ->constrained('teachers')
                  ->cascadeOnDelete();
            $table->foreignId('class_hour_id')
                  ->constrained('class_hours')
                  ->cascadeOnDelete();
            $table->foreignId('semester_id')
                  ->constrained('semesters')
                  ->cascadeOnDelete();
            $table->enum('day', [
                'monday', 'tuesday', 'wednesday',
                'thursday', 'friday', 'saturday', 'sunday'
            ]);
            $table->string('room')->nullable();
            $table->date('effective_start_date')->nullable();
            $table->date('effective_end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['class_id', 'day']);
            $table->index(['teacher_id', 'day']);
            $table->index(['semester_id', 'is_active']);
            $table->unique(
                ['class_id', 'class_hour_id', 'day', 'semester_id'],
                'unique_schedule_per_class'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};