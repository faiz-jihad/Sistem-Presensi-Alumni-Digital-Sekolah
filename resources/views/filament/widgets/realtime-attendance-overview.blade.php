@php
    $data = $this->getData();
    $classes = $data['classes'];
    $stats = $data['stats'];
@endphp

<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-o-presentation-chart-line" class="shadow-sm">
        <x-slot name="heading">
            Kehadiran & KBM Real-time Hari Ini
        </x-slot>
        
        <x-slot name="description">
            Pantau status pembukaan kelas oleh guru secara langsung hari ini. Data diperbarui otomatis.
        </x-slot>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 mt-3">
            <!-- Card Belum Dibuka -->
            <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/30 p-5 rounded-2xl flex flex-col justify-center shadow-sm">
                <span class="text-xs uppercase font-semibold tracking-wider text-amber-700 dark:text-amber-400">Belum Dibuka</span>
                <span class="text-3xl font-extrabold text-amber-800 dark:text-amber-300 mt-2 flex items-baseline gap-1.5">
                    {{ $stats['unopened'] }} <span class="text-sm font-medium text-amber-600 dark:text-amber-500">Kelas</span>
                </span>
            </div>
            
            <!-- Card Sedang Belajar -->
            <div class="bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/30 p-5 rounded-2xl flex flex-col justify-center shadow-sm">
                <span class="text-xs uppercase font-semibold tracking-wider text-emerald-700 dark:text-emerald-400">Sedang Belajar</span>
                <span class="text-3xl font-extrabold text-emerald-800 dark:text-emerald-300 mt-2 flex items-baseline gap-1.5">
                    {{ $stats['open'] }} <span class="text-sm font-medium text-emerald-600 dark:text-emerald-500">Kelas</span>
                </span>
            </div>

            <!-- Card Selesai KBM -->
            <div class="bg-blue-50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/30 p-5 rounded-2xl flex flex-col justify-center shadow-sm">
                <span class="text-xs uppercase font-semibold tracking-wider text-blue-700 dark:text-blue-400">Selesai KBM</span>
                <span class="text-3xl font-extrabold text-blue-800 dark:text-blue-300 mt-2 flex items-baseline gap-1.5">
                    {{ $stats['closed'] }} <span class="text-sm font-medium text-blue-600 dark:text-blue-500">Kelas</span>
                </span>
            </div>

            <!-- Card Terlewat -->
            <div class="bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/30 p-5 rounded-2xl flex flex-col justify-center shadow-sm">
                <span class="text-xs uppercase font-semibold tracking-wider text-rose-700 dark:text-rose-400">Terlewat (Missed)</span>
                <span class="text-3xl font-extrabold text-rose-800 dark:text-rose-300 mt-2 flex items-baseline gap-1.5">
                    {{ $stats['missed'] }} <span class="text-sm font-medium text-rose-600 dark:text-rose-500">Kelas</span>
                </span>
            </div>
        </div>

        <style>
            .kbm-table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            .kbm-table th {
                padding: 12px 18px !important;
                font-weight: 700 !important;
                font-size: 11px !important;
                text-transform: uppercase !important;
                color: #6b7280 !important;
                background: #f9fafb !important;
                border-bottom: 1px solid #e5e7eb !important;
            }
            .dark .kbm-table th {
                color: #9ca3af !important;
                background: #111827 !important;
                border-bottom: 1px solid #1f2937 !important;
            }
            .kbm-table td {
                padding: 14px 18px !important;
                font-size: 13px !important;
                border-bottom: 1px solid #f3f4f6 !important;
            }
            .dark .kbm-table td {
                border-bottom: 1px solid #1f2937 !important;
            }
        </style>

        <!-- Real-time Table List -->
        <div class="overflow-x-auto border border-gray-200 dark:border-gray-800 rounded-2xl bg-white dark:bg-gray-900/50 shadow-sm">
            <table class="kbm-table">
                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengampu</th>
                        <th>Jam KBM</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Log Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-gray-700 dark:text-gray-300">
                    @forelse($classes as $item)
                        <tr class="transition-colors duration-150 hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ $item['class_name'] }}</td>
                            <td class="px-6 py-4 font-semibold text-primary-600 dark:text-primary-400">{{ $item['subject_name'] }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $item['teacher_name'] }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $item['time_range'] }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($item['status'] === 'open')
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-900/30">
                                        {{ $item['status_label'] }}
                                    </span>
                                @elseif($item['status'] === 'closed')
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/30 dark:text-blue-400 dark:border-blue-900/30">
                                        {{ $item['status_label'] }}
                                    </span>
                                @elseif($item['status'] === 'missed')
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/30 dark:text-rose-400 dark:border-rose-900/30">
                                        {{ $item['status_label'] }}
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/30 dark:text-amber-400 dark:border-amber-900/30">
                                        {{ $item['status_label'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-xs text-gray-500 dark:text-gray-400 font-medium">
                                @if($item['status'] === 'open')
                                    Mulai: <span class="color-success-600 dark:color-success-400 font-bold text-emerald-600 dark:text-emerald-400">{{ $item['opened_at'] }}</span>
                                @elseif($item['status'] === 'closed')
                                    Tutup: <span class="color-primary-600 dark:color-primary-400 font-bold text-blue-600 dark:text-blue-400">{{ $item['closed_at'] }}</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 dark:text-gray-500 text-sm">
                                Tidak ada jadwal aktif pelajaran hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
