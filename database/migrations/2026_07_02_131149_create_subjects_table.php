<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')
                  ->constrained('schools')
                  ->cascadeOnDelete();
            $table->string('code', 20)->comment('Kode mata pelajaran');
            $table->string('name')->comment('Nama: Matematika, dll');
            $table->string('short_name', 10)->nullable()->comment('Singkatan: MTK');
            $table->enum('group', [
                'general',
                'specialized',
                'local',
                'extracurricular'
            ])->default('general');
            $table->integer('credit_hours')->default(2);
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['school_id', 'group']);
            $table->unique(['school_id', 'code'], 'unique_subject_code_per_school');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};