// Khởi tạo Firebase
firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// Đăng ký service worker để nhận notification background
if ("serviceWorker" in navigator) {
    navigator.serviceWorker.register("/firebase-messaging-sw.js")
        .then(function (registration) {
            console.log("Service Worker registered:", registration);
            messaging.useServiceWorker(registration); // gắn messaging với SW
        })
        .catch(function (err) {
            console.error("Service Worker registration failed:", err);
        });
}

// Hàm đăng ký lấy token
function initFirebaseMessagingRegistration() {
    Notification.requestPermission().then((permission) => {
        if (permission === "granted") {
            messaging.getToken({
                vapidKey: "BCdBgpIkE7Ofqg_qfzJEBOXfq7NVDCb1FGyGOutHa8Yy2hzHikfu-_QJ8C_yImcsvKAGN9_kmDk0bXSMWpiNJ8M"
            }).then((token) => {
                console.log("Firebase Token:", token);

                // Gửi token về server Laravel
                fetch("/save-token", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                    },
                    body: JSON.stringify({ device_token: token })
                })
                .then(res => res.json())
                .then(res => console.log("Server response:", res))
                .catch(err => console.error("Lỗi khi gửi token:", err));
            }).catch((err) => {
                console.error("Lỗi khi lấy token:", err);
            });
        } else {
            console.warn("Người dùng từ chối cấp quyền thông báo.");
        }
    });
}

initFirebaseMessagingRegistration();

// Nhận notification khi app đang mở (foreground)
messaging.onMessage((payload) => {
    console.log("Foreground message:", payload);
    new Notification(payload.notification.title, {
        body: payload.notification.body,
        icon: "/icon.png"
    });
});
