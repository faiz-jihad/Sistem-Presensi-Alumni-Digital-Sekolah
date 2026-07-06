<x-filament-widgets::widget>
    @php
        $data = $this->getData();
        $user = auth()->user();
    @endphp

    <style>
        .hero-container {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            margin-bottom: 24px;
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 40%, #2563eb 70%, #0ea5e9 100%);
            font-family: system-ui, -apple-system, sans-serif;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.15);
        }
        .hero-pattern-1 {
            position: absolute;
            top: 0;
            right: 0;
            width: 288px;
            height: 288px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            transform: translate(30%, -30%);
            pointer-events: none;
        }
        .hero-pattern-2 {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 192px;
            height: 192px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            transform: translate(-30%, 30%);
            pointer-events: none;
        }
        .hero-content {
            position: relative;
            z-index: 10;
            padding: 32px;
        }
        .hero-header {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        @media (min-width: 768px) {
            .hero-header {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        .greeting-section {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .greeting-emoji {
            font-size: 36px;
            line-height: 1;
        }
        .greeting-text-container {
            display: flex;
            flex-direction: column;
        }
        .greeting-text-title {
            color: #bfdbfe;
            font-size: 13px;
            font-weight: 500;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .greeting-name {
            color: #ffffff;
            font-size: 26px;
            font-weight: 700;
            margin: 4px 0 0 0;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }
        .status-badges {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }
        .badge-active {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 9999px;
            padding: 5px 12px;
        }
        .dot-active {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #34d399;
            box-shadow: 0 0 8px #34d399;
            animation: pulse-dot 2s infinite;
        }
        @keyframes pulse-dot {
            0% { opacity: 0.5; }
            50% { opacity: 1; }
            100% { opacity: 0.5; }
        }
        .badge-text-white {
            color: #ffffff;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-date {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 9999px;
            padding: 5px 12px;
            color: #bfdbfe;
            font-size: 11px;
            font-weight: 500;
        }
        
        .attendance-summary-cards {
            display: flex;
            gap: 12px;
            width: 100%;
        }
        @media (min-width: 768px) {
            .attendance-summary-cards {
                width: auto;
            }
        }
        .summary-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 18px;
            padding: 16px;
            text-align: center;
            min-width: 110px;
            flex: 1;
            transition: transform 0.2s;
        }
        .summary-card:hover {
            transform: translateY(-2px);
        }
        .summary-value {
            font-size: 28px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1;
        }
        .summary-label {
            font-size: 11px;
            color: #bfdbfe;
            font-weight: 600;
            margin-top: 6px;
        }
        .summary-subtext {
            font-size: 10px;
            color: #93c5fd;
            margin-top: 2px;
        }

        .stats-grid {
            display: grid;
            grid-template-cols: repeat(2, 1fr);
            gap: 12px;
            margin-top: 28px;
        }
        @media (min-width: 768px) {
            .stats-grid {
                grid-template-cols: repeat(4, 1fr);
            }
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-2px);
        }
        .stat-icon-wrapper {
            padding: 8px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .stat-val {
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.1;
        }
        .stat-lbl {
            font-size: 11px;
            color: #bfdbfe;
            font-weight: 500;
        }

        /* QUICK ACTIONS */
        .quick-actions-section {
            margin-top: 28px;
            margin-bottom: 8px;
        }
        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        .dark .section-title {
            color: #9ca3af;
        }
        .action-grid {
            display: grid;
            grid-template-cols: repeat(2, 1fr);
            gap: 12px;
        }
        @media (min-width: 768px) {
            .action-grid {
                grid-template-cols: repeat(4, 1fr);
            }
        }
        .action-card {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            padding: 20px;
            display: block;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .action-card.siswa { background: linear-gradient(135deg, #10b981, #0d9488); }
        .action-card.guru { background: linear-gradient(135deg, #3b82f6, #4f46e5); }
        .action-card.presensi { background: linear-gradient(135deg, #f59e0b, #ea580c); }
        .action-card.laporan { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        
        .action-card.ajukan-event { background: linear-gradient(135deg, #ec4899, #d946ef); }
        .action-card.daftar-event { background: linear-gradient(135deg, #3b82f6, #6366f1); }

        .action-pattern {
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            transform: translate(24px, -24px);
            pointer-events: none;
        }
        .action-icon-wrapper {
            padding: 8px;
            background: rgba(255, 255, 255, 0.18);
            border-radius: 12px;
            width: fit-content;
            margin-bottom: 12px;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .action-title {
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            margin: 0;
        }
        .action-desc {
            color: rgba(255, 255, 255, 0.85);
            font-size: 11px;
            margin: 4px 0 0 0;
        }
    </style>

    {{-- HERO BANNER SECTION --}}
    <div class="hero-container">
        <div class="hero-pattern-1"></div>
        <div class="hero-pattern-2"></div>

        <div class="hero-content">
            <div class="hero-header">
                {{-- Left: Greeting --}}
                <div>
                    <div class="greeting-section">
                        <span class="greeting-emoji">👋</span>
                        <div class="greeting-text-container">
                            <p class="greeting-text-title">{{ $data['greeting'] }},</p>
                            <h1 class="greeting-name">{{ $data['user_name'] }}</h1>
                        </div>
                    </div>
                    <div class="status-badges">
                        <div class="badge-active">
                            <div class="dot-active"></div>
                            <span class="badge-text-white">Sistem Aktif</span>
                        </div>
                        <div class="badge-date">
                            <span>📅 {{ $data['today_formatted'] }}</span>
                        </div>
                    </div>
                </div>

                {{-- Right: Today's Attendance Ring (Only for Non-Alumni) --}}
                @if($user->role !== 'alumni')
                    <div class="attendance-summary-cards">
                        <div class="summary-card">
                            <div class="summary-value">{{ $data['present_percent'] }}%</div>
                            <div class="summary-label">Hadir Hari Ini</div>
                            <div class="summary-subtext">{{ $data['present_today'] }} / {{ $data['total_today'] }} siswa</div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-value">{{ $data['week_percent'] }}%</div>
                            <div class="summary-label">Minggu Ini</div>
                            <div class="summary-subtext">Rata-rata kehadiran</div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Quick Stats Row (Only for Non-Alumni) --}}
            @if($user->role !== 'alumni')
                <div class="stats-grid">
                    {{-- Siswa --}}
                    <div class="stat-card">
                        <div class="stat-icon-wrapper">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="stat-val">{{ number_format($data['total_students']) }}</div>
                            <div class="stat-lbl">Total Siswa</div>
                        </div>
                    </div>

                    {{-- Guru --}}
                    <div class="stat-card">
                        <div class="stat-icon-wrapper">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="stat-val">{{ number_format($data['total_teachers']) }}</div>
                            <div class="stat-lbl">Guru Aktif</div>
                        </div>
                    </div>

                    {{-- Kelas --}}
                    <div class="stat-card">
                        <div class="stat-icon-wrapper">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <div class="stat-val">{{ number_format($data['total_classes']) }}</div>
                            <div class="stat-lbl">Total Kelas</div>
                        </div>
                    </div>

                    {{-- Alumni --}}
                    <div class="stat-card">
                        <div class="stat-icon-wrapper">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="stat-val">{{ number_format($data['total_alumni']) }}</div>
                            <div class="stat-lbl">Data Alumni</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="quick-actions-section">
        <h2 class="section-title">⚡ Aksi Cepat</h2>
        
        <div class="action-grid">
            @if(in_array($user->role, ['super_admin', 'admin', 'teacher']))
                {{-- Kelola Siswa --}}
                <a href="/admin/students" class="action-card siswa">
                    <div class="action-pattern"></div>
                    <div class="action-icon-wrapper">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        </svg>
                    </div>
                    <h3 class="action-title">Kelola Siswa</h3>
                    <p class="action-desc">Data siswa aktif</p>
                </a>

                {{-- Kelola Guru --}}
                <a href="/admin/teachers" class="action-card guru">
                    <div class="action-pattern"></div>
                    <div class="action-icon-wrapper">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="action-title">Kelola Guru</h3>
                    <p class="action-desc">Data guru pengajar</p>
                </a>

                {{-- Rekap Harian --}}
                <a href="/admin/student-attendances" class="action-card presensi">
                    <div class="action-pattern"></div>
                    <div class="action-icon-wrapper">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <h3 class="action-title">Rekap Harian</h3>
                    <p class="action-desc">Presensi hari ini</p>
                </a>

                {{-- Laporan Bulanan --}}
                <a href="/admin/laporans" class="action-card laporan">
                    <div class="action-pattern"></div>
                    <div class="action-icon-wrapper">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="action-title">Laporan Bulanan</h3>
                    <p class="action-desc">Rekap & ekspor data</p>
                </a>
            @endif

            @if($user->role === 'alumni')
                {{-- Ajukan Event --}}
                <a href="/admin/alumni-events/create" class="action-card ajukan-event">
                    <div class="action-pattern"></div>
                    <div class="action-icon-wrapper">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <h3 class="action-title">Ajukan Event</h3>
                    <p class="action-desc">Buat kegiatan alumni baru</p>
                </a>

                {{-- Daftar Event --}}
                <a href="/admin/alumni-events" class="action-card daftar-event">
                    <div class="action-pattern"></div>
                    <div class="action-icon-wrapper">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="action-title">Daftar Event</h3>
                    <p class="action-desc">Lihat semua kegiatan alumni</p>
                </a>
            @endif
        </div>
    </div>
</x-filament-widgets::widget>
