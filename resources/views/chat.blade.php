<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat Test</title>
    @vite('resources/js/app.js')
</head>
<body>
    <h1>Laravel 12 Chat Test</h1>
    <form id="form">
        <input id="message" autocomplete="off" placeholder="Nhập tin nhắn..." />
        <button>Gửi</button>
    </form>
    <ul id="messages"></ul>

    <script>
        // Đợi trang load xong thì socket mới sẵn sàng
        document.addEventListener("DOMContentLoaded", () => {
            const socket = window.socket;

            if (!socket) {
                console.error("❌ Socket chưa được khởi tạo");
                return;
            }

            socket.on("connect", () => {
                console.log("✅ Connected (blade):", socket.id);
            });

            socket.on("disconnect", () => {
                console.log("❌ Disconnected");
            });

            // Gửi tin nhắn
            document.getElementById("form").addEventListener("submit", (e) => {
                e.preventDefault();
                let msg = document.getElementById("message").value;
                socket.emit("public-message", msg);
                document.getElementById("message").value = "";
            });

            // Nhận tin nhắn từ server.js
            socket.on("public-message", (msg) => {
                let li = document.createElement("li");
                li.textContent = msg;
                document.getElementById("messages").appendChild(li);
            });
        });
        
    </script>
</body>
</html>
