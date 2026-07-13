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
            ->brandLogo(asset('logo_transparent.png'))
            ->darkModeBrandLogo(asset('logo_white.png'))
            ->brandLogoHeight('4rem')
            ->favicon(asset('logo_icon.png'))
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
                    /* Full screen mountain background on wrapper */
                    .custom-auth-wrapper {
                        display: flex !important;
                        width: 100% !important;
                        min-height: 100vh !important;
                        background: url(\'' . asset('background.png') . '\') center center / cover no-repeat !important;
                        position: relative !important;
                    }
                    
                    /* Hide left panel entirely */
                    .custom-auth-empty-panel {
                        display: none !important;
                    }
                    
                    /* Full screen transparent form panel (fills the wrapper) */
                    .custom-auth-form-panel {
                        display: flex !important;
                        flex-direction: column !important;
                        justify-content: center !important;
                        align-items: center !important;
                        width: 100% !important;
                        min-height: 100vh !important;
                        background-color: transparent !important;
                        padding: 1.5rem !important;
                        flex-grow: 1 !important;
                    }
                    
                    /* Form wrapper centered inside the panel */
                    .custom-auth-form-wrapper {
                        margin-left: auto !important;
                        margin-right: auto !important;
                        width: 100% !important;
                        max-width: 28rem !important;
                        display: flex !important;
                        justify-content: center !important;
                        align-items: center !important;
                    }
                    
                    /* Styling Form Login (Glassmorphic Card) */
                    .fi-simple-layout {
                        background: transparent !important;
                    }
                    
                    .fi-simple-main,
                    main,
                    .fi-simple-layout main,
                    .custom-auth-form-wrapper > div {
                        background-color: rgba(245, 247, 250, 0.35) !important;
                        backdrop-filter: blur(8px) !important;
                        -webkit-backdrop-filter: blur(20px) !important;
                        border: 1px solid rgba(255, 255, 255, 0.25) !important;
                        border-radius: 24px !important;
                        padding: 2.5rem 2rem !important;
                        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2) !important;
                        width: 100% !important;
                        max-width: 100% !important;
                    }
                    
                    .dark .fi-simple-main,
                    .dark main,
                    .dark .fi-simple-layout main,
                    .dark .custom-auth-form-wrapper > div {
                        background-color: rgba(30, 41, 59, 0.45) !important;
                        border-color: rgba(255, 255, 255, 0.15) !important;
                    }
                    
                    .fi-simple-main-ctn {
                        width: 100% !important;
                        max-width: 100% !important;
                    }

                    /* Heading and Labels styled in white */
                    .fi-simple-header-heading {
                        color: #3d3d3dff !important;
                        font-weight: 700 !important;
                        font-size: 1.75rem !important;
                        text-align: center !important;
                    }

                    .fi-fo-field-wrp-label, .fi-label, label, label span, .fi-simple-main label span {
                        color: #3d3d3dff !important;
                        font-weight: 650 !important;
                        font-size: 0.875rem !important;
                    }

                    /* Filament Input Styling (Glassmorphic Inputs) */
                    .fi-input-wrp {
                        background-color: rgba(255, 255, 255, 0.08) !important;
                        border: 0.2px solid #434343ff !important;
                        border-radius: 12px !important;
                        box-shadow: none !important;
                        transition: all 0.2s ease !important;
                    }

                    .fi-input-wrp:focus-within {
                        border-color: #434343ff !important;
                        background-color: rgba(255, 255, 255, 0.12) !important;
                        box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.1) !important;
                    }

                    .fi-input-wrp input {
                        background-color: transparent !important;
                        color: #434343ff !important;
                    }

                    .fi-input-wrp input::placeholder {
                        color: #434343ff !important;
                    }

                    /* Password hide/show button */
                    .fi-input-wrp button {
                        background-color: transparent !important;
                        color: #434343ff !important;
                    }
                    .fi-input-wrp button:hover {
                        color: #434343ff !important;
                    }

                    /* Button Login (Burgundy Color) */
                    .fi-simple-main button[type="submit"], .fi-btn-color-primary {
                        background-color: #802B47 !important;
                        border-radius: 12px !important;
                        color: #ffffff !important;
                        font-weight: 700 !important;
                        padding-top: 0.65rem !important;
                        padding-bottom: 0.65rem !important;
                        transition: all 0.25s ease !important;
                        box-shadow: 0 4px 12px rgba(128, 43, 71, 0.4) !important;
                        border: none !important;
                        width: 100% !important;
                        cursor: pointer !important;
                    }

                    .fi-simple-main button[type="submit"]:hover, .fi-btn-color-primary:hover {
                        background-color: #682239 !important;
                        box-shadow: 0 6px 16px rgba(128, 43, 71, 0.55) !important;
                        transform: translateY(-1px) !important;
                    }

                    /* Remember Me Checkbox */
                    .fi-checkbox {
                        border-color: rgba(255, 255, 255, 0.3) !important;
                        background-color: rgba(255, 255, 255, 0.08) !important;
                        border-radius: 4px !important;
                    }
                    .fi-checkbox:checked {
                        background-color: #802B47 !important;
                        border-color: #802B47 !important;
                    }

                    /* Google Login Button Styles */
                    #google-login-btn {
                        display: flex !important;
                        align-items: center !important;
                        justify-content: center !important;
                        gap: 0.75rem !important;
                        width: 100% !important;
                        padding: 0.625rem 1rem !important;
                        border-radius: 12px !important;
                        background-color: white !important;
                        color: #434343ff !important;
                        font-weight: 600 !important;
                        font-size: 0.875rem !important;
                        box-shadow: none !important;
                        cursor: pointer !important;
                        transition: all 0.2s ease !important;
                    }

                    #google-login-btn:hover {
                        background-color: rgba(255, 255, 255, 0.62) !important;
                        border-color: rgba(255, 255, 255, 0.25) !important;
                    }

                    #google-login-btn svg {
                        width: 1.25rem !important;
                        height: 1.25rem !important;
                        flex-shrink: 0 !important;
                    }

                    /* Divider Line */
                    .custom-auth-divider {
                        display: flex !important;
                        align-items: center !important;
                        width: 100% !important;
                        margin-top: 0.5rem !important;
                        margin-bottom: 1.5rem !important;
                    }

                    .custom-auth-divider-line {
                        flex-grow: 1 !important;
                        border-top: 0.1rem solid rgba(0, 0, 0, 0.15) !important;
                    }

                    .custom-auth-divider-text {
                        flex-shrink: 0 !important;
                        margin-left: 1rem !important;
                        margin-right: 1rem !important;
                        font-size: 0.75rem !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.05em !important;
                        font-weight: 700 !important;
                        color: rgba(0, 0, 0, 0.6) !important;
                    }

                    /* Error Messages */
                    .fi-fo-field-wrp-error-message, .text-danger-600 {
                        color: #ff6b6b !important;
                    }

                    /* Dark Mode Specific Overrides */
                    .dark .fi-simple-header-heading {
                        color: #ffffff !important;
                    }

                    .dark .fi-fo-field-wrp-label, 
                    .dark .fi-label, 
                    .dark label, 
                    .dark label span, 
                    .dark .fi-simple-main label span {
                        color: rgba(255, 255, 255, 0.95) !important;
                    }

                    .dark .fi-input-wrp {
                        background-color: rgba(255, 255, 255, 0.08) !important;
                        border: 0.2px solid rgba(255, 255, 255, 0.25) !important;
                    }

                    .dark .fi-input-wrp:focus-within {
                        border-color: rgba(255, 255, 255, 0.4) !important;
                    }

                    .dark .fi-input-wrp input {
                        color: #ffffff !important;
                    }

                    .dark .fi-input-wrp input::placeholder {
                        color: rgba(255, 255, 255, 0.4) !important;
                    }

                    .dark .fi-input-wrp button {
                        background-color: transparent !important;
                        color: rgba(255, 255, 255, 0.7) !important;
                    }
                    
                    .dark .fi-input-wrp button:hover {
                        color: #ffffff !important;
                    }

                    .dark #google-login-btn {
                        background-color: white !important;
                        border-color: rgba(255, 255, 255, 0.2) !important;
                        color: #434343ff !important;
                    }

                    .dark #google-login-btn:hover {
                        background-color: rgba(255, 255, 255, 0.62) !important;
                        border-color: rgba(255, 255, 255, 0.25) !important;
                    }

                    .dark .custom-auth-divider-text {
                        color: white !important;
                    }

                    .dark .custom-auth-divider-line {
                        flex-grow: 1 !important;
                        border-top: 0.1rem solid rgba(255, 255, 255, 0.15) !important;
                    }

                    </style>
                ')
            )
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->databaseNotificationsPolling('5s')
            ->userMenuItems([
                'profile' => \Filament\Navigation\MenuItem::make()
                    ->label('Profil Saya')
                    ->url(fn (): string => \App\Filament\Pages\Profile::getUrl())
                    ->icon('heroicon-o-user'),
            ]);
    }

    public function boot(): void
    {
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): string => view('partials.webpush-subscribe')->render(),
        );
    }
}