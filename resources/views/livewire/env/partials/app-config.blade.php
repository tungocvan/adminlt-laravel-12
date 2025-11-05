 <!-- Modal cấu hình Ứng dụng -->
 <div wire:ignore.self class="modal fade" id="appModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
      <form wire:submit="updateAppConfig" class="modal-content">
          <div class="modal-header bg-info text-white">
              <h5 class="modal-title"><i class="fas fa-cube"></i> Cấu hình Ứng dụng</h5>
              <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
              <div class="form-group"><label>APP_NAME</label><input wire:model="app.name" class="form-control"></div>
              <div class="form-group"><label>APP_ENV</label><input wire:model="app.env" class="form-control"></div>
              <div class="form-group"><label>APP_DEBUG</label>
                  <select wire:model="app.debug" class="form-control">
                      <option value="true">true</option>
                      <option value="false">false</option>
                  </select>
              </div>
              <div class="form-group"><label>APP_URL</label><input wire:model="app.url" class="form-control"></div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
              <button type="submit" class="btn btn-info"><i class="fas fa-check-circle"></i> Lưu thay đổi</button>
          </div>
      </form>
  </div>
</div>
