<div>
    <div wire:ignore class="form-group">
        <label class="form-label">{{ $placeholder }}</label>    
        <select class="form-control tnv-options-multiple" style="width: 100%" multiple wire:model.live="selected">
            @foreach($options as $id => $title)
                <option value="{{ $id }}"
                    @if(collect($selected)->contains($id)) selected @endif>
                    {{ $title }}
                </option>
            @endforeach
        </select>
    </div>

    
    <script>
        document.addEventListener('livewire:navigated', () => {
            let el = $('.tnv-options-multiple');

            el.select2({ theme: "classic" });

            // set lại giá trị đã chọn
            el.val(@json($selected)).trigger('change');

            // lắng nghe thay đổi
            el.on('change', function () {
                let values = $(this).val() || [];
                @this.set('selected', values);
            });
        });
    </script>
    
</div>
