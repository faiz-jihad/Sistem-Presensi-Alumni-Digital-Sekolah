<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();
            $table->foreignId('class_id')
                ->nullable()
                ->constrained('classes')
                ->nullOnDelete();
            $table->foreignId('parent_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('Orang tua/Wali');
            $table->string('nis', 20)->unique()->comment('Nomor Induk Siswa');
            $table->string('nisn', 10)->unique()->comment('Nomor Induk Siswa Nasional');
            $table->string('name');
            $table->enum('gender', ['male', 'female']);
            $table->date('birth_date');
            $table->enum('status', [
                'active',
                'inactive',
                'graduated',
                'transferred',
                'dropout',
            ])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'class_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};