<x-filament-panels::page>
<style>
    .set-card {
        border-radius: 1rem;
        background: white;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .dark .set-card { background: #18181b; border-color: #27272a; }
    .set-card-body { padding: 1.5rem 1.75rem; }
    .set-card-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .dark .set-card-title { color: #f3f4f6; }
    .set-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.75rem;
        font-weight: 700;
        font-size: 0.85rem;
    }
    .set-status-connected {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }
    .dark .set-status-connected { background: rgba(16,185,129,0.15); border-color: rgba(16,185,129,0.25); color: #34d399; }
    .set-status-disconnected {
        background: #fef2f2;
        border: 1px solid #fca5a5;
        color: #991b1b;
    }
    .dark .set-status-disconnected { background: rgba(239,68,68,0.15); border-color: rgba(239,68,68,0.25); color: #f87171; }
    .set-list {
        margin: 1rem 0;
        padding-left: 1.25rem;
        font-size: 0.85rem;
        color: #4b5563;
        line-height: 1.6;
    }
    .dark .set-list { color: #9ca3af; }
    .set-list li { margin-bottom: 0.5rem; }
</style>

<div style="display:flex;flex-direction:column;gap:1.5rem;">

    {{-- WhatsApp Gateway Status Card --}}
    <div class="set-card" style="border-top: 4px solid {{ $gatewayStatus['ready'] ? '#10b981' : '#ef4444' }};">
        <div class="set-card-body">
            <div class="set-card-title">
                <x-heroicon-o-chat-bubble-left-right style="width:24px;height:24px;color:#4f46e5;" />
                <span>Status &amp; Koneksi WhatsApp Gateway</span>
            </div>

            <div style="display:flex;flex-direction:column;gap:1.5rem;margin-top:1.25rem;">
                
                {{-- Badge Status --}}
                <div>
                    <span class="set-status-badge {{ $gatewayStatus['ready'] ? 'set-status-connected' : 'set-status-disconnected' }}">
                        <span style="width:8px;height:8px;border-radius:50%;background:{{ $gatewayStatus['ready'] ? '#10b981' : '#ef4444' }};"></span>
                        WhatsApp Gateway: {{ $gatewayStatus['ready'] ? 'TERHUBUNG (SIAP)' : 'BELUM TERHUBUNG / OFFLINE' }}
                    </span>
                </div>

                {{-- Status Description --}}
                <div style="font-size: 0.85rem; color:#4b5563; line-height:1.5;">
                    @if($gatewayStatus['ready'])
                        <p style="color:#059669; font-weight: 600;">Sistem terhubung ke WhatsApp Web local dan siap mengirimkan notifikasi presensi orang tua.</p>
                    @else
                        <p style="color:#dc2626; font-weight: 600; margin-bottom: 0.5rem;">
                            {{ $gatewayStatus['error'] ?? 'WhatsApp gateway tidak aktif atau belum melakukan login.' }}
                        </p>
                        <p>Silakan ikuti instruksi di bawah ini untuk menghubungkan sistem dengan akun WhatsApp Anda:</p>
                        
                        <ul class="set-list">
                            <li>Pastikan Anda sudah menjalankan perintah: <code>npx pm2 start ecosystem.config.cjs</code> (atau <code>node whatsapp-service.js</code>) di server/komputer host Anda.</li>
                            <li>Setelah server gateway aktif, scan QR Code di bawah menggunakan menu <strong>WhatsApp Web / Linked Devices</strong> pada aplikasi WhatsApp di handphone Anda.</li>
                            <li>Halaman ini akan diperbarui statusnya secara otomatis setelah proses login berhasil.</li>
                        </ul>
                    @endif
                </div>

                {{-- QR Code Display --}}
                @if(!$gatewayStatus['ready'])
                    <div style="display:flex;justify-content:center;padding:1.5rem 0;background:#f9fafb;border-radius:0.75rem;border:1px dashed #cbd5e1;flex-direction:column;align-items:center;gap:1rem;">
                        <span style="font-size: 0.85rem; font-weight: 700; color: #4b5563;">Scan QR Code Berikut:</span>
                        
                        @if($gatewayStatus['qr'])
                            <div style="background: white; padding: 10px; border: 1px solid #e2e8f0; border-radius: 0.5rem; display: inline-block; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode($gatewayStatus['qr']) }}" alt="WhatsApp QR Code" />
                            </div>
                            <span style="font-size: 0.75rem; color: #9ca3af; font-style: italic;">Halaman merefresh QR Code secara berkala. Silakan segarkan halaman jika QR kedaluwarsa.</span>
                        @else
                            <div style="display:flex;flex-direction:column;align-items:center;gap:0.5rem;padding:2rem;">
                                <div style="width: 2.5rem; height: 2.5rem; border: 3px solid #cbd5e1; border-top-color: #3b82f6; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                                <span style="font-size: 0.8rem; color: #9ca3af;">Menunggu QR Code dari server gateway...</span>
                            </div>
                            <style>@keyframes spin { to { transform: rotate(360deg); } }</style>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Settings Form --}}
    <form wire:submit="save">
        {{ $this->form }}

        <div style="margin-top: 1.5rem; display: flex; gap: 0.5rem; justify-content: flex-end;">
            <x-filament::button type="submit" size="lg">
                Simpan Pengaturan
            </x-filament::button>
        </div>
    </form>

    {{-- System Queue Status Card --}}
    <div class="set-card">
        <div class="set-card-body">
            <div class="set-card-title">
                <x-heroicon-o-cpu-chip style="width:24px;height:24px;color:#10b981;" />
                <span>Sistem Antrean (Queue)</span>
            </div>
            
            <div style="font-size:0.85rem; color:#4b5563; line-height:1.6; margin-top:0.75rem;">
                <p>Untuk mode production, pengiriman pesan WhatsApp dijalankan secara asinkron di background menggunakan antrean database (Queue) agar tidak membebani server dan tidak membuat aplikasi lambat saat presensi massal.</p>
                
                <table style="width:100%; border-collapse:collapse; margin-top:1rem; margin-bottom:1rem;">
                    <tr style="border-bottom:1px solid #e2e8f0;">
                        <td style="padding:0.5rem 0; font-weight:700;">Driver Queue saat ini (<code>.env</code>)</td>
                        <td style="padding:0.5rem 0; color:#4f46e5; font-weight:700;">{{ config('queue.default') }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid #e2e8f0;">
                        <td style="padding:0.5rem 0; font-weight:700;">Status Antrean</td>
                        <td style="padding:0.5rem 0;">
                            @if(config('queue.default') === 'database')
                                <span style="color:#059669; font-weight:700;">Aktif (Siap Production)</span>
                            @else
                                <span style="color:#d97706; font-weight:700;">Sync / Non-Queue (Kurang direkomendasikan untuk production massal)</span>
                            @endif
                        </td>
                    </tr>
                </table>

                @if(config('queue.default') === 'database')
                    <div style="background:#eff6ff; border-left:4px solid #3b82f6; padding:0.75rem 1rem; border-radius:0.375rem; color:#1e40af; font-size:0.8rem;">
                        <strong>Catatan Production:</strong> Pastikan Anda menjalankan command <code>php artisan queue:work</code> di server Anda (bisa dikelola dengan PM2 atau Supervisor) agar antrean notifikasi diproses secara otomatis.
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

<x-filament-actions::modals />
</x-filament-panels::page>
