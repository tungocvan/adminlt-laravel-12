<div>
    <div wire:ignore>
        <label class="form-label">{{ $placeholder }}</label>

        <select class="form-control {{ $class }}" style="width:100%">
            <option wire:key="{{ $class }}-0"  value="">{{ $placeholder }}</option>
            @foreach ($options as $key => $val)
                <option  wire:key="{{ $class }}-{{ $key }}" value="{{ $key }}">{{ $val }}</option>
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
 