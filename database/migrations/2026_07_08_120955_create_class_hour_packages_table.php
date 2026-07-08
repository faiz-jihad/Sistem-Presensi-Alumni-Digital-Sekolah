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
        Schema::create('class_hour_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')
                  ->constrained('schools')
                  ->cascadeOnDelete();
            $table->string('name');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::table('class_hours', function (Blueprint $table) {
            $table->foreignId('class_hour_package_id')
                  ->nullable()
                  ->constrained('class_hour_packages')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_hours', function (Blueprint $table) {
            $table->dropForeign(['class_hour_package_id']);
            $table->dropColumn('class_hour_package_id');
        });

        Schema::dropIfExists('class_hour_packages');
    }
};
