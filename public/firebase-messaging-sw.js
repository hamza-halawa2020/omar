importScripts('https://www.gstatic.com/firebasejs/10.3.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.3.1/firebase-messaging-compat.js');

firebase.initializeApp({
        apiKey: "AIzaSyA8XAA3PmpKcq4jcxM5A8AAyrOEqwgSKcI",
        authDomain: "upedia-d1c7b.firebaseapp.com",
        projectId: "upedia-d1c7b",
        storageBucket: "upedia-d1c7b.appspot.com",
        messagingSenderId: "141880628391",
        appId: "1:141880628391:web:96b93a0b276b1d7d39f782",
        measurementId: "G-SY275K79WF"
});

const messaging = firebase.messaging();

// messaging.onBackgroundMessage(function(payload) {
//     console.log('[firebase-messaging-sw.js] Received background message ', payload);
//     const notificationTitle = payload.notification.title;
//     const notificationOptions = {
//         body: payload.notification.body,
//         icon: '/icon.png'
//     };
//     self.registration.showNotification(notificationTitle, notificationOptions);
// });
// messaging.onBackgroundMessage(function(payload) {
//     console.log('[firebase-messaging-sw.js] Received background message ', payload);

//     const notificationTitle = payload.notification.title;
//     const notificationOptions = {
//         body: payload.notification.body,
//         icon: '/icon.png'
//     };

//     self.registration.getNotifications().then(notifications => {
//         const isDuplicate = notifications.some(notification => notification.title === notificationTitle);
//         if (!isDuplicate) {
//             self.registration.showNotification(notificationTitle, notificationOptions);
//         }
//     });
// });