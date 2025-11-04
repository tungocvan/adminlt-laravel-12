<x-adminlte-modal id="modalRole" title="Cập nhật Role" size="lg" theme="teal"
        icon="fas fa-user-shield" v-centered static-backdrop scrollable wire:ignore.self>

        @if(count($selectedUsers) > 0)
            <div class="form-group">
                <label>Chọn vai trò:</label>
                <select class="form-control" wire:model.defer="selectedRoleId">
                    <option value="">-- Chọn role --</option>
                    @foreach($roles as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                @error('selectedRoleId') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        @else
            <div class="text-center text-muted">
                Vui lòng chọn ít nhất một người dùng để cập nhật role.
            </div>
        @endif

        <x-slot name="footerSlot">
            <x-adminlte-button class="mr-auto" theme="success" label="Cập nhật" 
                wire:click="updateUserRole" :disabled="count($selectedUsers) === 0"/>
            <x-adminlte-button theme="danger" label="Đóng" data-dismiss="modal"/>
        </x-slot>
    </x-adminlte-modal>