<!-- Modal cấu hình Cache & Queue -->
<div wire:ignore.self class="modal fade" id="cacheModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form wire:submit="updateCacheConfig" class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title"><i class="fas fa-sync"></i> Cấu hình Cache & Queue</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group"><label>CACHE_DRIVER</label><input wire:model="cache.cache_driver" class="form-control"></div>
                <div class="form-group"><label>QUEUE_CONNECTION</label><input wire:model="cache.queue_connection" class="form-control"></div>
                <div class="form-group"><label>SESSION_DRIVER</label><input wire:model="cache.session_driver" class="form-control"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-secondary"><i class="fas fa-check-circle"></i> Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>