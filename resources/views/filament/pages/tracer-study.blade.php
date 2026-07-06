<x-filament-panels::page>
<style>
    .ts-header {
        position: relative;
        overflow: hidden;
        border-radius: 1.25rem;
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
        padding: 1.75rem 2rem;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        border: 1px solid rgba(255,255,255,0.08);
        box-shadow: 0 20px 60px -10px rgba(15, 23, 42, 0.5);
    }
    @media (min-width: 768px) {
        .ts-header { flex-direction: row; align-items: center; justify-content: space-between; }
    }
    .ts-header-deco1 {
        position: absolute; top: -3rem; right: -3rem;
        width: 14rem; height: 14rem; border-radius: 50%;
        background: rgba(99,102,241,0.15); pointer-events: none;
        filter: blur(40px);
    }
    .ts-header-deco2 {
        position: absolute; bottom: -4rem; left: 30%;
        width: 10rem; height: 10rem; border-radius: 50%;
        background: rgba(139,92,246,0.1); pointer-events: none;
        filter: blur(30px);
    }
    .ts-header-left { display: flex; align-items: flex-start; gap: 1rem; position: relative; z-index: 1; }
    @media (min-width: 768px) { .ts-header-left { align-items: center; } }
    .ts-header-icon {
        width: 3.5rem; height: 3.5rem; flex-shrink: 0;
        background: rgba(99,102,241,0.25);
        border: 1px solid rgba(165,180,252,0.3);
        border-radius: 1rem;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    }
    .ts-header-title {
        font-size: 1.5rem; font-weight: 800; color: white;
        letter-spacing: -0.025em; line-height: 1.2;
    }
    .ts-header-desc {
        font-size: 0.82rem; color: rgba(196,181,253,0.85);
        margin-top: 0.4rem; max-width: 40rem; line-height: 1.5;
    }
    .ts-header-badges { display: flex; gap: 0.5rem; flex-wrap: wrap; position: relative; z-index: 1; }
    .ts-badge {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.4rem 0.9rem;
        border-radius: 999px;
        font-size: 0.72rem; font-weight: 600;
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.12);
        color: rgba(255,255,255,0.85);
    }
    .ts-badge-dot { width: 7px; height: 7px; border-radius: 50%; background: #34d399; }

    /* Stats Grid */
    .ts-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }
    @media (max-width: 1024px) { .ts-stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px) { .ts-stats-grid { grid-template-columns: 1fr; } }

    .ts-stat-card {
        border-radius: 1.1rem;
        padding: 1.25rem 1.5rem;
        background: white;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        transition: transform 0.2s, box-shadow 0.2s;
        overflow: hidden;
        position: relative;
    }
    .ts-stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
    .dark .ts-stat-card { background: #18181b; border-color: #27272a; }
    .ts-stat-card-accent {
        position: absolute; top: 0; left: 0; right: 0; height: 3px;
    }
    .ts-stat-icon-wrap {
        width: 2.5rem; height: 2.5rem; border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 0.75rem;
    }
    .ts-stat-value {
        font-size: 2.25rem; font-weight: 900; line-height: 1;
        color: #111827;
    }
    .dark .ts-stat-value { color: #f9fafb; }
    .ts-stat-label {
        font-size: 0.72rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.07em;
        color: #6b7280; margin-top: 0.25rem;
    }
    .dark .ts-stat-label { color: #9ca3af; }
    .ts-stat-sub {
        font-size: 0.72rem; color: #9ca3af; margin-top: 0.35rem;
    }

    /* Section card */
    .ts-section {
        border-radius: 1.1rem;
        background: white;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        overflow: hidden;
    }
    .dark .ts-section { background: #18181b; border-color: #27272a; }
    .ts-section-header {
        padding: 1.1rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; gap: 0.6rem;
    }
    .dark .ts-section-header { border-bottom-color: #27272a; }
    .ts-section-title {
        font-size: 0.85rem; font-weight: 700; color: #111827;
    }
    .dark .ts-section-title { color: #f9fafb; }
    .ts-section-body { padding: 1.25rem 1.5rem; }

    /* Status breakdown */
    .ts-status-item {
        margin-bottom: 1rem;
    }
    .ts-status-item:last-child { margin-bottom: 0; }
    .ts-status-row {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 0.35rem;
    }
    .ts-status-name {
        font-size: 0.8rem; font-weight: 600; color: #374151;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .dark .ts-status-name { color: #d1d5db; }
    .ts-status-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .ts-status-count {
        font-size: 0.8rem; font-weight: 700; color: #111827;
    }
    .dark .ts-status-count { color: #f9fafb; }
    .ts-status-pct { font-size: 0.7rem; color: #9ca3af; margin-left: 0.25rem; }
    .ts-progress-bar {
        height: 8px; border-radius: 999px; background: #f1f5f9;
        overflow: hidden;
    }
    .dark .ts-progress-bar { background: #27272a; }
    .ts-progress-fill {
        height: 100%; border-radius: 999px;
        transition: width 0.8s ease;
    }

    /* List items */
    .ts-list-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.6rem 0;
        border-bottom: 1px solid #f8fafc;
    }
    .dark .ts-list-item { border-bottom-color: #27272a; }
    .ts-list-item:last-child { border-bottom: none; }
    .ts-list-name {
        font-size: 0.8rem; font-weight: 500; color: #374151;
        flex: 1; min-width: 0;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        padding-right: 0.5rem;
    }
    .dark .ts-list-name { color: #d1d5db; }
    .ts-list-count {
        font-size: 0.75rem; font-weight: 700;
        padding: 0.2rem 0.65rem; border-radius: 999px;
        background: #f1f5f9; color: #374151;
        flex-shrink: 0;
    }
    .dark .ts-list-count { background: #27272a; color: #d1d5db; }

    /* Grid layouts */
    .ts-two-col {
        display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;
    }
    @media (max-width: 768px) { .ts-two-col { grid-template-columns: 1fr; } }

    .ts-three-col {
        display: grid; grid-template-columns: 5fr 3fr 4fr; gap: 1.5rem;
    }
    @media (max-width: 1024px) { .ts-three-col { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 768px) { .ts-three-col { grid-template-columns: 1fr; } }

    /* Graduation year bar chart */
    .ts-year-chart {
        display: flex; align-items: flex-end; gap: 0.5rem;
        height: 120px;
        padding-top: 0.5rem;
    }
    .ts-year-bar-wrap {
        flex: 1; display: flex; flex-direction: column;
        align-items: center; gap: 0.3rem; height: 100%;
        justify-content: flex-end;
    }
    .ts-year-bar {
        width: 100%; border-radius: 4px 4px 0 0;
        background: linear-gradient(to top, #6366f1, #818cf8);
        min-height: 4px;
        transition: height 0.5s ease;
    }
    .ts-year-num {
        font-size: 0.6rem; font-weight: 700; color: #111827;
    }
    .dark .ts-year-num { color: #f9fafb; }
    .ts-year-label {
        font-size: 0.58rem; color: #9ca3af;
        writing-mode: vertical-rl; text-orientation: mixed;
        transform: rotate(180deg);
    }
    .ts-empty {
        text-align: center; padding: 2rem 1rem;
        font-size: 0.8rem; color: #9ca3af;
    }
</style>

@php
    $stats    = $this->getStats();
    $statuses = $this->getStatusBreakdown();
    $univs    = $this->getTopUniversities();
    $companies= $this->getTopCompanies();
    $provinces= $this->getTopProvinces();
    $years    = $this->getGraduationYearBreakdown();
    $maxYearCount = collect($years)->max('count') ?: 1;
@endphp

<div style="display:flex;flex-direction:column;gap:1.5rem;">

    {{-- Header --}}
    <div class="ts-header">
        <div class="ts-header-deco1"></div>
        <div class="ts-header-deco2"></div>

        <div class="ts-header-left">
            <div class="ts-header-icon">
                <x-heroicon-o-chart-pie style="width:28px;height:28px;color:#a5b4fc;" />
            </div>
            <div>
                <div class="ts-header-title">Tracer Study Alumni</div>
                <div class="ts-header-desc">
                    Statistik dan analisis data alumni berdasarkan status karir, persebaran wilayah, universitas, dan angkatan kelulusan.
                </div>
            </div>
        </div>

        <div class="ts-header-badges">
            <span class="ts-badge">
                <span class="ts-badge-dot"></span>
                {{ $stats['response_rate'] }}% Response Rate
            </span>
            <span class="ts-badge">
                <x-heroicon-o-user-group style="width:13px;height:13px;" />
                {{ $stats['total_alumni'] }} Total Alumni
            </span>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="ts-stats-grid">
        {{-- Total Alumni --}}
        <div class="ts-stat-card">
            <div class="ts-stat-card-accent" style="background:linear-gradient(to right,#6366f1,#818cf8);"></div>
            <div class="ts-stat-icon-wrap" style="background:#eef2ff;">
                <x-heroicon-o-users style="width:20px;height:20px;color:#4f46e5;" />
            </div>
            <div class="ts-stat-value" style="color:#4f46e5;">{{ number_format($stats['total_alumni']) }}</div>
            <div class="ts-stat-label">Total Alumni</div>
            <div class="ts-stat-sub">Terdaftar di sistem</div>
        </div>

        {{-- Verified --}}
        <div class="ts-stat-card">
            <div class="ts-stat-card-accent" style="background:linear-gradient(to right,#10b981,#34d399);"></div>
            <div class="ts-stat-icon-wrap" style="background:#ecfdf5;">
                <x-heroicon-o-check-badge style="width:20px;height:20px;color:#059669;" />
            </div>
            <div class="ts-stat-value" style="color:#059669;">{{ number_format($stats['verified_alumni']) }}</div>
            <div class="ts-stat-label">Sudah Terverifikasi</div>
            <div class="ts-stat-sub">
                @if($stats['total_alumni'] > 0)
                    {{ round(($stats['verified_alumni'] / $stats['total_alumni']) * 100) }}% dari total
                @else
                    -
                @endif
            </div>
        </div>

        {{-- Isi Profil --}}
        <div class="ts-stat-card">
            <div class="ts-stat-card-accent" style="background:linear-gradient(to right,#f59e0b,#fbbf24);"></div>
            <div class="ts-stat-icon-wrap" style="background:#fffbeb;">
                <x-heroicon-o-document-text style="width:20px;height:20px;color:#d97706;" />
            </div>
            <div class="ts-stat-value" style="color:#d97706;">{{ number_format($stats['with_profile']) }}</div>
            <div class="ts-stat-label">Sudah Isi Profil</div>
            <div class="ts-stat-sub">Data tracer study</div>
        </div>

        {{-- Response Rate --}}
        <div class="ts-stat-card">
            <div class="ts-stat-card-accent" style="background:linear-gradient(to right,#8b5cf6,#a78bfa);"></div>
            <div class="ts-stat-icon-wrap" style="background:#f5f3ff;">
                <x-heroicon-o-arrow-trending-up style="width:20px;height:20px;color:#7c3aed;" />
            </div>
            <div class="ts-stat-value" style="color:#7c3aed;">{{ $stats['response_rate'] }}%</div>
            <div class="ts-stat-label">Response Rate</div>
            <div class="ts-stat-sub">Tingkat partisipasi</div>
        </div>
    </div>

    {{-- Main Content: Status + Universitas + Perusahaan --}}
    <div class="ts-three-col">

        {{-- Status Karir --}}
        <div class="ts-section">
            <div class="ts-section-header">
                <x-heroicon-o-briefcase style="width:18px;height:18px;color:#6366f1;" />
                <span class="ts-section-title">Status Karir Alumni</span>
            </div>
            <div class="ts-section-body">
                @if(count($statuses) > 0)
                    @foreach($statuses as $status)
                        <div class="ts-status-item">
                            <div class="ts-status-row">
                                <span class="ts-status-name">
                                    <span class="ts-status-dot" style="background:{{ $status['color'] }};"></span>
                                    {{ $status['label'] }}
                                </span>
                                <span class="ts-status-count">
                                    {{ $status['count'] }}
                                    <span class="ts-status-pct">({{ $status['percentage'] }}%)</span>
                                </span>
                            </div>
                            <div class="ts-progress-bar">
                                <div class="ts-progress-fill" style="width:{{ $status['percentage'] }}%;background:{{ $status['color'] }};"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="ts-empty">Belum ada data status karir</div>
                @endif
            </div>
        </div>

        {{-- Persebaran Provinsi --}}
        <div class="ts-section">
            <div class="ts-section-header">
                <x-heroicon-o-map-pin style="width:18px;height:18px;color:#f59e0b;" />
                <span class="ts-section-title">Persebaran Provinsi</span>
            </div>
            <div class="ts-section-body">
                @if(count($provinces) > 0)
                    @foreach($provinces as $prov)
                        <div class="ts-list-item">
                            <span class="ts-list-name">{{ $prov['name'] }}</span>
                            <span class="ts-list-count">{{ $prov['count'] }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="ts-empty">Belum ada data provinsi</div>
                @endif
            </div>
        </div>

        {{-- Angkatan Kelulusan --}}
        <div class="ts-section">
            <div class="ts-section-header">
                <x-heroicon-o-academic-cap style="width:18px;height:18px;color:#8b5cf6;" />
                <span class="ts-section-title">Angkatan Kelulusan</span>
            </div>
            <div class="ts-section-body">
                @if(count($years) > 0)
                    <div class="ts-year-chart">
                        @foreach(array_reverse($years) as $yr)
                            <div class="ts-year-bar-wrap">
                                <span class="ts-year-num">{{ $yr['count'] }}</span>
                                <div class="ts-year-bar" style="height:{{ max(4, ($yr['count'] / $maxYearCount) * 100) }}px;"></div>
                                <span class="ts-year-label">{{ $yr['year'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="ts-empty">Belum ada data angkatan</div>
                @endif
            </div>
        </div>

    </div>

    {{-- Universitas & Perusahaan --}}
    <div class="ts-two-col">

        {{-- Top Universitas --}}
        <div class="ts-section">
            <div class="ts-section-header">
                <x-heroicon-o-building-library style="width:18px;height:18px;color:#3b82f6;" />
                <span class="ts-section-title">Universitas Terbanyak</span>
            </div>
            <div class="ts-section-body">
                @if(count($univs) > 0)
                    @foreach($univs as $i => $univ)
                        <div class="ts-list-item">
                            <span style="font-size:0.7rem;font-weight:700;color:#9ca3af;width:1.5rem;flex-shrink:0;">#{{ $i + 1 }}</span>
                            <span class="ts-list-name">{{ $univ['name'] }}</span>
                            <span class="ts-list-count" style="background:#eff6ff;color:#2563eb;">{{ $univ['count'] }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="ts-empty">Belum ada data universitas</div>
                @endif
            </div>
        </div>

        {{-- Top Perusahaan --}}
        <div class="ts-section">
            <div class="ts-section-header">
                <x-heroicon-o-building-office-2 style="width:18px;height:18px;color:#10b981;" />
                <span class="ts-section-title">Perusahaan Terbanyak</span>
            </div>
            <div class="ts-section-body">
                @if(count($companies) > 0)
                    @foreach($companies as $i => $company)
                        <div class="ts-list-item">
                            <span style="font-size:0.7rem;font-weight:700;color:#9ca3af;width:1.5rem;flex-shrink:0;">#{{ $i + 1 }}</span>
                            <span class="ts-list-name">{{ $company['name'] }}</span>
                            <span class="ts-list-count" style="background:#ecfdf5;color:#059669;">{{ $company['count'] }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="ts-empty">Belum ada data perusahaan</div>
                @endif
            </div>
        </div>

    </div>

</div>

</x-filament-panels::page>
