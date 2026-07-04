<x-filament-panels::page>
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
        .summary-item-sick     { background: #faf5ff; border-color: #e9d5ff; }
        .summary-item-izin     { background: #f0f9ff; border-color: #bae6fd; }
        .summary-item-absent   { background: #fef2f2; border-color: #fca5a5; }

        .dark .summary-item-present  { background: rgba(6,95,70,0.15);  border-color: rgba(6,95,70,0.4);  }
        .dark .summary-item-late     { background: rgba(146,64,14,0.15); border-color: rgba(146,64,14,0.4); }
        .dark .summary-item-sick     { background: rgba(107,33,168,0.15); border-color: rgba(107,33,168,0.4); }
        .dark .summary-item-izin     { background: rgba(7,89,133,0.15);  border-color: rgba(7,89,133,0.4); }
        .dark .summary-item-absent   { background: rgba(153,27,27,0.15); border-color: rgba(153,27,27,0.4); }

        .summary-number { font-size: 22px; font-weight: 800; }
        .summary-label  { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px; opacity: 0.75; }

        .summary-item-present .summary-number  { color: #065f46; }
        .summary-item-late .summary-number     { color: #92400e; }
        .summary-item-sick .summary-number     { color: #6b21a8; }
        .summary-item-izin .summary-number     { color: #075985; }
        .summary-item-absent .summary-number   { color: #991b1b; }

        .dark .summary-item-present .summary-number  { color: #34d399; }
        .dark .summary-item-late .summary-number     { color: #fbbf24; }
        .dark .summary-item-sick .summary-number     { color: #c084fc; }
        .dark .summary-item-izin .summary-number     { color: #38bdf8; }
        .dark .summary-item-absent .summary-number   { color: #f87171; }
    </style>

    <div class="space-y-5">
        {{-- Filter Section --}}
        <x-filament::section>
            <x-slot name="heading">Presensi Manual Guru</x-slot>
            <x-slot name="description">
                Pilih kelas dan tanggal, lalu klik <strong>Muat Daftar Siswa</strong> atau tambah siswa satu per satu.
            </x-slot>

            <form wire:submit.prevent="submit">
                <div class="space-y-5">
                    {{ $this->form }}

                    {{-- Summary Bar --}}
                    @php $summary = $this->getAttendanceSummary(); @endphp
                    @if($summary['total'] > 0)
                        <div>
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">
                                Ringkasan Kehadiran ({{ $summary['total'] }} Siswa)
                            </p>
                            <div class="summary-bar">
                                <div class="summary-item summary-item-present">
                                    <p class="summary-number">{{ $summary['present'] }}</p>
                                    <p class="summary-label">Hadir</p>
                                </div>
                                <div class="summary-item summary-item-late">
                                    <p class="summary-number">{{ $summary['late'] }}</p>
                                    <p class="summary-label">Terlambat</p>
                                </div>
                                <div class="summary-item summary-item-sick">
                                    <p class="summary-number">{{ $summary['sick'] }}</p>
                                    <p class="summary-label">Sakit</p>
                                </div>
                                <div class="summary-item summary-item-izin">
                                    <p class="summary-number">{{ $summary['permission'] }}</p>
                                    <p class="summary-label">Izin</p>
                                </div>
                                <div class="summary-item summary-item-absent">
                                    <p class="summary-number">{{ $summary['absent'] }}</p>
                                    <p class="summary-label">Alpha</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap gap-3 justify-between items-center pt-2 border-t border-slate-200 dark:border-slate-700">
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
                            Simpan Presensi & Kirim Notif WA
                        </x-filament::button>
                    </div>
                </div>
            </form>
        </x-filament::section>

        {{-- Info Box --}}
        <div style="background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); border: 1px solid #bfdbfe; border-radius: 12px; padding: 16px 20px;" class="dark:border-blue-900" style="">
            <p class="text-sm font-semibold text-blue-900 dark:text-blue-300">ℹ️ Informasi Otomatis</p>
            <ul class="mt-1 text-sm text-blue-700 dark:text-blue-400 space-y-1 list-disc list-inside">
                <li>Notifikasi WhatsApp dikirim otomatis ke orang tua saat presensi disimpan</li>
                <li>Status <strong>Izin</strong> dan <strong>Sakit</strong> memerlukan verifikasi wali kelas/admin</li>
                <li>Data presensi yang sudah tersimpan dapat dilihat di menu <strong>Presensi Siswa</strong></li>
            </ul>
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
