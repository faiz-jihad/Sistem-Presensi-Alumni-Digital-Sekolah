<x-filament-panels::page>
<style>
    .set-page {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        font-family: 'Inter', sans-serif;
    }
    .set-card {
        border-radius: 1.25rem;
        background: white;
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        transition: all 0.2s ease;
    }
    .dark .set-card {
        background: #18181b;
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
    }
    .set-card-body {
        padding: 2rem;
    }
    .set-card-title {
        font-size: 1.2rem;
        font-weight: 800;
        margin-bottom: 1.25rem;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .dark .set-card-title {
        color: #f8fafc;
    }
    .set-status-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        margin-top: 1.25rem;
    }
    .set-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.625rem 1.25rem;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.875rem;
        width: fit-content;
    }
    .set-status-connected {
        background: #d1fae5;
        border: 1px solid #10b981;
        color: #065f46;
    }
    .dark .set-status-connected {
        background: rgba(16, 185, 129, 0.12);
        border-color: rgba(16, 185, 129, 0.35);
        color: #34d399;
    }
    .set-status-disconnected {
        background: #fee2e2;
        border: 1px solid #ef4444;
        color: #991b1b;
    }
    .dark .set-status-disconnected {
        background: rgba(239, 68, 68, 0.12);
        border-color: rgba(239, 68, 68, 0.35);
        color: #f87171;
    }
    .status-text-panel {
        font-size: 0.9rem;
        line-height: 1.6;
        color: #475569;
    }
    .dark .status-text-panel {
        color: #94a3b8;
    }
    .status-text-success {
        color: #059669;
        font-weight: 700;
    }
    .dark .status-text-success {
        color: #34d399;
    }
    .status-text-error {
        color: #dc2626;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .dark .status-text-error {
        color: #f87171;
    }
    .set-list {
        margin: 1.25rem 0;
        padding-left: 1.5rem;
        font-size: 0.875rem;
        color: #475569;
        line-height: 1.6;
    }
    .dark .set-list {
        color: #94a3b8;
    }
    .set-list li {
        margin-bottom: 0.625rem;
    }
    .set-list code {
        background: #f1f5f9;
        color: #0f172a;
        padding: 0.125rem 0.375rem;
        border-radius: 0.25rem;
        font-family: monospace;
        font-size: 0.85em;
    }
    .dark .set-list code {
        background: #27272a;
        color: #f4f4f5;
    }
    .qr-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1.25rem;
        padding: 2rem;
        background: #f8fafc;
        border-radius: 1rem;
        border: 2px dashed #cbd5e1;
        width: 100%;
        max-width: 420px;
        margin: 1rem auto;
    }
    .dark .qr-container {
        background: rgba(30, 41, 59, 0.4);
        border-color: rgba(148, 163, 184, 0.2);
    }
    .qr-title {
        font-size: 0.9rem;
        font-weight: 800;
        color: #334155;
    }
    .dark .qr-title {
        color: #cbd5e1;
    }
    .qr-img-wrapper {
        background: white;
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }
    .qr-subtext {
        font-size: 0.75rem;
        color: #64748b;
        font-style: italic;
        text-align: center;
    }
    .dark .qr-subtext {
        color: #94a3b8;
    }
    .qr-loading-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        padding: 2.5rem;
    }
    .qr-spinner {
        width: 3rem;
        height: 3rem;
        border: 4px solid #cbd5e1;
        border-top-color: #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    .qr-loading-text {
        font-size: 0.85rem;
        color: #64748b;
    }
    .dark .qr-loading-text {
        color: #94a3b8;
    }
    .set-user-profile {
        margin-top: 1.25rem;
        padding: 1.25rem;
        background: #f0fdf4;
        border: 1px solid rgba(16, 185, 129, 0.2);
        border-radius: 0.875rem;
        color: #14532d;
        display: flex;
        align-items: center;
        gap: 1rem;
        width: fit-content;
        min-width: 320px;
    }
    .dark .set-user-profile {
        background: rgba(16, 185, 129, 0.08);
        border-color: rgba(16, 185, 129, 0.25);
        color: #34d399;
    }
    .set-user-avatar {
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        background: #10b981;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.25rem;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
    }
    .set-user-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    .set-user-name {
        font-weight: 800;
        font-size: 1rem;
    }
    .set-user-phone {
        font-size: 0.85rem;
        opacity: 0.8;
        font-family: monospace;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<div class="set-page">

    {{-- WhatsApp Gateway Status Card --}}
    <div class="set-card" style="border-top: 4px solid {{ $gatewayStatus['ready'] ? '#10b981' : '#ef4444' }};">
        <div class="set-card-body">
            <div class="set-card-title">
                <x-heroicon-o-chat-bubble-left-right style="width:24px;height:24px;color:#4f46e5;" />
                <span>Status &amp; Koneksi WhatsApp Gateway</span>
            </div>

            <div class="set-status-wrapper">
                
                {{-- Badge Status --}}
                <div>
                    <span class="set-status-badge {{ $gatewayStatus['ready'] ? 'set-status-connected' : 'set-status-disconnected' }}">
                        <span style="width:8px;height:8px;border-radius:50%;background:{{ $gatewayStatus['ready'] ? '#10b981' : '#ef4444' }};"></span>
                        WhatsApp Gateway: {{ $gatewayStatus['ready'] ? 'TERHUBUNG (SIAP)' : 'BELUM TERHUBUNG / OFFLINE' }}
                    </span>
                </div>

                {{-- Status Description --}}
                <div class="status-text-panel">
                    @if($gatewayStatus['ready'])
                        <p class="status-text-success">Sistem terhubung ke WhatsApp Web local dan siap mengirimkan notifikasi presensi orang tua.</p>
                        @if(!empty($gatewayStatus['user']))
                            <div class="set-user-profile">
                                <div class="set-user-avatar">
                                    {{ substr($gatewayStatus['user']['name'] ?? 'W', 0, 1) }}
                                </div>
                                <div class="set-user-info">
                                    <span class="set-user-name">{{ $gatewayStatus['user']['name'] ?? 'WhatsApp Client' }}</span>
                                    <span class="set-user-phone">{{ $gatewayStatus['user']['number'] ?? '-' }}</span>
                                </div>
                            </div>
                        @endif
                    @else
                        <p class="status-text-error">
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
                    <div class="qr-container">
                        <span class="qr-title">Scan QR Code Berikut:</span>
                        
                        @if($gatewayStatus['qr'])
                            <div class="qr-img-wrapper">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ urlencode($gatewayStatus['qr']) }}" alt="WhatsApp QR Code" />
                            </div>
                            <span class="qr-subtext">Halaman merefresh QR Code secara berkala. Silakan segarkan halaman jika QR kedaluwarsa.</span>
                        @else
                            <div class="qr-loading-wrapper">
                                <div class="qr-spinner"></div>
                                <span class="qr-loading-text">Menunggu QR Code dari server gateway...</span>
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    </div>

</div>

<x-filament-actions::modals />
</x-filament-panels::page>
