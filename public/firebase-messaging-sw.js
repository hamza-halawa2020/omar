importScripts('https://www.gstatic.com/firebasejs/10.3.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.3.1/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSyC9655aXFRUDOg7tcGqYt2cCKoS44EA6h8",
    authDomain: "test-be06b.firebaseapp.com",
    projectId: "test-be06b",
    storageBucket: "test-be06b.firebasestorage.app",
    messagingSenderId: "796086195914",
    appId: "1:796086195914:web:66c4b9441e333209974633",
    measurementId: "G-58NQ2EXNR1"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
    console.log('dffdf','[firebase-messaging-sw.js] Received background message ', payload);
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: "tistinnnnnxxxxxxxxxxxxxxxxxxnng",
        icon: '/icon.png'
    };
    self.registration.showNotification(notificationTitle, notificationOptions);
});
