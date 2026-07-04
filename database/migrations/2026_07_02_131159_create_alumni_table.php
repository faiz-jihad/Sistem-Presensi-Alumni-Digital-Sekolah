<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('alumni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('nisn', 10)->unique();
            $table->string('name');
            $table->enum('gender', ['male', 'female']);
            $table->year('graduation_year');
            $table->string('class_name')->comment('Kelas saat lulus');
            $table->string('major')->nullable();
            $table->string('photo')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])
                ->default('pending');
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'graduation_year']);
            $table->index('verification_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni');
    }
};