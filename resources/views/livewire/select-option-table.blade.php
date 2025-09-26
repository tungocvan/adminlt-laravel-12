<div>
    <div wire:ignore class="form-group">
        <label class="form-label">{{ $placeholder }}</label>    
        <select class="form-control tnv-option" 
                style="width: 100%">
            <option value="">{{ $placeholder }}</option>
            @foreach($options as $id => $title)
                <option value="{{ $id }}">{{ $title }}</option>
            @endforeach
        </select>
    </div>
    <script>
        document.addEventListener('livewire:navigated', () => {
            let el = $('.tnv-option');

            el.select2({ theme: "classic" });

            // set lại giá trị mặc định khi load lại component
            el.val(@json($selected)).trigger('change');

            el.on('change', function (e) {
                let value = $(this).val();
                @this.set('selected', value); // cập nhật vào Livewire property
            });
        });
    </script>
</div>
