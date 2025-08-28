<div class="card card-outline card-success" id="communityChatBox">
    <div class="card-header">
        <h3 class="card-title">💬 Chat Cộng Đồng</h3>
    </div>

    <div class="card-body" style="height: 200px; overflow-y: auto;" id="communityMessages">
        <!-- render tin nhắn cộng đồng -->
    </div>

    <div class="card-footer">
        <form id="communityForm">
            <div class="input-group">
                <input type="text" id="communityInput" placeholder="Nhập tin nhắn cộng đồng..." class="form-control">
                <span class="input-group-append">
                    <button type="submit" class="btn btn-success">Gửi</button>
                </span>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const authUser = { 
        id: "{{ auth()->id() }}", 
        name: "{{ auth()->user()->name }}" 
    };

    const communityMessages = document.getElementById("communityMessages");
    const communityForm = document.getElementById("communityForm");
    const communityInput = document.getElementById("communityInput");

    // load lịch sử
    fetch("/chat/community/history")
        .then(res => res.json())
        .then(messages => {
            messages.forEach(m => {
                appendCommunityMessage({id:m.user.id,name:m.user.name}, m.message, m.created_at);
            });
        });

    // nhận realtime
    window.socket.on("community-message", ({ from, message }) => {
        appendCommunityMessage(from, message);
    });

    function appendCommunityMessage(from, msg, time = null) {
        const isMine = from.id == authUser.id;
        const msgDiv = document.createElement("div");
        msgDiv.className = "direct-chat-msg " + (isMine ? "right" : "");
        msgDiv.innerHTML = `
            <div class="direct-chat-infos clearfix">
                <span class="direct-chat-name ${isMine ? 'float-right' : 'float-left'}"><strong>${from.name}</strong></span>
                <span class="direct-chat-timestamp ${isMine ? 'float-left' : 'float-right'}">${time ? new Date(time).toLocaleTimeString() : new Date().toLocaleTimeString()}</span>
            </div>
            <div class="direct-chat-text">${msg}</div>
        `;
        communityMessages.appendChild(msgDiv);
        communityMessages.scrollTop = communityMessages.scrollHeight;
    }

    communityForm.onsubmit = async (e) => {
        e.preventDefault();
        const msg = communityInput.value.trim();
        if (!msg) return;

        //appendCommunityMessage(authUser, msg);

        try {
            await fetch('/chat/community/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ message: msg }),
                credentials: 'include'
            });

            window.socket.emit("community-message", {
                from: authUser,
                message: msg
            });
        } catch (err) {
            console.error("Gửi chat cộng đồng thất bại", err);
        }

        communityInput.value = "";
    };
});
</script>
