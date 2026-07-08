// Import the Firebase scripts inside the service worker
importScripts('https://www.gstatic.com/firebasejs/10.8.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.8.0/firebase-messaging-compat.js');

// Parse Firebase configuration dynamically from query parameters passed during registration
const urlParams = new URLSearchParams(location.search);

const firebaseConfig = {
    apiKey: urlParams.get('apiKey'),
    authDomain: urlParams.get('authDomain'),
    projectId: urlParams.get('projectId'),
    storageBucket: urlParams.get('storageBucket'),
    messagingSenderId: urlParams.get('messagingSenderId'),
    appId: urlParams.get('appId'),
};

// Initialize the Firebase app in the service worker
firebase.initializeApp(firebaseConfig);

// Retrieve an instance of Firebase Cloud Messaging
const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message: ', payload);
    
    const notificationTitle = payload.notification?.title || payload.data?.title || 'Notifikasi Baru';
    const notificationOptions = {
        body: payload.notification?.body || payload.data?.body || '',
        icon: '/favicon.ico',
        data: payload.data || {}
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
