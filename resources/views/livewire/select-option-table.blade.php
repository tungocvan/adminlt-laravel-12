<div>
    <div wire:ignore>
        <select class="form-control {{ $class }}" style="width:100%">
            <option value="">{{ $placeholder ?? '-- Tất cả --'}}</option>
            @foreach ($options as $key => $val)
                <option   value="{{ $key }}">{{ $val }}</option>
            @endforeach
        </select>
    </div>
    <script>
        document.addEventListener('livewire:navigated', () => {
    
            const el = $('.{{ $class }}');
            if (el.hasClass("select2-hidden-accessible")) {
                    el.select2('destroy');
                }
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
 