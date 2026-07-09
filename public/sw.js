self.addEventListener('install', function (event) {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('push', function (event) {
    var payload = {
        title: 'Notifikasi Baru',
        body: '',
        data: {}
    };

    if (event.data) {
        try {
            payload = event.data.json();
        } catch (e) {
            payload.body = event.data.text();
        }
    }

    var payloadData = payload.data || {};
    var title = payload.title || payloadData.title || 'Notifikasi Baru';
    var options = {
        body: payload.body || payloadData.body || '',
        icon: payload.icon || payloadData.icon || '/favicon.ico',
        badge: payload.badge || payloadData.badge || '/favicon.ico',
        image: payload.image || payloadData.image,
        tag: payload.tag || payloadData.tag || 'simpad-notification',
        renotify: true,
        actions: payload.actions || [],
        data: Object.assign({}, payloadData, {
            url: payloadData.url || payload.url || '/admin'
        })
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            var targetUrl = new URL(event.notification.data && event.notification.data.url ? event.notification.data.url : '/admin', self.location.origin).href;

            for (var i = 0; i < clientList.length; i++) {
                var client = clientList[i];

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