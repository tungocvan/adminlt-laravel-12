<div wire:ignore.self class="modal fade" id="loginModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title">Đăng nhập</h5>
                <button type="button" class="close" wire:click="$dispatch('closeLoginModal')">
                    <span>&times;</span>
                </button>
            </div>

            <!-- Body -->
            <form wire:submit.prevent="submit">
                <div class="modal-body">

                    <!-- Login (email / username) -->
                    <div class="form-group">
                        <label>Email hoặc Username</label>
                        <input type="text" class="form-control @error('login') is-invalid @enderror"
                            wire:model.defer="login" placeholder="Nhập email hoặc username">

                        @error('login')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label>Mật khẩu</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            wire:model.defer="password" placeholder="Nhập mật khẩu">

                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember -->
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" wire:model="remember" id="remember">
                        <label class="form-check-label" for="remember">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>

                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$dispatch('closeLoginModal')">
                        Đóng
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Đăng nhập
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@push('js')
    <script>
        document.addEventListener('livewire:init', () => {

            Livewire.on('openLoginModal', () => {                
                $('#loginModal').modal({
                    backdrop: 'static', // ❌ click ngoài không đóng
                    keyboard: false     // ❌ ESC không đóng
                });
                $('#loginModal').modal('show');
            });

            Livewire.on('closeLoginModal', () => {
                $('#loginModal').modal('hide');
            });
            Livewire.on('userLoggedIn', () => {
                window.location.reload();
            });

        });
    </script>
@endpush
