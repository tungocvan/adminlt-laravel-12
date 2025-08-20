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
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
    });
});

</script>