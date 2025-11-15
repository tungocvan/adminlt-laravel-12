<x-adminlte-modal
    wire:ignore.self
    id="{{ $id }}"
    title="{{ $title }}"
    size="{{ $size }}"
    theme="{{ $theme }}"
    icon="{{ $icon }}"
    :v-centered="$vCentered"
    :scrollable="$scrollable"
>
    <div class="container-fluid">
        {{ $slot }}
    </div>

    <x-slot name="footerSlot">
        <button type="submit" class="btn btn-success">Lưu</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Đóng</button>
    </x-slot>
</x-adminlte-modal>

@push('scripts')
<script>
    document.addEventListener('show-modal-{{ $id }}', () => {
        $('#{{ $id }}').modal({
            backdrop: 'static',
            keyboard: false,
        }).modal('show');
    });

    document.addEventListener('hide-modal-{{ $id }}', () => {
        $('#{{ $id }}').modal('hide');
    });

    $('[data-dismiss="modal"]').on('click', function() {
        $(this).closest('.modal').modal('hide');
    });
</script>
@endpush
