<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')
                  ->constrained('schools')
                  ->cascadeOnDelete();
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->enum('type', [
                'attendance_report',
                'student_report',
                'teacher_report',
                'alumni_report',
                'academic_report',
                'custom'
            ]);
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type')->default('xlsx');
            $table->integer('file_size')->nullable();
            $table->json('filters')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])
                  ->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'type']);
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exports');
    }
};