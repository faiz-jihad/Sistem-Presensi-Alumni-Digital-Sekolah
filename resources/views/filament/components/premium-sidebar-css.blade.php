@php
    $user = auth()->user();
    $userName = $user ? $user->name : 'Pengguna SIMPAD';
    $userRole = match ($user?->role) {
        'super_admin' => 'Super Admin',
        'admin' => 'Admin Sekolah',
        'teacher' => 'Guru',
        'student' => 'Siswa',
        'alumni' => 'Alumni',
        'parent' => 'Orang Tua / Wali',
        default => 'Pengguna',
    };
    $userEmail = $user ? $user->email : '';
    $isGoogleLinked = !empty($user?->google_id);
    $avatarUrl = $user?->avatar_url;

    // Initials calculation
    $initials = '';
    if ($user) {
        $words = explode(' ', trim($user->name));
        $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) && !empty($words[1]) ? substr($words[1], 0, 1) : ''));
    } else {
        $initials = 'SP';
    }
@endphp

{{-- Meta tags untuk passing data dari Blade ke JavaScript --}}
<meta name="simpad-user-name" content="{{ $userName }}">
<meta name="simpad-user-role" content="{{ $userRole }}">
<meta name="simpad-user-email" content="{{ $userEmail }}">
<meta name="simpad-user-initials" content="{{ $initials }}">
<meta name="simpad-user-avatar" content="{{ $avatarUrl ?? '' }}">
<meta name="simpad-user-google-linked" content="{{ $isGoogleLinked ? '1' : '0' }}">

