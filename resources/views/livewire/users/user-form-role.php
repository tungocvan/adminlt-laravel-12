{{-- Role modal (Alpine + entangle showModalRole) --}}
<div x-data="{ open: @entangle('showModalRole') }" x-cloak>
    <div x-show="open" x-transition.opacity style="display:none"
         class="modal-backdrop fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center">
        <div x-show="open" x-transition class="bg-white rounded shadow-lg w-full max-w-md mx-3" @click.away="open = false">
            <div class="modal-header p-3 bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="modal-title mb-0">Cập nhật Role cho người dùng</h5>
                <button type="button" class="close text-white" @click="open = false">&times;</button>
            </div>

            <div class="modal-body p-3">
                @if (!empty($selectedUsers))
                    <p><strong>{{ count($selectedUsers) }}</strong> user được chọn.</p>
                    <div class="form-group">
                        <label>Chọn Role</label>
                        <select wire:model="selectedRoleId" class="form-control">
                            <option value="">-- Chọn role --</option>
                            @foreach($this->roles as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('selectedRoleId') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                @else
                    <div class="alert alert-warning mb-0">Vui lòng chọn ít nhất một người dùng trước khi cập nhật role.</div>
                @endif
            </div>

            <div class="modal-footer p-3 d-flex justify-content-end">
                <button class="btn btn-secondary btn-sm mr-2" @click="open = false">Đóng</button>
                <button class="btn btn-primary btn-sm" wire:click="updateUserRole">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>
