<div id="notificationPermissionAlert"
     class="hidden fixed bottom-4 right-4 w-80 bg-white border border-gray-300 text-gray-800 rounded-lg shadow-lg p-4">
    <h3 id="notificationAlertTitle" class="font-bold mb-2"></h3>
    <div id="notificationAlertContent" class="text-sm"></div>

    <div class="mt-3 flex justify-end space-x-2">
        <button id="notificationEnableBtn"
                class="hidden bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm">
            Bật thông báo
        </button>
        <button onclick="document.getElementById('notificationPermissionAlert').classList.add('hidden')"
                class="bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600 text-sm">
            Đóng
        </button>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const alertBox = document.getElementById("notificationPermissionAlert");
        const alertTitle = document.getElementById("notificationAlertTitle");
        const alertContent = document.getElementById("notificationAlertContent");
        const enableBtn = document.getElementById("notificationEnableBtn");

        if (Notification.permission === "denied") {
            alertTitle.innerText = "⚠️ Bạn đã chặn thông báo";
            alertContent.innerHTML = `
                <p class="mb-2">Để nhận thông báo, vui lòng bật lại trong cài đặt trình duyệt:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li><b>Chrome:</b> 🔒 → Site settings → Notifications → Allow</li>
                    <li><b>Edge:</b> 🔒 → Permissions → Notifications → Allow</li>
                    <li><b>Firefox:</b> 🔒 → Permissions → Send Notifications → Allow</li>
                    <li><b>Safari:</b> Preferences → Websites → Notifications → Allow</li>
                </ul>
            `;
            alertBox.classList.remove("hidden");
        }

        if (Notification.permission === "default") {
            alertTitle.innerText = "🔔 Bật thông báo";
            alertContent.innerHTML = `<p>Nhấn nút bên dưới để nhận thông báo từ hệ thống.</p>`;
            enableBtn.classList.remove("hidden");
            alertBox.classList.remove("hidden");
        }

        enableBtn.addEventListener("click", function () {
            Notification.requestPermission().then((permission) => {
                if (permission === "granted") {
                    alertBox.classList.add("hidden");
                    // Sau khi được phép, lấy token Firebase
                    messaging.getToken().then((token) => {
                        console.log("Firebase Token:", token);
                        fetch("/save-token", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                            },
                            body: JSON.stringify({ device_token: token })
                        });
                    });
                } else if (permission === "denied") {
                    // Reload để hiển thị lại popup hướng dẫn denied
                    location.reload();
                }
            });
        });
    });
</script>
