<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presensi_session_id')
                ->constrained('presensi_sessions')
                ->cascadeOnDelete();
            $table->string('token', 64)->unique()->comment('Token QR unik (random)');
            $table->timestamp('expired_at')->comment('Waktu kadaluarsa token (5 menit)');
            $table->boolean('used')->default(false)->comment('Apakah token sudah digunakan');
            $table->timestamps();

            $table->index(['token', 'used']);
            $table->index(['presensi_session_id', 'used']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_tokens');
    }
};
