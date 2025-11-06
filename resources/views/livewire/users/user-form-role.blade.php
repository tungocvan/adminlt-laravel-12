<form wire:submit="updateUserRole">
    <x-components::tnv-modal id="modalRole" title="Cập nhật Role" >
        @if (count($selectedUsers) > 0)
            <div class="form-group">
                <label>Chọn vai trò:</label>
                <select class="form-control" wire:model="selectedRoleId">
                    <option value="">-- Chọn role --</option>
                    @foreach ($roles as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
                <div class="form-group mt-2">
                    <label for="referral_code">Mã giới thiệu</label>
                    <input type="text" id="referral_code" name="referral_code" class="form-control"
                        placeholder="Mã giới thiệu" wire:model="referral_code">
                </div>
                @error('selectedRoleId')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        @else
            <div class="text-muted text-center">
                Vui lòng chọn ít nhất một người dùng để cập nhật role.
            </div>
        @endif
    </x-components::tnv-modal>
</form>
 