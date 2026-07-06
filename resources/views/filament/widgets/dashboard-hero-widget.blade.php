<x-filament-widgets::widget>
    @php
        $data = $this->getData();
    @endphp

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- HERO BANNER SECTION --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden rounded-3xl mb-6"
         style="background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 40%, #2563eb 70%, #0ea5e9 100%);">

        {{-- Background pattern circles --}}
        <div class="absolute top-0 right-0 w-72 h-72 rounded-full opacity-10"
             style="background: white; transform: translate(30%, -30%);"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 rounded-full opacity-10"
             style="background: white; transform: translate(-30%, 30%);"></div>
        <div class="absolute top-1/2 right-1/4 w-32 h-32 rounded-full opacity-5"
             style="background: white; transform: translateY(-50%);"></div>

        <div class="relative z-10 p-8 md:p-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

                {{-- Left: Greeting --}}
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-3xl">👋</span>
                        <div>
                            <p class="text-blue-200 text-sm font-medium">{{ $data['greeting'] }},</p>
                            <h1 class="text-white text-2xl md:text-3xl font-bold tracking-tight">
                                {{ $data['user_name'] }}
                            </h1>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mt-4">
                        <div class="flex items-center gap-1.5 bg-white/20 backdrop-blur rounded-full px-3 py-1">
                            <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                            <span class="text-white text-xs font-medium">Sistem Aktif</span>
                        </div>
                        <div class="bg-white/10 backdrop-blur rounded-full px-3 py-1">
                            <span class="text-blue-200 text-xs">📅 {{ $data['today_formatted'] }}</span>
                        </div>
                    </div>
                </div>

                {{-- Right: Today's Attendance Ring --}}
                <div class="flex gap-4">
                    {{-- Hadir Hari Ini --}}
                    <div class="bg-white/15 backdrop-blur rounded-2xl p-5 text-center min-w-[100px]">
                        <div class="text-3xl font-bold text-white">{{ $data['present_percent'] }}%</div>
                        <div class="text-blue-200 text-xs mt-1">Hadir Hari Ini</div>
                        <div class="text-blue-300 text-xs mt-0.5">{{ $data['present_today'] }} / {{ $data['total_today'] }} siswa</div>
                    </div>
                    {{-- Minggu Ini --}}
                    <div class="bg-white/15 backdrop-blur rounded-2xl p-5 text-center min-w-[100px]">
                        <div class="text-3xl font-bold text-white">{{ $data['week_percent'] }}%</div>
                        <div class="text-blue-200 text-xs mt-1">Minggu Ini</div>
                        <div class="text-blue-300 text-xs mt-0.5">Rata-rata kehadiran</div>
                    </div>
                </div>
            </div>

            {{-- ── Quick Stats Row ── --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-8">
                {{-- Siswa --}}
                <div class="bg-white/10 backdrop-blur rounded-2xl p-4 hover:bg-white/20 transition-all duration-200 group">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/20 rounded-xl text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-white">{{ number_format($data['total_students']) }}</div>
                            <div class="text-blue-200 text-xs">Total Siswa</div>
                        </div>
                    </div>
                </div>

                {{-- Guru --}}
                <div class="bg-white/10 backdrop-blur rounded-2xl p-4 hover:bg-white/20 transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/20 rounded-xl text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-white">{{ number_format($data['total_teachers']) }}</div>
                            <div class="text-blue-200 text-xs">Guru Aktif</div>
                        </div>
                    </div>
                </div>

                {{-- Kelas --}}
                <div class="bg-white/10 backdrop-blur rounded-2xl p-4 hover:bg-white/20 transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/20 rounded-xl text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-white">{{ number_format($data['total_classes']) }}</div>
                            <div class="text-blue-200 text-xs">Total Kelas</div>
                        </div>
                    </div>
                </div>

                {{-- Alumni --}}
                <div class="bg-white/10 backdrop-blur rounded-2xl p-4 hover:bg-white/20 transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white/20 rounded-xl text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-white">{{ number_format($data['total_alumni']) }}</div>
                            <div class="text-blue-200 text-xs">Data Alumni</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- QUICK ACTION CARDS --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="mb-4">
        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">⚡ Aksi Cepat</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

            <a href="/admin/students"
               class="group relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 transition-all duration-300 hover:scale-[1.02] hover:shadow-xl shadow-emerald-200 dark:shadow-none">
                <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full transform translate-x-6 -translate-y-6"></div>
                <div class="relative">
                    <div class="p-2 bg-white/20 rounded-xl w-fit mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-bold text-sm">Kelola Siswa</h3>
                    <p class="text-emerald-100 text-xs mt-0.5">Data siswa aktif</p>
                </div>
            </a>

            <a href="/admin/teachers"
               class="group relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 hover:scale-[1.02] hover:shadow-xl shadow-blue-200 dark:shadow-none">
                <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full transform translate-x-6 -translate-y-6"></div>
                <div class="relative">
                    <div class="p-2 bg-white/20 rounded-xl w-fit mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-bold text-sm">Kelola Guru</h3>
                    <p class="text-blue-100 text-xs mt-0.5">Data guru pengajar</p>
                </div>
            </a>

            <a href="/admin/student-attendances"
               class="group relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 transition-all duration-300 hover:scale-[1.02] hover:shadow-xl shadow-amber-200 dark:shadow-none">
                <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full transform translate-x-6 -translate-y-6"></div>
                <div class="relative">
                    <div class="p-2 bg-white/20 rounded-xl w-fit mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-bold text-sm">Rekap Harian</h3>
                    <p class="text-amber-100 text-xs mt-0.5">Presensi hari ini</p>
                </div>
            </a>

            <a href="/admin/attendance-report"
               class="group relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 transition-all duration-300 hover:scale-[1.02] hover:shadow-xl shadow-violet-200 dark:shadow-none">
                <div class="absolute top-0 right-0 w-20 h-20 bg-white/10 rounded-full transform translate-x-6 -translate-y-6"></div>
                <div class="relative">
                    <div class="p-2 bg-white/20 rounded-xl w-fit mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-bold text-sm">Laporan Bulanan</h3>
                    <p class="text-violet-100 text-xs mt-0.5">Rekap & ekspor data</p>
                </div>
            </a>

        </div>
    </div>

</x-filament-widgets::widget>
