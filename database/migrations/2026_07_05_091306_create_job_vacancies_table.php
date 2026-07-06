<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_vacancies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('posted_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');

            $table->string('company_name');

            $table->string('company_logo')->nullable();

            $table->longText('description');

            $table->longText('requirements');

            $table->string('location');

            $table->unsignedBigInteger('salary_min')->nullable();

            $table->unsignedBigInteger('salary_max')->nullable();

            $table->enum('job_type', [
                'full_time',
                'part_time',
                'freelance',
                'internship',
            ]);

            $table->enum('category', [
                'technology',
                'education',
                'health',
                'business',
                'creative',
                'engineering',
                'others',
            ]);

            $table->date('deadline')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_vacancies');
    }
};