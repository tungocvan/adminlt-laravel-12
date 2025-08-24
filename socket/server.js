const express = require("express");
const app = express();
const { createServer } = require("http");
const { Server } = require("socket.io");

const httpServer = createServer(app); // ✅ phải truyền app vào
const io = new Server(httpServer, {
    cors: {
        origin: "*", // hoặc domain Laravel nếu muốn bảo mật
        methods: ["GET", "POST"],
    }
});

// Route test 
app.get("/", (req, res) => {
    res.send("NodeJS Socket.IO Server đang chạy trên cổng 6001 🚀");
});

app.use(express.json());

app.post("/post-create", (req, res) => {
    const data = req.body;
    console.log("📡 Received post-create from Laravel:", data);

    // Gửi tới tất cả client
    io.emit("post-create", data);

    res.json({ success: true });
});


// Danh sách user online
let onlineUsers = {};

io.on("connection", (socket) => {
    console.log("🔌 Client connected:", socket.id);

    // Nhận sự kiện khi user login
    socket.on("user-connected", (user) => {
        console.log("user-connected", user);
        onlineUsers[socket.id] = user;
        io.emit("online-users", Object.values(onlineUsers));
    });

    // Chat riêng
    socket.on("private-message", ({ to, message, from }) => {
        for (let [id, u] of Object.entries(onlineUsers)) {
            if (u.id === to) {
                io.to(id).emit("private-message", { from, message });
            }
        }
    });

    // 📢 Chat public
    socket.on("public-message", (msg) => {
        io.emit("public-message", msg);
    });

    socket.on("disconnect", () => {
        console.log("❌ Client disconnected:", socket.id);
        delete onlineUsers[socket.id];
        io.emit("online-users", Object.values(onlineUsers));
    });
});

httpServer.listen(6001, "0.0.0.0", () => {
    console.log("🚀 Socket.IO server running at http://0.0.0.0:6001");
});
