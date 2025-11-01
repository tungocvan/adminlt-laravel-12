<div>
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fa fa-file-excel-o"></i> Danh sách file Excel (/storage/app/public/excel)
            </h5>
            <div class="d-flex gap-2">
                <button wire:click="loadExcelFiles" class="btn btn-outline-secondary btn-sm mx-2">
                    <i class="fa fa-refresh"></i> Tải lại
                </button>
                <button wire:click="deleteSelected" class="btn btn-danger btn-sm"
                        onclick="return confirm('Bạn có chắc muốn xóa các file đã chọn không?')">
                    <i class="fa fa-trash"></i> Xóa đã chọn
                </button>
            </div>
        </div>

        <div class="card-body">
            @if (session('message'))
                <div class="alert alert-success py-2">{{ session('message') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger py-2">{{ session('error') }}</div>
            @endif

            @if (count($excelFiles) > 0)
                <table class="table table-bordered table-sm align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th width="40"><input type="checkbox" wire:model.live="selectAll"></th>
                            <th>Tên file Excel</th>
                            <th>Kích thước</th>
                            <th>Cập nhật</th>
                            <th width="300">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($excelFiles as $file)
                            <tr>
                                <td><input type="checkbox" wire:model.live="selected" value="{{ $file['name'] }}"></td>
                                <td>
                                    @if ($renameFile === $file['name'])
                                        <div class="d-flex align-items-center">
                                            <input type="text"
                                                   wire:model.defer="newFileName"
                                                   class="form-control form-control-sm mr-2"
                                                   style="max-width: 200px;">
                                            <button wire:click="renameFileConfirm" class="btn btn-success btn-sm mr-1">
                                                <i class="fa fa-save"></i>
                                            </button>
                                            <button wire:click="cancelRename" class="btn btn-secondary btn-sm">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    @else
                                        <i class="fa fa-file-excel-o text-success"></i>
                                        {{ $file['name'] }}
                                    @endif
                                </td>
                                <td>{{ $file['size'] }}</td>
                                <td>{{ $file['updated'] }}</td>
                                <td>
                                    @if ($renameFile !== $file['name'])
                                        <button wire:click="startRename('{{ $file['name'] }}')" class="btn btn-warning btn-sm mr-1">
                                            <i class="fa fa-edit"></i> Đổi tên
                                        </button>

                                        <button wire:click="convertToJson('{{ $file['name'] }}')" class="btn btn-success btn-sm mr-1">
                                            <i class="fa fa-exchange"></i> Convert
                                        </button>

                                        @if ($file['json_exists'])
                                            <a href="{{ asset('storage/json/' . $file['json_name']) }}" 
                                               target="_blank" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fa fa-download"></i> Tải JSON
                                            </a>
                                        @else
                                            <span class="text-muted small"><i class="fa fa-ban"></i> Chưa có JSON</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-muted">
                    ⚠️ Không có file Excel nào trong thư mục <code>storage/app/public/excel</code>.
                </div>
            @endif
        </div>
    </div>
</div>
