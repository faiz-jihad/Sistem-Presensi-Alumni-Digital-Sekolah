<!-- Import Firebase App & Messaging Compat SDKs -->
<script src="https://www.gstatic.com/firebasejs/10.8.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.8.0/firebase-messaging-compat.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Dynamic configuration from services.php
        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}"
        };

        const vapidKey = "{{ config('services.firebase.vapid_key') }}";

        // Only initialize if firebaseConfig is defined
        if (!firebaseConfig.apiKey || !firebaseConfig.projectId) {
            console.warn('[FCM Web] Firebase credentials not configured in environment.');
            return;
        }

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        // Pass Firebase config into service worker registration url
        const queryParams = new URLSearchParams(firebaseConfig).toString();
        const swUrl = '/firebase-messaging-sw.js?' + queryParams;

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register(swUrl)
                .then((registration) => {
                    console.log('[FCM Web] Service Worker registered scope:', registration.scope);
                    
                    const requestPermissionAndToken = () => {
                        if (typeof Notification !== 'undefined') {
                            Notification.requestPermission().then((permission) => {
                                console.log('[FCM Web] Notification permission status:', permission);
                                if (permission === 'granted') {
                                    messaging.getToken({
                                        vapidKey: vapidKey,
                                        serviceWorkerRegistration: registration
                                    }).then((currentToken) => {
                                        if (currentToken) {
                                            console.log('[FCM Web] Acquired client token:', currentToken);
                                            sendTokenToServer(currentToken);
                                        } else {
                                            console.warn('[FCM Web] No registration token available.');
                                        }
                                    }).catch((err) => {
                                        console.error('[FCM Web] Error acquiring messaging token:', err);
                                    });
                                }
                            });
                        }
                    };

                    // Auto request if already granted, otherwise wait for first click/key
                    if (Notification.permission === 'granted') {
                        requestPermissionAndToken();
                    } else if (Notification.permission === 'default') {
                        document.addEventListener('click', requestPermissionAndToken, { once: true });
                        document.addEventListener('keydown', requestPermissionAndToken, { once: true });
                    }
                })
                .catch((err) => {
                    console.error('[FCM Web] Service Worker registration failed:', err);
                });
        }

        // Handle foreground messages
        messaging.onMessage((payload) => {
            console.log('[FCM Web] Foreground messaging received payload:', payload);
            const title = payload.notification?.title || payload.data?.title || 'Notifikasi Baru';
            const body = payload.notification?.body || payload.data?.body || '';

            if (typeof Notification !== 'undefined' && Notification.permission === 'granted') {
                try {
                    new Notification(title, {
                        body: body,
                        icon: '/favicon.ico'
                    });
                } catch (e) {
                    console.error('[FCM Web] Failed to display native foreground notification:', e);
                }
            }
        });

        // Sync Filament DB notifications to native notifications
        window.addEventListener('notificationSent', (event) => {
            const notification = event.detail.notification;
            if (!notification) return;

            console.log('[FCM Web] Syncing Filament DB Notification to Desktop:', notification);
            const title = notification.title || 'Notifikasi Baru';
            const body = notification.body || '';

            if (typeof Notification !== 'undefined' && Notification.permission === 'granted') {
                try {
                    new Notification(title, {
                        body: body,
                        icon: '/favicon.ico'
                    });
                } catch (e) {
                    console.error('[FCM Web] Failed to display native sync notification:', e);
                }
            }
        });

        // Helper to register token to database
        function sendTokenToServer(token) {
            fetch('/admin/device-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ token: token })
            })
            .then(response => response.json())
            .then(data => {
                console.log('[FCM Web] Token successfully registered on server:', data);
            })
            .catch(error => {
                console.error('[FCM Web] Failed to register token on server:', error);
            });
        }
    });
</script>
