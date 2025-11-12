// import js vào master.blade.php
// TnvMenuHelper::loadMenuNavbar => set route, thời gian 
document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    document.addEventListener('click', async (e) => {
        const item = e.target.closest('.alert-item');
        if (!item) return;

        const id = item.dataset.id;
        if (!id) return;

        try {
            const response = await fetch(`/notifications/read/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (data.success) {
                console.log(`✅ Notification ${id} marked as read`);

                // 🔄 Gửi event yêu cầu AdminLTE reload dropdown ngay
                const evt = new CustomEvent('update.navbar-notification', {
                    detail: { id: 'my-notification' }
                });
                document.dispatchEvent(evt);
            }
        } catch (error) {
            console.error('❌ Lỗi mark-as-read:', error);
        }
    });
});
