{{-- SIMPAD Firebase Cloud Messaging untuk panel web Filament. --}}

@php
    $firebaseWebConfig = [
        'apiKey' => config('services.firebase.api_key'),
        'authDomain' => config('services.firebase.auth_domain'),
        'projectId' => config('services.firebase.project_id'),
        'storageBucket' => config('services.firebase.storage_bucket'),
        'messagingSenderId' => config('services.firebase.messaging_sender_id'),
        'appId' => config('services.firebase.app_id'),
    ];
    $firebaseSwVersion = file_exists(public_path('firebase-messaging-sw.js'))
        ? filemtime(public_path('firebase-messaging-sw.js'))
        : time();
@endphp

<script src="/js/core-app.js?v={{ file_exists(public_path('js/core-app.js')) ? filemtime(public_path('js/core-app.js')) : time() }}"></script>
<script src="/js/core-msg.js?v={{ file_exists(public_path('js/core-msg.js')) ? filemtime(public_path('js/core-msg.js')) : time() }}"></script>

<script>
(function () {
    'use strict';

    var firebaseConfig = {{ Illuminate\Support\Js::from($firebaseWebConfig) }};
    var vapidKey = {{ Illuminate\Support\Js::from(config('services.firebase.vapid_key')) }};
    var csrfToken = {{ Illuminate\Support\Js::from(csrf_token()) }};
    var messaging = null;
    var swRegistration = null;

    function hasFirebaseConfig() {
        return firebaseConfig.apiKey &&
            firebaseConfig.projectId &&
            firebaseConfig.messagingSenderId &&
            firebaseConfig.appId &&
            vapidKey;
    }

    function showOS(title, body, data) {
        if (!swRegistration || Notification.permission !== 'granted') return;

        swRegistration.showNotification(title || 'Notifikasi Baru', {
            body: body || '',
            icon: '/favicon.ico',
            badge: '/favicon.ico',
            tag: data && data.notification_id
                ? 'simpad-' + data.notification_id
                : 'simpad-notification',
            data: Object.assign({ url: '/admin' }, data || {})
        });
    }

    function saveToken(token) {
        return fetch('/admin/device-token', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ token: token })
        }).then(function (response) {
            if (!response.ok) {
                return response.text().then(function (body) {
                    throw new Error('HTTP ' + response.status + ': ' + body);
                });
            }

            console.log('[FCM Web] Token browser berhasil terdaftar.');
            return response.json();
        });
    }

    function registerCurrentToken() {
        if (!messaging || !swRegistration || Notification.permission !== 'granted') {
            return Promise.resolve();
        }

        return messaging.getToken({
            vapidKey: vapidKey,
            serviceWorkerRegistration: swRegistration
        }).then(function (token) {
            if (!token) {
                throw new Error('Firebase tidak memberikan token browser.');
            }

            return saveToken(token);
        }).catch(function (error) {
            console.error('[FCM Web] Gagal mendaftarkan token:', error);
        });
    }

    function requestPermission() {
        if (!('Notification' in window)) return Promise.resolve('unsupported');

        if (Notification.permission === 'granted') {
            return registerCurrentToken().then(function () { return 'granted'; });
        }

        if (Notification.permission === 'denied') {
            console.warn('[FCM Web] Izin notifikasi diblokir di browser.');
            return Promise.resolve('denied');
        }

        return Notification.requestPermission().then(function (permission) {
            if (permission === 'granted') {
                return registerCurrentToken().then(function () { return permission; });
            }

            return permission;
        });
    }

    if (!hasFirebaseConfig()) {
        console.warn('[FCM Web] Konfigurasi Firebase/VAPID belum lengkap.');
        return;
    }

    if (!window.isSecureContext) {
        console.warn('[FCM Web] Push notification membutuhkan HTTPS atau localhost.');
        return;
    }

    if (!('serviceWorker' in navigator) || !('Notification' in window)) {
        console.warn('[FCM Web] Browser tidak mendukung push notification.');
        return;
    }

    if (!window.firebase || !firebase.messaging) {
        console.error('[FCM Web] Firebase Messaging SDK gagal dimuat.');
        return;
    }

    if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
    }
    messaging = firebase.messaging();

    var swParams = new URLSearchParams(firebaseConfig);
    var swVersion = {{ Illuminate\Support\Js::from($firebaseSwVersion) }};
    swParams.set('v', swVersion);

    navigator.serviceWorker.register('/firebase-messaging-sw.js?' + swParams.toString(), {
        scope: '/'
    }).then(function (registration) {
        swRegistration = registration;
        console.log('[FCM Web] Firebase service worker aktif:', registration.scope);

        if (Notification.permission === 'granted') {
            registerCurrentToken();
        }
    }).catch(function (error) {
        console.error('[FCM Web] Service worker gagal didaftarkan:', error);
    });

    messaging.onMessage(function (payload) {
        var title = payload.notification && payload.notification.title
            ? payload.notification.title
            : (payload.data && payload.data.title) || 'Notifikasi Baru';
        var body = payload.notification && payload.notification.body
            ? payload.notification.body
            : (payload.data && payload.data.body) || '';

        showOS(title, body, payload.data || {});
    });

    document.addEventListener('click', function onFirstInteraction() {
        requestPermission();
        document.removeEventListener('click', onFirstInteraction);
    }, { once: true });

    window.SIMPAD_requestPermission = requestPermission;
    window.SIMPAD_getPermission = function () {
        return Notification.permission;
    };
    window.SIMPAD_refreshFcmToken = registerCurrentToken;
})();
</script>
