<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('password_reset_otps')) {
            return;
        }

        Schema::table('password_reset_otps', function (Blueprint $table) {
            if (!Schema::hasColumn('password_reset_otps', 'failed_attempts')) {
                $table->unsignedTinyInteger('failed_attempts')->default(0)->after('expires_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('password_reset_otps')) {
            return;
        }

        Schema::table('password_reset_otps', function (Blueprint $table) {
            if (Schema::hasColumn('password_reset_otps', 'failed_attempts')) {
                $table->dropColumn('failed_attempts');
            }
        });
    }
};
