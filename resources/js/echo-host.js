import Echo from "laravel-echo";
import { io } from "socket.io-client";

// Khởi tạo trực tiếp client
const socket = io("https://node.tungocvan.com", {
    transports: ["websocket", "polling"],
    withCredentials: false,
});

window.io = io;
// Tạo socket client toàn cục
window.socket = io("https://node.tungocvan.com", {
    transports: ["websocket", "polling"],
});

// Nếu muốn dùng Laravel Echo:
window.Echo = new Echo({
    broadcaster: "socket.io",
    client: io,
    host: "https://node.tungocvan.com",
});

socket.on("connect", () => {
    console.log("✅ Socket.IO connected (echo.js):", socket.id);
});

socket.on("disconnect", () => {
    console.log("❌ Socket.IO disconnected (echo.js)");
});


// xem log nodejs: pm2 monit 