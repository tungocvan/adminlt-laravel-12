<div class="modal fade @if($showMenuModal) show d-block @endif" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-database mr-2"></i>Backup - Restore Menu</h5>
                <button type="button" class="close text-white" wire:click="showCloseMenu">&times;</button>
            </div>
            <div class="modal-body">
                @if($actionMenu == 'backup')
                    <x-adminlte-input name="nameJson" label="Tên file backup" wire:model.live="nameJson" />
                @else
                    <ul class="list-group">
                        @foreach($backupFiles as $file)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $file }}
                                <div class="btn-group btn-group-sm">
                                    <button wire:click.prevent="restoreFile('{{ $file }}')" class="btn btn-success">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    <button wire:click.prevent="downloadFile('{{ $file }}')" class="btn btn-info">
                                        <i class="fas fa-download"></i>
                                    </button>
                                    <button wire:click.prevent="deleteFile('{{ $file }}')" class="btn btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="showCloseMenu">Đóng</button>
                @if($actionMenu == 'backup')
                    <button type="button" class="btn btn-primary" wire:click="updateMenuJson">Tạo Backup</button>
                @endif
            </div>
        </div>
    </div>
</div>
