<div wire:ignore.self
     class="modal fade"
     id="registerModal"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title">Đăng ký tài khoản</h5>

                <button type="button"
                        class="close"
                        wire:click="$dispatch('closeRegisterModal')"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Body -->
            <form wire:submit.prevent="submit">
                <div class="modal-body">

                    <!-- Name -->
                    <div class="form-group">
                        <label>Tên hiển thị</label>
                        <input type="text"
                               class="form-control @error('name') is-invalid @enderror"
                               wire:model.defer="name"
                               placeholder="Tên hiển thị (không bắt buộc)">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               wire:model.defer="email"
                               placeholder="Nhập email">

                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label>Mật khẩu *</label>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               wire:model.defer="password"
                               placeholder="Ít nhất 8 ký tự">

                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password confirmation -->
                    <div class="form-group">
                        <label>Nhập lại mật khẩu *</label>
                        <input type="password"
                               class="form-control"
                               wire:model.defer="password_confirmation"
                               placeholder="Nhập lại mật khẩu">
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            wire:click="$dispatch('closeRegisterModal')">
                        Đóng
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        Đăng ký
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@push('js')
<script>
document.addEventListener('livewire:init', () => {

    Livewire.on('openRegisterModal', () => {
        $('#registerModal').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#registerModal').modal('show');

    });

    Livewire.on('closeRegisterModal', () => {
        $('#registerModal').modal('hide');
    });

});
</script>
@endpush
