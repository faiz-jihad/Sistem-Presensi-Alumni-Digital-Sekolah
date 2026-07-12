<x-filament-panels::page>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

    /* ==========================================
       BASE LAYOUT
    ========================================== */
    .ar-page {
        font-family: 'Inter', sans-serif;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* ==========================================
       FILTER PANEL - Frosted Glass Card
    ========================================== */
    .ar-filter-panel {
        position: relative;
        background: linear-gradient(135deg, #ffffff 0%, #eff6ff 100%);
        border: 1px solid rgba(37, 99, 235, 0.15);
        border-radius: 20px;
        padding: 28px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(37, 99, 235, 0.06), 0 1px 2px rgba(0,0,0,0.03);
    }
    .dark .ar-filter-panel {
        background: linear-gradient(135deg, rgba(15, 38, 102, 0.8) 0%, rgba(15, 23, 42, 0.95) 100%);
        border-color: rgba(37, 99, 235, 0.25);
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.4);
    }
    .ar-filter-panel::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.12) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .ar-filter-panel::after {
        content: '';
        position: absolute;
        bottom: -40px;
        left: 20%;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.07) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .ar-filter-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
    }
    .ar-filter-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
    }
    .ar-filter-icon svg {
        width: 20px;
        height: 20px;
        color: white;
    }
    .ar-filter-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }
    .dark .ar-filter-title { color: #f1f5f9; }
    .ar-filter-subtitle {
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
    }
    .dark .ar-filter-subtitle { color: #94a3b8; }

    /* ==========================================
       REPORT RESULTS SECTION
    ========================================== */
    .ar-results {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Hero Banner */
    .ar-hero {
        position: relative;
        background: linear-gradient(135deg, #0f2666 0%, #1e3a8a 40%, #1e40af 100%);
        border-radius: 20px;
        padding: 28px 32px;
        overflow: hidden;
        color: white;
    }
    .ar-hero::before {
        content: '';
        position: absolute;
        top: -80px;
        right: -80px;
        width: 280px;
        height: 280px;
        background: radial-gradient(circle, rgba(147, 197, 253, 0.25) 0%, transparent 65%);
        border-radius: 50%;
        pointer-events: none;
    }
    .ar-hero::after {
        content: '';
        position: absolute;
        bottom: -60px;
        left: 10%;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, transparent 65%);
        border-radius: 50%;
        pointer-events: none;
    }
    .ar-hero-grid {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    @media (min-width: 768px) {
        .ar-hero-grid {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }
    .ar-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 999px;
        padding: 4px 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgba(255,255,255,0.85);
        margin-bottom: 8px;
        width: fit-content;
    }
    .ar-hero-badge-dot {
        width: 6px;
        height: 6px;
        background: #34d399;
        border-radius: 50%;
        animation: ar-pulse 2s infinite;
    }
    @keyframes ar-pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.6; transform: scale(1.3); }
    }
    .ar-hero-title {
        font-size: 26px;
        font-weight: 800;
        line-height: 1.2;
        color: white;
        letter-spacing: -0.02em;
    }
    .ar-hero-title span {
        background: linear-gradient(90deg, #a5f3fc, #6ee7b7);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .ar-hero-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }
    .ar-hero-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(6px);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 8px;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 500;
        color: rgba(255,255,255,0.9);
    }
    .ar-hero-pill svg {
        width: 13px;
        height: 13px;
        opacity: 0.7;
    }
    .ar-export-group {
        display: flex;
        gap: 10px;
        flex-shrink: 0;
    }
    .ar-export-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 200ms ease;
        text-decoration: none;
    }
    .ar-export-btn svg { width: 15px; height: 15px; }
    .ar-export-btn:hover { transform: translateY(-1px); }
    .ar-export-btn-excel {
        background: linear-gradient(135deg, #059669, #047857);
        color: white;
        box-shadow: 0 4px 14px rgba(5, 150, 105, 0.4);
    }
    .ar-export-btn-excel:hover {
        box-shadow: 0 6px 20px rgba(5, 150, 105, 0.5);
    }
    .ar-export-btn-pdf {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        color: white;
    }
    .ar-export-btn-pdf:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    /* ==========================================
       STAT CARDS — Premium Numeric Panels
    ========================================== */
    .ar-stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    @media (min-width: 640px) {
        .ar-stats-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (min-width: 1024px) {
        .ar-stats-grid { grid-template-columns: repeat(6, 1fr); }
    }
    .ar-stat {
        position: relative;
        border-radius: 16px;
        padding: 18px 16px;
        overflow: hidden;
        cursor: default;
        transition: all 220ms cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
    }
    .ar-stat:hover {
        transform: translateY(-3px) scale(1.01);
    }
    .ar-stat-glow {
        position: absolute;
        top: 0; right: 0;
        width: 70px; height: 70px;
        border-radius: 50%;
        opacity: 0.25;
        transform: translate(30%, -30%);
        pointer-events: none;
    }
    .ar-stat-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }
    .ar-stat-icon svg { width: 18px; height: 18px; }
    .ar-stat-num {
        font-size: 30px;
        font-weight: 800;
        line-height: 1;
        letter-spacing: -0.03em;
    }
    .ar-stat-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        margin-top: 4px;
        opacity: 0.7;
    }

    /* Blue - Hadir */
    .ar-stat-hadir {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-color: #93c5fd;
        color: #1e3a8a;
        box-shadow: 0 2px 12px rgba(37, 99, 235, 0.12);
    }
    .dark .ar-stat-hadir {
        background: linear-gradient(135deg, rgba(30, 58, 138, 0.3) 0%, rgba(29, 78, 216, 0.2) 100%);
        border-color: rgba(147, 197, 253, 0.3);
        color: #93c5fd;
    }
    .ar-stat-hadir .ar-stat-glow { background: #3b82f6; }
    .ar-stat-hadir .ar-stat-icon { background: rgba(37, 99, 235, 0.15); color: #2563eb; }
    .dark .ar-stat-hadir .ar-stat-icon { background: rgba(147, 197, 253, 0.15); color: #93c5fd; }

    /* Cyan - Terlambat */
    .ar-stat-telat {
        background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%);
        border-color: #67e8f9;
        color: #164e63;
        box-shadow: 0 2px 12px rgba(6, 182, 212, 0.12);
    }
    .dark .ar-stat-telat {
        background: linear-gradient(135deg, rgba(22, 78, 99, 0.3) 0%, rgba(14, 116, 144, 0.2) 100%);
        border-color: rgba(103, 232, 249, 0.3);
        color: #a5f3fc;
    }
    .ar-stat-telat .ar-stat-glow { background: #06b6d4; }
    .ar-stat-telat .ar-stat-icon { background: rgba(6, 182, 212, 0.15); color: #0891b2; }
    .dark .ar-stat-telat .ar-stat-icon { background: rgba(103, 232, 249, 0.15); color: #67e8f9; }

    /* Indigo - Sakit */
    .ar-stat-sakit {
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        border-color: #a5b4fc;
        color: #312e81;
        box-shadow: 0 2px 12px rgba(79, 70, 229, 0.12);
    }
    .dark .ar-stat-sakit {
        background: linear-gradient(135deg, rgba(49, 46, 129, 0.3) 0%, rgba(67, 56, 202, 0.2) 100%);
        border-color: rgba(165, 180, 252, 0.3);
        color: #c7d2fe;
    }
    .ar-stat-sakit .ar-stat-glow { background: #6366f1; }
    .ar-stat-sakit .ar-stat-icon { background: rgba(79, 70, 229, 0.15); color: #4f46e5; }
    .dark .ar-stat-sakit .ar-stat-icon { background: rgba(165, 180, 252, 0.15); color: #a5b4fc; }

    /* Sky - Izin */
    .ar-stat-izin {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        border-color: #7dd3fc;
        color: #0c4a6e;
        box-shadow: 0 2px 12px rgba(14, 165, 233, 0.12);
    }
    .dark .ar-stat-izin {
        background: linear-gradient(135deg, rgba(12, 74, 110, 0.3) 0%, rgba(3, 105, 161, 0.2) 100%);
        border-color: rgba(125, 211, 252, 0.3);
        color: #bae6fd;
    }
    .ar-stat-izin .ar-stat-glow { background: #0ea5e9; }
    .ar-stat-izin .ar-stat-icon { background: rgba(14, 165, 233, 0.15); color: #0284c7; }
    .dark .ar-stat-izin .ar-stat-icon { background: rgba(125, 211, 252, 0.15); color: #7dd3fc; }

    /* Deep navy - Alpa */
    .ar-stat-alpa {
        background: linear-gradient(135deg, #dde4f5 0%, #c3cfed 100%);
        border-color: #a3b4e0;
        color: #1e3a8a;
        box-shadow: 0 2px 12px rgba(30, 58, 138, 0.12);
    }
    .dark .ar-stat-alpa {
        background: linear-gradient(135deg, rgba(30, 58, 138, 0.35) 0%, rgba(15, 38, 102, 0.45) 100%);
        border-color: rgba(147, 197, 253, 0.2);
        color: #bfdbfe;
    }
    .ar-stat-alpa .ar-stat-glow { background: #1e40af; }
    .ar-stat-alpa .ar-stat-icon { background: rgba(30, 58, 138, 0.15); color: #1d4ed8; }
    .dark .ar-stat-alpa .ar-stat-icon { background: rgba(147, 197, 253, 0.15); color: #93c5fd; }

    /* Pale blue - Netral */
    .ar-stat-netral {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-color: #bfdbfe;
        color: #1e40af;
        box-shadow: 0 2px 12px rgba(37, 99, 235, 0.08);
    }
    .dark .ar-stat-netral {
        background: linear-gradient(135deg, rgba(30, 64, 175, 0.2) 0%, rgba(15, 38, 102, 0.35) 100%);
        border-color: rgba(147, 197, 253, 0.2);
        color: #bfdbfe;
    }
    .ar-stat-netral .ar-stat-glow { background: #60a5fa; }
    .ar-stat-netral .ar-stat-icon { background: rgba(37, 99, 235, 0.12); color: #3b82f6; }
    .dark .ar-stat-netral .ar-stat-icon { background: rgba(147, 197, 253, 0.12); color: #93c5fd; }

    /* ==========================================
       DATA TABLE
    ========================================== */
    .ar-table-wrap {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .dark .ar-table-wrap {
        background: #0f172a;
        border-color: rgba(51, 65, 85, 0.6);
    }

    .ar-table-header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .dark .ar-table-header-bar {
        border-bottom-color: rgba(51, 65, 85, 0.6);
        background: rgba(15, 23, 42, 0.5);
    }
    .ar-table-header-label {
        font-size: 13px;
        font-weight: 700;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .dark .ar-table-header-label { color: #cbd5e1; }
    .ar-table-header-label svg { width: 15px; height: 15px; opacity: 0.6; }
    .ar-table-count {
        font-size: 11px;
        font-weight: 600;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        padding: 3px 8px;
        border-radius: 999px;
    }

    .ar-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .ar-table thead tr {
        background: linear-gradient(to right, #0f2666, #1e3a8a);
    }
    .ar-table thead th {
        padding: 13px 16px;
        text-align: left;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgba(255,255,255,0.7);
        white-space: nowrap;
    }
    .ar-table thead th.center { text-align: center; }

    .ar-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background 150ms ease;
    }
    .dark .ar-table tbody tr { border-bottom-color: rgba(30, 41, 59, 0.8); }
    .ar-table tbody tr:last-child { border-bottom: none; }
    .ar-table tbody tr:hover { background: #eff6ff; }
    .dark .ar-table tbody tr:hover { background: rgba(37, 99, 235, 0.05); }

    .ar-table td {
        padding: 13px 16px;
        color: #334155;
        vertical-align: middle;
    }
    .dark .ar-table td { color: #cbd5e1; }

    .ar-row-num {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        margin: 0 auto;
    }
    .dark .ar-row-num { background: rgba(51, 65, 85, 0.4); color: #94a3b8; }

    .ar-student-name {
        font-weight: 700;
        color: #0f172a;
        font-size: 13.5px;
    }
    .dark .ar-student-name { color: #f1f5f9; }
    .ar-student-nis {
        font-size: 11px;
        color: #94a3b8;
        font-weight: 500;
        margin-top: 1px;
    }

    .ar-time-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f1f5f9;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 11px;
        font-weight: 600;
        color: #475569;
        font-family: 'SF Mono', 'Fira Code', monospace;
    }
    .dark .ar-time-chip { background: rgba(30, 41, 59, 0.8); color: #94a3b8; }
    .ar-time-chip svg { width: 11px; height: 11px; }

    .ar-note {
        font-size: 12px;
        color: #94a3b8;
        font-style: italic;
        max-width: 180px;
    }

    /* Status Badges */
    .ar-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        white-space: nowrap;
    }
    .ar-badge::before {
        content: '';
        width: 5px;
        height: 5px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .ar-badge-hadir { background: #d1fae5; color: #064e3b; }
    .ar-badge-hadir::before { background: #10b981; }
    .dark .ar-badge-hadir { background: rgba(16, 185, 129, 0.15); color: #6ee7b7; }

    .ar-badge-telat { background: #fef3c7; color: #78350f; }
    .ar-badge-telat::before { background: #f59e0b; }
    .dark .ar-badge-telat { background: rgba(245, 158, 11, 0.15); color: #fcd34d; }

    .ar-badge-izin { background: #e0f2fe; color: #0c4a6e; }
    .ar-badge-izin::before { background: #38bdf8; }
    .dark .ar-badge-izin { background: rgba(56, 189, 248, 0.15); color: #bae6fd; }

    .ar-badge-sakit { background: #f3e8ff; color: #581c87; }
    .ar-badge-sakit::before { background: #a855f7; }
    .dark .ar-badge-sakit { background: rgba(168, 85, 247, 0.15); color: #e9d5ff; }

    .ar-badge-alpa { background: #fee2e2; color: #7f1d1d; }
    .ar-badge-alpa::before { background: #ef4444; }
    .dark .ar-badge-alpa { background: rgba(239, 68, 68, 0.15); color: #fca5a5; }

    .ar-badge-default { background: #f1f5f9; color: #475569; }
    .ar-badge-default::before { background: #94a3b8; }
    .dark .ar-badge-default { background: rgba(51, 65, 85, 0.4); color: #94a3b8; }

    /* Monthly stat cells */
    .ar-cell-num {
        text-align: center;
        font-weight: 700;
        font-size: 14px;
    }
    .ar-cell-hadir { color: #059669; }
    .dark .ar-cell-hadir { color: #34d399; }
    .ar-cell-telat { color: #d97706; }
    .dark .ar-cell-telat { color: #fbbf24; }
    .ar-cell-sakit { color: #7c3aed; }
    .dark .ar-cell-sakit { color: #c084fc; }
    .ar-cell-izin { color: #0284c7; }
    .dark .ar-cell-izin { color: #38bdf8; }
    .ar-cell-alpa { color: #dc2626; }
    .dark .ar-cell-alpa { color: #f87171; }
    .ar-cell-neutral { color: #64748b; }
    .dark .ar-cell-neutral { color: #94a3b8; }

    /* Attendance % pill */
    .ar-pct {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 58px;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: -0.01em;
    }
    .ar-pct-good { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; }
    .ar-pct-bad { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #7f1d1d; }
    .dark .ar-pct-good { background: rgba(16, 185, 129, 0.15); color: #6ee7b7; }
    .dark .ar-pct-bad { background: rgba(239, 68, 68, 0.15); color: #fca5a5; }

    /* Empty State */
    .ar-empty {
        position: relative;
        background: linear-gradient(135deg, #f0f7ff 0%, #eff6ff 100%);
        border: 2px dashed rgba(37, 99, 235, 0.2);
        border-radius: 20px;
        padding: 60px 24px;
        text-align: center;
        overflow: hidden;
    }
    .dark .ar-empty {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(15, 38, 102, 0.3) 100%);
        border-color: rgba(37, 99, 235, 0.3);
    }
    .ar-empty-icon {
        width: 72px;
        height: 72px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.3);
    }
    .ar-empty-icon svg { width: 36px; height: 36px; color: white; }
    .ar-empty-title {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
    }
    .dark .ar-empty-title { color: #f1f5f9; }
    .ar-empty-desc {
        font-size: 14px;
        color: #64748b;
        max-width: 340px;
        margin: 0 auto;
        line-height: 1.6;
    }
    .dark .ar-empty-desc { color: #94a3b8; }

    /* Steps hint */
    .ar-steps {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 12px;
        margin-top: 24px;
    }
    .ar-step {
        display: flex;
        align-items: center;
        gap: 8px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .dark .ar-step { background: rgba(30, 41, 59, 0.6); border-color: rgba(51, 65, 85, 0.6); color: #94a3b8; }
    .ar-step-num {
        width: 20px;
        height: 20px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        font-size: 10px;
        font-weight: 800;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
</style>

<div class="ar-page">

    {{-- ===================== FILTER PANEL ===================== --}}
    <div class="ar-filter-panel">
        <div class="ar-filter-header">
            <div class="ar-filter-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
            </div>
            <div>
                <p class="ar-filter-title">Filter Laporan</p>
                <p class="ar-filter-subtitle">Pilih kriteria untuk menampilkan rekap presensi harian atau bulanan.</p>
            </div>
        </div>
        <form>
            {{ $this->form }}
        </form>
    </div>

    {{-- ===================== REPORT DATA ===================== --}}
    @php
        $report = $this->getReport();
    @endphp

    @if($report)
        @php
            $isDaily = ($this->data['type'] ?? 'daily') === 'daily';
            $classMajor = $report['class']['major'] ? ' · ' . $report['class']['major'] : '';
            $periodLabel = $isDaily
                ? \Carbon\Carbon::parse($report['date'])->locale('id')->isoFormat('dddd, D MMMM Y')
                : \Carbon\Carbon::createFromDate($this->data['year'], $this->data['month'], 1)->locale('id')->isoFormat('MMMM Y');

            $monthlySummary = [
                'present' => 0, 'late' => 0, 'sick' => 0,
                'permission' => 0, 'absent' => 0, 'recorded' => 0,
            ];

            if (!$isDaily) {
                foreach ($report['students'] as $student) {
                    $monthlySummary['present'] += $student['summary']['present'] ?? 0;
                    $monthlySummary['late'] += $student['summary']['late'] ?? 0;
                    $monthlySummary['sick'] += $student['summary']['sick'] ?? 0;
                    $monthlySummary['permission'] += $student['summary']['permission'] ?? 0;
                    $monthlySummary['absent'] += $student['summary']['absent'] ?? 0;
                    $monthlySummary['recorded'] += $student['total_recorded_days'] ?? 0;
                }
            }
        @endphp

        <div class="ar-results">

            {{-- Hero Banner --}}
            <div class="ar-hero">
                <div class="ar-hero-grid">
                    <div>
                        <div class="ar-hero-badge">
                            <span class="ar-hero-badge-dot"></span>
                            Rekap {{ $isDaily ? 'Harian' : 'Bulanan' }}
                        </div>
                        <div class="ar-hero-title">
                            {{ $report['class']['name'] }}{{ $classMajor }}<br>
                            <span>{{ $periodLabel }}</span>
                        </div>
                        <div class="ar-hero-pills">
                            <div class="ar-hero-pill">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                                </svg>
                                {{ count($report['students']) }} Siswa
                            </div>
                            <div class="ar-hero-pill">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $isDaily ? 'Laporan Harian' : 'Laporan Bulanan' }}
                            </div>
                            @if(isset($report['school_name']))
                            <div class="ar-hero-pill">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                {{ $report['school_name'] }}
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="ar-export-group">
                        <button wire:click="exportExcel" class="ar-export-btn ar-export-btn-excel">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                            </svg>
                            Excel
                        </button>
                        <button wire:click="exportPdf" class="ar-export-btn ar-export-btn-pdf">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            PDF
                        </button>
                    </div>
                </div>
            </div>

            {{-- Stat Cards --}}
            @if($isDaily)
                <div class="ar-stats-grid">
                    <div class="ar-stat ar-stat-hadir">
                        <div class="ar-stat-glow"></div>
                        <div class="ar-stat-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ar-stat-num">{{ $report['summary']['present'] }}</div>
                        <div class="ar-stat-label">Hadir</div>
                    </div>
                    <div class="ar-stat ar-stat-telat">
                        <div class="ar-stat-glow"></div>
                        <div class="ar-stat-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ar-stat-num">{{ $report['summary']['late'] }}</div>
                        <div class="ar-stat-label">Terlambat</div>
                    </div>
                    <div class="ar-stat ar-stat-sakit">
                        <div class="ar-stat-glow"></div>
                        <div class="ar-stat-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <div class="ar-stat-num">{{ $report['summary']['sick'] }}</div>
                        <div class="ar-stat-label">Sakit</div>
                    </div>
                    <div class="ar-stat ar-stat-izin">
                        <div class="ar-stat-glow"></div>
                        <div class="ar-stat-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="ar-stat-num">{{ $report['summary']['permission'] }}</div>
                        <div class="ar-stat-label">Izin</div>
                    </div>
                    <div class="ar-stat ar-stat-alpa">
                        <div class="ar-stat-glow"></div>
                        <div class="ar-stat-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ar-stat-num">{{ $report['summary']['absent'] }}</div>
                        <div class="ar-stat-label">Alpa</div>
                    </div>
                    <div class="ar-stat ar-stat-netral">
                        <div class="ar-stat-glow"></div>
                        <div class="ar-stat-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ar-stat-num">{{ $report['summary']['not_recorded'] ?? 0 }}</div>
                        <div class="ar-stat-label">Belum Diisi</div>
                    </div>
                </div>
            @else
                <div class="ar-stats-grid">
                    <div class="ar-stat ar-stat-netral">
                        <div class="ar-stat-glow"></div>
                        <div class="ar-stat-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                            </svg>
                        </div>
                        <div class="ar-stat-num">{{ $report['total_students'] }}</div>
                        <div class="ar-stat-label">Total Siswa</div>
                    </div>
                    <div class="ar-stat ar-stat-hadir">
                        <div class="ar-stat-glow"></div>
                        <div class="ar-stat-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ar-stat-num">{{ $monthlySummary['present'] }}</div>
                        <div class="ar-stat-label">Hadir</div>
                    </div>
                    <div class="ar-stat ar-stat-telat">
                        <div class="ar-stat-glow"></div>
                        <div class="ar-stat-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ar-stat-num">{{ $monthlySummary['late'] }}</div>
                        <div class="ar-stat-label">Terlambat</div>
                    </div>
                    <div class="ar-stat ar-stat-sakit">
                        <div class="ar-stat-glow"></div>
                        <div class="ar-stat-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <div class="ar-stat-num">{{ $monthlySummary['sick'] }}</div>
                        <div class="ar-stat-label">Sakit</div>
                    </div>
                    <div class="ar-stat ar-stat-izin">
                        <div class="ar-stat-glow"></div>
                        <div class="ar-stat-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="ar-stat-num">{{ $monthlySummary['permission'] }}</div>
                        <div class="ar-stat-label">Izin</div>
                    </div>
                    <div class="ar-stat ar-stat-alpa">
                        <div class="ar-stat-glow"></div>
                        <div class="ar-stat-icon">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ar-stat-num">{{ $monthlySummary['absent'] }}</div>
                        <div class="ar-stat-label">Alpa</div>
                    </div>
                </div>
            @endif

            {{-- Data Table --}}
            <div class="ar-table-wrap">
                <div class="ar-table-header-bar">
                    <div class="ar-table-header-label">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18M10 4v16M14 4v16"/>
                        </svg>
                        Data Presensi Siswa
                    </div>
                    <span class="ar-table-count">{{ count($report['students']) }} Siswa</span>
                </div>
                <div style="overflow-x: auto;">
                    <table class="ar-table">
                        <thead>
                            <tr>
                                <th class="center" style="width:56px;">#</th>
                                <th style="min-width:200px;">Nama Siswa</th>
                                @if($isDaily)
                                    <th class="center" style="width:140px;">Status</th>
                                    <th class="center" style="width:110px;">Jam Masuk</th>
                                    <th style="min-width:160px;">Catatan</th>
                                @else
                                    <th class="center" style="width:100px;">Hari Direkap</th>
                                    <th class="center" style="width:75px;">Hadir</th>
                                    <th class="center" style="width:75px;">Terlambat</th>
                                    <th class="center" style="width:75px;">Sakit</th>
                                    <th class="center" style="width:75px;">Izin</th>
                                    <th class="center" style="width:75px;">Alpa</th>
                                    <th class="center" style="width:100px;">Kehadiran</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['students'] as $idx => $student)
                                <tr>
                                    <td style="text-align:center;">
                                        <div class="ar-row-num">{{ $idx + 1 }}</div>
                                    </td>
                                    <td>
                                        <div class="ar-student-name">{{ $student['name'] }}</div>
                                        <div class="ar-student-nis">{{ $student['nis'] }}</div>
                                    </td>

                                    @if($isDaily)
                                        @php
                                            $badgeMap = [
                                                'present' => 'ar-badge-hadir',
                                                'late' => 'ar-badge-telat',
                                                'permission' => 'ar-badge-izin',
                                                'sick' => 'ar-badge-sakit',
                                                'absent' => 'ar-badge-alpa',
                                            ];
                                            $labelMap = [
                                                'present' => 'Hadir',
                                                'late' => 'Terlambat',
                                                'permission' => 'Izin',
                                                'sick' => 'Sakit',
                                                'absent' => 'Alpa',
                                                'not_recorded' => 'Belum Diisi',
                                            ];
                                            $bClass = $badgeMap[$student['status']] ?? 'ar-badge-default';
                                            $bLabel = $labelMap[$student['status']] ?? 'Tidak Dikenal';
                                        @endphp
                                        <td style="text-align:center;">
                                            <span class="ar-badge {{ $bClass }}">{{ $bLabel }}</span>
                                        </td>
                                        <td style="text-align:center;">
                                            @if($student['check_in_time'])
                                                <span class="ar-time-chip">
                                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $student['check_in_time'] }}
                                                </span>
                                            @else
                                                <span style="color:#cbd5e1;">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="ar-note">{{ $student['note'] ?: '—' }}</span>
                                        </td>
                                    @else
                                        <td class="ar-cell-num ar-cell-neutral">{{ $student['total_recorded_days'] }}</td>
                                        <td class="ar-cell-num ar-cell-hadir">{{ $student['summary']['present'] }}</td>
                                        <td class="ar-cell-num ar-cell-telat">{{ $student['summary']['late'] }}</td>
                                        <td class="ar-cell-num ar-cell-sakit">{{ $student['summary']['sick'] }}</td>
                                        <td class="ar-cell-num ar-cell-izin">{{ $student['summary']['permission'] }}</td>
                                        <td class="ar-cell-num ar-cell-alpa">{{ $student['summary']['absent'] }}</td>
                                        <td style="text-align:center;">
                                            @php $pct = $student['attendance_percentage']; @endphp
                                            <span class="ar-pct {{ $pct >= 80 ? 'ar-pct-good' : 'ar-pct-bad' }}">
                                                {{ $pct }}%
                                            </span>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" style="text-align:center; padding:48px 16px; color:#94a3b8;">
                                        <div style="font-size:13px; font-weight:600;">Tidak ada data siswa ditemukan</div>
                                        <div style="font-size:12px; margin-top:4px; opacity:0.7;">Coba ubah kriteria filter Anda.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    @else
        {{-- Empty / Idle State --}}
        <div class="ar-empty">
            <div class="ar-empty-icon">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="ar-empty-title">Pratinjau Laporan</div>
            <p class="ar-empty-desc">
                Isi filter di atas untuk menampilkan rekap data presensi siswa secara harian atau bulanan.
            </p>
            <div class="ar-steps">
                <div class="ar-step">
                    <span class="ar-step-num">1</span>
                    Pilih Jenis Laporan
                </div>
                <div class="ar-step">
                    <span class="ar-step-num">2</span>
                    Pilih Kelas
                </div>
                <div class="ar-step">
                    <span class="ar-step-num">3</span>
                    Atur Tanggal / Bulan
                </div>
                <div class="ar-step">
                    <span class="ar-step-num">4</span>
                    Ekspor Excel atau PDF
                </div>
            </div>
        </div>
    @endif

</div>
</x-filament-panels::page>
