<!-- Modal cấu hình Email -->
<div wire:ignore.self class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form wire:submit="updateEmailConfig" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-envelope"></i> Cấu hình Email</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>MAIL_MAILER</label>
                    <input wire:model="email.mailer" type="text" class="form-control">
                </div>
                <div class="form-group">
                    <label>MAIL_HOST</label>
                    <input wire:model="email.host" type="text" class="form-control">
                </div>
                <div class="form-group">
                    <label>MAIL_PORT</label>
                    <input wire:model="email.port" type="number" class="form-control">
                </div>
                <div class="form-group">
                    <label>MAIL_USERNAME</label>
                    <input wire:model="email.username" type="text" class="form-control">
                </div>
                <div class="form-group">
                    <label>MAIL_PASSWORD</label>
                    <input wire:model="email.password" type="password" class="form-control">
                </div>
                <div class="form-group">
                    <label>MAIL_ENCRYPTION</label>
                    <input wire:model="email.encryption" type="text" class="form-control">
                </div>
                <div class="form-group">
                    <label>MAIL_FROM_ADDRESS</label>
                    <input wire:model="email.from_address" type="email" class="form-control">
                </div>
                <div class="form-group">
                    <label>MAIL_FROM_NAME</label>
                    <input wire:model="email.from_name" type="text" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>

                <!-- Nút kiểm tra kết nối -->
                <button type="button" wire:click="testEmailConnection" class="btn btn-warning">
                    <i class="fas fa-paper-plane"></i> Kiểm tra kết nối
                </button>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i> Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("alert-success", (e) => {
        console.log(e);
        toastr.success(e.detail.message);
    })
    document.addEventListener("alert-error", (e) => {
        toastr.error(e.detail.message);
    })
</script>