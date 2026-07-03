<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')
                  ->constrained('schools')
                  ->cascadeOnDelete();
            $table->string('name')->comment('Contoh: 2026/2027');
            $table->year('start_year');
            $table->year('end_year');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'is_active']);
            $table->unique(['school_id', 'name'], 'unique_academic_year_per_school');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};