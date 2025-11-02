<div class="modal fade @if($showModal) show d-block @endif" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-edit mr-2"></i>
                    {{ $addMenu ? 'Thêm mới Menu' : 'Chỉnh sửa Menu' }}
                </h5>
                <button type="button" class="close text-white" wire:click="closeModal">&times;</button>
            </div>
            <div class="modal-body">
                <form>
                    @if(isset($menuHeader))
                        <x-adminlte-input name="menuHeader" label="Tên Menu" wire:model="menuHeader" />
                        <x-adminlte-input name="menuCan" label="Can" wire:model="menuCan" />
                    @else
                        <x-adminlte-input name="menuText" label="Tên Menu" wire:model="menuText" />
                        <x-adminlte-input name="menuUrl" label="URL" wire:model="menuUrl" />
                        <x-adminlte-input name="menuIcon" label="Icon" wire:model="menuIcon" />
                        <x-adminlte-input name="menuCan" label="Can" wire:model="menuCan" />
                    @endif
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeModal">Đóng</button>
                <button type="button" class="btn btn-primary" wire:click="updateMenu">Cập nhật</button>
            </div>
        </div>
    </div>
</div>
