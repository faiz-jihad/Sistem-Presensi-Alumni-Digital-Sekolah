<?php

namespace App\Providers;

use App\Models\PresensiSession;
use App\Policies\AttendanceSessionPolicy;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set locale Bahasa Indonesia untuk seluruh aplikasi (termasuk Filament)
        App::setLocale('id');
        App::setFallbackLocale('id');

        // Set Carbon ke Bahasa Indonesia agar tanggal tampil dalam format Indonesia
        Carbon::setLocale('id');

        // ─── Policy Registration ────────────────────────────────────────────
        Gate::policy(PresensiSession::class, AttendanceSessionPolicy::class);
        Gate::policy(\App\Models\Schedule::class, \App\Policies\MasterDataPolicy::class);
        Gate::policy(\App\Models\Subject::class, \App\Policies\MasterDataPolicy::class);
        Gate::policy(\App\Models\ClassHour::class, \App\Policies\MasterDataPolicy::class);
        Gate::policy(\App\Models\StudentClass::class, \App\Policies\MasterDataPolicy::class);
        Gate::policy(\App\Models\AcademicYear::class, \App\Policies\MasterDataPolicy::class);
        Gate::policy(\App\Models\Teacher::class, \App\Policies\MasterDataPolicy::class);
    }
}
