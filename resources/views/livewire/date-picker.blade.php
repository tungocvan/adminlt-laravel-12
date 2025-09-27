<div wire:ignore>
    <label>{{ $placeholder }}</label>
    <div class="input-group date" id="reservationdate-{{ $name }}" data-target-input="nearest">
        <input type="text" 
               class="form-control datetimepicker-input" 
               data-target="#reservationdate-{{ $name }}" 
               wire:model.live="selected"
               name="{{ $name }}"
               autocomplete="off">
        <div class="input-group-append" data-target="#reservationdate-{{ $name }}" data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
        </div>
    </div>


   
<script>
    document.addEventListener("livewire:navigated", () => {
        let el = $('#reservationdate-{{ $name }}');
        
        el.daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoUpdateInput: false,
            locale: { format: '{{ $format }}' }
        }).on('apply.daterangepicker', function (ev, picker) {
            let value = picker.startDate.format('{{ $format }}');
            
            // set vào input
            $(this).find('input').val(value);

            // báo cho Livewire biết
            @this.set('selected', value);
        });

        // set giá trị ban đầu nếu có
        @if($selected)
            el.find('input').val("{{ $selected }}");
        @endif
    });
</script>


  
</div>
