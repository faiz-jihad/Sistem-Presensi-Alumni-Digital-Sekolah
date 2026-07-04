<x-filament-panels::page>
    <style>
        .report-section {
            background-color: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        .dark .report-section {
            background-color: #18181b;
            border-color: #27272a;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.5);
        }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        @media (min-width: 768px) {
            .stat-grid {
                grid-template-columns: repeat(5, minmax(0, 1fr));
            }
        }
        .stat-card {
            padding: 16px;
            border-radius: 10px;
            border-width: 1px;
            border-style: solid;
        }
        .stat-card-present { background-color: #ecfdf5; border-color: #a7f3d0; color: #065f46; }
        .dark .stat-card-present { background-color: rgba(6, 95, 70, 0.15); border-color: rgba(6, 95, 70, 0.4); color: #34d399; }
        
        .stat-card-late { background-color: #fffbeb; border-color: #fde68a; color: #92400e; }
        .dark .stat-card-late { background-color: rgba(146, 64, 14, 0.15); border-color: rgba(146, 64, 14, 0.4); color: #fbbf24; }
        
        .stat-card-sick { background-color: #faf5ff; border-color: #e9d5ff; color: #6b21a8; }
        .dark .stat-card-sick { background-color: rgba(107, 33, 168, 0.15); border-color: rgba(107, 33, 168, 0.4); color: #c084fc; }
        
        .stat-card-permission { background-color: #f0f9ff; border-color: #bae6fd; color: #075985; }
        .dark .stat-card-permission { background-color: rgba(7, 89, 133, 0.15); border-color: rgba(7, 89, 133, 0.4); color: #38bdf8; }
        
        .stat-card-absent { background-color: #fef2f2; border-color: #fca5a5; color: #991b1b; }
        .dark .stat-card-absent { background-color: rgba(153, 27, 27, 0.15); border-color: rgba(153, 27, 27, 0.4); color: #f87171; }

        .stat-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            margin-top: 4px;
        }

        .table-container {
            overflow-x: auto;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        .dark .table-container {
            border-color: #27272a;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            text-align: left;
        }
        .report-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            padding: 12px 16px;
            border-bottom: 2px solid #e2e8f0;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .dark .report-table th {
            background-color: #09090b;
            color: #94a3b8;
            border-bottom-color: #27272a;
        }
        .report-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: middle;
        }
        .dark .report-table td {
            border-bottom-color: #27272a;
            color: #cbd5e1;
        }
        .report-table tbody tr:hover {
            background-color: #f8fafc;
        }
        .dark .report-table tbody tr:hover {
            background-color: #18181b;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
        }
        .badge-present { background-color: #d1fae5; color: #065f46; }
        .badge-late { background-color: #fef3c7; color: #92400e; }
        .badge-permission { background-color: #e0f2fe; color: #075985; }
        .badge-sick { background-color: #f3e8ff; color: #6b21a8; }
        .badge-absent { background-color: #fee2e2; color: #991b1b; }
        .badge-default { background-color: #f1f5f9; color: #475569; }

        .dark .badge-present { background-color: rgba(52, 211, 153, 0.15); color: #34d399; }
        .dark .badge-late { background-color: rgba(251, 191, 36, 0.15); color: #fbbf24; }
        .dark .badge-permission { background-color: rgba(56, 189, 248, 0.15); color: #38bdf8; }
        .dark .badge-sick { background-color: rgba(192, 132, 252, 0.15); color: #c084fc; }
        .dark .badge-absent { background-color: rgba(248, 113, 113, 0.15); color: #f87171; }
    </style>

    <div class="space-y-6">
        <!-- Filter Card -->
        <x-filament::section>
            <x-slot name="heading">
                Filter Laporan
            </x-slot>
            <x-slot name="description">
                Pilih kriteria untuk menampilkan laporan presensi harian atau bulanan.
            </x-slot>

            <form>
                {{ $this->form }}
            </form>
        </x-filament::section>

        <!-- Preview Section -->
        @php
            $report = $this->getReport();
        @endphp

        @if($report)
            <div class="report-section space-y-6">
                <!-- Header Laporan & Ekspor -->
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">
                            Pratinjau Laporan Kehadiran
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Kelas: <strong>{{ $report['class']['name'] }} ({{ $report['class']['major'] }})</strong> | 
                            @if($this->data['type'] === 'daily')
                                Hari/Tanggal: <strong>{{ \Carbon\Carbon::parse($report['date'])->locale('id')->isoFormat('dddd, D MMMM Y') }}</strong>
                            @else
                                Periode: <strong>{{ \Carbon\Carbon::createFromDate($this->data['year'], $this->data['month'], 1)->locale('id')->isoFormat('MMMM Y') }}</strong>
                            @endif
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-filament::button
                            wire:click="exportExcel"
                            color="success"
                            icon="heroicon-m-document-text"
                            size="sm"
                        >
                            Ekspor Excel
                        </x-filament::button>

                        <x-filament::button
                            wire:click="exportPdf"
                            color="danger"
                            icon="heroicon-m-document-arrow-down"
                            size="sm"
                        >
                            Ekspor PDF
                        </x-filament::button>
                    </div>
                </div>

                <!-- Summary Cards -->
                @if($this->data['type'] === 'daily')
                    <div class="stat-grid">
                        <div class="stat-card stat-card-present">
                            <p class="stat-label">Hadir</p>
                            <p class="stat-value">{{ $report['summary']['present'] }}</p>
                        </div>
                        <div class="stat-card stat-card-late">
                            <p class="stat-label">Terlambat</p>
                            <p class="stat-value">{{ $report['summary']['late'] }}</p>
                        </div>
                        <div class="stat-card stat-card-sick">
                            <p class="stat-label">Sakit</p>
                            <p class="stat-value">{{ $report['summary']['sick'] }}</p>
                        </div>
                        <div class="stat-card stat-card-permission">
                            <p class="stat-label">Izin</p>
                            <p class="stat-value">{{ $report['summary']['permission'] }}</p>
                        </div>
                        <div class="stat-card stat-card-absent">
                            <p class="stat-label">Alpha</p>
                            <p class="stat-value">{{ $report['summary']['absent'] }}</p>
                        </div>
                    </div>
                @else
                    <div class="stat-grid" style="grid-template-columns: 1fr;">
                        <div class="stat-card" style="background-color: #f8fafc; border-color: #e2e8f0; color: #334155; max-width: 250px;">
                            <p class="stat-label" style="color: #64748b;">Total Siswa</p>
                            <p class="stat-value" style="color: #0f172a;">{{ $report['total_students'] }} Siswa</p>
                        </div>
                    </div>
                @endif

                <!-- Table -->
                <div class="table-container">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">No</th>
                                <th style="min-width: 200px;">Nama Siswa</th>
                                <th style="width: 120px;">NIS</th>
                                @if($this->data['type'] === 'daily')
                                    <th style="width: 150px;">Status</th>
                                    <th style="width: 110px; text-align: center;">Jam Masuk</th>
                                    <th>Catatan</th>
                                @else
                                    <th style="width: 80px; text-align: center;">Hadir</th>
                                    <th style="width: 80px; text-align: center;">Telat</th>
                                    <th style="width: 80px; text-align: center;">Sakit</th>
                                    <th style="width: 80px; text-align: center;">Izin</th>
                                    <th style="width: 80px; text-align: center;">Alpha</th>
                                    <th style="width: 100px; text-align: center;">Kehadiran (%)</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['students'] as $idx => $student)
                                <tr>
                                    <td style="text-align: center; font-weight: 500;">{{ $idx + 1 }}</td>
                                    <td style="font-weight: 600; color: #0f172a;" class="dark:text-white">
                                        {{ $student['name'] }}
                                    </td>
                                    <td>{{ $student['nis'] }}</td>
                                    
                                    @if($this->data['type'] === 'daily')
                                        <td>
                                            @php
                                                $badgeClass = match($student['status']) {
                                                    'present' => 'badge-present',
                                                    'late' => 'badge-late',
                                                    'permission' => 'badge-permission',
                                                    'sick' => 'badge-sick',
                                                    'absent' => 'badge-absent',
                                                    default => 'badge-default',
                                                };
                                                $statusLabel = match($student['status']) {
                                                    'present' => 'Hadir',
                                                    'late' => 'Terlambat',
                                                    'permission' => 'Izin',
                                                    'sick' => 'Sakit',
                                                    'absent' => 'Alpha',
                                                    default => 'Belum Diisi',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>
                                        <td style="text-align: center; font-family: monospace;">{{ $student['check_in_time'] ?: '-' }}</td>
                                        <td style="font-size: 12px; font-style: italic; color: #64748b;">
                                            {{ $student['note'] ?: '-' }}
                                        </td>
                                    @else
                                        <td style="text-align: center; font-weight: 600; color: #059669;">{{ $student['summary']['present'] }}</td>
                                        <td style="text-align: center; font-weight: 600; color: #d97706;">{{ $student['summary']['late'] }}</td>
                                        <td style="text-align: center; font-weight: 600; color: #7c3aed;">{{ $student['summary']['sick'] }}</td>
                                        <td style="text-align: center; font-weight: 600; color: #0284c7;">{{ $student['summary']['permission'] }}</td>
                                        <td style="text-align: center; font-weight: 600; color: #dc2626;">{{ $student['summary']['absent'] }}</td>
                                        <td style="text-align: center; font-weight: 700; color: {{ $student['attendance_percentage'] < 80 ? '#dc2626' : '#047857' }};">
                                            {{ $student['attendance_percentage'] }}%
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" style="text-align: center; padding: 32px; color: #64748b;">
                                        Tidak ada data siswa ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <x-filament::section class="flex flex-col items-center justify-center py-12 text-center">
                <div style="background-color: #f4f4f5; display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 9999px; color: #71717a;" class="dark:bg-zinc-800 dark:text-zinc-400">
                    <x-filament::icon
                        icon="heroicon-o-information-circle"
                        class="w-6 h-6"
                    />
                </div>
                <h3 class="mt-4 text-sm font-semibold text-slate-900 dark:text-slate-100">Pratinjau Belum Dimuat</h3>
                <p class="mt-1 text-sm text-slate-500 max-w-xs mx-auto">Silakan pilih kelas dan kriteria pencarian untuk menampilkan data rekapitulasi kehadiran.</p>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
