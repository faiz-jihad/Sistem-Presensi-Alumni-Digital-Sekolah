<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
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
    }
}
