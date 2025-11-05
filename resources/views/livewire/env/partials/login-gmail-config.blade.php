<!-- Modal cấu hình Gmail Login -->
<div wire:ignore.self class="modal fade" id="gmailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form wire:submit="updateGmailConfig" class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title"><i class="fab fa-google"></i> Cấu hình đăng nhập Gmail (OAuth2)</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
  
        <div class="modal-body">
          <div class="form-group">
            <label>GOOGLE_CLIENT_ID</label>
            <input wire:model="gmail.client_id" type="text" class="form-control" placeholder="Nhập Client ID">
          </div>
          <div class="form-group">
            <label>GOOGLE_CLIENT_SECRET</label>
            <input wire:model="gmail.client_secret" type="text" class="form-control" placeholder="Nhập Client Secret">
          </div>
          <div class="form-group">
            <label>GOOGLE_REDIRECT_URI</label>
            <input wire:model="gmail.redirect" type="text" class="form-control" placeholder="https://your-domain.com/auth/google/callback">
          </div>
  
          {{-- Nếu cần test cấu hình --}}
          @if ($gmailTestResult)
            <div class="alert alert-{{ $gmailTestSuccess ? 'success' : 'danger' }}">
              {{ $gmailTestResult }}
            </div>
          @endif
        </div>
  
        <div class="modal-footer">
          <button type="button" wire:click="testGmailConnection" class="btn btn-info">
            <i class="fas fa-plug"></i> Kiểm tra cấu hình
          </button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-danger"><i class="fas fa-save"></i> Lưu thay đổi</button>
        </div>
      </form>
    </div>
  </div>
  