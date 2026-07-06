<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presensi_sessions', function (Blueprint $table) {
            $table->string('latitude')->nullable()->after('notes');
            $table->string('longitude')->nullable()->after('latitude');
            $table->string('closed_latitude')->nullable()->after('longitude');
            $table->string('closed_longitude')->nullable()->after('closed_latitude');
            $table->string('photo')->nullable()->after('closed_longitude');
            $table->boolean('is_late')->default(false)->after('photo');
        });
    }

    public function down(): void
    {
        Schema::table('presensi_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude',
                'closed_latitude',
                'closed_longitude',
                'photo',
                'is_late',
            ]);
        });
    }
};
