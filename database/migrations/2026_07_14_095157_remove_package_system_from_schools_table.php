<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus kolom package_id dari tabel schools dan hapus tabel packages.
     * Sistem paket langganan telah dihapus sepenuhnya.
     */
    public function up(): void
    {
        // 1. Pastikan semua sekolah tidak memiliki package_id sebelum drop
        DB::table('schools')->whereNotNull('package_id')->update(['package_id' => null]);

        // 2. Drop kolom package_id dari tabel schools
        Schema::table('schools', function (Blueprint $table) {
            if (Schema::hasColumn('schools', 'package_id')) {
                $table->dropForeign(['package_id']);
                $table->dropColumn('package_id');
            }
        });

        // 3. Drop tabel packages
        Schema::dropIfExists('packages');
    }

    /**
     * Kembalikan perubahan (rollback jika dibutuhkan).
     */
    public function down(): void
    {
        // Buat ulang tabel packages
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('price')->default(0);
            $table->unsignedTinyInteger('duration_months')->default(1);
            $table->boolean('is_active')->default(true);
            $table->boolean('has_presensi')->default(true);
            $table->boolean('has_alumni')->default(true);
            $table->boolean('has_tracer_study')->default(true);
            $table->boolean('has_job_vacancy')->default(true);
            $table->boolean('has_export')->default(true);
            $table->timestamps();
        });

        // Tambah kembali kolom package_id di schools
        Schema::table('schools', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
        });
    }
};
