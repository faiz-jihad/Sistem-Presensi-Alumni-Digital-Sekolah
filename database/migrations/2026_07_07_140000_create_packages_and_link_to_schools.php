<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->integer('duration_months')->default(1);
            $table->boolean('is_active')->default(true);
            
            // Feature flags for feature-based restrictions
            $table->boolean('has_presensi')->default(true);
            $table->boolean('has_alumni')->default(true);
            $table->boolean('has_tracer_study')->default(true);
            $table->boolean('has_job_vacancy')->default(true);
            $table->boolean('has_export')->default(true);
            
            $table->timestamps();
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->foreignId('package_id')
                ->nullable()
                ->after('status')
                ->constrained('packages')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn('package_id');
        });

        Schema::dropIfExists('packages');
    }
};
