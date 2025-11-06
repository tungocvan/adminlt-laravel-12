@php 
    $config = $config ?? [];
@endphp

<x-adminlte-input-date
    name="{{ $name }}"
    :config="$config"
    label="{{ $label }}"
    placeholder="{{ $placeholder }}"
    data-placement="{{ $placement }}"
    wire:model.live="{{ $attributes->wire('model')->value() }}"
    
    onkeydown="return false"  {{-- Ngăn nhập bàn phím --}}
>
    <x-slot name="appendSlot">
        <x-adminlte-button
            theme="outline-primary"
            icon="fa fa-calendar"
            onclick="this.closest('.input-group').querySelector('input').focus(); this.closest('.input-group').querySelector('input').click()"
        />
    </x-slot>
</x-adminlte-input-date>

@pushOnce('js')
<script>
(() => {
    function initPlacement(input) {
        const placement = input.dataset.placement || 'bottom';
        const wrapper = input.closest('.input-group') || input;

        if (window.tempusDominus?.TempusDominus) {
            if (!wrapper._tempusDominus) {
                wrapper._tempusDominus = new tempusDominus.TempusDominus(input, {
                    display: { placement },
                });
            } else {
                wrapper._tempusDominus.updateOptions({ display: { placement } });
            }

            // Cho phép click vào input để mở lịch (dù readonly)
            input.addEventListener('click', () => wrapper._tempusDominus.show());
            input.addEventListener('open-calendar', () => wrapper._tempusDominus.show());
        }
    }

    function initAll() {
        document.querySelectorAll('input[data-placement]').forEach(initPlacement);
    }

    document.addEventListener('DOMContentLoaded', () => setTimeout(initAll, 300));
    document.addEventListener('livewire:afterDomUpdate', () => setTimeout(initAll, 150));
})();
</script>
@endpushOnce
