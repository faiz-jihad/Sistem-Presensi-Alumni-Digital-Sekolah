<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumni_profiles', function (Blueprint $table) {
            $table->timestamp('profile_completed_at')->nullable()->after('linkedin_url');
        });

        DB::table('alumni_profiles')
            ->whereNotNull('current_status')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->whereNotNull('province')
            ->where('province', '!=', '')
            ->whereNotNull('whatsapp')
            ->where('whatsapp', '!=', '')
            ->update(['profile_completed_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('alumni_profiles', function (Blueprint $table) {
            $table->dropColumn('profile_completed_at');
        });
    }
};
