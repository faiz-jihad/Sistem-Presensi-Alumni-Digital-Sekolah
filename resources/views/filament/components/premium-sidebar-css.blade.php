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
        margin: 0.35rem 0.75rem !important;
        padding: 0.65rem 0.85rem !important;
        border-radius: 8px !important;
        border: 1px solid rgba(226, 232, 240, 0.75) !important;
        background: rgba(255, 255, 255, 0.78) !important;
        color: #334155 !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
        transition: background-color 160ms ease, border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease !important;
    }

    .fi-sidebar-database-notifications-btn:hover {
        transform: translateX(3px) !important;
        border-color: rgba(59, 130, 246, 0.35) !important;
        background: #ffffff !important;
        box-shadow: 0 8px 22px rgba(37, 99, 235, 0.10) !important;
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
        font-weight: 700 !important;
        font-size: 0.875rem !important;
    }

    .fi-sidebar-database-notifications-btn-badge-ctn {
        margin-left: auto !important;
        flex-shrink: 0 !important;
    }

    .fi-sidebar-database-notifications-btn-badge-ctn .fi-badge,
    .fi-topbar-database-notifications-btn .fi-badge {
        min-width: 1.35rem !important;
        height: 1.35rem !important;
        padding: 0 0.35rem !important;
        border-radius: 999px !important;
        background: #2563eb !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25) !important;
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

    /* Database notification slide-over */
    .fi-no-database .fi-modal-window {
        border-radius: 8px 0 0 8px !important;
        border-left: 1px solid rgba(226, 232, 240, 0.9) !important;
        background: #f8fafc !important;
    }

    .dark .fi-no-database .fi-modal-window {
        border-left-color: rgba(51, 65, 85, 0.8) !important;
        background: #020617 !important;
    }

    .fi-no-database .fi-modal-header {
        border-bottom: 1px solid rgba(226, 232, 240, 0.85) !important;
        background: rgba(255, 255, 255, 0.96) !important;
        backdrop-filter: blur(12px) !important;
    }

    .dark .fi-no-database .fi-modal-header {
        border-bottom-color: rgba(51, 65, 85, 0.8) !important;
        background: rgba(15, 23, 42, 0.94) !important;
    }

    .fi-no-database .fi-modal-heading {
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        color: #0f172a !important;
        font-size: 1rem !important;
        font-weight: 800 !important;
    }

    .dark .fi-no-database .fi-modal-heading {
        color: #f8fafc !important;
    }

    .fi-no-database .fi-modal-content {
        display: grid !important;
        gap: 0.65rem !important;
        padding: 0.85rem !important;
    }

    .fi-no-database .fi-no-notification-read-ctn,
    .fi-no-database .fi-no-notification-unread-ctn {
        border-radius: 8px !important;
        overflow: hidden !important;
    }

    .fi-no-database .fi-no-notification {
        align-items: flex-start !important;
        gap: 0.75rem !important;
        border-radius: 8px !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        background: #ffffff !important;
        padding: 0.85rem !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04) !important;
    }

    .dark .fi-no-database .fi-no-notification {
        border-color: rgba(51, 65, 85, 0.82) !important;
        background: rgba(15, 23, 42, 0.92) !important;
    }

    .fi-no-database .fi-no-notification-unread-ctn .fi-no-notification {
        border-color: rgba(37, 99, 235, 0.28) !important;
        background: #eff6ff !important;
    }

    .dark .fi-no-database .fi-no-notification-unread-ctn .fi-no-notification {
        border-color: rgba(96, 165, 250, 0.32) !important;
        background: rgba(30, 41, 59, 0.94) !important;
    }

    .fi-no-database .fi-no-notification-title {
        color: #0f172a !important;
        font-size: 0.9rem !important;
        font-weight: 800 !important;
        line-height: 1.3 !important;
    }

    .dark .fi-no-database .fi-no-notification-title {
        color: #f8fafc !important;
    }

    .fi-no-database .fi-no-notification-body {
        color: #475569 !important;
        font-size: 0.8125rem !important;
        line-height: 1.45 !important;
    }

    .dark .fi-no-database .fi-no-notification-body {
        color: #cbd5e1 !important;
    }

    .fi-no-database .fi-no-notification-date {
        color: #64748b !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
    }

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
</style>