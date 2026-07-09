/**
 * sw-notify.js
 * Service Worker sederhana untuk menampilkan OS notifications di browser.
 * Tidak bergantung pada Firebase — hanya untuk desktop push via Filament.
 */
self.addEventListener('install', function (event) {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(clients.claim());
});

// Handle push event dari backend (jika nanti diintegrasikan dengan Web Push API)
self.addEventListener('push', function (event) {
    var data = {};
    try { data = event.data ? event.data.json() : {}; } catch (e) {}

    var title = data.title || 'Notifikasi SIMPAD';
    var options = {
        body: data.body || '',
        icon: '/favicon.ico',
        badge: '/favicon.ico'
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow('/')
    );
});
