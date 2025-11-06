@if ($isEdit)
    <form wire:submit="updateUser">
    @else
        <form wire:submit="createUser">
@endif
    <x-components::tnv-modal id="modalUser" title="{{ $isEdit ? 'Cập nhật User' : 'Tạo mới User' }}">
        @include('livewire.users.user-form-content') 
    </x-components::tnv-modal>
</form>
