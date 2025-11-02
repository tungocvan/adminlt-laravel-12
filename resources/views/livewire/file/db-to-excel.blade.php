<div class="container-fluid mt-3">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-database mr-2"></i> Danh sách bảng</h5>
            <input type="text" wire:model.live.debounce.500ms="search" class="form-control form-control-sm w-25" placeholder="🔍 Tìm bảng...">
        </div>

        <div class="card-body">

            @if ($message)
                <div class="alert alert-{{ $alertType }} alert-dismissible fade show">
                    {!! $message !!}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if (!empty($selectedTables))
                <div class="mb-2">
                    <button class="btn btn-sm btn-danger" wire:click="deleteSelected">
                        <i class="fas fa-trash-alt"></i> Xóa đã chọn
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:40px;">
                                <input type="checkbox" wire:model="selectAll">
                            </th>
                            <th style="width:60px;">#</th>
                            <th>Tên bảng</th>
                            <th style="width:120px;text-align:center">Trạng thái</th>
                            <th style="width:300px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tables as $index => $table)
                            <tr>
                                <td class="text-center">
                                    @if($table['exists'])
                                        <input type="checkbox" wire:model="selectedTables" value="{{ $table['name'] }}">
                                    @endif
                                </td>
                                <td>{{ $index + 1 }}</td>
                                <td><code>{{ $table['name'] }}</code></td>
                                <td class="text-center">
                                    @if($table['exists'])
                                        <span class="badge badge-success">Đã xuất</span>
                                    @else
                                        <span class="badge badge-secondary">Chưa xuất</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-primary" wire:click="export('{{ $table['name'] }}')">
                                            <i class="fas fa-file-export"></i> Xuất excel
                                        </button>
                                        <button class="btn btn-sm btn-primary mx-1" wire:click="exportMyslq('{{ $table['name'] }}')">
                                            <i class="fas fa-file-export"></i> Xuất mysql
                                        </button>

                                        @if($table['exists'])
                                            <a href="{{ $table['excel_path'] }}" target="_blank" class="btn btn-sm btn-info mx-1">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button class="btn btn-sm btn-danger" wire:click="deleteFile('{{ $table['name'] }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Không tìm thấy bảng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
