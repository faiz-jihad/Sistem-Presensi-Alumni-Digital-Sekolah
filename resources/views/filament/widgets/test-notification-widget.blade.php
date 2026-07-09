<x-filament-widgets::widget>
    <x-filament::section>
        <div class="simpad-notification-console">
            <div class="simpad-notification-console__header">
                <div>
                    <h2>Uji Notifikasi</h2>
                    <p>Status browser, service worker, dan subscription Web Push.</p>
                </div>

                <div class="simpad-notification-console__actions">
                    <x-filament::button
                        type="button"
                        color="gray"
                        icon="heroicon-o-information-circle"
                        onclick="simpadCheckStatus()"
                    >
                        Cek Status
                    </x-filament::button>

                    <x-filament::button
                        type="button"
                        color="primary"
                        icon="heroicon-o-bell"
                        onclick="simpadTestNotif()"
                    >
                        Test Browser
                    </x-filament::button>

                    <x-filament::button
                        type="button"
                        color="success"
                        icon="heroicon-o-paper-airplane"
                        wire:click="sendTestNotification"
                    >
                        Test Server
                    </x-filament::button>
                </div>
            </div>

            <div class="simpad-notification-console__meta">
                <span>Subscription tersimpan: {{ $this->getPushSubscriptionCount() }}</span>
                <span id="simpad-webpush-inline-status">Status browser: belum dicek</span>
            </div>

            <div id="simpad-notif-log" class="simpad-notification-console__log">
                <span>// Klik Cek Status untuk membaca kondisi Web Push.</span>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

@script
<script>
    function simpadLog(message, tone) {
        var box = document.getElementById('simpad-notif-log');
        if (!box) return;

        var line = document.createElement('div');
        line.className = 'simpad-notification-console__log-line simpad-notification-console__log-line--' + (tone || 'muted');
        line.textContent = '> ' + message;
        box.appendChild(line);
        box.scrollTop = box.scrollHeight;
    }

    function simpadSetInlineStatus(message) {
        var el = document.getElementById('simpad-webpush-inline-status');
        if (el) el.textContent = message;
    }

    function simpadCheckStatus() {
        var box = document.getElementById('simpad-notif-log');
        if (box) box.innerHTML = '';

        simpadLog('Cek status notifikasi dimulai.', 'info');

        if (!('Notification' in window)) {
            simpadLog('Notification API tidak tersedia di browser ini.', 'danger');
            simpadSetInlineStatus('Status browser: tidak didukung');
            return;
        }

        simpadLog('Permission: ' + Notification.permission, Notification.permission === 'granted' ? 'success' : 'warning');
        simpadSetInlineStatus('Status browser: ' + Notification.permission);

        if (!('serviceWorker' in navigator)) {
            simpadLog('Service Worker tidak tersedia.', 'danger');
            return;
        }

        navigator.serviceWorker.getRegistrations().then(function (registrations) {
            simpadLog('Service worker terdaftar: ' + registrations.length, 'info');

            registrations.forEach(function (registration) {
                var activeUrl = registration.active ? registration.active.scriptURL : 'belum aktif';
                simpadLog('Scope: ' + registration.scope, 'muted');
                simpadLog('Script: ' + activeUrl, activeUrl.indexOf('/sw.js') >= 0 ? 'success' : 'warning');
            });
        });

        if (window.SIMPAD_webPushState) {
            simpadLog('Subscribed: ' + (window.SIMPAD_webPushState.subscribed ? 'ya' : 'belum'), window.SIMPAD_webPushState.subscribed ? 'success' : 'warning');
            simpadLog('Synced server: ' + (window.SIMPAD_webPushState.synced ? 'ya' : 'belum'), window.SIMPAD_webPushState.synced ? 'success' : 'warning');

            if (window.SIMPAD_webPushState.error) {
                simpadLog('Error: ' + window.SIMPAD_webPushState.error, 'danger');
            }
        }
    }

    function simpadTestNotif() {
        var box = document.getElementById('simpad-notif-log');
        if (box) box.innerHTML = '';

        if (!('Notification' in window)) {
            simpadLog('Notification API tidak tersedia.', 'danger');
            return;
        }

        function show() {
            if (typeof window.SIMPAD_showOS === 'function') {
                window.SIMPAD_showOS('SIMPAD', 'Notifikasi browser berhasil tampil.');
                simpadLog('Notifikasi browser dikirim.', 'success');
                return;
            }

            new Notification('SIMPAD', {
                body: 'Notifikasi browser berhasil tampil.',
                icon: '/favicon.ico'
            });
            simpadLog('Notifikasi browser dikirim via Notification API.', 'success');
        }

        if (Notification.permission === 'granted') {
            show();
            return;
        }

        if (Notification.permission === 'default') {
            Notification.requestPermission().then(function (permission) {
                simpadLog('Permission: ' + permission, permission === 'granted' ? 'success' : 'warning');
                if (permission === 'granted') show();
            });
            return;
        }

        simpadLog('Permission ditolak. Aktifkan notification permission dari site settings browser.', 'danger');
    }
</script>
@endscript