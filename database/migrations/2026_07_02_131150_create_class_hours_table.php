<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')
                  ->constrained('schools')
                  ->cascadeOnDelete();
            $table->string('code', 10)->comment('Kode: J1, J2, dst');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes')->comment('Durasi dalam menit');
            $table->integer('order')->comment('Urutan jam ke-');
            $table->boolean('is_break')->default(false)->comment('Jam istirahat?');
            $table->enum('shift', ['morning', 'afternoon', 'evening'])->default('morning');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->index(['school_id', 'order']);
            $table->unique(['school_id', 'code'], 'unique_class_hour_code_per_school');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_hours');
    }
};