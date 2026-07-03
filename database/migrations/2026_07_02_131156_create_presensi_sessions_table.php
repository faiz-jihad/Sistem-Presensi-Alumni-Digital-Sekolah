<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensi_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')
                  ->constrained('schools')
                  ->cascadeOnDelete();
            $table->foreignId('schedule_id')
                  ->constrained('schedules')
                  ->cascadeOnDelete();
            $table->foreignId('teacher_id')
                  ->constrained('teachers')
                  ->cascadeOnDelete();
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('status', [
                'scheduled', 'open', 'closed', 'cancelled'
            ])->default('scheduled');
            $table->text('material_topic')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('closed_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'date']);
            $table->index(['schedule_id', 'date']);
            $table->index(['teacher_id', 'date']);
            $table->index('status');
            $table->unique(
                ['schedule_id', 'date'],
                'unique_session_per_schedule_per_day'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi_sessions');
    }
};