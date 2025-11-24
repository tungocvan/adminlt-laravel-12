<div>
    <div wire:ignore>
        <label class="form-label">{{ $placeholder }}</label>

        <select class="form-control tnv-option" style="width:100%">
            <option value="">{{ $placeholder }}</option>
            @foreach ($options as $key => $val)
                <option value="{{ $key }}">{{ $val }}</option>
            @endforeach
        </select>
    </div>
    <script>
        document.addEventListener('livewire:navigated', () => {
            let el = $('.tnv-option');
            el.select2({
                theme: "classic"
            });
            el.val(@json($selected)).trigger('change');
            el.on('change', function(e) {
                let value = $(this).val();
                @this.set('selected', value);
            });
        });
    </script>

</div>
