<!-- Sidebar danh sách user online -->
<div id="chatSidebar" 
    class="card shadow-lg"
    style="position: fixed; bottom: 20px; right: 20px; width: 260px; height: 400px; z-index:1050;">
  <div class="card-header bg-primary text-white p-2">
    <strong>💬 Người dùng online</strong>
  </div>
  <div class="card-body p-0" style="height: calc(100% - 40px); overflow-y: auto;">
    <ul id="userList" class="list-group list-group-flush small">
      <!-- Render user online -->
    </ul>
  </div>
</div>

<!-- Khu vực chứa nhiều cửa sổ chat -->
<div id="chatWindows" 
    style="position: fixed; bottom: 0; right: 300px; display:flex; gap:10px; z-index:1060;">
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        if ("Notification" in window && Notification.permission === "default") {
            Notification.requestPermission();
        }
        const userList = document.getElementById("userList");
        const chatWindows = document.getElementById("chatWindows");
    
        // Emit khi user login (Laravel truyền user qua blade)
        window.socket.emit("user-connected", {
            id: "{{ auth()->id() }}",
            name: "{{ auth()->user()->name }}"
        });
    
        // Nhận danh sách user online
        window.socket.on("online-users", (users) => {
            
            userList.innerHTML = "";
            users.forEach(user => {
                if (user.id == "{{ auth()->id() }}") return; // bỏ qua chính mình
                const li = document.createElement("li");
                li.className = "list-group-item list-group-item-action";
                li.textContent = user.name;
                li.style.cursor = "pointer";
                li.onclick = () => openChatWindow(user);
                userList.appendChild(li);
            });
        });
    
        // Nhận tin nhắn riêng
        window.socket.on("private-message", ({ from, message }) => {
            openChatWindow(from, message);
        });
    
        // Hàm mở cửa sổ chat riêng
        function openChatWindow(user, incomingMsg = null) {
            let isNewWindow = false;
            let win = document.getElementById("chat-" + user.id);
            if (!win) {
                isNewWindow = true;
                win = document.createElement("div");
                win.className = "card shadow";
                win.id = "chat-" + user.id;
                win.style.width = "250px";
                win.style.height = "300px";
    
                win.innerHTML = `
                  <div class="card-header bg-primary text-white">                     
                     <span class="card-title">${user.name}</span>       
                     <div class="card-tools">
                        <button type="button" class="btn-close btn btn-tool card-tools" data-card-widget="remove">
                               <i class="fas fa-times"></i>
                        </button>                        
                     </div>          
                     
                  </div>
                  
                  <div class="card-body p-2" style="overflow-y:auto; height:200px;">
                     <ul class="list-unstyled mb-0"></ul>
                  </div>
                  <div class="card-footer p-1">
                     <form class="d-flex">
                       <input class="form-control form-control-sm me-1" placeholder="Nhập tin...">
                       <button class="btn btn-sm btn-primary">Gửi</button>
                     </form>
                  </div>
                `;
                win.querySelector(".btn-close").onclick = () => win.remove();
    
                // Xử lý gửi tin nhắn
                const form = win.querySelector("form");
                const input = form.querySelector("input");
                const msgList = win.querySelector("ul");
                form.onsubmit = (e) => {
                    e.preventDefault();
                    const msg = input.value.trim();
                    if (!msg) return;
                    // render tin nhắn của mình
                    const li = document.createElement("li");
                    li.className = "text-end text-primary";
                    li.textContent = msg;
                    msgList.appendChild(li);
                    msgList.scrollTop = msgList.scrollHeight;
    
                    // gửi lên server
                    window.socket.emit("private-message", {
                        to: user.id,
                        from: {
                            id: "{{ auth()->id() }}",
                            name: "{{ auth()->user()->name }}"
                        },
                        message: msg
                    });
                    input.value = "";
                };
    
                chatWindows.appendChild(win);
            }
    
            if (incomingMsg) {
                const msgList = win.querySelector("ul");
                const li = document.createElement("li");
                li.className = "text-start text-dark";
                li.textContent = incomingMsg;
                msgList.appendChild(li);
                msgList.scrollTop = msgList.scrollHeight;
                // Nếu cửa sổ này mới tạo (chưa được mở trước đó) => báo notification
                if (isNewWindow) {
                    showToast(`Tin nhắn từ ${user.name}`, incomingMsg);

                    if (document.hidden) {
                        showBrowserNotification(user.name, incomingMsg);
                    }
                }
            }
        }
    });
    function showBrowserNotification(user, message) {
        if (!("Notification" in window)) return;

        if (Notification.permission === "default") {
            Notification.requestPermission();
        }

        if (Notification.permission === "granted") {
            const notification = new Notification("Tin nhắn mới", {
                body: `${user}: ${message}`,
                icon: "/images/chat-icon.png", // logo app
            });

            notification.onclick = function () {
                window.focus();
                this.close();
            };
        }
    }

</script>