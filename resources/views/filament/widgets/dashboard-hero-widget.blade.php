<x-filament-widgets::widget>
    @php
        $data = $this->getData();
        $user = auth()->user();
    @endphp

    <style>
        .hero-container{
            position:relative;
            overflow:hidden;
            border-radius:24px;
            padding:40px;

            background:
            radial-gradient(circle at top right,#60a5fa33,transparent 35%),
            linear-gradient(135deg,#1e3a8a,#1e3a8a,#1e3a8a);

            box-shadow:
            0 20px 45px rgba(37,99,235,.25);

            margin-bottom:30px;
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
        .hero-content{
            display:flex;
            flex-direction:column;
            gap:30px;
        }
        .hero-header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:24px;
        }
        @media(max-width:992px){
            .hero-header{
                flex-direction:column;
                align-items:flex-start;
            }
        }  
        .hero-icon{
            width:56px;
            height:56px;
            border-radius:16px;
            background:rgba(255,255,255,.15);
            display:flex;
            align-items:center;
            justify-content:center;
            color:#fff;
        }
        @media(max-width:768px){
            .hero-content{
                flex-direction:column;
                align-items:flex-start;
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
        .badge-active,
        .badge-date{
            display:flex;
            align-items:center;
            justify-content:center;

            height:38px;
            padding:0 14px;
            border-radius:999px;
            backdrop-filter:blur(8px);
            -webkit-backdrop-filter:blur(8px);
        }

        .badge-active{
            gap:8px;
            background:rgba(255,255,255,.18);
        }

        .badge-date{
            gap:8px;
            background:rgba(255,255,255,.10);
            color:#bfdbfe;
            font-size:11px;
            font-weight:500;
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
        .summary-card{
            width:150px;
            padding:20px;
            border-radius:18px;
            background:rgba(255,255,255,.15);
            backdrop-filter:blur(16px);
            transition:.3s;
        }
        .summary-card:hover{
            transform:translateY(-6px);
            background:rgba(255,255,255,.22);
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
        .stats-grid{
            display:grid;
            grid-template-columns:repeat(4,minmax(0,1fr));
            gap:16px;
        }
        @media(max-width:992px){
            .stats-grid{
                grid-template-columns:repeat(2,minmax(0,1fr));
            }
        }
        @media(max-width:640px){
            .stats-grid{
                grid-template-columns:1fr;
            }
        }
        .stat-icon-wrapper{
            width:52px;
            height:52px;
            border-radius:14px;
            background:#eff6ff;
            border: 0.3px solid #5d93ffff ;
            color:#2563eb;

            display:flex;
            align-items:center;
            justify-content:center;

            flex-shrink:0;
            transition:.35s ease;
        }
        .dark .stat-icon-wrapper{
            width:52px;
            height:52px;
            border-radius:14px;
            background:#eff6ff;
            color:#2563eb;

            display:flex;
            align-items:center;
            justify-content:center;

            flex-shrink:0;
            transition:.35s ease;
        }

        .stat-card:hover .stat-icon-wrapper{
            transform:rotate(-8deg) scale(1.12);
            background:#2563eb;
            color:#fff;
        }
        @media (min-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        .stat-card{
            background:#fff;
            border:1px solid #e5e7eb;
            border-radius:18px;
            padding:20px;

            display:flex;
            align-items:center;
            gap:18px;

            transition:all .35s ease;
            box-shadow:0 10px 25px rgba(0,0,0,.08);
        }
        
        .stat-val{
            color: #5d93ffff;
        }

        .stat-lbl{
            color: #5d93ffff;
        }
        
        .stat-card:hover{
            transform:translateY(-8px) scale(1.03);
            background:rgba(255,255,255,.25);
            box-shadow:
                0 18px 35px rgba(0,0,0,.30),
                0 0 20px rgba(59,130,246,.25);
            .stat-val{
                color: white;
            }
            .stat-lbl{
                color: white;
            }
        }
        .dark .stat-card{
            background:rgba(17,24,39,.75);
            border:1px solid rgba(255,255,255,.08);
            backdrop-filter:blur(10px);
        }

        .dark .stat-card:hover{
            transform:translateY(-8px) scale(1.03);
            background:rgba(255,255,255,.25);
            box-shadow:
                0 18px 35px rgba(0,0,0,.30),
                0 0 20px rgba(59,130,246,.25);
            .stat-val{
                color: white;
            }
            .stat-lbl{
                color: white;
            }
        }

        .dark .stat-val{
            color:#fff;
        }

        .dark .stat-lbl{
            color:#bfdbfe;
        }

        /* QUICK ACTIONS */
        .quick-actions-section {
            margin-top: 28px;
            margin-bottom: 8px;
        }
        .section-title{
            display:flex;
            align-items:center;
            gap:8px;
            font-size:15px;
            font-weight:700;
        }
        .dark .section-title {
            color: #9ca3af;
        }
        .action-grid{
            display:grid;
            grid-template-columns:repeat(2,minmax(0,1fr));
            gap:18px;
        }

        @media(min-width:768px){
            .action-grid{
                grid-template-columns:repeat(4,minmax(0,1fr));
            }
        }
        .action-card{
            position:relative;
            display:block;
            overflow:hidden;

            border-radius:18px;
            padding:24px;
            min-height:140px;

            text-decoration:none;

            box-shadow:0 12px 25px rgba(0,0,0,.18);

            transition:all .35s ease;
        }

        .action-card:hover{
            transform:translateY(-8px) scale(1.03);
            box-shadow:
                0 20px 40px rgba(0,0,0,.35),
                0 0 24px rgba(255,255,255,.15);
        }

        .action-card:hover .action-pattern{
            transform:translate(10px,-10px) scale(1.25);
        }

        .action-card:hover .action-icon-wrapper{
            transform:rotate(-8deg) scale(1.08);
            background:rgba(255,255,255,.25);
        }
        .action-card.siswa { background: linear-gradient(135deg, #1E88E5, #1E88E5); }
        .action-card.guru { background: linear-gradient(135deg, #1E88E5, #1E88E5); }
        .action-card.presensi { background: linear-gradient(135deg, #1E88E5, #1E88E5); }
        .action-card.laporan { background: linear-gradient(135deg, #1E88E5, #1E88E5); }
        
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
            transition:.4s ease;
        }
        .action-icon-wrapper{
            width:55px;
            height:55px;
            border-radius:15px;
            background:rgba(255,255,255,.18);
            display:flex;
            align-items:center;
            justify-content:center;
            width: 50px;
            height: 50px;        
            margin-bottom:18px;
            transition:.35s ease;
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

        .hero-container svg,
        .quick-actions-section svg{
            width:20px !important;
            height:20px !important;
            display:block;
            flex-shrink:0;
        }

        .quick-actions-section svg{
            color: white !important;
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
                           <span style="display:flex;align-items:center;gap:6px;">
                                <x-heroicon-o-calendar-days class="w-4 h-4"/>
                                {{ $data['today_formatted'] }}
                            </span>
                        </div>
                        @if($user->role !== 'alumni')
                        <div style="display:flex; align-items:center; background:rgba(255,255,255,.12); padding:0 14px; height:38px; border-radius:999px; backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px);">
                            <span style="display:flex; align-items:center; gap:6px; color:#bfdbfe; font-size:11px; font-weight:600;">
                                <x-heroicon-o-funnel class="w-4 h-4" style="color:#bfdbfe; width:16px; height:16px;"/>
                                <select wire:model.live="classId" style="background:transparent; border:none; color:white; font-size:11px; font-weight:600; outline:none; cursor:pointer; padding:0 16px 0 0; margin:0;">
                                    <option value="" style="color:#1e3a8a; background:white;">Semua Kelas</option>
                                    @foreach($this->getClasses() as $id => $name)
                                        <option value="{{ $id }}" style="color:#1e3a8a; background:white;">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </span>
                        </div>
                        @endif
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
        <div class="action-grid">
            @if(in_array($user->role, ['super_admin', 'admin']))
                {{-- Kelola Siswa --}}
                <a href="/admin/students" class="action-card siswa">
                    <div class="action-pattern"></div>
                    <div class="action-icon-wrapper">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
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
                <a href="/admin/attendance-report" class="action-card laporan">
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
