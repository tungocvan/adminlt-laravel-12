// public/firebase-messaging-sw.js
importScripts("https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js");
importScripts("/firebase-config.js");

firebase.initializeApp(firebaseConfig);

const messaging = firebase.messaging();

// Nhận notification khi app chạy nền
messaging.setBackgroundMessageHandler(function(payload) {
    console.log("[firebase-messaging-sw.js] Nhận background message:", payload);

    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: "/icon.png" // nhớ thêm icon.png vào public/
    };

    return self.registration.showNotification(notificationTitle, notificationOptions);
});

// Xử lý khi user click vào notification
self.addEventListener("notificationclick", function(event) {
    console.log("[firebase-messaging-sw.js] Notification click:", event);

    event.notification.close();
    event.waitUntil(
        clients.openWindow("/notifications") // điều hướng khi click
    );
});
