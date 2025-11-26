<div wire:ignore>
    <label>{{ $placeholder }}</label>
    <select x-ref="ts" class="form-control">
        <option value="">{{ $placeholder }}</option>

        @foreach ($options as $key => $val)
            <option value="{{ $key }}">{{ $val }}</option>
        @endforeach
    </select>
</div>

@once
    @push('css')
        <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap4.min.css" rel="stylesheet">
    @endpush

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    @endpush
@endonce

<script>
document.addEventListener("livewire:navigated", () => {
    let el = document.querySelector('[x-ref="ts"]');
    if (!el) return;

    // Nếu đã init rồi thì skip
    if (el.tomselect) return;

    let ts = new TomSelect(el, {});

    // Sync về Livewire
    ts.on('change', () => {
        el.dispatchEvent(new Event('input', { bubbles: true }));
    });
});
</script>
