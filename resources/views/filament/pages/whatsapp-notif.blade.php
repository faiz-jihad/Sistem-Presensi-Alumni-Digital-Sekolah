<x-filament-panels::page>
<style>
    .wn-header {
        position: relative;
        overflow: hidden;
        border-radius: 1.25rem;
        background: linear-gradient(135deg, #1d4ed8 0%, #4f46e5 50%, #1e40af 100%);
        padding: 1.75rem 2rem;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 20px 40px -10px rgba(30, 64, 175, 0.4);
    }
    @media (min-width: 768px) {
        .wn-header { flex-direction: row; align-items: center; justify-content: space-between; }
    }
    .wn-header-deco1 {
        position: absolute; top: -3rem; right: -3rem;
        width: 10rem; height: 10rem; border-radius: 50%;
        background: rgba(255,255,255,0.08); pointer-events: none;
    }
    .wn-header-deco2 {
        position: absolute; bottom: -4rem; left: -2rem;
        width: 12rem; height: 12rem; border-radius: 50%;
        background: rgba(255,255,255,0.05); pointer-events: none;
    }
    .wn-header-left { display: flex; align-items: flex-start; gap: 1rem; position: relative; z-index: 1; }
    @media (min-width: 768px) { .wn-header-left { align-items: center; } }
    .wn-header-icon {
        width: 3.25rem; height: 3.25rem; flex-shrink: 0;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        border-radius: 1rem;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    .wn-header-title {
        font-size: 1.4rem; font-weight: 800; color: white;
        letter-spacing: -0.02em; line-height: 1.2;
    }
    .wn-header-desc {
        font-size: 0.85rem; color: rgba(219,234,254,0.9);
        margin-top: 0.35rem; max-width: 38rem; line-height: 1.5;
    }
    .wn-header-badge {
        position: relative; z-index: 1;
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: rgba(0,0,0,0.18);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 0.75rem;
        padding: 0.6rem 1rem;
        font-size: 0.75rem; font-weight: 600; color: rgba(255,255,255,0.9);
        white-space: nowrap;
    }
    .wn-badge-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #fbbf24; flex-shrink: 0;
        animation: wn-pulse 1.5s infinite;
    }
    @keyframes wn-pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }

    .wn-card {
        position: relative;
        border-radius: 1.25rem;
        background: white;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .dark .wn-card { background: #18181b; border-color: #27272a; }
    .wn-card-topbar {
        height: 4px;
        background: linear-gradient(to right, #3b82f6, #6366f1, #a855f7);
    }
    .wn-card-body { padding: 1.75rem 2rem; }

    .wn-form-wrap {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
    }
    .dark .wn-form-wrap { background: rgba(39,39,42,0.4); border-color: rgba(63,63,70,0.5); }

    .wn-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
    @media (max-width: 767px) {
        .wn-stats-grid { grid-template-columns: 1fr; }
    }

    .wn-stat-card {
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        background: white;
        border: 1px solid #e2e8f0;
        border-left: 4px solid #3b82f6;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .wn-stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
    .dark .wn-stat-card { background: #18181b; border-color: #27272a; }

    .wn-stat-card-green { border-left-color: #059669; }
    .wn-stat-card-rose { border-left-color: #e11d48; }

    .wn-stat-icon {
        width: 2.75rem; height: 2.75rem; flex-shrink: 0;
        border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center;
    }
    .wn-stat-icon-blue { background: #eff6ff; color: #2563eb; }
    .dark .wn-stat-icon-blue { background: rgba(37,99,235,0.15); color: #60a5fa; }
    .wn-stat-icon-green { background: #ecfdf5; color: #059669; }
    .dark .wn-stat-icon-green { background: rgba(5,150,105,0.15); color: #34d399; }
    .wn-stat-icon-rose { background: #fff1f2; color: #e11d48; }
    .dark .wn-stat-icon-rose { background: rgba(225,29,72,0.15); color: #fb7185; }

    .wn-stat-label {
        font-size: 0.7rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.08em;
        color: #6b7280;
    }
    .dark .wn-stat-label { color: #9ca3af; }
    .wn-stat-value {
        font-size: 2rem; font-weight: 800; line-height: 1.2;
        color: #111827;
    }
    .dark .wn-stat-value { color: #f9fafb; }
    .wn-stat-value-green { color: #059669; }
    .dark .wn-stat-value-green { color: #34d399; }
    .wn-stat-value-rose { color: #e11d48; }
    .dark .wn-stat-value-rose { color: #fb7185; }

    .wn-actions {
        display: flex; flex-wrap: wrap; gap: 0.75rem;
        justify-content: space-between; align-items: center;
        padding-top: 1.25rem; margin-top: 1.25rem;
        border-top: 1px solid #f1f5f9;
    }
    .dark .wn-actions { border-top-color: rgba(39,39,42,0.8); }

    .wn-recipient-badge {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.75rem;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        font-size: 0.75rem; font-weight: 600; color: #065f46;
    }
    .dark .wn-recipient-badge { background: rgba(6,78,59,0.2); border-color: rgba(6,95,70,0.3); color: #6ee7b7; }

    .wn-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-top: 0.5rem;
    }
    @media (max-width: 767px) {
        .wn-info-grid { grid-template-columns: 1fr; }
    }

    .wn-info-box {
        border-radius: 1rem;
        padding: 1.25rem 1.5rem;
        background: white;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    }
    .dark .wn-info-box { background: #18181b; border-color: #27272a; }

    .wn-info-title {
        display: flex; align-items: center; gap: 0.5rem;
        font-size: 0.85rem; font-weight: 700; color: #111827;
        margin-bottom: 0.75rem;
    }
    .dark .wn-info-title { color: #f9fafb; }

    .wn-step {
        display: flex; gap: 0.75rem; margin-bottom: 0.75rem;
    }
    .wn-step-num {
        width: 1.75rem; height: 1.75rem; flex-shrink: 0;
        border-radius: 50%;
        background: #eff6ff; color: #2563eb;
        font-size: 0.7rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
    }
    .dark .wn-step-num { background: rgba(37,99,235,0.15); color: #60a5fa; }
    .wn-step-text {
        font-size: 0.75rem; color: #4b5563; line-height: 1.4;
    }
    .dark .wn-step-text { color: #9ca3af; }
    .wn-step-text strong { color: #111827; }
    .dark .wn-step-text strong { color: #f9fafb; }

    .wn-check-item {
        display: flex; align-items: flex-start; gap: 0.5rem;
        font-size: 0.75rem; color: #4b5563; line-height: 1.4;
        margin-bottom: 0.5rem;
    }
    .dark .wn-check-item { color: #9ca3af; }
    .wn-check-item code {
        background: #f1f5f9; padding: 0.1rem 0.4rem;
        border-radius: 0.25rem; font-size: 0.65rem;
        color: #be123c;
    }
    .dark .wn-check-item code { background: rgba(39,39,42,0.8); color: #fb7185; }

    .wn-warning-box {
        margin-top: 0.75rem;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        background: #fffbeb;
        border: 1px solid #fde68a;
        font-size: 0.7rem; color: #92400e; line-height: 1.5;
        display: flex; align-items: flex-start; gap: 0.5rem;
    }
    .dark .wn-warning-box { background: rgba(120,53,15,0.15); border-color: rgba(146,64,14,0.3); color: #fcd34d; }
</style>

<div style="display:flex;flex-direction:column;gap:1.5rem;">

    {{-- Header Banner --}}
    <div class="wn-header">
        <div class="wn-header-deco1"></div>
        <div class="wn-header-deco2"></div>

        <div class="wn-header-left">
            <div class="wn-header-icon">
                <x-heroicon-o-chat-bubble-left-right style="width:26px;height:26px;color:white;" />
            </div>
            <div>
                <div class="wn-header-title">Kirim Notifikasi WhatsApp</div>
                <div class="wn-header-desc">
                    Kirim rekapitulasi kehadiran massal ke nomor WhatsApp orang tua siswa terdaftar secara otomatis.
                    Notifikasi akan <strong style="color:white;">dikirim via Fonnte</strong> ke orang tua siswa.
                </div>
            </div>
        </div>

        <div class="wn-header-badge">
            <span class="wn-badge-dot"></span>
            Antrean Aktif
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="wn-stats-grid">
        <div class="wn-stat-card">
            <div class="wn-stat-icon wn-stat-icon-blue">
                <x-heroicon-o-user-group style="width:22px;height:22px;" />
            </div>
            <div>
                <div class="wn-stat-label">Total Siswa Aktif</div>
                <div class="wn-stat-value">{{ $this->getStats()['total_students'] ?? 0 }}</div>
            </div>
        </div>

        <div class="wn-stat-card wn-stat-card-green">
            <div class="wn-stat-icon wn-stat-icon-green">
                <x-heroicon-o-device-phone-mobile style="width:22px;height:22px;" />
            </div>
            <div>
                <div class="wn-stat-label">Orang Tua Terdaftar WA</div>
                <div class="wn-stat-value wn-stat-value-green">{{ $this->getStats()['has_phone'] ?? 0 }}</div>
            </div>
        </div>

        <div class="wn-stat-card wn-stat-card-rose">
            <div class="wn-stat-icon wn-stat-icon-rose">
                <x-heroicon-o-exclamation-triangle style="width:22px;height:22px;" />
            </div>
            <div>
                <div class="wn-stat-label">Belum Ada No. WA</div>
                <div class="wn-stat-value wn-stat-value-rose">{{ $this->getStats()['no_phone'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="wn-card">
        <div class="wn-card-topbar"></div>

        <div class="wn-card-body">
            <div style="display:flex;flex-direction:column;gap:1.75rem;">

                {{-- Filter & Configuration --}}
                <div>
                    <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.75rem;">
                        <x-heroicon-o-adjustments-horizontal style="width:20px;height:20px;color:#4f46e5;" />
                        <span style="font-size:0.85rem;font-weight:700;color:#111827;">Filter & Konfigurasi Notifikasi</span>
                        <span style="font-size:0.7rem;color:#6b7280;margin-left:0.25rem;">Tentukan tipe laporan dan periode rekapitulasi</span>
                    </div>
                    <div class="wn-form-wrap">
                        {{ $this->form }}
                    </div>
                </div>

                {{-- Actions --}}
                <div class="wn-actions">
                    <div class="wn-recipient-badge">
                        <x-heroicon-s-users style="width:16px;height:16px;" />
                        <span>{{ $this->getRecipientCount() }} Orang tua terdaftar sebagai penerima</span>
                    </div>
                    <div style="display:flex;gap:0.5rem;">
                        {{ $this->sendNotifAction }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Info & Warning Grid --}}
    <div class="wn-info-grid">

        {{-- Info Box --}}
        <div class="wn-info-box">
            <div class="wn-info-title">
                <x-heroicon-o-information-circle style="width:20px;height:20px;color:#2563eb;" />
                <span>Alur &amp; Cara Kerja</span>
            </div>

            <div class="wn-step">
                <span class="wn-step-num">1</span>
                <div class="wn-step-text">
                    <strong>Pilih Periode Rekap</strong><br>
                    Tentukan format rekapitulasi harian (tanggal) atau bulanan (bulan &amp; tahun).
                </div>
            </div>

            <div class="wn-step">
                <span class="wn-step-num">2</span>
                <div class="wn-step-text">
                    <strong>Kirim Notifikasi</strong><br>
                    Klik tombol kirim di atas. Konfirmasi jumlah penerima akan muncul sebelum pengiriman.
                </div>
            </div>

            <div class="wn-step">
                <span class="wn-step-num">3</span>
                <div class="wn-step-text">
                    <strong>Sistem Antrean (Queue)</strong><br>
                    Pesan diproses satu persatu secara asinkron agar aman dari rate limiting WhatsApp.
                </div>
            </div>
        </div>

        {{-- Warning Box --}}
        <div class="wn-info-box">
            <div class="wn-info-title">
                <x-heroicon-o-exclamation-triangle style="width:20px;height:20px;color:#d97706;" />
                <span>Persyaratan &amp; Status</span>
            </div>

            <div class="wn-check-item">
                <x-heroicon-s-check-circle style="width:16px;height:16px;color:#059669;flex-shrink:0;margin-top:1px;" />
                <span>Token Fonnte terkonfigurasi di env <code>WHATSAPP_API_TOKEN</code>.</span>
            </div>

            <div class="wn-check-item">
                <x-heroicon-s-check-circle style="width:16px;height:16px;color:#059669;flex-shrink:0;margin-top:1px;" />
                <span>Jalankan Queue worker: <code>php artisan queue:work</code>.</span>
            </div>

            <div class="wn-warning-box">
                <x-heroicon-s-bell-alert style="width:16px;height:16px;flex-shrink:0;margin-top:1px;" />
                <span>Sistem juga mengirim notifikasi otomatis real-time ke orang tua setiap kali siswa melakukan scan QR mandiri atau guru menginput kehadiran di kelas.</span>
            </div>
        </div>

    </div>

</div>

<x-filament-actions::modals />
</x-filament-panels::page>