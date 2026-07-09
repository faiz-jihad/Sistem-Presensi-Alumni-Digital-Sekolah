{{-- SIMPAD Notification Script --}}
{{-- Menampilkan notifikasi OS desktop dari notifikasi Filament dan Web Push. --}}

<script>
(function () {
    'use strict';

    var swRegistration = null;
    var swUrl = '/sw.js?v={{ file_exists(public_path('sw.js')) ? filemtime(public_path('sw.js')) : time() }}';

    function showOS(title, body) {
        if (!('Notification' in window)) return;
        if (Notification.permission !== 'granted') return;

        var options = {
            body: body || '',
            icon: '/favicon.ico',
            badge: '/favicon.ico',
            data: { url: '/admin' }
        };

        if (swRegistration) {
            swRegistration.showNotification(title || 'Notifikasi', options);
            return;
        }

        try {
            new Notification(title || 'Notifikasi', options);
        } catch (e) {}
    }

    function requestPermission(cb) {
        if (!('Notification' in window)) return;

        if (Notification.permission === 'granted') {
            if (cb) cb();
            return;
        }

        if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(function (permission) {
                if (permission === 'granted' && cb) cb();
            });
        }
    }

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register(swUrl, { scope: '/' })
            .then(function (registration) {
                swRegistration = registration;
                console.log('[SIMPAD] Service Worker notifikasi aktif.');
            })
            .catch(function (error) {
                console.warn('[SIMPAD] Service Worker notifikasi gagal:', error.message);
            });
    }

    document.addEventListener('click', function onFirstClick() {
        requestPermission();
        document.removeEventListener('click', onFirstClick);
    }, { once: true });

    window.addEventListener('notificationSent', function (event) {
        var notification = event && event.detail && event.detail.notification;
        if (!notification) return;

        requestPermission(function () {
            showOS(notification.title || 'Notifikasi Baru', notification.body || '');
        });
    });

    window.SIMPAD_showOS = function (title, body) {
        requestPermission(function () {
            showOS(title, body);
        });
    };

    window.SIMPAD_requestPermission = requestPermission;
    window.SIMPAD_getPermission = function () {
        return ('Notification' in window) ? Notification.permission : 'unsupported';
    };
})();
</script>