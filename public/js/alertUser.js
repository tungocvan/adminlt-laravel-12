function notifications() {
    return {
        unreadCount: 0,
        dropdownHtml: '',
        init() {
            this.fetchData();

            // Lắng nghe browser event từ Livewire / Listener
            window.addEventListener('alert-created', () => {
                this.fetchData();
            });
        },
        fetchData() {
            fetch('/notifications/get')
                .then(res => res.json())
                .then(data => {
                    this.unreadCount = data.label;
                    this.dropdownHtml = data.dropdown;
                });
        }
    }
}
