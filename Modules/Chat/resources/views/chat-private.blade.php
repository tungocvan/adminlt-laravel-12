<div class="container">
    <div class="row justify-content-center">
      <!-- Cột danh sách user online -->
      <div class="col-md-3">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Users đang Online</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body p-0" style="display: block;">
            <ul id="userList" class="nav nav-pills flex-column">
              <!-- render users online -->
            </ul>
          </div>
        </div>
      </div>
  
      <!-- Cột khung chat chính -->
      <div class="col-md-9">
        <div class="card card-primary direct-chat direct-chat-primary" id="chatBox" style="display:none;">
          <div class="card-header">
            <h3 class="card-title"><strong id="chatWithName">Chọn user để chat</strong></h3>
            <div class="card-tools">
              <span id="newMessageBadge" class="badge badge-light" style="display:none;">0</span>
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            <!-- Messages -->
            <div class="direct-chat-messages" id="chatMessages"></div>
          </div>
          <div class="card-footer">
            <form id="chatForm">
              <div class="input-group">
                <input type="text" id="chatInput" placeholder="Nhập tin..." class="form-control">
                <span class="input-group-append">
                  <button type="submit" class="btn btn-primary">Gửi</button>
                </span>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  
<script>

document.addEventListener("DOMContentLoaded", () => {
    const authUser = { 
        id: "{{ auth()->id() }}", 
        name: "{{ auth()->user()->name }}" 
    };

    const userList = document.getElementById("userList");
    const chatBox = document.getElementById("chatBox");
    const chatWithName = document.getElementById("chatWithName");
    const chatMessages = document.getElementById("chatMessages");
    const chatForm = document.getElementById("chatForm");
    const chatInput = document.getElementById("chatInput");
    let currentChatUser = null;

    // Lưu badge chưa đọc
    let unreadCounts = {};
    // Lưu toàn bộ tin nhắn
    let messagesStore = {};

    // emit khi user login
    window.socket.emit("user-connected", authUser);

    // render danh sách users online
    window.socket.on("online-users", (users) => {
    userList.innerHTML = "";
    users.forEach(user => {
        if (user.id == authUser.id) return;

        const unread = unreadCounts[user.id] || 0;

        const li = document.createElement("li");
        li.className = "nav-item";
        li.innerHTML = `
          <a href="#" class="nav-link d-flex justify-content-between align-items-center" id="user-${user.id}">
            <span><i class="far fa-circle text-success"></i> ${user.name}</span>
            <span class="badge badge-danger badge-sm" id="badge-${user.id}" style="display:${unread ? 'inline-block':'none'};">${unread}</span>
          </a>`;
        li.onclick = () => {
            // bỏ active cũ
            document.querySelectorAll("#userList .nav-link").forEach(el => el.classList.remove("active", "font-weight-bold"));

                // set active mới
                const link = document.getElementById(`user-${user.id}`);
                link.classList.add("active", "font-weight-bold");

                openChat(user);
            };
            userList.appendChild(li);
        });
    });


    // khi nhận tin nhắn riêng
    window.socket.on("private-message", ({ from, message }) => {
        // luôn lưu tin nhắn vào store
        if (!messagesStore[from.id]) messagesStore[from.id] = [];
        messagesStore[from.id].push({ from, message });

        if (!currentChatUser || currentChatUser.id !== from.id) {
            // tăng số tin chưa đọc
            unreadCounts[from.id] = (unreadCounts[from.id] || 0) + 1;
            const badge = document.getElementById(`badge-${from.id}`);
            if (badge) {
                badge.textContent = unreadCounts[from.id];
                badge.style.display = "inline-block";
            }
        } else {
            // nếu đang chat với user đó → append trực tiếp
            appendMessage(from, message);
        }
    });

    // mở khung chat với user


    function openChat(user) {
        currentChatUser = user;
        chatWithName.textContent = user.name;
        chatMessages.innerHTML = "";
        chatBox.style.display = "block";

        // reset badge
        unreadCounts[user.id] = 0;
        const badge = document.getElementById(`badge-${user.id}`);
        if (badge) badge.style.display = "none";

        // gọi API load lịch sử
        fetch(`/chat/history/${user.id}`)
            .then(res => res.json())
            .then(messages => {
                messages.forEach(msg => {
                    appendMessage(
                        { id: msg.from_user.id, name: msg.from_user.name },
                        msg.message,
                        msg.created_at
                    );
                });
            });
    }



    // render 1 tin nhắn vào khung chat
    function appendMessage(from, msg, time = null) {
        const isMine = from.id == authUser.id;
        const msgDiv = document.createElement("div");
        msgDiv.className = "direct-chat-msg " + (isMine ? "right" : "");
        msgDiv.innerHTML = `
        <div class="direct-chat-infos clearfix">
            <span class="direct-chat-name ${isMine ? 'float-right' : 'float-left'}">
            <strong>${from.name}</strong>
            </span>
            <span class="direct-chat-timestamp ${isMine ? 'float-left' : 'float-right'}">
            ${time ? new Date(time).toLocaleTimeString() : new Date().toLocaleTimeString()}
            </span>
        </div>
        <div class="direct-chat-text">${msg}</div>
        `;
        chatMessages.appendChild(msgDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }


    // gửi tin nhắn  
    // lưu DB qua API

    chatForm.onsubmit = async (e) => {
      e.preventDefault();

      const msg = chatInput.value.trim();
      if (!msg || !currentChatUser) return;

      appendMessage(authUser, msg);

      try {
          // lưu DB
          await fetch('/chat/send', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
              },
              body: JSON.stringify({
                  to_id: currentChatUser.id,
                  message: msg
              }),
              credentials: 'include'
          });

          // emit realtime để người kia nhận được ngay
          window.socket.emit("private-message", {
              to: currentChatUser.id,
              from: authUser,
              message: msg
          });

      } catch (err) {
          console.error("Gửi tin nhắn thất bại", err);
      }

      chatInput.value = "";
  };



});



</script>