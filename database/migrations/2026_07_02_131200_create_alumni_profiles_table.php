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

            // Pendidikan/Kuliah
            $table->string('university_name')->nullable();
            $table->string('study_program')->nullable();
            $table->year('entry_year')->nullable();
            $table->year('graduation_year_university')->nullable();

            // Pekerjaan
            $table->string('company_name')->nullable();
            $table->string('job_position')->nullable();
            $table->string('industry')->nullable();
            $table->integer('salary_range_min')->nullable();
            $table->integer('salary_range_max')->nullable();

            // Wirausaha
            $table->string('business_name')->nullable();
            $table->string('business_field')->nullable();
            $table->year('business_start_year')->nullable();

            // Kontak & Lokasi
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('instagram')->nullable();

            // Lainnya
            $table->text('achievements')->nullable();
            $table->text('testimonial')->nullable();
            $table->boolean('is_willing_to_be_contacted')->default(false);
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();

            $table->index('current_status');
            $table->index('city');
            $table->index('province');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_profiles');
    }
};