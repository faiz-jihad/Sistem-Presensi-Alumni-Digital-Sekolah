<x-filament-panels::page>
<<<<<<< Updated upstream
<style>
    .ma-header {
        position: relative;
        overflow: hidden;
        border-radius: 1.25rem;
        background: linear-gradient(135deg, #1d4ed8 0%, #4f46e5 50%, #1e40af 100%);
        padding: 1.75rem 2rem;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 20px 40px -10px rgba(30, 64, 175, 0.4);
    }
    @media (min-width: 768px) {
        .ma-header { flex-direction: row; align-items: center; justify-content: space-between; }
    }
    .ma-header-deco1 {
        position: absolute; top: -3rem; right: -3rem;
        width: 10rem; height: 10rem; border-radius: 50%;
        background: rgba(255,255,255,0.08); pointer-events: none;
    }
    .ma-header-deco2 {
        position: absolute; bottom: -4rem; left: -2rem;
        width: 12rem; height: 12rem; border-radius: 50%;
        background: rgba(255,255,255,0.05); pointer-events: none;
    }
    .ma-header-left { display: flex; align-items: flex-start; gap: 1rem; position: relative; z-index: 1; }
    @media (min-width: 768px) { .ma-header-left { align-items: center; } }
    .ma-header-icon {
        width: 3.25rem; height: 3.25rem; flex-shrink: 0;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 1rem;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    .ma-header-title {
        font-size: 1.4rem; font-weight: 800; color: white;
        letter-spacing: -0.02em; line-height: 1.2;
    }
    .ma-header-desc {
        font-size: 0.85rem; color: rgba(219,234,254,0.9);
        margin-top: 0.35rem; max-width: 38rem; line-height: 1.5;
    }
    .ma-header-badge {
        position: relative; z-index: 1;
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: rgba(0,0,0,0.18);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 0.75rem;
        padding: 0.6rem 1rem;
        font-size: 0.75rem; font-weight: 600; color: rgba(255,255,255,0.9);
        white-space: nowrap;
    }
    .ma-badge-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #fbbf24; flex-shrink: 0;
        animation: ma-pulse 1.5s infinite;
    }
    @keyframes ma-pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }

    .ma-card {
        position: relative;
        border-radius: 1.25rem;
        background: white;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .dark .ma-card { background: #18181b; border-color: #27272a; }
    .ma-card-topbar {
        height: 4px;
        background: linear-gradient(to right, #3b82f6, #6366f1, #a855f7);
    }
    .ma-card-body { padding: 1.75rem 2rem; }
=======
    <style>
        .summary-bar {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }
        @media (max-width: 640px) {
            .summary-bar { grid-template-columns: repeat(3, 1fr); }
        }
        .summary-item {
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid transparent;
        }
        .summary-item-present  { background: #ecfdf5; border-color: #a7f3d0; }
        .summary-item-late     { background: #fffbeb; border-color: #fde68a; }
        .summary-item-sick     { background: #eff6ff; border-color: #bae6fd; }
        .summary-item-izin     { background: #fffbeb; border-color: #fde68a; }
        .summary-item-absent   { background: #fef2f2; border-color: #fca5a5; }

        .dark .summary-item-present  { background: rgba(6,95,70,0.15);  border-color: rgba(6,95,70,0.4);  }
        .dark .summary-item-late     { background: rgba(146,64,14,0.15); border-color: rgba(146,64,14,0.4); }
        .dark .summary-item-sick     { background: rgba(7,89,133,0.15);  border-color: rgba(7,89,133,0.4);  }
        .dark .summary-item-izin     { background: rgba(146,64,14,0.15); border-color: rgba(146,64,14,0.4); }
        .dark .summary-item-absent   { background: rgba(153,27,27,0.15); border-color: rgba(153,27,27,0.4); }
>>>>>>> Stashed changes

    .ma-form-wrap {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
    }
    .dark .ma-form-wrap { background: rgba(39,39,42,0.4); border-color: rgba(63,63,70,0.5); }

<<<<<<< Updated upstream
    .ma-divider {
        border: none; border-top: 1px solid #f1f5f9;
        margin: 1.5rem 0 0;
    }
    .dark .ma-divider { border-top-color: rgba(39,39,42,0.8); }

    .ma-summary-header { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; }
    .ma-summary-icon {
        width: 2rem; height: 2rem;
        background: #eef2ff; border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center;
        color: #4f46e5; flex-shrink: 0;
    }
    .dark .ma-summary-icon { background: rgba(79,70,229,0.2); color: #a5b4fc; }
    .ma-summary-title {
        font-size: 0.78rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.08em;
        color: #374151;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .dark .ma-summary-title { color: #e5e7eb; }
    .ma-summary-badge {
        background: #e0e7ff; color: #4338ca;
        padding: 0.18rem 0.65rem; border-radius: 999px;
        font-size: 0.7rem; font-weight: 600;
    }
    .dark .ma-summary-badge { background: rgba(79,70,229,0.25); color: #a5b4fc; }
=======
        .summary-item-present .summary-number  { color: #065f46; }
        .summary-item-late .summary-number     { color: #92400e; }
        .summary-item-sick .summary-number     { color: #075985; }
        .summary-item-izin .summary-number     { color: #92400e; }
        .summary-item-absent .summary-number   { color: #991b1b; }

        .dark .summary-item-present .summary-number  { color: #34d399; }
        .dark .summary-item-late .summary-number     { color: #fbbf24; }
        .dark .summary-item-sick .summary-number     { color: #38bdf8; }
        .dark .summary-item-izin .summary-number     { color: #fbbf24; }
        .dark .summary-item-absent .summary-number   { color: #f87171; }
    </style>
>>>>>>> Stashed changes

    .ma-grid-5 {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0.85rem;
    }
    @media (max-width: 767px) { .ma-grid-5 { grid-template-columns: repeat(2, 1fr); } }

    .ma-stat {
        border-radius: 1rem; padding: 1.1rem 0.75rem;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center; gap: 0.35rem;
        border: 1px solid transparent;
        transition: transform 0.2s, box-shadow 0.2s;
        text-align: center; cursor: default;
    }
    .ma-stat:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
    .ma-stat-hadir     { background: linear-gradient(135deg,#ecfdf5,#d1fae5); border-color: #a7f3d0; }
    .ma-stat-terlambat { background: linear-gradient(135deg,#fffbeb,#fef3c7); border-color: #fde68a; }
    .ma-stat-sakit     { background: linear-gradient(135deg,#faf5ff,#ede9fe); border-color: #ddd6fe; }
    .ma-stat-izin      { background: linear-gradient(135deg,#f0f9ff,#e0f2fe); border-color: #bae6fd; }
    .ma-stat-alpha     { background: linear-gradient(135deg,#fff1f2,#ffe4e6); border-color: #fecdd3; }

    .dark .ma-stat-hadir     { background: rgba(6,78,59,0.18);   border-color: rgba(6,95,70,0.3); }
    .dark .ma-stat-terlambat { background: rgba(120,53,15,0.18);  border-color: rgba(146,64,14,0.3); }
    .dark .ma-stat-sakit     { background: rgba(88,28,135,0.18);  border-color: rgba(107,33,168,0.3); }
    .dark .ma-stat-izin      { background: rgba(7,89,133,0.18);   border-color: rgba(14,116,144,0.3); }
    .dark .ma-stat-alpha     { background: rgba(127,29,29,0.18);  border-color: rgba(153,27,27,0.3); }

    .ma-stat-num { font-size: 2.25rem; font-weight: 900; line-height: 1; }
    .ma-stat-hadir     .ma-stat-num { color: #047857; }
    .ma-stat-terlambat .ma-stat-num { color: #b45309; }
    .ma-stat-sakit     .ma-stat-num { color: #7c3aed; }
    .ma-stat-izin      .ma-stat-num { color: #0369a1; }
    .ma-stat-alpha     .ma-stat-num { color: #be123c; }
    .dark .ma-stat-hadir     .ma-stat-num { color: #34d399; }
    .dark .ma-stat-terlambat .ma-stat-num { color: #fbbf24; }
    .dark .ma-stat-sakit     .ma-stat-num { color: #c084fc; }
    .dark .ma-stat-izin      .ma-stat-num { color: #38bdf8; }
    .dark .ma-stat-alpha     .ma-stat-num { color: #fb7185; }

    .ma-stat-label {
        font-size: 0.65rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.1em; opacity: 0.75;
    }
    .ma-stat-hadir     .ma-stat-label { color: #065f46; }
    .ma-stat-terlambat .ma-stat-label { color: #92400e; }
    .ma-stat-sakit     .ma-stat-label { color: #6b21a8; }
    .ma-stat-izin      .ma-stat-label { color: #075985; }
    .ma-stat-alpha     .ma-stat-label { color: #9f1239; }
    .dark .ma-stat-hadir     .ma-stat-label { color: #6ee7b7; }
    .dark .ma-stat-terlambat .ma-stat-label { color: #fcd34d; }
    .dark .ma-stat-sakit     .ma-stat-label { color: #d8b4fe; }
    .dark .ma-stat-izin      .ma-stat-label { color: #7dd3fc; }
    .dark .ma-stat-alpha     .ma-stat-label { color: #fda4af; }

    .ma-actions {
        display: flex; flex-wrap: wrap; gap: 0.75rem;
        justify-content: space-between; align-items: center;
        padding-top: 1.5rem; margin-top: 1.5rem;
        border-top: 1px solid #f1f5f9;
    }
    .dark .ma-actions { border-top-color: rgba(39,39,42,0.8); }
</style>

<div style="display:flex;flex-direction:column;gap:1.5rem;">

    {{-- Header Banner --}}
    <div class="ma-header">
        <div class="ma-header-deco1"></div>
        <div class="ma-header-deco2"></div>

        <div class="ma-header-left">
            <div class="ma-header-icon">
                <x-heroicon-o-clipboard-document-list style="width:26px;height:26px;color:white;" />
            </div>
            <div>
                <div class="ma-header-title">Presensi Manual Kelas</div>
                <div class="ma-header-desc">
                    Pilih kelas dan tanggal, lalu muat daftar siswa untuk mengisi kehadiran.
                    Notifikasi WhatsApp akan <strong style="color:white;">dikirim otomatis</strong> ke orang tua saat disimpan.
                </div>
            </div>
        </div>

<<<<<<< Updated upstream
        <div class="ma-header-badge">
            <span class="ma-badge-dot"></span>
            Status Sakit/Izin butuh verifikasi
=======
        {{-- Info Box --}}
        <div style="background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); border: 1px solid #bfdbfe; border-radius: 12px; padding: 16px 20px;" class="dark:border-blue-900">
            <p class="text-sm font-semibold text-blue-900 dark:text-blue-300">Informasi Otomatis</p>
            <ul class="mt-1 text-sm text-blue-700 dark:text-blue-400 space-y-1 list-disc list-inside">
                <li>Notifikasi WhatsApp dikirim otomatis ke orang tua saat presensi disimpan</li>
                <li>Status <strong>Izin</strong> dan <strong>Sakit</strong> memerlukan verifikasi wali kelas/admin</li>
                <li>Data presensi yang sudah tersimpan dapat dilihat di menu <strong>Presensi Siswa</strong></li>
            </ul>
>>>>>>> Stashed changes
        </div>
    </div>

    {{-- Main Card --}}
    <div class="ma-card">
        <div class="ma-card-topbar"></div>

        <form wire:submit.prevent="submit" class="ma-card-body">
            <div style="display:flex;flex-direction:column;gap:1.75rem;">

                {{-- Form Inputs --}}
                <div class="ma-form-wrap">
                    {{ $this->form }}
                </div>

                {{-- Summary --}}
                @php $summary = $this->getAttendanceSummary(); @endphp
                @if($summary['total'] > 0)
                    <div>
                        <div class="ma-summary-header">
                            <div class="ma-summary-icon">
                                <x-heroicon-o-chart-pie style="width:16px;height:16px;" />
                            </div>
                            <div class="ma-summary-title">
                                Ringkasan Kehadiran
                                <span class="ma-summary-badge">{{ $summary['total'] }} Siswa</span>
                            </div>
                        </div>

                        <div class="ma-grid-5">
                            <div class="ma-stat ma-stat-hadir">
                                <x-heroicon-o-check-circle style="width:24px;height:24px;color:#047857;" class="ma-stat-icon" />
                                <span class="ma-stat-num">{{ $summary['present'] }}</span>
                                <span class="ma-stat-label">Hadir</span>
                            </div>
                            <div class="ma-stat ma-stat-terlambat">
                                <x-heroicon-o-clock style="width:24px;height:24px;color:#b45309;" class="ma-stat-icon" />
                                <span class="ma-stat-num">{{ $summary['late'] }}</span>
                                <span class="ma-stat-label">Terlambat</span>
                            </div>
                            <div class="ma-stat ma-stat-sakit">
                                <x-heroicon-o-heart style="width:24px;height:24px;color:#7c3aed;" class="ma-stat-icon" />
                                <span class="ma-stat-num">{{ $summary['sick'] }}</span>
                                <span class="ma-stat-label">Sakit</span>
                            </div>
                            <div class="ma-stat ma-stat-izin">
                                <x-heroicon-o-document-check style="width:24px;height:24px;color:#0369a1;" class="ma-stat-icon" />
                                <span class="ma-stat-num">{{ $summary['permission'] }}</span>
                                <span class="ma-stat-label">Izin</span>
                            </div>
                            <div class="ma-stat ma-stat-alpha">
                                <x-heroicon-o-x-circle style="width:24px;height:24px;color:#be123c;" class="ma-stat-icon" />
                                <span class="ma-stat-num">{{ $summary['absent'] }}</span>
                                <span class="ma-stat-label">Alpha</span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="ma-actions">
                    <x-filament::button
                        wire:click.prevent="loadAllStudents"
                        color="info"
                        icon="heroicon-m-users"
                        outlined
                    >
                        Muat Semua Siswa Kelas
                    </x-filament::button>

                    <x-filament::button
                        type="submit"
                        color="primary"
                        icon="heroicon-m-check-circle"
                    >
                        Simpan Presensi &amp; Kirim Notif WA
                    </x-filament::button>
                </div>

            </div>
        </form>
    </div>

</div>

<x-filament-actions::modals />
</x-filament-panels::page>