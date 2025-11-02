<!-- Modal cấu hình Database -->
<div wire:ignore.self class="modal fade" id="databaseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form wire:submit.prevent="updateDatabaseConfig" class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title"><i class="fas fa-database"></i> Cấu hình Database</h5>
          <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>DB_CONNECTION</label>
            <input wire:model.defer="database.connection" type="text" class="form-control">
          </div>
          <div class="form-group">
            <label>DB_HOST</label>
            <input wire:model.defer="database.host" type="text" class="form-control">
          </div>
          <div class="form-group">
            <label>DB_PORT</label>
            <input wire:model.defer="database.port" type="text" class="form-control">
          </div>
          <div class="form-group">
            <label>DB_DATABASE</label>
            <input wire:model.defer="database.database" type="text" class="form-control">
          </div>
          <div class="form-group">
            <label>DB_USERNAME</label>
            <input wire:model.defer="database.username" type="text" class="form-control">
          </div>
          <div class="form-group">
            <label>DB_PASSWORD</label>
            <input wire:model.defer="database.password" type="password" class="form-control">
          </div>
  
          {{-- Hiển thị thông báo kiểm tra kết nối --}}
          @if ($testResult)
            <div class="alert alert-{{ $testSuccess ? 'success' : 'danger' }} mt-3">
              <i class="fas fa-{{ $testSuccess ? 'check-circle' : 'times-circle' }}"></i>
              {{ $testResult }}
            </div>
          @endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-info" wire:click="testConnection">
            <i class="fas fa-plug"></i> Kiểm tra kết nối
          </button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-success"><i class="fas fa-check-circle"></i> Lưu thay đổi</button>
        </div>
      </form>
    </div>
  </div>
  