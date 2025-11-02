<div>
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h3 class="card-title m-0">
                <i class="fas fa-database"></i> Migration Manager
            </h3>
            <div class="position-relative mb-3">
                <input type="text" wire:model.live.debounce.300ms="search" class="form-control pr-5"
                    placeholder="Tìm kiếm bảng...">

                @if ($search)
                    <button type="button" wire:click="$set('search', '')"
                        class="btn btn-sm btn-outline-secondary position-absolute"
                        style="top: 50%; right: 10px; transform: translateY(-50%);">
                        &times;
                    </button>
                @endif
            </div>

            <div class="btn-group">
                <button wire:click="backupDatabase" class="btn btn-warning btn-sm">
                    <i class="fas fa-download"></i> Backup Database
                </button>
                <button wire:click="restoreDatabase" class="btn btn-success btn-sm">
                    <i class="fas fa-upload"></i> Restore Database
                </button>
            </div>
        </div>

        <div class="card-body">
            {{-- Thông báo --}}
            <div class="my-2">
                @if (session()->has('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('message') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif
            </div>

            {{-- Danh sách migration --}}
            @forelse ($this->filteredMigrations as $table => $tableData)
                <div class="card mb-3" @if (!$tableData['exists']) style="border-color: red;" @endif>
                    <div class="card-header d-flex justify-content-between align-items-center text-white"
                        style="background-color: #17a2b8;">
                        <div>
                            <strong><i class="fas fa-table"></i> Bảng:</strong> {{ $table }}

                            @if (!$tableData['exists'])
                                <span class="badge badge-danger ml-2">Chưa tồn tại</span>
                            @endif

                            @if ($tableData['imported'])
                                <span class="badge badge-success ml-2">Đã import</span>
                            @endif

                        </div>
                        @php
                            $fileInfo = $this->getExportFileInfo($table);
                        @endphp

                        <div class="btn-group">
                            <button wire:click="confirmDelete('{{ $table }}')" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash-alt"></i> Xóa & Migrate lại
                            </button>
                            <button wire:click="exportMyslq('{{ $table }}')"
                                class="btn btn-outline-light btn-sm">
                                <i class="fas fa-file-export"></i> Export
                            </button>
                            <button wire:click="importMyslq('{{ $table }}')"
                                class="btn btn-outline-light btn-sm">
                                <i class="fas fa-file-import"></i> Import
                            </button>

                            @if ($fileInfo)
                                <a href="{{ asset('storage/mysql/' . $table . '.mysql') }}"
                                    class="btn btn-outline-light btn-sm" download>
                                    <i class="fas fa-download"></i> Tải về
                                </a>
                                <span class="ml-2" style="color: #ffeb3b;font-weight: 600;text-shadow: 0 0 2px #000;">
                                    ({{ $fileInfo['modified'] }})
                                </span>
                            @endif
                        </div>

                    </div>
                    <div class="card-body bg-light">
                        <ul class="mb-0">
                            @foreach ($tableData['migrations'] as $migration)
                                <li>
                                    <i class="fas fa-code-branch text-secondary"></i>
                                    {{ $migration->migration }}
                                    <small class="text-muted">(batch: {{ $migration->batch }})</small>
                                </li>
                            @endforeach

                            {{-- Hiển thị quan hệ FK --}}
                            @if (!empty($tableData['references_to']) || !empty($tableData['referenced_by']))
                                <ul class="text-muted ml-4 mt-2 text-sm">
                                    @if (!empty($tableData['references_to']))
                                        <li>
                                            <i class="fas fa-link text-success"></i>
                                            Tham chiếu đến: {{ implode(', ', $tableData['references_to']) }}
                                        </li>
                                    @endif
                                    @if (!empty($tableData['referenced_by']))
                                        <li>
                                            <i class="fas fa-share text-warning"></i>
                                            Được tham chiếu bởi: {{ implode(', ', $tableData['referenced_by']) }}
                                        </li>
                                    @endif
                                </ul>
                            @endif
                        </ul>
                    </div>
                </div>
            @empty
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> Không có migration nào để hiển thị.
                </div>
            @endforelse

        </div>
    </div>

    {{-- Modal xác nhận --}}
    <div class="modal fade @if ($modalVisible) show d-block @endif" tabindex="-1" role="dialog"
        style="@if ($modalVisible) display:block; @endif">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Xác nhận xóa bảng</h5>
                    <button type="button" class="close" wire:click="cancelDelete">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa bảng <strong>{{ $selectedTable }}</strong> không?</p>
                    <p class="text-danger mb-0"><i class="fas fa-warning"></i> Dữ liệu trong bảng sẽ bị mất!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="cancelDelete">Hủy</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteTableMigrations">Xóa & Migrate
                        lại</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Nền mờ khi modal mở --}}
    <div class="modal-backdrop fade show" style="@if (!$modalVisible) display:none; @endif"></div>

    {{-- Modal loading khi đang chạy Artisan --}}
    <div wire:loading.delay.longest>
        <div class="modal fade show d-block" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-4 text-center">
                    <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                        <span class="sr-only">Đang xử lý...</span>
                    </div>
                    <h5 class="mb-2">Đang thực hiện lệnh Artisan...</h5>
                    <p class="text-muted">Vui lòng chờ trong giây lát</p>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    </div>
</div>
