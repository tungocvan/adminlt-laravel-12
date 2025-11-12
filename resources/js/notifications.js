document.addEventListener('alpine:init', () => {
    Alpine.data('notifications', () => ({
        badgeLabel: 0,
        items: [],
        init() {
            this.fetchData();
            window.addEventListener('alert-created', () => this.fetchData());
        },
        fetchData() {
            fetch('/notifications/get')
                .then(res => res.json())
                .then(data => {
                    this.badgeLabel = data.badge;
                    this.items = data.items;

                    // Render dropdown items
                    const template = document.getElementById('alerts-items-template');
                    template.innerHTML = this.items.map(item => `
                        <a href="#" class="dropdown-item" onclick="markRead(${item.id})">
                            <i class="mr-2 ${item.icon}"></i>
                            ${item.title}
                            <span class="float-right text-muted text-sm">${item.created_at}</span>
                        </a>
                    `).join('');
                });
        }
    }));
});

function markRead(id) {
    fetch(`/notifications/read/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    }).then(() => {
        // Dispatch lại để reload dropdown
        window.dispatchEvent(new Event('alert-created'));
    });
}
