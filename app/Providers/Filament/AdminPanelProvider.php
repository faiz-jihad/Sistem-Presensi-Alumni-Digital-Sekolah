<?php

namespace App\Providers\Filament;


use App\Filament\Pages\Dashboard;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use DiogoGPinto\AuthUIEnhancer\AuthUIEnhancerPlugin;


use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\DiogoGPinto\AuthUIEnhancer\Pages\Auth\AuthUiEnhancerLogin::class)
            ->brandName('SIMPAD')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
                \App\Filament\Resources\StudentAttendances\Pages\ManualAttendance::class,
                \App\Filament\Pages\WhatsappNotifPage::class,
                \App\Filament\Pages\DashboardGrafik::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\DailyAttendanceChartWidget::class,
                \App\Filament\Widgets\AttendanceChartWidget::class,
                \App\Filament\Widgets\AlumniStatusChartWidget::class,
                \App\Filament\Widgets\RecentSchools::class,
                \App\Filament\Widgets\RecentStudents::class,
                \App\Filament\Widgets\RecentTeachers::class,
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                AuthUIEnhancerPlugin::make()
                    ->formPanelPosition('right')
                    ->formPanelWidth('40%')
                    ->emptyPanelBackgroundImageUrl('https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1920&q=80')
                    ->emptyPanelBackgroundImageOpacity('95%')
            ])
            ->renderHook(
                'panels::head.end',
                fn () => new \Illuminate\Support\HtmlString('
                    <style>
                    /* Custom Auth Split Layout Styles */
                    .custom-auth-wrapper {
                        display: flex;
                        width: 100%;
                        min-height: 100vh;
                        flex-direction: column;
                    }
                    
                    @media (min-width: 1024px) {
                        .custom-auth-wrapper {
                            flex-direction: row !important;
                        }
                        .lg\:flex-row-reverse {
                            flex-direction: row-reverse !important;
                        }
                    }
                    
                    .custom-auth-empty-panel {
                        position: relative;
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        flex-grow: 1;
                        padding-left: 1rem;
                        padding-right: 1rem;
                        background-color: var(--empty-panel-background-color, #1e3a8a);
                        overflow: hidden;
                    }
                    
                    @media (max-width: 1023px) {
                        .custom-auth-empty-panel {
                            display: none !important;
                        }
                    }
                    
                    .custom-auth-empty-panel .absolute {
                        position: absolute !important;
                    }
                    .custom-auth-empty-panel .inset-0 {
                        top: 0 !important;
                        right: 0 !important;
                        bottom: 0 !important;
                        left: 0 !important;
                    }
                    .custom-auth-empty-panel .w-full {
                        width: 100% !important;
                    }
                    .custom-auth-empty-panel .h-full {
                        height: 100% !important;
                    }
                    .custom-auth-empty-panel .bg-cover {
                        background-size: cover !important;
                    }
                    .custom-auth-empty-panel .bg-center {
                        background-position: center !important;
                    }
                    
                    .custom-auth-form-panel {
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        width: 100%;
                        background-color: var(--form-panel-background-color, #ffffff);
                        padding: 3rem 1.5rem;
                    }
                    
                    @media (min-width: 640px) {
                        .custom-auth-form-panel {
                            padding-left: 2.5rem;
                            padding-right: 2.5rem;
                        }
                    }
                    
                    @media (min-width: 1024px) {
                        .custom-auth-form-panel {
                            width: var(--form-panel-width, 40%) !important;
                            padding-left: 4rem;
                            padding-right: 4rem;
                        }
                    }
                    
                    @media (min-width: 1280px) {
                        .custom-auth-form-panel {
                            padding-left: 6rem;
                            padding-right: 6rem;
                        }
                    }
                    
                    .custom-auth-form-wrapper {
                        margin-left: auto;
                        margin-right: auto;
                        width: 100%;
                        max-width: 24rem;
                    }
                    
                    /* Styling Form Login */
                    .fi-simple-layout {
                        background: transparent !important;
                    }
                    
                    .fi-simple-main {
                        background-color: rgba(255, 255, 255, 0.8) !important;
                        backdrop-filter: blur(12px) !important;
                        border: 1px solid rgba(229, 231, 235, 0.5) !important;
                        border-radius: 1rem !important;
                        padding: 2rem !important;
                        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
                        width: 100% !important;
                        max-width: 100% !important;
                    }
                    
                    .dark .fi-simple-main {
                        background-color: rgba(24, 24, 27, 0.8) !important;
                        border-color: rgba(63, 63, 70, 0.4) !important;
                    }
                    
                    .fi-simple-main-ctn {
                        width: 100% !important;
                        max-width: 100% !important;
                    }

                    /* Google Login Button Styles */
                    #google-login-btn {
                        display: flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        gap: 0.75rem !important;
                        width: 100% !important;
                        padding: 0.625rem 1rem !important;
                        border: 1px solid #d1d5db !important;
                        border-radius: 0.75rem !important;
                        background-color: #ffffff !important;
                        color: #374151 !important;
                        font-weight: 600 !important;
                        font-size: 0.875rem !important;
                        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
                        cursor: pointer !important;
                        transition: all 0.2s ease !important;
                    }

                    #google-login-btn:hover {
                        background-color: #f9fafb !important;
                        border-color: #c0c4cc !important;
                    }

                    #google-login-btn svg {
                        width: 1.25rem !important;
                        height: 1.25rem !important;
                        flex-shrink: 0 !important;
                    }

                    .dark #google-login-btn {
                        background-color: #0f172a !important;
                        border-color: #334155 !important;
                        color: #e2e8f0 !important;
                    }

                    .dark #google-login-btn:hover {
                        background-color: #1e293b !important;
                    }

                    /* Divider Line */
                    .custom-auth-divider {
                        display: flex !important;
                        align-items: center !important;
                        width: 100% !important;
                        margin-top: 1rem !important;
                        margin-bottom: 0.5rem !important;
                    }

                    .custom-auth-divider-line {
                        flex-grow: 1 !important;
                        border-top: 1px solid #e5e7eb !important;
                    }

                    .dark .custom-auth-divider-line {
                        border-color: #334155 !important;
                    }

                    .custom-auth-divider-text {
                        flex-shrink: 0 !important;
                        margin-left: 1rem !important;
                        margin-right: 1rem !important;
                        font-size: 0.75rem !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.05em !important;
                        font-weight: 700 !important;
                        color: #9ca3af !important;
                    }
                    </style>
                ')
            )
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->databaseNotificationsPolling('5s')
            ->profile();
    }

    public function boot(): void
    {
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): string => view('partials.webpush-subscribe')->render(),
        );
    }
}