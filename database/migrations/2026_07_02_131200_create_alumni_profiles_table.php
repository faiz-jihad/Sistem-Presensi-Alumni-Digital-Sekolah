<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumni_id')
                  ->unique()
                  ->constrained('alumni')
                  ->cascadeOnDelete();
            $table->enum('current_status', [
                'studying',         // Kuliah
                'working',          // Bekerja
                'entrepreneur',     // Wirausaha
                'unemployed',       // Belum bekerja
                'studying_working'  // Kuliah sambil bekerja
            ])->nullable();

            $table->string('university_name')->nullable();
            $table->string('study_program')->nullable();

            $table->string('company_name')->nullable();
            $table->string('job_position')->nullable();

            $table->string('business_name')->nullable();

            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('linkedin_url')->nullable();
            $table->timestamps();

            $table->index('current_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_profiles');
    }
};