@php
    $user = auth()->user();
    $userName = $user ? $user->name : 'Pengguna SIMPAD';
    $userRole = match($user?->role) {
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
    
    // Initials calculation
    $initials = '';
    if ($user) {
        $words = explode(' ', trim($user->name));
        $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) && !empty($words[1]) ? substr($words[1], 0, 1) : ''));
    } else {
        $initials = 'SP';
    }
@endphp
<!-- Premium Sidebar Theme Style Hook -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

    /* Global Typography integration */
    body, .fi-body, .fi-sidebar, .fi-sidebar-header, .fi-sidebar-item-button {
        font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif !important;
    }

    /* ─── Sidebar Wrapper ─────────────────────────── */
    .fi-sidebar {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.95) 100%) !important;
        backdrop-filter: blur(20px) !important;
        border-right: 1px solid rgba(226, 232, 240, 0.8) !important;
        box-shadow: 4px 0 24px rgba(15, 23, 42, 0.015) !important;
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    /* Dark Mode Sidebar Wrapper */
    .dark .fi-sidebar {
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.98) 0%, rgba(9, 15, 30, 0.98) 100%) !important;
        border-right: 1px solid rgba(30, 41, 59, 0.6) !important;
        box-shadow: 4px 0 30px rgba(0, 0, 0, 0.25) !important;
    }

    /* ─── Sidebar Header & Brand Name ─────────────── */
    .fi-sidebar-header {
        border-bottom: 1px dashed rgba(226, 232, 240, 0.8) !important;
        padding-top: 1.75rem !important;
        padding-bottom: 1.75rem !important;
        background: transparent !important;
    }
    .dark .fi-sidebar-header {
        border-bottom: 1px dashed rgba(51, 65, 85, 0.4) !important;
    }

    .fi-sidebar-header a, .fi-sidebar-header-title {
        font-family: 'Outfit', sans-serif !important;
        font-weight: 800 !important;
        letter-spacing: 0.075em !important;
        text-transform: uppercase;
        background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #8b5cf6 100%) !important;
        -webkit-background-clip: text !important;
        -webkit-text-fill-color: transparent !important;
        filter: drop-shadow(0 2px 8px rgba(99, 102, 241, 0.15)) !important;
        font-size: 1.25rem !important;
    }

    /* ─── Navigation Groups ────────────────────────── */
    .fi-sidebar-group-header-label {
        font-family: 'Outfit', sans-serif !important;
        text-transform: uppercase !important;
        font-size: 0.72rem !important;
        letter-spacing: 0.12em !important;
        font-weight: 700 !important;
        color: #64748b !important;
        padding-left: 1.25rem !important;
        opacity: 0.85;
    }
    .dark .fi-sidebar-group-header-label {
        color: #94a3b8 !important;
    }

    /* ─── Navigation Items / Links ─────────────────── */
    .fi-sidebar-item-button {
        border-radius: 12px !important;
        margin: 0.2rem 0.75rem !important;
        padding-top: 0.65rem !important;
        padding-bottom: 0.65rem !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
        color: #475569 !important;
        border: 1px solid transparent !important;
    }
    .dark .fi-sidebar-item-button {
        color: #94a3b8 !important;
    }

    /* Hover State (Non-Active) */
    .fi-sidebar-item-button:hover:not(.fi-sidebar-item-button-active):not([class*="-active"]) {
        background: rgba(241, 245, 249, 0.9) !important;
        color: #0f172a !important;
        transform: translateX(5px) !important;
        border-color: rgba(226, 232, 240, 0.5) !important;
    }
    .dark .fi-sidebar-item-button:hover:not(.fi-sidebar-item-button-active):not([class*="-active"]) {
        background: rgba(30, 41, 59, 0.5) !important;
        color: #f8fafc !important;
        transform: translateX(5px) !important;
        border-color: rgba(51, 65, 85, 0.3) !important;
    }

    /* Active State styling */
    .fi-sidebar-item-button.fi-sidebar-item-button-active,
    .fi-sidebar-item-button[class*="-active"],
    .fi-sidebar-item-button[aria-current="page"] {
        background: linear-gradient(135deg, #3b82f6 0%, #4f46e5 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px 0 rgba(79, 70, 229, 0.25) !important;
        font-weight: 700 !important;
        border: 1px solid rgba(99, 102, 241, 0.2) !important;
    }
    .dark .fi-sidebar-item-button.fi-sidebar-item-button-active,
    .dark .fi-sidebar-item-button[class*="-active"] {
        box-shadow: 0 4px 20px 0 rgba(79, 70, 229, 0.2) !important;
    }

    /* Sidebar Item Icon styling */
    .fi-sidebar-item-icon {
        transition: all 0.2s ease !important;
        color: #64748b !important;
    }
    .dark .fi-sidebar-item-icon {
        color: #64748b !important;
    }

    /* Active Icon state */
    .fi-sidebar-item-button.fi-sidebar-item-button-active .fi-sidebar-item-icon,
    .fi-sidebar-item-button[class*="-active"] .fi-sidebar-item-icon {
        color: #ffffff !important;
        transform: scale(1.1) rotate(1deg) !important;
    }

    /* Hover Icon state */
    .fi-sidebar-item-button:hover:not(.fi-sidebar-item-button-active) .fi-sidebar-item-icon {
        color: #3b82f6 !important;
    }
    .dark .fi-sidebar-item-button:hover:not(.fi-sidebar-item-button-active) .fi-sidebar-item-icon {
        color: #60a5fa !important;
    }

    /* Subtle divider spacing */
    .fi-sidebar-nav {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }

    /* Notification trigger in sidebar */
    .fi-sidebar-database-notifications-btn {
        display: flex !important;
        align-items: center !important;
        gap: 0.75rem !important;
        width: calc(100% - 1.5rem) !important;
        min-height: 2.75rem !important;
        margin: 0.2rem 0.75rem !important; /* Match other navigation items */
        padding: 0.65rem 0.85rem !important;
        border-radius: 12px !important; /* Match 12px radius of other navigation items */
        border: 1px solid rgba(226, 232, 240, 0.75) !important;
        background: rgba(255, 255, 255, 0.78) !important;
        color: #334155 !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
        transition: background-color 160ms ease, border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease !important;
    }

    /* Style button when sidebar is collapsed (label is hidden via display: none) */
    .fi-sidebar-database-notifications-btn:has(.fi-sidebar-database-notifications-btn-label[style*="display: none"]) {
        justify-content: center !important;
        padding: 0.65rem 0 !important;
    }

    .fi-sidebar-database-notifications-btn:hover {
        transform: translateX(5px) !important; /* Match translateX of other navigation items */
        border-color: rgba(59, 130, 246, 0.35) !important;
        background: #ffffff !important;
        box-shadow: 0 8px 22px rgba(37, 99, 235, 0.10) !important;
    }

    /* Keep translation normal when collapsed on hover to prevent layout shifting */
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
        color: #2563eb !important;
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
        font-weight: 600 !important; /* Match 600 weight of other menu items */
        font-size: 0.875rem !important;
    }

    .fi-sidebar-database-notifications-btn-badge-ctn {
        margin-left: auto !important;
        flex-shrink: 0 !important;
    }

    .fi-sidebar-database-notifications-btn-badge-ctn .fi-badge,
    .fi-topbar-database-notifications-btn .fi-badge {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 1.35rem !important;
        height: 1.35rem !important;
        padding: 0 0.35rem !important;
        border-radius: 999px !important;
        background: #2563eb !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25) !important;
    }

    .fi-sidebar-database-notifications-btn-badge-ctn .fi-badge span,
    .fi-topbar-database-notifications-btn .fi-badge span {
        line-height: 1 !important;
        font-size: 0.68rem !important;
        font-weight: 800 !important;
    }

    .fi-topbar-database-notifications-btn {
        border-radius: 8px !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        background: rgba(255, 255, 255, 0.9) !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05) !important;
    }

    .dark .fi-topbar-database-notifications-btn {
        border-color: rgba(51, 65, 85, 0.85) !important;
        background: rgba(15, 23, 42, 0.8) !important;
    }

    /* ────────────────────────────────────────────────────────── */
    /* DATABASE NOTIFICATIONS DROPDOWN (Vercel & Linear Redesign) */
    /* ────────────────────────────────────────────────────────── */
    
    /* 1. Modal Window & Responsiveness */
    .fi-no-database .fi-modal-window {
        position: fixed !important;
        top: 4.75rem !important; /* Float below header bar */
        right: 1.5rem !important;
        bottom: auto !important;
        left: auto !important;
        width: 440px !important; /* Desktop: Width 440px */
        max-width: 440px !important;
        height: auto !important;
        max-height: 70vh !important; /* Tinggi maksimal 70vh */
        border-radius: 16px !important; /* Border radius 16px */
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        background: #ffffff !important;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.04), 0 4px 12px rgba(0, 0, 0, 0.02) !important; /* Shadow lembut */
        box-sizing: border-box !important;
        overflow: hidden !important;
        margin-top: 0 !important;
        display: flex !important;
        flex-direction: column !important;
    }

    .dark .fi-no-database .fi-modal-window {
        border-color: rgba(51, 65, 85, 0.5) !important;
        background: #090f1e !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4) !important;
    }

    /* Tablet Responsive */
    @media (max-width: 768px) {
        .fi-no-database .fi-modal-window {
            width: 90% !important; /* Tablet: Width 90% */
            max-width: 90% !important;
            right: 5% !important;
            left: 5% !important;
            top: 5rem !important;
            border-radius: 16px !important;
        }
    }

    /* Mobile Responsive (Floating bottom sheet style) */
    @media (max-width: 640px) {
        .fi-no-database .fi-modal-window {
            width: calc(100% - 1.5rem) !important; /* Mobile: Full width with safety margin */
            max-width: calc(100% - 1.5rem) !important;
            right: 0.75rem !important;
            left: 0.75rem !important;
            top: auto !important;
            bottom: 0.75rem !important;
            border-radius: 16px !important;
        }
    }

    /* 2. Modal Header & Close Button */
    .fi-no-database .fi-modal-header {
        position: sticky !important;
        top: 0 !important;
        z-index: 20 !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.7) !important; /* Thin divider line */
        background: rgba(255, 255, 255, 0.94) !important;
        backdrop-filter: blur(12px) !important;
        padding: 1.25rem 1.25rem 1rem 1.25rem !important;
        padding-right: 3.5rem !important; /* Keep room for the close button */
        box-sizing: border-box !important;
    }

    .dark .fi-no-database .fi-modal-header {
        border-bottom-color: rgba(51, 65, 85, 0.35) !important;
        background: rgba(9, 15, 30, 0.94) !important;
    }

    .fi-no-database .fi-modal-close-btn {
        position: absolute !important;
        right: 1.25rem !important;
        top: 1.25rem !important;
        z-index: 9999 !important; /* Force on top of header contents */
        pointer-events: auto !important;
        cursor: pointer !important;
        opacity: 0.45 !important;
        transition: opacity 200ms ease !important;
    }

    .fi-no-database .fi-modal-close-btn:hover {
        opacity: 0.8 !important;
    }

    /* Header title & badge */
    .fi-no-database .fi-modal-header .fi-modal-heading {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 0.5rem !important;
        color: #0f172a !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-size: 18px !important; /* Judul lebih besar (18px) */
        font-weight: 750 !important;
        width: max-content !important;
        max-width: calc(100vw - 8rem) !important;
    }

    .dark .fi-no-database .fi-modal-header .fi-modal-heading {
        color: #f8fafc !important;
    }

    /* Small badge next to title */
    .fi-no-database .fi-modal-header .fi-modal-heading .fi-badge {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background-color: rgba(37, 99, 235, 0.08) !important;
        color: #2563eb !important;
        font-size: 11px !important; /* Badge jumlah notifikasi kecil */
        font-weight: 750 !important;
        padding: 0 0.4rem !important;
        border-radius: 999px !important;
        border: 1px solid rgba(37, 99, 235, 0.12) !important;
        min-width: 1.2rem !important;
        height: 1.2rem !important;
        margin-left: 0.35rem !important;
        box-shadow: none !important;
    }

    .dark .fi-no-database .fi-modal-header .fi-modal-heading .fi-badge {
        background-color: rgba(96, 165, 250, 0.08) !important;
        color: #60a5fa !important;
        border-color: rgba(96, 165, 250, 0.15) !important;
    }

    /* Wrapper for header title & actions */
    .fi-no-database .fi-modal-header > div {
        display: flex !important;
        flex-direction: column !important;
        gap: 0.65rem !important;
        width: 100% !important;
    }

    /* 3. Action Buttons */
    .fi-no-database .fi-ac {
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        margin-top: 0.15rem !important;
        border-top: none !important;
        padding-top: 0 !important;
        justify-content: flex-start !important;
    }

    .fi-no-database .fi-ac a,
    .fi-no-database .fi-ac button {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        height: 34px !important; /* Height 34-36px */
        padding: 0 0.85rem !important;
        font-size: 13px !important; /* Font 13-14px */
        font-weight: 600 !important;
        border-radius: 8px !important; /* Rounded 8px */
        transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1) !important; /* Smooth transition 200ms */
        text-decoration: none !important;
        border: 1px solid transparent !important;
        cursor: pointer !important;
        white-space: nowrap !important;
    }

    /* ✓ Tandai semua */
    .fi-no-database .fi-ac a:first-child,
    .fi-no-database .fi-ac button:first-child {
        background-color: #F5F9FF !important; /* Light blue */
        color: #2563eb !important;
        border: 1px solid rgba(59, 130, 246, 0.15) !important;
    }

    .fi-no-database .fi-ac a:first-child:hover,
    .fi-no-database .fi-ac button:first-child:hover {
        background-color: #2563eb !important;
        color: #ffffff !important;
        border-color: #2563eb !important;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.08) !important;
    }

    .dark .fi-no-database .fi-ac a:first-child,
    .dark .fi-no-database .fi-ac button:first-child {
        background-color: rgba(96, 165, 250, 0.06) !important;
        color: #60a5fa !important;
        border-color: rgba(96, 165, 250, 0.15) !important;
    }

    .dark .fi-no-database .fi-ac a:first-child:hover,
    .dark .fi-no-database .fi-ac button:first-child:hover {
        background-color: #3b82f6 !important;
        color: #ffffff !important;
        border-color: #3b82f6 !important;
    }

    /* 🗑 Bersihkan */
    .fi-no-database .fi-ac a:last-child,
    .fi-no-database .fi-ac button:last-child {
        background-color: rgba(244, 63, 94, 0.04) !important;
        color: #e11d48 !important;
        border: 1px solid rgba(244, 63, 94, 0.1) !important;
    }

    .fi-no-database .fi-ac a:last-child:hover,
    .fi-no-database .fi-ac button:last-child:hover {
        background-color: #e11d48 !important;
        color: #ffffff !important;
        border-color: #e11d48 !important;
        box-shadow: 0 4px 10px rgba(225, 29, 72, 0.08) !important;
    }

    .dark .fi-no-database .fi-ac a:last-child,
    .dark .fi-no-database .fi-ac button:last-child {
        background-color: rgba(248, 113, 113, 0.06) !important;
        color: #f87171 !important;
        border-color: rgba(248, 113, 113, 0.15) !important;
    }

    .dark .fi-no-database .fi-ac a:last-child:hover,
    .dark .fi-no-database .fi-ac button:last-child:hover {
        background-color: #ef4444 !important;
        color: #ffffff !important;
        border-color: #ef4444 !important;
    }

    /* 4. Modal Scrollable Content Container */
    .fi-no-database .fi-modal-content {
        display: flex !important;
        flex-direction: column !important;
        padding: 1rem 1.25rem 1.25rem 1.25rem !important;
        overflow-x: hidden !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    /* Active List Container: has sticky header, scrollable body */
    .fi-no-database:has(.fi-modal-header) .fi-modal-content {
        align-items: stretch !important;
        justify-content: flex-start !important;
        text-align: left !important;
        overflow-y: auto !important;
        max-height: calc(70vh - 6.5rem) !important; /* Limit body height for scroll */
        gap: 0px !important; /* Handled by margins on items */
        padding: 0.85rem 1.25rem 1.25rem 1.25rem !important; /* Explicit padding to prevent cards from touching the edges */
        scrollbar-width: thin !important; /* Firefox support */
    }

    /* Custom thin scrollbar to prevent layout shift and maintain symmetric padding */
    .fi-no-database .fi-modal-content::-webkit-scrollbar {
        width: 6px !important;
        height: 6px !important;
    }

    .fi-no-database .fi-modal-content::-webkit-scrollbar-track {
        background: transparent !important;
    }

    .fi-no-database .fi-modal-content::-webkit-scrollbar-thumb {
        background-color: rgba(156, 163, 175, 0.35) !important;
        border-radius: 999px !important;
    }

    .dark .fi-no-database .fi-modal-content::-webkit-scrollbar-thumb {
        background-color: rgba(75, 85, 99, 0.45) !important;
    }

    /* 5. Notification Items Layout & Color States */
    .fi-no-database .fi-no-notification-read-ctn,
    .fi-no-database .fi-no-notification-unread-ctn {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    /* Card spacing 10px-12px */
    .fi-no-database .fi-no-notification-read-ctn + .fi-no-notification-read-ctn,
    .fi-no-database .fi-no-notification-unread-ctn + .fi-no-notification-unread-ctn,
    .fi-no-database .fi-no-notification-read-ctn + .fi-no-notification-unread-ctn,
    .fi-no-database .fi-no-notification-unread-ctn + .fi-no-notification-read-ctn {
        margin-top: 11px !important;
    }

    .fi-no-database .fi-no-notification {
        display: flex !important;
        flex-direction: row !important;
        align-items: flex-start !important;
        gap: 0.85rem !important; /* Left status icon layout */
        border-radius: 12px !important;
        padding: 14px 16px !important; /* Padding 14-16px */
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        box-shadow: none !important; /* Remove excessive shadows */
        transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1) !important; /* Transition 200ms */
    }

    /* UNREAD: Background biru sangat muda (#F5F9FF), Border biru tipis */
    .fi-no-database .fi-no-notification-unread-ctn .fi-no-notification {
        background-color: #F5F9FF !important;
        border: 1px solid rgba(59, 130, 246, 0.15) !important;
    }

    .fi-no-database .fi-no-notification-unread-ctn .fi-no-notification:hover {
        background-color: #EBF3FF !important; /* Slightly darker hover background */
    }

    .dark .fi-no-database .fi-no-notification-unread-ctn .fi-no-notification {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.35) 0%, rgba(15, 23, 42, 0.5) 100%) !important;
        border-color: rgba(96, 165, 250, 0.18) !important;
    }

    .dark .fi-no-database .fi-no-notification-unread-ctn .fi-no-notification:hover {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.5) 0%, rgba(15, 23, 42, 0.7) 100%) !important;
    }

    /* READ: Background putih, Border abu tipis */
    .fi-no-database .fi-no-notification-read-ctn .fi-no-notification {
        background-color: #ffffff !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
    }

    .fi-no-database .fi-no-notification-read-ctn .fi-no-notification:hover {
        background-color: #F8FAFC !important; /* Slightly darker hover background */
    }

    .dark .fi-no-database .fi-no-notification-read-ctn .fi-no-notification {
        background-color: #0b1120 !important;
        border-color: rgba(51, 65, 85, 0.4) !important;
    }

    .dark .fi-no-database .fi-no-notification-read-ctn .fi-no-notification:hover {
        background-color: #10192d !important;
    }

    /* Typography & Hierarchy (Visual Reordering) */
    .fi-no-database .fi-no-notification div:has(> .fi-no-notification-title) {
        display: flex !important;
        flex-direction: column !important;
        flex-grow: 1 !important;
        min-width: 0 !important;
    }

    .fi-no-database .fi-no-notification-title {
        color: #0f172a !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-size: 0.875rem !important;
        font-weight: 700 !important; /* Judul bold */
        line-height: 1.35 !important;
        order: 1 !important; /* Title first */
    }

    .dark .fi-no-database .fi-no-notification-title {
        color: #f1f5f9 !important;
    }

    .fi-no-database .fi-no-notification-body {
        color: #475569 !important;
        font-size: 0.8125rem !important; /* Deskripsi normal */
        line-height: 1.45 !important;
        margin-top: 0.25rem !important;
        order: 2 !important; /* Description second */
    }

    .dark .fi-no-database .fi-no-notification-body {
        color: #94a3b8 !important;
    }

    .fi-no-database .fi-no-notification-date {
        color: #94a3b8 !important; /* Waktu warna abu kecil */
        font-size: 0.7rem !important;
        font-weight: 500 !important;
        margin-top: 0.35rem !important;
        display: block !important;
        order: 3 !important; /* Date third (placed at the bottom) */
    }

    .dark .fi-no-database .fi-no-notification-date {
        color: #64748b !important;
    }

    /* Status Icon on the left */
    .fi-no-database .fi-no-notification .fi-icon-btn,
    .fi-no-database .fi-no-notification .fi-no-notification-icon {
        flex-shrink: 0 !important;
        margin-top: 0.15rem !important;
    }

    /* Close/Dismiss button inside notifications card (X button on the right) */
    .fi-no-database .fi-no-notification button[wire:click] {
        opacity: 0.3 !important;
        transform: scale(0.85) !important;
        transition: all 200ms ease !important;
        flex-shrink: 0 !important;
        margin-left: auto !important;
        align-self: flex-start !important;
        margin-top: 0.1rem !important;
    }

    .fi-no-database .fi-no-notification button[wire:click]:hover {
        opacity: 0.8 !important;
        transform: scale(1) !important;
        background-color: rgba(0, 0, 0, 0.04) !important;
        border-radius: 4px !important;
    }

    .dark .fi-no-database .fi-no-notification button[wire:click]:hover {
        background-color: rgba(255, 255, 255, 0.05) !important;
    }

    /* 6. Empty State (Centered, Clean) */
    .fi-no-database .fi-modal-content {
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
    }

    /* Empty state icon ring styling */
    .fi-no-database .fi-modal-icon-bg {
        background-color: rgba(241, 245, 249, 0.8) !important;
        color: #64748b !important;
        width: 3.25rem !important;
        height: 3.25rem !important;
        border-radius: 999px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: none !important;
        margin-bottom: 0.25rem !important;
    }

    .dark .fi-no-database .fi-modal-icon-bg {
        background-color: rgba(30, 41, 59, 0.4) !important;
        color: #94a3b8 !important;
    }

    /* Empty state title and subtitle styling */
    .fi-no-database .fi-modal-content > div > .fi-modal-heading {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-size: 0.95rem !important;
        font-weight: 700 !important;
        color: #1e293b !important;
        margin-top: 0.5rem !important;
    }

    .dark .fi-no-database .fi-modal-content > div > .fi-modal-heading {
        color: #f1f5f9 !important;
    }

    .fi-no-database .fi-modal-content > div > .fi-modal-description {
        font-size: 0.8125rem !important;
        color: #64748b !important;
        margin-top: 0.25rem !important;
        max-width: 280px !important;
        line-height: 1.45 !important;
    }

    .dark .fi-no-database .fi-modal-content > div > .fi-modal-description {
        color: #94a3b8 !important;
    }

    /* 7. General Clean-ups & Micro-interactions */

    .simpad-notification-console {
        display: grid;
        gap: 0.85rem;
    }

    .simpad-notification-console__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .simpad-notification-console__header h2 {
        margin: 0;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 800;
    }

    .dark .simpad-notification-console__header h2 {
        color: #f8fafc;
    }

    .simpad-notification-console__header p {
        margin: 0.2rem 0 0;
        color: #64748b;
        font-size: 0.8125rem;
    }

    .simpad-notification-console__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        justify-content: flex-end;
    }

    .simpad-notification-console__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .simpad-notification-console__meta span {
        border-radius: 999px;
        border: 1px solid rgba(226, 232, 240, 0.95);
        background: #f8fafc;
        color: #475569;
        padding: 0.3rem 0.65rem;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .dark .simpad-notification-console__meta span {
        border-color: rgba(51, 65, 85, 0.9);
        background: rgba(15, 23, 42, 0.72);
        color: #cbd5e1;
    }

    .simpad-notification-console__log {
        min-height: 6rem;
        max-height: 13rem;
        overflow-y: auto;
        border-radius: 8px;
        border: 1px solid rgba(15, 23, 42, 0.9);
        background: #0f172a;
        color: #cbd5e1;
        padding: 0.8rem;
        font-family: var(--mono-font-family), ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.75rem;
        line-height: 1.55;
    }

    .simpad-notification-console__log-line--success { color: #86efac; }
    .simpad-notification-console__log-line--warning { color: #fde68a; }
    .simpad-notification-console__log-line--danger { color: #fca5a5; }
    .simpad-notification-console__log-line--info { color: #93c5fd; }
    .simpad-notification-console__log-line--muted { color: #cbd5e1; }

    @media (max-width: 768px) {
        .simpad-notification-console__header {
            align-items: stretch;
            flex-direction: column;
        }

        .simpad-notification-console__actions {
            justify-content: flex-start;
        }
    }

    /* ────────────────────────────────────────────────────────── */
    /* CLEAN & MODERN FORM & SECTION ELEMENTS (Vercel/Linear style) */
    /* ────────────────────────────────────────────────────────── */

    /* Global Main Content & Page Title adjustments */
    .fi-main {
        background-color: #fafafa !important;
    }
    .dark .fi-main {
        background-color: #0b1120 !important;
    }

    .fi-header-heading {
        font-family: 'Outfit', sans-serif !important;
        font-weight: 800 !important;
        letter-spacing: -0.02em !important;
        color: #0f172a !important;
    }
    .dark .fi-header-heading {
        color: #f8fafc !important;
    }

    /* Forms Section styling (Vercel-like Card Container) */
    .fi-section {
        border-radius: 16px !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        background-color: #ffffff !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.01), 0 1px 2px rgba(0, 0, 0, 0.02) !important;
        overflow: hidden !important;
        transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
    }

    .dark .fi-section {
        border-color: rgba(51, 65, 85, 0.5) !important;
        background-color: #0d1527 !important;
        box-shadow: none !important;
    }

    /* Section Header spacing and separator */
    .fi-section-header {
        padding: 1.25rem 1.5rem !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.8) !important;
        background-color: #fcfdfe !important;
    }

    .dark .fi-section-header {
        border-bottom-color: rgba(51, 65, 85, 0.4) !important;
        background-color: #10192e !important;
    }

    .fi-section-header-title {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-size: 0.95rem !important;
        font-weight: 750 !important;
        color: #0f172a !important;
        letter-spacing: -0.01em !important;
    }

    .dark .fi-section-header-title {
        color: #f1f5f9 !important;
    }

    .fi-section-header-description {
        font-size: 0.8125rem !important;
        color: #64748b !important;
        margin-top: 0.2rem !important;
    }

    .dark .fi-section-header-description {
        color: #94a3b8 !important;
    }

    .fi-section-content {
        padding: 1.5rem !important;
    }

    /* TextInput and Select elements (Inputs styling) */
    .fi-input-wrp {
        border-radius: 8px !important;
        border: 1px solid rgba(203, 213, 225, 0.8) !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.02) !important;
        transition: all 0.2s ease !important;
        background-color: #ffffff !important;
    }

    .dark .fi-input-wrp {
        border-color: rgba(71, 85, 105, 0.6) !important;
        background-color: #090f1d !important;
    }

    .fi-input-wrp:focus-within {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 1px #2563eb, 0 4px 12px rgba(37, 99, 235, 0.05) !important;
    }

    .dark .fi-input-wrp:focus-within {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 1px #3b82f6, 0 4px 12px rgba(59, 130, 246, 0.1) !important;
    }

    .fi-input {
        font-size: 0.875rem !important;
        color: #0f172a !important;
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }

    .dark .fi-input {
        color: #f1f5f9 !important;
    }

    /* Buttons styling (Clean rounded Vercel style) */
    .fi-btn {
        border-radius: 8px !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        padding: 0.5rem 1rem !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
    }

    /* Primary save button */
    .fi-btn-color-primary,
    .fi-btn[type="submit"] {
        background-color: #2563eb !important;
        color: #ffffff !important;
        border: 1px solid #2563eb !important;
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
        background-color: #3b82f6 !important;
        border-color: #3b82f6 !important;
    }

    .dark .fi-btn-color-primary:hover,
    .dark .fi-btn[type="submit"]:hover {
        background-color: #2563eb !important;
        border-color: #2563eb !important;
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.25) !important;
    }

    /* Secondary / cancel button */
    .fi-btn-color-gray {
        background-color: #ffffff !important;
        color: #475569 !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
    }

    .fi-btn-color-gray:hover {
        background-color: #f8fafc !important;
        color: #0f172a !important;
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

    /* ─── Topbar Layout (Glassmorphism Header) ─────────────── */
    .fi-topbar {
        background-color: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(12px) !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.8) !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.01) !important;
    }

    .dark .fi-topbar {
        background-color: rgba(15, 23, 42, 0.8) !important;
        border-bottom-color: rgba(51, 65, 85, 0.5) !important;
        box-shadow: none !important;
    }

    /* Breadcrumbs styling */
    .fi-breadcrumbs-item-label {
        font-size: 0.8125rem !important;
        font-weight: 500 !important;
        color: #64748b !important;
    }

    .fi-breadcrumbs-item-label:hover {
        color: #0f172a !important;
    }

    .dark .fi-breadcrumbs-item-label:hover {
        color: #f1f5f9 !important;
    }

    /* ─── Table Styling (Modern Data Grid) ───────────────── */
    .fi-ta-ctn {
        border-radius: 16px !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        background-color: #ffffff !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.01), 0 1px 2px rgba(0, 0, 0, 0.02) !important;
        overflow: hidden !important;
    }

    .dark .fi-ta-ctn {
        border-color: rgba(51, 65, 85, 0.5) !important;
        background-color: #0d1527 !important;
        box-shadow: none !important;
    }

    /* Table Headers */
    .fi-ta-header-cell {
        background-color: #f8fafc !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.9) !important;
        padding-top: 0.85rem !important;
        padding-bottom: 0.85rem !important;
    }

    .dark .fi-ta-header-cell {
        background-color: #10192e !important;
        border-bottom-color: rgba(51, 65, 85, 0.4) !important;
    }

    .fi-ta-header-cell-label {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-size: 0.72rem !important;
        font-weight: 750 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        color: #475569 !important;
    }

    .dark .fi-ta-header-cell-label {
        color: #cbd5e1 !important;
    }

    /* Table Body Rows & Cells */
    .fi-ta-row {
        transition: background-color 0.15s ease !important;
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

    /* Table Search and Filters header bar */
    .fi-ta-header {
        background-color: #ffffff !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.6) !important;
        padding: 1.25rem 1.5rem !important;
    }

    .dark .fi-ta-header {
        background-color: #0d1527 !important;
        border-bottom-color: rgba(51, 65, 85, 0.4) !important;
    }

    /* ─── Stats Overview Widget (Linear/Stripe style) ────── */
    .fi-wi-stats-overview-card-ctn {
        gap: 1.25rem !important;
    }

    .fi-wi-stats-overview-card {
        border-radius: 16px !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        background-color: #ffffff !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.01), 0 1px 2px rgba(0, 0, 0, 0.02) !important;
        padding: 1.5rem !important;
        overflow: hidden !important;
        position: relative !important;
        transition: border-color 0.25s ease !important;
    }

    .dark .fi-wi-stats-overview-card {
        border-color: rgba(51, 65, 85, 0.5) !important;
        background-color: #0d1527 !important;
        box-shadow: none !important;
    }

    .fi-wi-stats-overview-card:hover {
        border-color: rgba(37, 99, 235, 0.4) !important;
    }

    .dark .fi-wi-stats-overview-card:hover {
        border-color: rgba(59, 130, 246, 0.4) !important;
    }

    /* Stat Label */
    .fi-wi-stats-overview-card .text-sm {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        color: #64748b !important;
    }

    .dark .fi-wi-stats-overview-card .text-sm {
        color: #94a3b8 !important;
    }

    /* Stat Value */
    .fi-wi-stats-overview-card .text-3xl {
        font-family: 'Outfit', sans-serif !important;
        font-size: 1.75rem !important;
        font-weight: 800 !important;
        letter-spacing: -0.02em !important;
        color: #0f172a !important;
        margin-top: 0.35rem !important;
    }

    .dark .fi-wi-stats-overview-card .text-3xl {
        color: #f8fafc !important;
    }

    /* Description / Subtext in Stats */
    .fi-wi-stats-overview-card .text-xs {
        font-size: 0.78rem !important;
        margin-top: 0.5rem !important;
    }

    /* Premium Profile Card Header */
    .simpad-profile-card {
        display: flex !important;
        align-items: center !important;
        gap: 1.5rem !important;
        background: #ffffff !important;
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

    .simpad-profile-card__avatar {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 64px !important;
        height: 64px !important;
        border-radius: 999px !important;
        background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%) !important;
        color: #ffffff !important;
        font-family: 'Outfit', sans-serif !important;
        font-size: 1.5rem !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25) !important;
    }

    .simpad-profile-card__name {
        font-family: 'Outfit', sans-serif !important;
        font-size: 1.5rem !important;
        font-weight: 800 !important;
        letter-spacing: -0.02em !important;
        color: #0f172a !important;
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
    }

    .simpad-profile-card__badge {
        font-size: 0.72rem !important;
        font-weight: 750 !important;
        color: #2563eb !important;
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
        color: #64748b !important;
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

    /* Split Column settings layout for Profile Page (Vercel Style) */
    @media (min-width: 1024px) {
        .simpad-profile-page form {
            display: flex !important;
            flex-direction: column !important;
            gap: 2rem !important;
        }

        .simpad-profile-page .fi-section {
            display: grid !important;
            grid-template-columns: 1fr 2.15fr !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            gap: 3rem !important;
            align-items: start !important;
            padding: 0 !important;
        }

        .simpad-profile-page .fi-section-header {
            background: transparent !important;
            border-bottom: none !important;
            padding: 0.5rem 0 !important;
        }

        .simpad-profile-page .fi-section-content {
            background: #ffffff !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
            border-radius: 16px !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.01), 0 1px 2px rgba(0, 0, 0, 0.02) !important;
            padding: 1.75rem !important;
        }

        .dark .simpad-profile-page .fi-section-content {
            background: #0d1527 !important;
            border-color: rgba(51, 65, 85, 0.5) !important;
        }

        /* Fix alignment of button save area */
        .simpad-profile-page form > div:last-child {
            display: grid !important;
            grid-template-columns: 1fr 2.15fr !important;
            gap: 3rem !important;
        }

        .simpad-profile-page form > div:last-child > * {
            grid-column: 2 !important;
            display: flex !important;
            justify-content: flex-end !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        let lastUnreadCount = 0;
        let isFirstCheck = true;
        let audioContext = null;

        // Clean synthesized notification chime using Web Audio API (Slack/Vercel style)
        const playNotificationSound = () => {
            try {
                // Initialize AudioContext on first sound play to avoid browser warning
                if (!audioContext) {
                    audioContext = new (window.AudioContext || window.webkitAudioContext)();
                }
                
                if (audioContext.state === 'suspended') {
                    audioContext.resume();
                }

                const playChime = (frequency, startTime, duration) => {
                    const osc = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();

                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(frequency, startTime);

                    // Envelope: fast attack, smooth decay
                    gainNode.gain.setValueAtTime(0, startTime);
                    gainNode.gain.linearRampToValueAtTime(0.18, startTime + 0.02);
                    gainNode.gain.exponentialRampToValueAtTime(0.0001, startTime + duration);

                    osc.connect(gainNode);
                    gainNode.connect(audioContext.destination);

                    osc.start(startTime);
                    osc.stop(startTime + duration);
                };

                const now = audioContext.currentTime;
                // High-pitched Slack-like double chime (C5 -> E5)
                playChime(523.25, now, 0.25);
                playChime(659.25, now + 0.06, 0.35);
            } catch (e) {
                console.log('Web Audio API chime failed:', e);
            }
        };

        // 1. Listen for Livewire/Filament instant toast notifications
        window.addEventListener('notification-sent', () => {
            playNotificationSound();
        });

        // 2. Poll for background database notification changes (from Livewire poll)
        const checkBadgeCount = () => {
            const badges = document.querySelectorAll(
                '.fi-topbar-database-notifications-btn .fi-badge span, ' +
                '.fi-sidebar-database-notifications-btn-badge-ctn .fi-badge span, ' +
                '.fi-no-database .fi-modal-header .fi-badge'
            );
            
            if (badges.length > 0) {
                const count = parseInt(badges[0].textContent.trim()) || 0;
                
                // If count has increased and it is not the initial load check, play the chime
                if (count > lastUnreadCount) {
                    if (!isFirstCheck) {
                        playNotificationSound();
                    }
                }
                lastUnreadCount = count;
            } else {
                lastUnreadCount = 0;
            }
            isFirstCheck = false;
        };

        // Check badge count every 2 seconds
        setInterval(checkBadgeCount, 2000);

        // Pre-initialize audio context on first click on page to guarantee playback
        const initAudioOnInteraction = () => {
            audioContext = new (window.AudioContext || window.webkitAudioContext)();
            document.removeEventListener('click', initAudioOnInteraction);
            document.removeEventListener('keydown', initAudioOnInteraction);
        };
        document.addEventListener('click', initAudioOnInteraction);
        document.addEventListener('keydown', initAudioOnInteraction);

        // 3. Render custom profile page header if on profile page
        if (window.location.pathname.endsWith('/profile')) {
            const renderProfileHeader = () => {
                const headerCtn = document.querySelector('.fi-header');
                const mainForm = document.querySelector('.fi-main-content form, .fi-content form');
                
                if (headerCtn && mainForm && !document.querySelector('.simpad-profile-card')) {
                    // Hide default plain header to avoid redundancy
                    headerCtn.style.display = 'none';
                    
                    // Build Profile Card using Blade-initialized session values
                    const profileCard = document.createElement('div');
                    profileCard.className = 'simpad-profile-card';
                    profileCard.innerHTML = `
                        <div class="simpad-profile-card__avatar">
                            {{ $initials }}
                        </div>
                        <div class="simpad-profile-card__info">
                            <h1 class="simpad-profile-card__name">{{ $userName }}</h1>
                            <div class="simpad-profile-card__meta">
                                <span class="simpad-profile-card__badge">{{ $userRole }}</span>
                                <span class="simpad-profile-card__email">{{ $userEmail }}</span>
                                ${ "{{ $isGoogleLinked }}" === "1" ? `
                                    <span class="simpad-profile-card__google-status linked">
                                        <svg style="width: 0.85rem; height: 0.85rem; margin-right: 0.25rem; display: inline-block; vertical-align: middle;" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12.24 10.285V13.4h6.887c-.275 1.565-1.88 4.604-6.887 4.604-4.33 0-7.866-3.577-7.866-8s3.536-8 7.866-8c2.46 0 4.105 1.025 5.047 1.926l2.427-2.334C17.955 2.192 15.34 1 12.24 1 6.12 1 1.16 5.92 1.16 12s4.96 11 11.08 11c6.39 0 10.646-4.414 10.646-10.725 0-.728-.078-1.284-.177-1.99H12.24z"/>
                                        </svg>
                                        Google Connected
                                    </span>
                                ` : `
                                    <span class="simpad-profile-card__google-status unlinked">
                                        Google Unlinked
                                    </span>
                                ` }
                            </div>
                        </div>
                    `;
                    
                    // Insert right before the form
                    mainForm.parentNode.insertBefore(profileCard, mainForm);
                }
            };
            
            // Run immediately and after a short delay to ensure DOM is ready
            renderProfileHeader();
            setTimeout(renderProfileHeader, 500);
        }
    });
</script>