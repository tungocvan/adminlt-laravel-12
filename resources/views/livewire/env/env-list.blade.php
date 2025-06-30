<div>
    <h1>Chỉnh sửa file .env</h1>
    
    <!-- Thêm cặp key=value mới -->
    <div class="my-3">
        @if($errorMessage)
            <div class="alert alert-danger">{{ $errorMessage }}</div>
        @endif
        <div class="d-flex flex-row bd-highlight mb-3 w-50">
        <input type="text" wire:model="newKey" placeholder="Key" class="form-control my-1 mr-2" style="width:200px"/>
        <input type="text" wire:model="newValue" placeholder="Value" class="form-control my-1 mr-2" style="width:400px" />        
        <button wire:click="addItem" style="width: 150px;" class="btn btn-outline-success btn-sm">
            <i class="fa fa-plus"></i> Thêm mới
        </button>
        </div>
        
    </div>
    
    <ul class="list-group">
        @foreach($envVariables as $line)
            @if (strpos($line, '=') !== false)
                @php
                    [$key, $value] = explode('=', trim($line), 2);
                    $value = trim($value);
                @endphp
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-md-3"><strong>{{ $key }}</strong></div>
                        <div class="col-md-3" style="overflow-wrap: break-word;">{{ $value }}</div>
                        <div class="col-md-2">
                            <button wire:click="openModal('{{ $key }}', '{{ $value }}')" class="btn btn-warning btn-sm">Chỉnh sửa</button>
                            <button wire:click="deleteEnv('{{ $key }}')" class="btn btn-danger btn-sm">Xóa</button>
                        </div>
                    </div>                    
                </li>
            @endif
        @endforeach
    </ul>

    <!-- Modal -->
    <div class="modal fade @if($showModal) show @endif" style="@if($showModal) display: block; @endif" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa {{ $selectedKey }}</h5>
                    <button wire:click="resetModal" type="button" class="close" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="text" wire:model="selectedValue" class="form-control" />
                    @if($errorMessage)
                        <div class="alert alert-danger mt-2">{{ $errorMessage }}</div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button wire:click="updateEnv" class="btn btn-primary">Cập nhật</button>
                    <button wire:click="resetModal" class="btn btn-secondary">Đóng</button>
                </div>
            </div>
        </div>
    </div>
</div>