<!-- Premium Sidebar Theme Style Hook -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

    /* ============================================================
       CSS CUSTOM PROPERTIES (DESIGN TOKENS)
       ============================================================ */
    :root {
        --sidebar-active-gradient: linear-gradient(135deg, #3b82f6 0%, #4f46e5 100%);
        --sidebar-hover-translate: translateX(5px);
        --notification-unread-bg: #F5F9FF;
        --notification-hover-bg: #EBF3FF;
        --primary-blue: #3b82f6;
        --primary-blue-dark: #2563eb;
        --primary-indigo: #4f46e5;
        --text-primary-light: #0f172a;
        --text-secondary-light: #475569;
        --text-muted-light: #64748b;
        --border-light: rgba(226, 232, 240, 0.8);
        --bg-white: #ffffff;
        --bg-light-gray: #f8fafc;
        --transition-smooth: cubic-bezier(0.4, 0, 0.2, 1);
        --transition-bounce: cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    /* ============================================================
       GLOBAL TYPOGRAPHY
       ============================================================ */
    body,
    .fi-body,
    .fi-sidebar,
    .fi-sidebar-header,
    .fi-sidebar-item-button {
        font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif !important;
    }

    /* ============================================================
       SIDEBAR WRAPPER
       ============================================================ */
    .fi-sidebar {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.95) 100%) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border-right: 1px solid var(--border-light) !important;
        box-shadow: 4px 0 24px rgba(15, 23, 42, 0.015) !important;
        transition: width 0.3s var(--transition-smooth) !important;
    }

    .dark .fi-sidebar {
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.98) 0%, rgba(9, 15, 30, 0.98) 100%) !important;
        border-right: 1px solid rgba(30, 41, 59, 0.6) !important;
        box-shadow: 4px 0 30px rgba(0, 0, 0, 0.25) !important;
    }

    /* ============================================================
       SIDEBAR HEADER & BRAND NAME
       ============================================================ */
    .fi-sidebar-header {
        border-bottom: 1px dashed var(--border-light) !important;
        padding-top: 1.75rem !important;
        padding-bottom: 1.75rem !important;
        background: transparent !important;
    }

    .dark .fi-sidebar-header {
        border-bottom: 1px dashed rgba(51, 65, 85, 0.4) !important;
    }

    .fi-sidebar-header a,
    .fi-sidebar-header-title {
        font-family: 'Outfit', sans-serif !important;
        font-weight: 800 !important;
        letter-spacing: 0.075em !important;
        text-transform: uppercase;
        background: var(--sidebar-active-gradient) !important;
        -webkit-background-clip: text !important;
        -webkit-text-fill-color: transparent !important;
        background-clip: text !important;
        filter: drop-shadow(0 2px 8px rgba(99, 102, 241, 0.15)) !important;
        font-size: 1.25rem !important;
    }

    /* ============================================================
       NAVIGATION GROUPS LABEL
       ============================================================ */
    .fi-sidebar-group-header-label {
        font-family: 'Outfit', sans-serif !important;
        text-transform: uppercase !important;
        font-size: 0.72rem !important;
        letter-spacing: 0.12em !important;
        font-weight: 700 !important;
        color: var(--text-muted-light) !important;
        padding-left: 1.25rem !important;
        opacity: 0.85;
    }

    .dark .fi-sidebar-group-header-label {
        color: #94a3b8 !important;
    }

    /* ============================================================
       NAVIGATION ITEMS / LINKS
       ============================================================ */
    .fi-sidebar-item-button {
        border-radius: 12px !important;
        margin: 0.2rem 0.75rem !important;
        padding: 0.65rem 0.85rem !important;
        transition: all 0.2s var(--transition-smooth) !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
        color: var(--text-secondary-light) !important;
        border: 1px solid transparent !important;
        outline: none !important;
    }

    .dark .fi-sidebar-item-button {
        color: #94a3b8 !important;
    }

    /* FOCUS VISIBLE (Aksesibilitas keyboard) */
    .fi-sidebar-item-button:focus-visible {
        outline: 2px solid var(--primary-blue) !important;
        outline-offset: 2px !important;
    }

    /* Hover State (Non-Active) */
    .fi-sidebar-item-button:hover:not(.fi-sidebar-item-button-active):not([class*="-active"]) {
        background: rgba(241, 245, 249, 0.9) !important;
        color: var(--text-primary-light) !important;
        transform: var(--sidebar-hover-translate) !important;
        border-color: rgba(226, 232, 240, 0.5) !important;
    }

    .dark .fi-sidebar-item-button:hover:not(.fi-sidebar-item-button-active):not([class*="-active"]) {
        background: rgba(30, 41, 59, 0.5) !important;
        color: #f8fafc !important;
        transform: var(--sidebar-hover-translate) !important;
        border-color: rgba(51, 65, 85, 0.3) !important;
    }

    /* Active State */
    .fi-sidebar-item-button.fi-sidebar-item-button-active,
    .fi-sidebar-item-button[class*="-active"],
    .fi-sidebar-item-button[aria-current="page"] {
        background: var(--sidebar-active-gradient) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px 0 rgba(79, 70, 229, 0.25) !important;
        font-weight: 700 !important;
        border: 1px solid rgba(99, 102, 241, 0.2) !important;
    }

    .dark .fi-sidebar-item-button.fi-sidebar-item-button-active,
    .dark .fi-sidebar-item-button[class*="-active"] {
        box-shadow: 0 4px 20px 0 rgba(79, 70, 229, 0.2) !important;
    }

    /* Sidebar Item Icon */
    .fi-sidebar-item-icon {
        transition: all 0.2s ease !important;
        color: var(--text-muted-light) !important;
    }

    .dark .fi-sidebar-item-icon {
        color: #64748b !important;
    }

    /* Active Icon */
    .fi-sidebar-item-button-active .fi-sidebar-item-icon,
    .fi-sidebar-item-button[class*="-active"] .fi-sidebar-item-icon {
        color: #ffffff !important;
        transform: scale(1.1) rotate(1deg) !important;
    }

    /* Hover Icon */
    .fi-sidebar-item-button:hover:not(.fi-sidebar-item-button-active) .fi-sidebar-item-icon {
        color: var(--primary-blue) !important;
    }

    .dark .fi-sidebar-item-button:hover:not(.fi-sidebar-item-button-active) .fi-sidebar-item-icon {
        color: #60a5fa !important;
    }

    /* ============================================================
       SIDEBAR NAV SPACING
       ============================================================ */
    .fi-sidebar-nav {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }

    /* ============================================================
       NOTIFICATION TRIGGER BUTTON (SIDEBAR)
       ============================================================ */
    .fi-sidebar-database-notifications-btn {
        display: flex !important;
        align-items: center !important;
        gap: 0.75rem !important;
        width: calc(100% - 1.5rem) !important;
        min-height: 2.75rem !important;
        margin: 0.2rem 0.75rem !important;
        padding: 0.65rem 0.85rem !important;
        border-radius: 12px !important;
        border: 1px solid rgba(226, 232, 240, 0.75) !important;
        background: rgba(255, 255, 255, 0.78) !important;
        color: #334155 !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
        transition: background-color 160ms ease, border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease !important;
        outline: none !important;
    }

    .fi-sidebar-database-notifications-btn:focus-visible {
        outline: 2px solid var(--primary-blue) !important;
        outline-offset: 2px !important;
    }

    /* Collapsed state */
    .fi-sidebar-database-notifications-btn:has(.fi-sidebar-database-notifications-btn-label[style*="display: none"]) {
        justify-content: center !important;
        padding: 0.65rem 0 !important;
    }

    .fi-sidebar-database-notifications-btn:hover {
        transform: var(--sidebar-hover-translate) !important;
        border-color: rgba(59, 130, 246, 0.35) !important;
        background: #ffffff !important;
        box-shadow: 0 8px 22px rgba(37, 99, 235, 0.10) !important;
    }

    .fi-sidebar-database-notifications-btn:has(.fi-sidebar-database-notifications-btn-label[style*="display: none"]):hover {
        transform: none !important;
    }

    .dark .fi-sidebar-database-notifications-btn {
        border-color: rgba(51, 65, 85, 0.85) !important;
        background: rgba(15, 23, 42, 0.72) !important;
        color: #cbd5e1 !important;
        box-shadow: none !important;
    }

    .dark .fi-sidebar-database-notifications-btn:hover {
        border-color: rgba(96, 165, 250, 0.35) !important;
        background: rgba(30, 41, 59, 0.82) !important;
        box-shadow: 0 10px 26px rgba(2, 6, 23, 0.28) !important;
    }

    .fi-sidebar-database-notifications-btn .fi-icon {
        color: var(--primary-blue-dark) !important;
        flex-shrink: 0 !important;
    }

    .dark .fi-sidebar-database-notifications-btn .fi-icon {
        color: #60a5fa !important;
    }

    .fi-sidebar-database-notifications-btn-label {
        flex: 1 !important;
        min-width: 0 !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
    }

    /* Badge styling */
    .fi-sidebar-database-notifications-btn-badge-ctn {
        margin-left: auto !important;
        flex-shrink: 0 !important;
    }

    .fi-badge {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 1.35rem !important;
        height: 1.35rem !important;
        padding: 0 0.35rem !important;
        border-radius: 999px !important;
        background: var(--primary-blue-dark) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25) !important;
    }

    .fi-badge span {
        line-height: 1 !important;
        font-size: 0.68rem !important;
        font-weight: 800 !important;
    }

    /* ============================================================
       TOPBAR NOTIFICATION BUTTON
       ============================================================ */
    .fi-topbar-database-notifications-btn {
        border-radius: 8px !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        background: rgba(255, 255, 255, 0.9) !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05) !important;
    }

    .dark .fi-topbar-database-notifications-btn {
        background: rgba(15, 23, 42, 0.8) !important;
    }

    /* ============================================================
       DATABASE NOTIFICATIONS PANEL
       ============================================================ */

    /* ── 1. Panel Window ── */
    .fi-no-database .fi-modal-window,
    [data-fi-modal-id="database-notifications"] {
        position: fixed;
        top: 4.75rem;
        right: 1.5rem;
        width: 420px;
        max-width: 420px;
        height: auto;
        max-height: 75vh;
        border-radius: 12px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        background: #ffffff;
        box-shadow: 
            0 1px 3px 0 rgba(0, 0, 0, 0.05),
            0 10px 15px -3px rgba(0, 0, 0, 0.05),
            0 4px 6px -4px rgba(0, 0, 0, 0.05);
        box-sizing: border-box;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        animation: notif-panel-in 180ms cubic-bezier(0.16, 1, 0.3, 1);
        z-index: 999;
    }

    @keyframes notif-panel-in {
        from {
            opacity: 0;
            transform: translateY(-4px) scale(0.99);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .dark .fi-no-database .fi-modal-window,
    .dark [data-fi-modal-id="database-notifications"] {
        border-color: rgba(255, 255, 255, 0.08);
        background: #0b1329;
        box-shadow: 
            0 10px 15px -3px rgba(0, 0, 0, 0.3),
            0 4px 6px -4px rgba(0, 0, 0, 0.3);
    }

    @media (max-width: 768px) {
        .fi-no-database .fi-modal-window,
        [data-fi-modal-id="database-notifications"] {
            width: calc(100vw - 2rem);
            max-width: calc(100vw - 2rem);
            right: 1rem;
            top: 5rem;
        }
    }

    @media (max-width: 640px) {
        .fi-no-database .fi-modal-window,
        [data-fi-modal-id="database-notifications"] {
            width: calc(100% - 1rem);
            max-width: calc(100% - 1rem);
            right: 0.5rem;
            left: 0.5rem;
            top: auto;
            bottom: 0.75rem;
            border-radius: 14px;
        }
    }

    /* ── 2. Panel Header ── */
    .fi-no-database .fi-modal-header,
    [data-fi-modal-id="database-notifications"] .fi-modal-header {
        position: relative;
        flex-shrink: 0;
        border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        background: #ffffff;
        padding: 16px;
        box-sizing: border-box;
    }

    .dark .fi-no-database .fi-modal-header,
    .dark [data-fi-modal-id="database-notifications"] .fi-modal-header {
        border-bottom-color: rgba(255, 255, 255, 0.08);
        background: #0b1329;
    }

    /* Header title & actions inside header */
    .fi-no-database .fi-modal-header > div:first-child,
    [data-fi-modal-id="database-notifications"] .fi-modal-header > div:first-child {
        display: flex;
        flex-direction: column;
        gap: 8px;
        width: 100%;
    }

    /* Title row wrapper */
    .fi-no-database .fi-modal-header .fi-modal-heading,
    [data-fi-modal-id="database-notifications"] .fi-modal-header .fi-modal-heading {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #0f172a;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -0.01em;
        line-height: 1.2;
        margin: 0;
    }

    .dark .fi-no-database .fi-modal-header .fi-modal-heading,
    .dark [data-fi-modal-id="database-notifications"] .fi-modal-header .fi-modal-heading {
        color: #f8fafc;
    }

    /* Count badge */
    .fi-no-database .fi-modal-header .fi-modal-heading .fi-badge,
    [data-fi-modal-id="database-notifications"] .fi-modal-header .fi-modal-heading .fi-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(59, 130, 246, 0.08);
        color: #2563eb;
        font-size: 10px;
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 999px;
        border: 1px solid rgba(59, 130, 246, 0.12);
        min-width: 18px;
        height: 18px;
        box-shadow: none;
    }

    .dark .fi-no-database .fi-modal-header .fi-modal-heading .fi-badge,
    .dark [data-fi-modal-id="database-notifications"] .fi-modal-header .fi-modal-heading .fi-badge {
        background-color: rgba(96, 165, 250, 0.15);
        color: #60a5fa;
        border-color: rgba(96, 165, 250, 0.2);
    }

    /* Close (✕) button */
    .fi-no-database .fi-modal-close-btn,
    [data-fi-modal-id="database-notifications"] .fi-modal-close-btn {
        position: absolute;
        right: 14px;
        top: 14px;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 6px;
        opacity: 0.5;
        cursor: pointer;
        transition: opacity 120ms ease, background-color 120ms ease;
        background: transparent;
        border: none;
    }

    .fi-no-database .fi-modal-close-btn:hover,
    [data-fi-modal-id="database-notifications"] .fi-modal-close-btn:hover {
        opacity: 0.8;
        background-color: rgba(15, 23, 42, 0.05);
    }

    .dark .fi-no-database .fi-modal-close-btn:hover,
    .dark [data-fi-modal-id="database-notifications"] .fi-modal-close-btn:hover {
        background-color: rgba(255, 255, 255, 0.08);
    }

    /* ── 3. Action Buttons ── */
    .fi-no-database .fi-ac,
    [data-fi-modal-id="database-notifications"] .fi-ac {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .fi-no-database .fi-ac a,
    .fi-no-database .fi-ac button,
    [data-fi-modal-id="database-notifications"] .fi-ac a,
    [data-fi-modal-id="database-notifications"] .fi-ac button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        height: 26px;
        padding: 0 10px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 6px;
        transition: background-color 120ms ease, color 120ms ease, border-color 120ms ease;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        white-space: nowrap;
    }

    /* "Tandai semua" button */
    .fi-no-database .fi-ac a:first-child,
    .fi-no-database .fi-ac button:first-child,
    [data-fi-modal-id="database-notifications"] .fi-ac a:first-child,
    [data-fi-modal-id="database-notifications"] .fi-ac button:first-child {
        background-color: #f1f5f9;
        color: #334155;
        border-color: rgba(203, 213, 225, 0.5);
    }

    .fi-no-database .fi-ac a:first-child:hover,
    .fi-no-database .fi-ac button:first-child:hover,
    [data-fi-modal-id="database-notifications"] .fi-ac a:first-child:hover,
    [data-fi-modal-id="database-notifications"] .fi-ac button:first-child:hover {
        background-color: #e2e8f0;
        color: #0f172a;
    }

    .dark .fi-no-database .fi-ac a:first-child,
    .dark .fi-no-database .fi-ac button:first-child,
    .dark [data-fi-modal-id="database-notifications"] .fi-ac a:first-child,
    .dark [data-fi-modal-id="database-notifications"] .fi-ac button:first-child {
        background-color: rgba(255, 255, 255, 0.04);
        color: #cbd5e1;
        border-color: rgba(255, 255, 255, 0.06);
    }

    .dark .fi-no-database .fi-ac a:first-child:hover,
    .dark .fi-no-database .fi-ac button:first-child:hover,
    .dark [data-fi-modal-id="database-notifications"] .fi-ac a:first-child:hover,
    .dark [data-fi-modal-id="database-notifications"] .fi-ac button:first-child:hover {
        background-color: rgba(255, 255, 255, 0.08);
        color: #ffffff;
    }

    /* "Bersihkan" button */
    .fi-no-database .fi-ac a:last-child:not(:first-child),
    .fi-no-database .fi-ac button:last-child:not(:first-child),
    [data-fi-modal-id="database-notifications"] .fi-ac a:last-child:not(:first-child),
    [data-fi-modal-id="database-notifications"] .fi-ac button:last-child:not(:first-child) {
        background-color: rgba(244, 63, 94, 0.05);
        color: #e11d48;
        border-color: rgba(244, 63, 94, 0.1);
        margin-left: auto;
    }

    .fi-no-database .fi-ac a:last-child:hover,
    .fi-no-database .fi-ac button:last-child:hover,
    [data-fi-modal-id="database-notifications"] .fi-ac a:last-child:hover,
    [data-fi-modal-id="database-notifications"] .fi-ac button:last-child:hover {
        background-color: #e11d48;
        color: #ffffff;
        border-color: #e11d48;
    }

    .dark .fi-no-database .fi-ac a:last-child,
    .dark .fi-no-database .fi-ac button:last-child,
    .dark [data-fi-modal-id="database-notifications"] .fi-ac a:last-child,
    .dark [data-fi-modal-id="database-notifications"] .fi-ac button:last-child {
        background-color: rgba(239, 68, 68, 0.08);
        color: #fca5a5;
        border-color: rgba(239, 68, 68, 0.15);
    }

    .dark .fi-no-database .fi-ac a:last-child:hover,
    .dark .fi-no-database .fi-ac button:last-child:hover,
    .dark [data-fi-modal-id="database-notifications"] .fi-ac a:last-child:hover,
    .dark [data-fi-modal-id="database-notifications"] .fi-ac button:last-child:hover {
        background-color: #ef4444;
        color: #ffffff;
        border-color: #ef4444;
    }

    /* ── 4. Scrollable Content Area ── */
    .fi-no-database .fi-modal-content,
    [data-fi-modal-id="database-notifications"] .fi-modal-content {
        flex: 1 1 auto;
        overflow-x: hidden;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 12px 0; /* Horizontal padding is 0 to let the scrollbar sit cleanly at the right edge */
        box-sizing: border-box;
        scrollbar-width: thin;
        scrollbar-color: rgba(156, 163, 175, 0.2) transparent;
        max-height: 300px !important; /* Limits the view dynamically to ~3 notifications, then scrolls */
    }

    .fi-no-database .fi-modal-content::-webkit-scrollbar,
    [data-fi-modal-id="database-notifications"] .fi-modal-content::-webkit-scrollbar {
        width: 5px;
    }

    .fi-no-database .fi-modal-content::-webkit-scrollbar-track,
    [data-fi-modal-id="database-notifications"] .fi-modal-content::-webkit-scrollbar-track {
        background: transparent;
    }

    .fi-no-database .fi-modal-content::-webkit-scrollbar-thumb,
    [data-fi-modal-id="database-notifications"] .fi-modal-content::-webkit-scrollbar-thumb {
        background-color: rgba(156, 163, 175, 0.2);
        border-radius: 99px;
    }

    /* ── 5. Notification Card Wrapper (Provides spacing from panel borders and scrollbar) ── */
    .fi-no-database .fi-no-notification-read-ctn,
    .fi-no-database .fi-no-notification-unread-ctn,
    [data-fi-modal-id="database-notifications"] .fi-no-notification-read-ctn,
    [data-fi-modal-id="database-notifications"] .fi-no-notification-unread-ctn {
        width: auto;
        margin: 0 16px;
        box-sizing: border-box;
        flex-shrink: 0;
    }

    /* ── 6. Notification Card ── */
    .fi-no-database .fi-no-notification,
    [data-fi-modal-id="database-notifications"] .fi-no-notification {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 12px;
        align-items: start;
        width: 100%;
        padding: 16px;
        border-radius: 12px;
        box-sizing: border-box;
        border: 1px solid rgba(226, 232, 240, 0.6);
        transition: background-color 150ms ease, border-color 150ms ease, box-shadow 150ms ease;
        cursor: default;
    }

    /* UNREAD Card Style (Soft blue background like GitHub/Vercel) */
    .fi-no-database .fi-no-notification-unread-ctn .fi-no-notification,
    [data-fi-modal-id="database-notifications"] .fi-no-notification-unread-ctn .fi-no-notification {
        background-color: #eff6ff;
        border-color: rgba(59, 130, 246, 0.12);
        box-shadow: 0 1px 2px rgba(59, 130, 246, 0.02);
    }

    .fi-no-database .fi-no-notification-unread-ctn .fi-no-notification:hover,
    [data-fi-modal-id="database-notifications"] .fi-no-notification-unread-ctn .fi-no-notification:hover {
        background-color: #e0f2fe;
        border-color: rgba(59, 130, 246, 0.22);
    }

    .dark .fi-no-database .fi-no-notification-unread-ctn .fi-no-notification,
    .dark [data-fi-modal-id="database-notifications"] .fi-no-notification-unread-ctn .fi-no-notification {
        background-color: rgba(30, 58, 138, 0.15);
        border-color: rgba(96, 165, 250, 0.15);
    }

    .dark .fi-no-database .fi-no-notification-unread-ctn .fi-no-notification:hover,
    .dark [data-fi-modal-id="database-notifications"] .fi-no-notification-unread-ctn .fi-no-notification:hover {
        background-color: rgba(30, 58, 138, 0.25);
        border-color: rgba(96, 165, 250, 0.25);
    }

    /* READ Card Style (White background) */
    .fi-no-database .fi-no-notification-read-ctn .fi-no-notification,
    [data-fi-modal-id="database-notifications"] .fi-no-notification-read-ctn .fi-no-notification {
        background-color: #ffffff;
        border-color: rgba(226, 232, 240, 0.8);
    }

    .fi-no-database .fi-no-notification-read-ctn .fi-no-notification:hover,
    [data-fi-modal-id="database-notifications"] .fi-no-notification-read-ctn .fi-no-notification:hover {
        background-color: #f8fafc;
        border-color: rgba(203, 213, 225, 0.8);
    }

    .dark .fi-no-database .fi-no-notification-read-ctn .fi-no-notification,
    .dark [data-fi-modal-id="database-notifications"] .fi-no-notification-read-ctn .fi-no-notification {
        background-color: rgba(255, 255, 255, 0.02);
        border-color: rgba(255, 255, 255, 0.04);
    }

    .dark .fi-no-database .fi-no-notification-read-ctn .fi-no-notification:hover,
    .dark [data-fi-modal-id="database-notifications"] .fi-no-notification-read-ctn .fi-no-notification:hover {
        background-color: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.08);
    }

    /* ── 7. Card Content Elements ── */

    /* Left status icon container */
    .fi-no-database .fi-no-notification .fi-icon-btn,
    .fi-no-database .fi-no-notification .fi-no-notification-icon,
    [data-fi-modal-id="database-notifications"] .fi-no-notification .fi-icon-btn,
    [data-fi-modal-id="database-notifications"] .fi-no-notification .fi-no-notification-icon {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background-color: rgba(148, 163, 184, 0.08);
        color: #475569;
    }

    .dark .fi-no-database .fi-no-notification .fi-icon-btn,
    .dark .fi-no-database .fi-no-notification .fi-no-notification-icon,
    .dark [data-fi-modal-id="database-notifications"] .fi-no-notification .fi-icon-btn,
    .dark [data-fi-modal-id="database-notifications"] .fi-no-notification .fi-no-notification-icon {
        background-color: rgba(255, 255, 255, 0.05);
        color: #94a3b8;
    }

    /* Middle text wrapper container */
    .fi-no-database .fi-no-notification > div:not(:last-child):not(.fi-icon-btn):not(.fi-no-notification-icon),
    [data-fi-modal-id="database-notifications"] .fi-no-notification > div:not(:last-child):not(.fi-icon-btn):not(.fi-no-notification-icon) {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 0; /* Prevents text overflow breaking layouts */
    }

    /* Title text styling */
    .fi-no-database .fi-no-notification-title,
    [data-fi-modal-id="database-notifications"] .fi-no-notification-title {
        color: #1e293b;
        font-size: 13.5px;
        font-weight: 600;
        line-height: 1.4;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: normal;
    }

    .dark .fi-no-database .fi-no-notification-title,
    .dark [data-fi-modal-id="database-notifications"] .fi-no-notification-title {
        color: #f1f5f9;
    }

    /* Body description text */
    .fi-no-database .fi-no-notification-body,
    [data-fi-modal-id="database-notifications"] .fi-no-notification-body {
        color: #475569;
        font-size: 12.5px;
        font-weight: 400;
        line-height: 1.5;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: normal;
    }

    .dark .fi-no-database .fi-no-notification-body,
    .dark [data-fi-modal-id="database-notifications"] .fi-no-notification-body {
        color: #94a3b8;
    }

    /* Timestamp metadata text */
    .fi-no-database .fi-no-notification-date,
    [data-fi-modal-id="database-notifications"] .fi-no-notification-date {
        color: #94a3b8;
        font-size: 11px;
        font-weight: 500;
        margin: 0;
        display: block;
    }

    .dark .fi-no-database .fi-no-notification-date,
    .dark [data-fi-modal-id="database-notifications"] .fi-no-notification-date {
        color: #64748b;
    }

    /* Clean Card Dismiss Button (✕) */
    .fi-no-database .fi-no-notification button[wire\:click],
    [data-fi-modal-id="database-notifications"] .fi-no-notification button[wire\:click] {
        grid-column: 3;
        align-self: start;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        border: none;
        background: transparent;
        cursor: pointer;
        opacity: 0;
        transform: scale(0.9);
        transition: opacity 120ms ease, transform 120ms ease, background-color 120ms ease;
    }

    /* Show dismiss button on hover of the card */
    .fi-no-database .fi-no-notification:hover button[wire\:click],
    [data-fi-modal-id="database-notifications"] .fi-no-notification:hover button[wire\:click] {
        opacity: 0.5;
        transform: scale(1);
    }

    .fi-no-database .fi-no-notification button[wire\:click]:hover,
    [data-fi-modal-id="database-notifications"] .fi-no-notification button[wire\:click]:hover {
        opacity: 1;
        background-color: rgba(15, 23, 42, 0.06);
    }

    .dark .fi-no-database .fi-no-notification button[wire\:click]:hover,
    .dark [data-fi-modal-id="database-notifications"] .fi-no-notification button[wire\:click]:hover {
        background-color: rgba(255, 255, 255, 0.08);
    }

    /* ── 8. Empty State Design ── */
    .fi-no-database .fi-modal-content > div:only-child,
    [data-fi-modal-id="database-notifications"] .fi-modal-content > div:only-child {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 32px 24px;
        width: 100%;
        min-height: 14rem;
        gap: 0;
        box-sizing: border-box;
    }

    .fi-no-database .fi-modal-icon-bg,
    [data-fi-modal-id="database-notifications"] .fi-modal-icon-bg {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 8px;
        background-color: #f1f5f9;
        color: #94a3b8;
        margin-bottom: 12px;
        box-shadow: none;
    }

    .dark .fi-no-database .fi-modal-icon-bg,
    .dark [data-fi-modal-id="database-notifications"] .fi-modal-icon-bg {
        background-color: rgba(255, 255, 255, 0.04);
        color: #4b5563;
    }

    .fi-no-database .fi-modal-content > div > .fi-modal-heading,
    [data-fi-modal-id="database-notifications"] .fi-modal-content > div > .fi-modal-heading {
        font-size: 13.5px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
        letter-spacing: -0.01em;
    }

    .dark .fi-no-database .fi-modal-content > div > .fi-modal-heading,
    .dark [data-fi-modal-id="database-notifications"] .fi-modal-content > div > .fi-modal-heading {
        color: #f1f5f9;
    }

    .fi-no-database .fi-modal-content > div > .fi-modal-description,
    [data-fi-modal-id="database-notifications"] .fi-modal-content > div > .fi-modal-description {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
        max-width: 240px;
        line-height: 1.5;
    }

    .dark .fi-no-database .fi-modal-content > div > .fi-modal-description,
    .dark [data-fi-modal-id="database-notifications"] .fi-modal-content > div > .fi-modal-description {
        color: #4b5563;
    }

    /* ============================================================
       FORMS & SECTIONS
       ============================================================ */

    .fi-section {
        border-radius: 16px !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        background-color: var(--bg-white) !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.01), 0 1px 2px rgba(0, 0, 0, 0.02) !important;
        overflow: hidden !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
    }

    .dark .fi-section {
        border-color: rgba(51, 65, 85, 0.5) !important;
        background-color: #0d1527 !important;
        box-shadow: none !important;
    }

    .fi-section-header {
        padding: 1.25rem 1.5rem !important;
        border-bottom: 1px solid var(--border-light) !important;
        background-color: #fcfdfe !important;
    }

    .dark .fi-section-header {
        border-bottom-color: rgba(51, 65, 85, 0.4) !important;
        background-color: #10192e !important;
    }

    .fi-section-header-title {
        font-size: 0.95rem !important;
        font-weight: 750 !important;
        color: var(--text-primary-light) !important;
        letter-spacing: -0.01em !important;
    }

    .dark .fi-section-header-title {
        color: #f1f5f9 !important;
    }

    .fi-section-content {
        padding: 1.5rem !important;
    }

    /* ============================================================
       INPUT STYLING
       ============================================================ */
    .fi-input-wrp {
        border-radius: 8px !important;
        border: 1px solid rgba(203, 213, 225, 0.8) !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
        transition: all 0.2s ease !important;
        background-color: var(--bg-white) !important;
    }

    .dark .fi-input-wrp {
        border-color: rgba(71, 85, 105, 0.6) !important;
        background-color: #090f1d !important;
    }

    .fi-input-wrp:focus-within {
        border-color: var(--primary-blue-dark) !important;
        box-shadow: 0 0 0 1px var(--primary-blue-dark), 0 4px 12px rgba(37, 99, 235, 0.05) !important;
    }

    .dark .fi-input-wrp:focus-within {
        border-color: var(--primary-blue) !important;
        box-shadow: 0 0 0 1px var(--primary-blue), 0 4px 12px rgba(59, 130, 246, 0.1) !important;
    }

    .fi-input {
        font-size: 0.875rem !important;
        color: var(--text-primary-light) !important;
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }

    .dark .fi-input {
        color: #f1f5f9 !important;
    }

    /* ============================================================
       BUTTONS
       ============================================================ */
    .fi-btn {
        border-radius: 8px !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        padding: 0.5rem 1rem !important;
        transition: all 0.2s var(--transition-smooth) !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
        outline: none !important;
    }

    .fi-btn:focus-visible {
        outline: 2px solid var(--primary-blue) !important;
        outline-offset: 2px !important;
    }

    .fi-btn-color-primary,
    .fi-btn[type="submit"] {
        background-color: var(--primary-blue-dark) !important;
        color: #ffffff !important;
        border: 1px solid var(--primary-blue-dark) !important;
    }

    .fi-btn-color-primary:hover,
    .fi-btn[type="submit"]:hover {
        background-color: #1d4ed8 !important;
        border-color: #1d4ed8 !important;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.2) !important;
        transform: translateY(-1px) !important;
    }

    .dark .fi-btn-color-primary,
    .dark .fi-btn[type="submit"] {
        background-color: var(--primary-blue) !important;
        border-color: var(--primary-blue) !important;
    }

    .dark .fi-btn-color-primary:hover,
    .dark .fi-btn[type="submit"]:hover {
        background-color: var(--primary-blue-dark) !important;
        border-color: var(--primary-blue-dark) !important;
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.25) !important;
    }

    .fi-btn-color-gray {
        background-color: var(--bg-white) !important;
        color: var(--text-secondary-light) !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
    }

    .fi-btn-color-gray:hover {
        background-color: var(--bg-light-gray) !important;
        color: var(--text-primary-light) !important;
        border-color: rgba(203, 213, 225, 0.9) !important;
    }

    .dark .fi-btn-color-gray {
        background-color: rgba(30, 41, 59, 0.5) !important;
        color: #cbd5e1 !important;
        border-color: rgba(51, 65, 85, 0.6) !important;
    }

    .dark .fi-btn-color-gray:hover {
        background-color: rgba(30, 41, 59, 0.8) !important;
        color: #ffffff !important;
        border-color: rgba(71, 85, 105, 0.8) !important;
    }

    /* ============================================================
       TOPBAR
       ============================================================ */
    .fi-topbar {
        background-color: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border-bottom: 1px solid var(--border-light) !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.01) !important;
    }

    .dark .fi-topbar {
        background-color: rgba(15, 23, 42, 0.8) !important;
        border-bottom-color: rgba(51, 65, 85, 0.5) !important;
        box-shadow: none !important;
    }

    /* ============================================================
       TABLE STYLING
       ============================================================ */
    .fi-ta-ctn {
        border-radius: 16px !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        background-color: var(--bg-white) !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.01), 0 1px 2px rgba(0, 0, 0, 0.02) !important;
        overflow: hidden !important;
    }

    .dark .fi-ta-ctn {
        border-color: rgba(51, 65, 85, 0.5) !important;
        background-color: #0d1527 !important;
        box-shadow: none !important;
    }

    .fi-ta-header-cell {
        background-color: var(--bg-light-gray) !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.9) !important;
        padding-top: 0.85rem !important;
        padding-bottom: 0.85rem !important;
    }

    .dark .fi-ta-header-cell {
        background-color: #10192e !important;
        border-bottom-color: rgba(51, 65, 85, 0.4) !important;
    }

    .fi-ta-header-cell-label {
        font-size: 0.72rem !important;
        font-weight: 750 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        color: var(--text-secondary-light) !important;
    }

    .dark .fi-ta-header-cell-label {
        color: #cbd5e1 !important;
    }

    .fi-ta-row:hover {
        background-color: #fafbfd !important;
    }

    .dark .fi-ta-row:hover {
        background-color: #111a31 !important;
    }

    .fi-ta-cell {
        border-bottom: 1px solid rgba(226, 232, 240, 0.5) !important;
        font-size: 0.85rem !important;
        color: #334155 !important;
    }

    .dark .fi-ta-cell {
        border-bottom-color: rgba(51, 65, 85, 0.2) !important;
        color: #cbd5e1 !important;
    }

    /* ============================================================
       PROFILE CARD (INJECTED VIA JAVASCRIPT)
       ============================================================ */
    .simpad-profile-card {
        display: flex !important;
        align-items: center !important;
        gap: 1.5rem !important;
        background: var(--bg-white) !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        border-radius: 16px !important;
        padding: 1.5rem !important;
        margin-bottom: 2rem !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.01), 0 1px 2px rgba(0, 0, 0, 0.02) !important;
    }

    .dark .simpad-profile-card {
        background: #0d1527 !important;
        border-color: rgba(51, 65, 85, 0.5) !important;
        box-shadow: none !important;
    }

    @media (max-width: 640px) {
        .simpad-profile-card {
            flex-direction: column !important;
            text-align: center !important;
            gap: 1rem !important;
        }
    }

    .simpad-profile-card__avatar {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 64px !important;
        height: 64px !important;
        border-radius: 999px !important;
        background: var(--sidebar-active-gradient) !important;
        color: #ffffff !important;
        font-family: 'Outfit', sans-serif !important;
        font-size: 1.5rem !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25) !important;
        flex-shrink: 0 !important;
        overflow: hidden !important;
    }

    .simpad-profile-card__avatar img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        border-radius: 999px !important;
    }

    .simpad-profile-card__name {
        font-family: 'Outfit', sans-serif !important;
        font-size: 1.5rem !important;
        font-weight: 800 !important;
        letter-spacing: -0.02em !important;
        color: var(--text-primary-light) !important;
        line-height: 1.2 !important;
    }

    .dark .simpad-profile-card__name {
        color: #f8fafc !important;
    }

    .simpad-profile-card__meta {
        display: flex !important;
        align-items: center !important;
        gap: 0.75rem !important;
        margin-top: 0.35rem !important;
        flex-wrap: wrap !important;
    }

    @media (max-width: 640px) {
        .simpad-profile-card__meta {
            justify-content: center !important;
        }
    }

    .simpad-profile-card__badge {
        font-size: 0.72rem !important;
        font-weight: 750 !important;
        color: var(--primary-blue-dark) !important;
        background: rgba(37, 99, 235, 0.08) !important;
        padding: 0.25rem 0.65rem !important;
        border-radius: 999px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
    }

    .dark .simpad-profile-card__badge {
        color: #60a5fa !important;
        background: rgba(59, 130, 246, 0.15) !important;
    }

    .simpad-profile-card__email {
        font-size: 0.8125rem !important;
        color: var(--text-muted-light) !important;
    }

    .dark .simpad-profile-card__email {
        color: #94a3b8 !important;
    }

    .simpad-profile-card__google-status {
        display: inline-flex !important;
        align-items: center !important;
        font-size: 0.72rem !important;
        font-weight: 700 !important;
        padding: 0.25rem 0.65rem !important;
        border-radius: 999px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        gap: 0.25rem !important;
    }

    .simpad-profile-card__google-status.linked {
        color: #10b981 !important;
        background: rgba(16, 185, 129, 0.08) !important;
        border: 1px solid rgba(16, 185, 129, 0.15) !important;
    }

    .dark .simpad-profile-card__google-status.linked {
        color: #34d399 !important;
        background: rgba(52, 211, 153, 0.1) !important;
    }

    .simpad-profile-card__google-status.unlinked {
        color: #f59e0b !important;
        background: rgba(245, 158, 11, 0.08) !important;
        border: 1px solid rgba(245, 158, 11, 0.15) !important;
    }

    .dark .simpad-profile-card__google-status.unlinked {
        color: #fbbf24 !important;
        background: rgba(251, 191, 36, 0.1) !important;
    }

    .simpad-profile-card__google-status svg {
        width: 0.85rem !important;
        height: 0.85rem !important;
        flex-shrink: 0 !important;
    }

    /* Target only class hour package and schedule break action modals */
    .fi-modal:has(.custom-class-hour-modal) {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .custom-class-hour-modal {
        height: 75vh !important;
        max-height: 600px !important;
        min-height: 400px !important;
        overflow-y: scroll !important;
    }
</style>

<script>
    (function () {
        'use strict';

        // ============================================================
        // UTILITY: DEBOUNCE
        // ============================================================
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // ============================================================
        // AUDIO CONTEXT & NOTIFICATION CHIME
        // ============================================================
        let audioContext = null;

        function getAudioContext() {
            if (!audioContext) {
                try {
                    audioContext = new (window.AudioContext || window.webkitAudioContext)();
                } catch (e) {
                    console.warn('Web Audio API not supported:', e);
                    return null;
                }
            }
            if (audioContext.state === 'suspended') {
                audioContext.resume().catch(() => { });
            }
            return audioContext;
        }

        function playNotificationSound() {
            const ctx = getAudioContext();
            if (!ctx) return;

            try {
                const now = ctx.currentTime;

                function playChime(frequency, startTime, duration) {
                    const osc = ctx.createOscillator();
                    const gainNode = ctx.createGain();

                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(frequency, startTime);

                    gainNode.gain.setValueAtTime(0, startTime);
                    gainNode.gain.linearRampToValueAtTime(0.18, startTime + 0.02);
                    gainNode.gain.exponentialRampToValueAtTime(0.0001, startTime + duration);

                    osc.connect(gainNode);
                    gainNode.connect(ctx.destination);

                    osc.start(startTime);
                    osc.stop(startTime + duration);
                }

                // Slack-style double chime: C5 -> E5
                playChime(523.25, now, 0.25);
                playChime(659.25, now + 0.06, 0.35);
            } catch (e) {
                console.warn('Notification chime failed:', e);
            }
        }

        // ============================================================
        // NOTIFICATION BADGE POLLING
        // ============================================================
        let lastUnreadCount = 0;
        let isFirstCheck = true;
        let pollInterval = null;

        function getCurrentBadgeCount() {
            const badges = document.querySelectorAll(
                '.fi-topbar-database-notifications-btn .fi-badge span, ' +
                '.fi-sidebar-database-notifications-btn-badge-ctn .fi-badge span, ' +
                '.fi-no-database .fi-modal-header .fi-badge'
            );

            if (badges.length > 0) {
                const text = badges[0].textContent.trim();
                const count = parseInt(text, 10);
                return isNaN(count) ? 0 : count;
            }
            return 0;
        }

        const checkBadgeCount = debounce(() => {
            const count = getCurrentBadgeCount();

            if (count > lastUnreadCount && !isFirstCheck) {
                playNotificationSound();
            }

            lastUnreadCount = count;
            isFirstCheck = false;
        }, 300);

        function startPolling() {
            if (pollInterval) clearInterval(pollInterval);
            checkBadgeCount(); // Immediate first check
            pollInterval = setInterval(checkBadgeCount, 2000);
        }

        function stopPolling() {
            if (pollInterval) {
                clearInterval(pollInterval);
                pollInterval = null;
            }
        }

        // ============================================================
        // AUDIO CONTEXT INITIALIZATION ON USER INTERACTION
        // ============================================================
        function initAudioOnInteraction() {
            getAudioContext();
            document.removeEventListener('click', initAudioOnInteraction);
            document.removeEventListener('keydown', initAudioOnInteraction);
            document.removeEventListener('touchstart', initAudioOnInteraction);
        }

        // ============================================================
        // LIVE WIRE EVENT LISTENER
        // ============================================================
        function setupLivewireListener() {
            if (typeof window.Livewire !== 'undefined') {
                window.Livewire.on('notification-sent', () => {
                    playNotificationSound();
                });
            } else {
                // Retry if Livewire hasn't loaded yet
                setTimeout(setupLivewireListener, 500);
            }
        }

        // ============================================================
        // PROFILE PAGE: INJECT CUSTOM PROFILE CARD
        // ============================================================
        function getMetaContent(name) {
            const meta = document.querySelector(`meta[name="${name}"]`);
            return meta ? meta.getAttribute('content') : '';
        }

        function renderProfileCard() {
            // Only run on profile page
            if (!window.location.pathname.endsWith('/profile')) return;

            // Prevent duplicate injection
            if (document.querySelector('.simpad-profile-card')) return;

            const headerCtn = document.querySelector('.fi-header');
            const mainForm = document.querySelector('.fi-main-content form, .fi-content form');

            if (!headerCtn || !mainForm) {
                // DOM not ready, retry
                setTimeout(renderProfileCard, 200);
                return;
            }

            // Hide default header
            headerCtn.style.display = 'none';

            // Read data from meta tags (passed from Blade)
            const userName = getMetaContent('simpad-user-name') || 'Pengguna';
            const userRole = getMetaContent('simpad-user-role') || 'Pengguna';
            const userEmail = getMetaContent('simpad-user-email') || '';
            const initials = getMetaContent('simpad-user-initials') || 'SP';
            const avatarUrl = getMetaContent('simpad-user-avatar');
            const isGoogleLinked = getMetaContent('simpad-user-google-linked') === '1';

            // Build profile card
            const profileCard = document.createElement('div');
            profileCard.className = 'simpad-profile-card';

            // Avatar section
            const avatarDiv = document.createElement('div');
            avatarDiv.className = 'simpad-profile-card__avatar';
            if (avatarUrl) {
                const img = document.createElement('img');
                img.src = avatarUrl;
                img.alt = userName;
                img.loading = 'lazy';
                avatarDiv.appendChild(img);
            } else {
                avatarDiv.textContent = initials;
            }

            // Info section
            const infoDiv = document.createElement('div');
            infoDiv.className = 'simpad-profile-card__info';

            const nameH1 = document.createElement('h1');
            nameH1.className = 'simpad-profile-card__name';
            nameH1.textContent = userName;

            const metaDiv = document.createElement('div');
            metaDiv.className = 'simpad-profile-card__meta';

            // Role badge
            const badge = document.createElement('span');
            badge.className = 'simpad-profile-card__badge';
            badge.textContent = userRole;

            // Email
            const emailSpan = document.createElement('span');
            emailSpan.className = 'simpad-profile-card__email';
            emailSpan.textContent = userEmail;

            metaDiv.appendChild(badge);
            metaDiv.appendChild(emailSpan);

            // Google status
            const googleStatus = document.createElement('span');
            googleStatus.className = 'simpad-profile-card__google-status';

            if (isGoogleLinked) {
                googleStatus.classList.add('linked');
                googleStatus.innerHTML = `
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12.24 10.285V13.4h6.887c-.275 1.565-1.88 4.604-6.887 4.604-4.33 0-7.866-3.577-7.866-8s3.536-8 7.866-8c2.46 0 4.105 1.025 5.047 1.926l2.427-2.334C17.955 2.192 15.34 1 12.24 1 6.12 1 1.16 5.92 1.16 12s4.96 11 11.08 11c6.39 0 10.646-4.414 10.646-10.725 0-.728-.078-1.284-.177-1.99H12.24z"/>
                    </svg>
                    Google Connected
                `;
            } else {
                googleStatus.classList.add('unlinked');
                googleStatus.textContent = 'Google Unlinked';
            }

            metaDiv.appendChild(googleStatus);
            infoDiv.appendChild(nameH1);
            infoDiv.appendChild(metaDiv);

            profileCard.appendChild(avatarDiv);
            profileCard.appendChild(infoDiv);

            // Insert before form
            mainForm.parentNode.insertBefore(profileCard, mainForm);
        }

        // ============================================================
        // INITIALIZATION
        // ============================================================
        function init() {
            // Setup audio on first interaction
            document.addEventListener('click', initAudioOnInteraction, { once: true });
            document.addEventListener('keydown', initAudioOnInteraction, { once: true });
            document.addEventListener('touchstart', initAudioOnInteraction, { once: true });

            // Start notification polling
            startPolling();

            // Setup Livewire listener
            setupLivewireListener();

            // Render profile card if on profile page
            renderProfileCard();

            // Handle page visibility changes
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    stopPolling();
                } else {
                    isFirstCheck = true; // Reset to avoid false chime on return
                    startPolling();
                }
            });
        }

        // Run on DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>