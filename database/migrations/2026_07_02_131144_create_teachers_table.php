<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')
                  ->constrained('schools')
                  ->cascadeOnDelete();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Link ke akun user');
            $table->string('nip', 18)->unique()->comment('Nomor Induk Pegawai');
            $table->string('name');
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();
            $table->enum('employment_status', [
                'pns', 'pppk', 'honorer', 'gtt', 'ptt', 'kontrak'
            ])->default('honorer');
            $table->string('field_of_study')->nullable()->comment('Bidang studi');
            $table->string('education_level')->nullable()->comment('S1, S2, S3');
            $table->string('university')->nullable();
            $table->date('join_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'retired'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'status']);
            $table->index('field_of_study');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};