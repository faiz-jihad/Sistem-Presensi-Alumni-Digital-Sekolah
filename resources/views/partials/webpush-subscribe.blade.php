@auth
<script>
(function () {
    'use strict';

    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        console.warn('[WebPush] Browser tidak mendukung Service Worker atau PushManager.');
        return;
    }

    const vapidPublicKey = "{{ config('webpush.vapid.public_key') }}";
    const swUrl = "/sw.js?v={{ file_exists(public_path('sw.js')) ? filemtime(public_path('sw.js')) : time() }}";

    if (!vapidPublicKey) {
        console.warn('[WebPush] VAPID_PUBLIC_KEY belum dikonfigurasi.');
        return;
    }

    window.SIMPAD_webPushState = {
        supported: true,
        permission: ('Notification' in window) ? Notification.permission : 'unsupported',
        subscribed: false,
        synced: false,
        serviceWorker: null,
        endpoint: null,
        error: null,
    };

    window.addEventListener('load', function () {
        prepareServiceWorker()
            .then(function (registration) {
                window.SIMPAD_webPushState.serviceWorker = registration.active ? registration.active.scriptURL : swUrl;
                console.log('[WebPush] Service Worker aktif:', registration.scope);
                ensurePermissionAndSubscribe(registration);
            })
            .catch(function (err) {
                window.SIMPAD_webPushState.error = err.message || String(err);
                console.warn('[WebPush] Service Worker gagal:', err);
            });
    });

    function prepareServiceWorker() {
        return navigator.serviceWorker.getRegistrations()
            .then(function (registrations) {
                return Promise.all(registrations.map(function (registration) {
                    const activeUrl = registration.active ? new URL(registration.active.scriptURL) : null;
                    const waitingUrl = registration.waiting ? new URL(registration.waiting.scriptURL) : null;
                    const installingUrl = registration.installing ? new URL(registration.installing.scriptURL) : null;
                    const urls = [activeUrl, waitingUrl, installingUrl].filter(Boolean);
                    const ownsRootScope = registration.scope === new URL('/', window.location.origin).href;
                    const usesDifferentWorker = urls.some(function (url) {
                        return url.pathname === '/firebase-messaging-sw.js' || url.pathname === '/sw-notify.js';
                    });

                    if (ownsRootScope && usesDifferentWorker) {
                        console.warn('[WebPush] Menghapus service worker lama yang bentrok:', urls.map(function (url) { return url.pathname; }).join(', '));
                        return registration.unregister();
                    }

                    return true;
                }));
            })
            .then(function () {
                return navigator.serviceWorker.register(swUrl, { scope: '/' });
            })
            .then(function (registration) {
                if (registration.update) {
                    registration.update().catch(function () {});
                }

                return navigator.serviceWorker.ready;
            });
    }

    function ensurePermissionAndSubscribe(registration) {
        if (!('Notification' in window)) {
            window.SIMPAD_webPushState.permission = 'unsupported';
            console.warn('[WebPush] Notification API tidak tersedia.');
            return;
        }

        window.SIMPAD_webPushState.permission = Notification.permission;

        if (Notification.permission === 'granted') {
            subscribeUser(registration);
            return;
        }

        if (Notification.permission === 'denied') {
            console.warn('[WebPush] Permission notifikasi ditolak browser.');
            return;
        }

        document.addEventListener('click', function requestOnFirstClick() {
            Notification.requestPermission().then(function (permission) {
                window.SIMPAD_webPushState.permission = permission;
                if (permission === 'granted') {
                    subscribeUser(registration);
                }
            });
        }, { once: true });
    }

    function subscribeUser(registration) {
        const applicationServerKey = urlB64ToUint8Array(vapidPublicKey);

        registration.pushManager.getSubscription()
            .then(function (subscription) {
                if (subscription && subscriptionHasDifferentKey(subscription, applicationServerKey)) {
                    return subscription.unsubscribe().then(function () {
                        return null;
                    });
                }

                return subscription;
            })
            .then(function (subscription) {
                if (subscription) {
                    window.SIMPAD_webPushState.subscribed = true;
                    window.SIMPAD_webPushState.endpoint = subscription.endpoint;
                    sendSubscriptionToServer(subscription);
                    return subscription;
                }

                return registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: applicationServerKey
                }).then(function (newSubscription) {
                    console.log('[WebPush] User berhasil subscribe.');
                    window.SIMPAD_webPushState.subscribed = true;
                    window.SIMPAD_webPushState.endpoint = newSubscription.endpoint;
                    sendSubscriptionToServer(newSubscription);
                    return newSubscription;
                });
            })
            .catch(function (err) {
                window.SIMPAD_webPushState.error = err.message || String(err);
                console.warn('[WebPush] Subscribe gagal:', err);
            });
    }

    function subscriptionHasDifferentKey(subscription, expectedKey) {
        if (!subscription.options || !subscription.options.applicationServerKey) {
            return false;
        }

        const current = new Uint8Array(subscription.options.applicationServerKey);
        if (current.length !== expectedKey.length) {
            return true;
        }

        for (let i = 0; i < current.length; i++) {
            if (current[i] !== expectedKey[i]) {
                return true;
            }
        }

        return false;
    }

    function sendSubscriptionToServer(subscription) {
        const key = subscription.getKey('p256dh');
        const token = subscription.getKey('auth');
        const encodings = PushManager.supportedContentEncodings || ['aesgcm'];
        const contentEncoding = encodings.includes('aes128gcm') ? 'aes128gcm' : encodings[0];

        fetch('/webpush/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                endpoint: subscription.endpoint,
                keys: {
                    p256dh: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
                    auth: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null
                },
                content_encoding: contentEncoding
            })
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                window.SIMPAD_webPushState.synced = !!data.success;
                console.log('[WebPush] Subscription tersimpan:', data);
            })
            .catch(function (err) {
                window.SIMPAD_webPushState.error = err.message || String(err);
                console.warn('[WebPush] Sync subscription gagal:', err);
            });
    }

    function urlB64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }

        return outputArray;
    }
})();
</script>
@endauth