<!-- Nút Chat nổi -->
<button id="chatToggle"
    class="btn btn-primary rounded-circle"
    style="position: fixed; bottom: 20px; right: 20px; z-index: 1050; width: 60px; height: 60px;">
    💬
</button>

<!-- Modal Chat -->
<div class="modal fade" id="chatModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-bottom-right">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Chat Realtime</h5>        
        <button type="button" class="btn-close close btn btn-tool card-tools" data-card-widget="remove">
            <i class="fas fa-times"></i>
        </button> 
      </div>
      <div class="modal-body" style="max-height: 300px; overflow-y: auto;">
        <ul id="chatMessages" class="list-unstyled mb-0"></ul>
      </div>
      <div class="modal-footer">
        <form id="chatForm" class="w-100 d-flex">
          <input id="chatInput" class="form-control me-2" placeholder="Nhập tin nhắn..." autocomplete="off">
          <button class="btn btn-primary">Gửi</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Toast Container -->
<div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 2000;"></div>

<style>
.modal-dialog-bottom-right {
    position: fixed;
    bottom: 80px;
    right: 20px;
    margin: 0;
}
</style>
<script>
    document.addEventListener("DOMContentLoaded", () => {
    const socket = window.socket;
    if (!socket) return;
    
    if ("Notification" in window && Notification.permission === "default") {
        Notification.requestPermission();
    }

    const chatToggle = document.getElementById("chatToggle");
    const chatModalEl = document.getElementById("chatModal");
    if (!chatToggle || !chatModalEl) return;

    const chatModal = new bootstrap.Modal(chatModalEl);
    const chatForm = document.getElementById("chatForm");
    const chatInput = document.getElementById("chatInput");
    const chatMessages = document.getElementById("chatMessages");

    // Toggle modal
    chatToggle.addEventListener("click", () => {
        chatModal.show();
        document.querySelector(".btn-close").onclick = () => chatModal.hide();
    });

    // Gửi tin nhắn
    chatForm.addEventListener("submit", (e) => {
        e.preventDefault();
        const msg = chatInput.value.trim();
        if (msg === "") return;

        socket.emit("public-message", {
            user: window.Laravel.user.name,
            message: msg
        });

        chatInput.value = "";
    });

    // Nhận tin nhắn
    socket.on("public-message", (data) => {
        const li = document.createElement("li");
        li.classList.add("mb-1");
        li.innerHTML = `<strong>${data.user}:</strong> ${data.message}`;
        chatMessages.appendChild(li);

        chatMessages.scrollTop = chatMessages.scrollHeight;
        // Hiển thị Toast thông báo
       showToast(`${data.user}`, data.message);
       // Nếu tab không active thì dùng Notification API
      if (document.hidden) {
          showBrowserNotification(`${data.user}`, data.message);
      }
    });
});
function showToast(user, message) {
    const container = document.getElementById("toastContainer");

    // Tạo ID random để tránh trùng
    const toastId = "toast-" + Date.now();

    const toast = document.createElement("div");
    toast.id = toastId;
    toast.className = "toast align-items-center text-bg-primary border-0 mb-2";
    toast.setAttribute("role", "alert");
    toast.setAttribute("aria-live", "assertive");
    toast.setAttribute("aria-atomic", "true");

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <strong>${user}</strong>: ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    container.appendChild(toast);

    // Khởi tạo Toast Bootstrap
    const bsToast = new bootstrap.Toast(toast, { delay: 4000 });
    bsToast.show();

    // Xóa DOM sau khi ẩn
    toast.addEventListener("hidden.bs.toast", () => {
        toast.remove();
    });
}
function showBrowserNotification(user, message) {
    if (!("Notification" in window)) {
        console.log("Trình duyệt không hỗ trợ Notification API");
        return;
    }

    // Nếu chưa được cấp quyền thì xin quyền
    if (Notification.permission === "default") {
        Notification.requestPermission();
    }

    if (Notification.permission === "granted") {
        const notification = new Notification("Tin nhắn mới", {
            body: `${user}: ${message}`,
            icon: "/images/chat-icon.png", // đổi icon theo logo app bạn
        });

        // Click vào notification thì focus lại tab
        notification.onclick = function () {
            window.focus();
            this.close();
        };
    }
}

</script>