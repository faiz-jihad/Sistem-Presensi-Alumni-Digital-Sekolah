importScripts('/js/core-app.js');
importScripts('/js/core-msg.js');

const urlParams = new URLSearchParams(self.location.search);
const firebaseConfig = {
    apiKey: urlParams.get('apiKey'),
    authDomain: urlParams.get('authDomain'),
    projectId: urlParams.get('projectId'),
    storageBucket: urlParams.get('storageBucket'),
    messagingSenderId: urlParams.get('messagingSenderId'),
    appId: urlParams.get('appId'),
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

self.addEventListener('install', function () {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(self.clients.claim());
});

messaging.onBackgroundMessage(function (payload) {
    if (payload.notification) return;

    const data = payload.data || {};
    const title = data.title || 'Notifikasi Baru';

    self.registration.showNotification(title, {
        body: data.body || '',
        icon: data.icon || '/favicon.ico',
        badge: data.badge || '/favicon.ico',
        tag: data.notification_id
            ? 'simpad-' + data.notification_id
            : 'simpad-notification',
        data: Object.assign({ url: '/admin' }, data),
    });
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const targetUrl = new URL(
        event.notification.data && event.notification.data.url
            ? event.notification.data.url
            : '/admin',
        self.location.origin
    ).href;

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(function (clientList) {
                for (let index = 0; index < clientList.length; index++) {
                    const client = clientList[index];
                    if (client.url === targetUrl && 'focus' in client) {
                        return client.focus();
                    }
                }

                if (self.clients.openWindow) {
                    return self.clients.openWindow(targetUrl);
                }
            })
    );
});
