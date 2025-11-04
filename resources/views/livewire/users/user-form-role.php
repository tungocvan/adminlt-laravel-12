{{-- resources/views/livewire/users/user-form-role.blade.php --}}
<div wire:ignore.self class="modal fade" id="roleModal" tabindex="-1" role="dialog" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="roleModalLabel">
                    <i class="fas fa-user-shield mr-2"></i> Cập nhật vai trò người dùng
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                @if($selectedUserId || count($selectedUsers) > 0)
                    <div class="form-group">
                        <label for="roleSelect">Chọn vai trò:</label>
                        <select wire:model="selectedRoleId" id="roleSelect" class="form-control">
                            <option value="">-- Chọn role --</option>
                            @foreach($roles as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('selectedRoleId')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                @else
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Vui lòng chọn ít nhất một người dùng trước khi cập nhật role.
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Đóng
                </button>
                <button type="button" class="btn btn-primary" wire:click="updateRole">
                    <i class="fas fa-save mr-1"></i> Lưu thay đổi
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    // Khi Livewire gọi event mở modal
    window.addEventListener('user-form-role:open', () => {
        $('#roleModal').modal('show');
    });

    // Khi Livewire gọi event đóng modal
    window.addEventListener('user-form-role:close', () => {
        $('#roleModal').modal('hide');
    });

    // Khi modal đóng (bằng X hoặc click ra ngoài)
    $('#roleModal').on('hidden.bs.modal', function () {
        Livewire.dispatch('modalRoleClosed'); // gửi sự kiện về server nếu cần
    });
</script>

