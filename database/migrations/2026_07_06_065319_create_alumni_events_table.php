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
        Schema::create('alumni_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('posted_by')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('title');
            $table->longText('description');
            $table->dateTime('event_date');
            $table->string('location');
            $table->string('banner_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni_events');
    }
};
