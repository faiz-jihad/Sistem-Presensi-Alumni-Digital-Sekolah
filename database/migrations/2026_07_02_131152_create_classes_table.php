<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')
                  ->constrained('schools')
                  ->cascadeOnDelete();
            $table->foreignId('academic_year_id')
                  ->nullable()
                  ->constrained('academic_years')
                  ->nullOnDelete();
            $table->string('name')->comment('Contoh: XII RPL 1');
            $table->enum('grade', ['10', '11', '12', '13'])->comment('Tingkat');
            $table->string('major')->nullable()->comment('Jurusan: RPL, TKJ, dll');
            $table->foreignId('homeroom_teacher_id')
                  ->nullable()
                  ->constrained('teachers')
                  ->nullOnDelete()
                  ->comment('Wali Kelas');
            $table->integer('capacity')->default(30);
            $table->string('room_number')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'grade']);
            $table->index('academic_year_id');
            $table->unique(
                ['school_id', 'name', 'major', 'academic_year_id'],
                'unique_class_per_school_year'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};