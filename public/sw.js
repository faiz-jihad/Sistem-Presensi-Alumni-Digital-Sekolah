self.addEventListener('install', function (event) {
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(self.clients.claim());
});

function parseJsonObject(value) {
    if (!value) return {};
    if (typeof value === 'object') return value;

    try {
        var parsed = JSON.parse(value);
        return parsed && typeof parsed === 'object' ? parsed : {};
    } catch (error) {
        return {};
    }
}

function resolveNotificationPayload(payload) {
    var rootPayload = payload && typeof payload === 'object' ? payload : {};
    var rootData = parseJsonObject(rootPayload.data);
    var fcmEnvelope = parseJsonObject(
        rootPayload.FCM_MSG ||
        rootData.FCM_MSG ||
        rootData.fcmMessage
    );
    var notification = rootPayload.notification || fcmEnvelope.notification || {};
    var envelopeData = parseJsonObject(fcmEnvelope.data);
    var data = Object.assign({}, envelopeData, rootData);

    delete data.FCM_MSG;
    delete data.fcmMessage;

    return {
        title: rootPayload.title || notification.title || data.title || 'SIMPAD',
        body: rootPayload.body || notification.body || data.body || 'Ada informasi terbaru untuk Anda.',
        icon: rootPayload.icon || notification.icon || data.icon || '/favicon.ico',
        badge: rootPayload.badge || notification.badge || data.badge || '/favicon.ico',
        image: rootPayload.image || notification.image || data.image,
        tag: rootPayload.tag || notification.tag || data.tag || 'simpad-notification',
        actions: rootPayload.actions || notification.actions || [],
        data: Object.assign({}, data, {
            url: data.url || rootPayload.url || '/admin'
        })
    };
}

self.addEventListener('push', function (event) {
    var payload = {};

    if (event.data) {
        try {
            payload = event.data.json();
        } catch (e) {
            payload = { body: event.data.text() };
        }
    }

    var resolvedPayload = resolveNotificationPayload(payload);
    var options = {
        body: resolvedPayload.body,
        icon: resolvedPayload.icon,
        badge: resolvedPayload.badge,
        image: resolvedPayload.image,
        tag: resolvedPayload.tag,
        renotify: true,
        actions: resolvedPayload.actions,
        data: resolvedPayload.data
    };

    event.waitUntil(self.registration.showNotification(resolvedPayload.title, options));
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
