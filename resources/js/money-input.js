export default function moneyInput(liveValue) {
    return {
        value: liveValue,
        display: '',
        init() {
            this.display = this.formatDisplay(this.value);
        },
        formatDisplay(v) {
            if (!v) return '';
            return new Intl.NumberFormat('vi-VN').format(v);
        },
        formatInput(e) {
            let raw = e.target.value.replace(/\D/g, '');
            this.value = raw ? parseInt(raw) : null;
            this.display = this.value;
        },
        formatBlur(e) {
            e.target.value = this.formatDisplay(this.value);
        }
    }
}
