<div wire:ignore>
    <label>{{ $placeholder }}</label>
    <div class="input-group" id="datepicker-container-{{ $name }}">
        <input type="text" 
               class="form-control" 
               id="datepicker-{{ $name }}"
               wire:model.defer="selected"
               name="{{ $name }}"
               placeholder="{{ $placeholder }}"
               autocomplete="off">
        <div class="input-group-append">
            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
        </div>
    </div>
</div>

@push('js')
<script>
document.addEventListener('livewire:load', function () {
    const inputEl = document.getElementById('datepicker-{{ $name }}');

    const picker = new tempusDominus.TempusDominus(inputEl, {
        display: {
            components: {
                decades: false,
                year: true,
                month: true,
                date: true,
                hours: false,
                minutes: false,
                seconds: false
            },
            buttons: {
                today: true,
                clear: true,
                close: true
            },
            keepOpen: false
        },
        useCurrent: false,
        allowInputToggle: true,
        clickInput: true // <-- quan trọng: click input xổ calendar
    });

    // Khi chọn ngày → cập nhật Livewire
    picker.dates.subscribe(function(selectedDate){
        if(selectedDate){
            const value = moment(selectedDate).format('{{ $format }}');
            @this.set('selected', value);
        }
    });

    // Đồng bộ giá trị Livewire khi component re-render
    Livewire.hook('message.processed', () => {
        const value = @this.get('selected');
        if(value){
            picker.dates.setValue(moment(value, '{{ $format }}'));
        } else {
            picker.dates.setValue(null);
        }
    });
});
</script>
@endpush
