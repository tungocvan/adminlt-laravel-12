const { createServer } = require("http");
const { Server } = require("socket.io");

const httpServer = createServer();
const io = new Server(httpServer, {
    cors: {
        origin: "*", // chỉ định domain Laravel của bạn nếu muốn bảo mật
        methods: ["GET", "POST"],
    }
});

// Danh sách user online
let onlineUsers = {};

io.on("connection", (socket) => {
    console.log("🔌 Client connected:", socket.id);

    // Nhận sự kiện khi user login
    socket.on("user-connected", (user) => {
        console.log('user-connected',user);
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

httpServer.listen(6001, () => {
    console.log("🚀 Socket.IO server running at http://0.0.0.0:6001");
});
