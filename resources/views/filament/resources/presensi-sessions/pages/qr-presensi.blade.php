<x-filament-panels::page>
    <style>
        .qr-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
        }
        .qr-card {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 20px;
            padding: 32px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            max-width: 500px;
            width: 100%;
        }
        .dark .qr-card {
            background: #18181b;
            border-color: #27272a;
            box-shadow: 0 4px 24px rgba(0,0,0,0.4);
        }
        .qr-header {
            text-align: center;
        }
        .qr-badge-open {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: #d1fae5;
            color: #065f46;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
        }
        .dark .qr-badge-open {
            background-color: rgba(52, 211, 153, 0.15);
            color: #34d399;
        }
        .qr-badge-closed {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: #f1f5f9;
            color: #475569;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
        }
        .dark .qr-badge-closed {
            background-color: rgba(71, 85, 105, 0.2);
            color: #94a3b8;
        }
        .qr-image {
            border-radius: 12px;
            border: 3px solid #3b82f6;
            padding: 4px;
            background: white;
        }
        .qr-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            width: 100%;
        }
        .qr-info-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
        }
        .dark .qr-info-item {
            background: #09090b;
            border-color: #27272a;
        }
        .qr-info-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
        }
        .dark .qr-info-label {
            color: #94a3b8;
        }
        .qr-info-value {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 4px;
        }
        .dark .qr-info-value {
            color: #f1f5f9;
        }
        .qr-data-code {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 16px;
            font-family: monospace;
            font-size: 14px;
            color: #334155;
            letter-spacing: 0.05em;
            text-align: center;
        }
        .dark .qr-data-code {
            background: #09090b;
            border-color: #27272a;
            color: #94a3b8;
        }
        .instruction-box {
            background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%);
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 16px 20px;
            max-width: 500px;
            width: 100%;
        }
        .dark .instruction-box {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(34, 197, 94, 0.1) 100%);
            border-color: rgba(59, 130, 246, 0.3);
        }
        .pulse-dot {
            width: 10px;
            height: 10px;
            background-color: #22c55e;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.5); }
            70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }
    </style>

    <div class="qr-container">
        {{-- QR Card --}}
        <div class="qr-card">
            {{-- Header --}}
            <div class="qr-header">
                @if($this->record->status === 'open')
                    <span class="qr-badge-open">
                        <span class="pulse-dot"></span>
                        SESI AKTIF
                    </span>
                @else
                    <span class="qr-badge-closed">
                        ● SESI {{ strtoupper(match($this->record->status) {
                            'closed' => 'Ditutup',
                            'scheduled' => 'Terjadwal',
                            'cancelled' => 'Dibatalkan',
                            default => $this->record->status
                        }) }}
                    </span>
                @endif

                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mt-3">
                    QR Code Presensi
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Arahkan kamera ke kode di bawah untuk presensi
                </p>
            </div>

            {{-- QR Image --}}
            @if($this->record->status === 'open')
                <img
                    src="{{ $this->getQrUrl() }}"
                    alt="QR Code Presensi Sesi {{ $this->record->id }}"
                    class="qr-image"
                    width="300"
                    height="300"
                    loading="eager"
                />
                <div class="qr-data-code">
                    {{ $this->getQrData() }}
                </div>
            @else
                <div style="width: 300px; height: 300px; background: #f1f5f9; border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px; border: 2px dashed #cbd5e1;" class="dark:bg-zinc-900 dark:border-zinc-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 48px; height: 48px; color: #94a3b8;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                    <p style="font-size: 14px; font-weight: 600; color: #64748b; text-align: center;">QR Code tidak tersedia<br><span style="font-weight: 400;">Buka sesi terlebih dahulu</span></p>
                </div>
            @endif

            {{-- Info Grid --}}
            <div class="qr-info-grid">
                <div class="qr-info-item">
                    <p class="qr-info-label">Tanggal</p>
                    <p class="qr-info-value">{{ \Carbon\Carbon::parse($this->record->date)->locale('id')->isoFormat('dddd, D MMM Y') }}</p>
                </div>
                <div class="qr-info-item">
                    <p class="qr-info-label">Guru</p>
                    <p class="qr-info-value">{{ $this->record->teacher->name ?? '-' }}</p>
                </div>
                <div class="qr-info-item">
                    <p class="qr-info-label">Jam Mulai</p>
                    <p class="qr-info-value" style="font-family: monospace;">{{ $this->record->start_time ? \Carbon\Carbon::parse($this->record->start_time)->format('H:i') : '-' }}</p>
                </div>
                <div class="qr-info-item">
                    <p class="qr-info-label">Jam Selesai</p>
                    <p class="qr-info-value" style="font-family: monospace;">{{ $this->record->end_time ? \Carbon\Carbon::parse($this->record->end_time)->format('H:i') : '-' }}</p>
                </div>
            </div>

            {{-- Action Buttons --}}
            @if($this->record->status === 'open')
                <div class="flex gap-3">
                    <x-filament::button
                        tag="a"
                        href="{{ $this->getQrUrl() }}"
                        target="_blank"
                        color="primary"
                        icon="heroicon-m-arrow-down-tray"
                    >
                        Unduh QR
                    </x-filament::button>
                    <x-filament::button
                        tag="a"
                        href="{{ url()->previous() }}"
                        color="gray"
                        icon="heroicon-m-arrow-left"
                    >
                        Kembali
                    </x-filament::button>
                </div>
            @else
                <x-filament::button
                    tag="a"
                    href="{{ url()->previous() }}"
                    color="gray"
                    icon="heroicon-m-arrow-left"
                >
                    Kembali ke Daftar
                </x-filament::button>
            @endif
        </div>

        {{-- Instruction Box --}}
        @if($this->record->status === 'open')
        <div class="instruction-box">
            <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 18px; height: 18px; color: #3b82f6;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
                Cara Presensi Siswa
            </h4>
            <ol class="mt-2 space-y-1" style="font-size: 13px; color: #475569; padding-left: 16px;">
                <li>1. Siswa buka aplikasi SIMPAD di smartphone</li>
                <li>2. Klik menu <strong>Presensi</strong> → <strong>Scan QR</strong></li>
                <li>3. Arahkan kamera ke QR Code di layar ini</li>
                <li>4. Sistem otomatis mencatat jam masuk & status kehadiran</li>
                <li>5. Notifikasi WhatsApp dikirim ke orang tua secara otomatis</li>
            </ol>
        </div>
        @endif
    </div>
</x-filament-panels::page>
