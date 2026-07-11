{{-- resources/views/filament/pages/profile.blade.php --}}
<x-filament-panels::page>
    <style>
        /* Perkecil icon section */
        .fi-section-header-icon {
            width: 1.25rem !important;
            height: 1.25rem !important;
        }

        /* Custom Google SSO Integration Card Widget Styles */
        .google-sso-card {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 1rem 1.25rem !important;
            border-radius: 12px !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
            background-color: #f8fafc !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            margin-top: 0.5rem !important;
        }

        .dark .google-sso-card {
            border-color: rgba(51, 65, 85, 0.6) !important;
            background-color: rgba(15, 23, 42, 0.3) !important;
        }

        .google-sso-card:hover {
            border-color: rgba(66, 133, 244, 0.4) !important;
            box-shadow: 0 4px 12px rgba(66, 133, 244, 0.05) !important;
        }

        .google-sso-card__left {
            display: flex !important;
            align-items: center !important;
            gap: 1rem !important;
        }

        .google-sso-card__logo-wrapper {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 40px !important;
            height: 40px !important;
            border-radius: 8px !important;
            background-color: #ffffff !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02) !important;
        }

        .dark .google-sso-card__logo-wrapper {
            background-color: #0d1527 !important;
            border-color: rgba(51, 65, 85, 0.6) !important;
        }

        .google-sso-card__logo {
            width: 18px !important;
            height: 18px !important;
            display: block !important;
        }

        .google-sso-card__text {
            display: flex !important;
            flex-direction: column !important;
            gap: 0.15rem !important;
            text-align: left !important;
        }

        .google-sso-card__title {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-size: 0.8125rem !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin: 0 !important;
            padding: 0 !important;
            line-height: 1.25 !important;
        }

        .dark .google-sso-card__title {
            color: #f8fafc !important;
        }

        .google-sso-card__desc {
            font-size: 0.72rem !important;
            color: #64748b !important;
            margin: 0 !important;
            padding: 0 !important;
            line-height: 1.4 !important;
        }

        .dark .google-sso-card__desc {
            color: #94a3b8 !important;
        }

        .google-sso-badge {
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.35rem !important;
            padding: 0.35rem 0.75rem !important;
            border-radius: 9999px !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-size: 0.6875rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            line-height: 1 !important;
        }

        .google-sso-badge--connected {
            color: #065f46 !important;
            background-color: #ecfdf5 !important;
            border: 1px solid #a7f3d0 !important;
        }

        .dark .google-sso-badge--connected {
            color: #34d399 !important;
            background-color: rgba(6, 78, 59, 0.25) !important;
            border-color: rgba(52, 211, 153, 0.25) !important;
        }

        .google-sso-badge--disconnected {
            color: #475569 !important;
            background-color: #f1f5f9 !important;
            border: 1px solid #e2e8f0 !important;
        }

        .dark .google-sso-badge--disconnected {
            color: #94a3b8 !important;
            background-color: rgba(51, 65, 85, 0.25) !important;
            border-color: rgba(148, 163, 184, 0.2) !important;
        }

        .google-sso-badge__dot {
            width: 6px !important;
            height: 6px !important;
            border-radius: 999px !important;
            background-color: #64748b !important;
            display: inline-block !important;
        }

        .google-sso-badge--connected .google-sso-badge__dot {
            background-color: #10b981 !important;
        }

        @keyframes ssoPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            70% {
                box-shadow: 0 0 0 5px rgba(16, 185, 129, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        .google-sso-badge__dot--pulse {
            animation: ssoPulse 2s infinite !important;
        }
    </style>

    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <x-filament::actions
            :actions="$this->getFormActions()"
        />
    </form>
</x-filament-panels::page>