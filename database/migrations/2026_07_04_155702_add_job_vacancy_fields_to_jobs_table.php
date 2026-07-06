<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            // Ubah primary key menjadi id biasa
            $table->bigIncrements('id')->change();
            
            // Tambahkan kolom untuk lowongan kerja
            $table->string('title')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_logo')->nullable();
            $table->longText('description')->nullable();
            $table->longText('requirements')->nullable();
            $table->string('location')->nullable();
            $table->string('salary_min')->nullable();
            $table->string('salary_max')->nullable();
            $table->string('job_type')->nullable()->comment('full_time, part_time, freelance, internship');
            $table->string('category')->nullable();
            $table->date('deadline')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->unsignedBigInteger('school_id')->nullable();

            // Foreign key
            $table->foreign('posted_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropForeign(['posted_by']);
            $table->dropForeign(['school_id']);
            
            $table->dropColumn([
                'title',
                'company_name',
                'company_logo',
                'description',
                'requirements',
                'location',
                'salary_min',
                'salary_max',
                'job_type',
                'category',
                'deadline',
                'is_active',
                'posted_by',
                'school_id',
            ]);
        });
    }
};