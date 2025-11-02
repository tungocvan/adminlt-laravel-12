<div>
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h3 class="card-title m-0">
                <i class="fas fa-database"></i> Migration Manager
            </h3>
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
            @forelse ($groupedMigrations as $table => $migrations)
                <div class="card border-info mb-3">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <div>
                            <strong><i class="fas fa-table"></i> Bảng:</strong> {{ $table }}
                        </div>
                        <div class="btn-group">
                            <button wire:click="confirmDelete('{{ $table }}')" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash-alt"></i> Xóa & Migrate lại
                            </button>
                            <button wire:click="exportMyslq('{{ $table }}')" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-file-export"></i> Export
                            </button>
                            <button wire:click="importMyslq('{{ $table }}')" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-file-import"></i> Import
                            </button>
                        </div>
                    </div>
                    <div class="card-body bg-light">
                        <ul class="mb-0">
                            @foreach ($migrations as $migration)
                                <li>
                                    <i class="fas fa-code-branch text-secondary"></i>
                                    {{ $migration->migration }} 
                                    <small class="text-muted">(batch: {{ $migration->batch }})</small>
                                </li>
                            @endforeach
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
    <div class="modal fade @if($modalVisible) show d-block @endif" tabindex="-1" role="dialog" style="@if($modalVisible) display:block; @endif">
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
                    <button type="button" class="btn btn-danger" wire:click="deleteTableMigrations">Xóa & Migrate lại</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Nền mờ khi modal mở --}}
    <div class="modal-backdrop fade show" style="@if(!$modalVisible)display:none;@endif"></div>

    {{-- Modal loading khi đang chạy Artisan --}}
    <div wire:loading.delay.longest>
        <div class="modal fade show d-block" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center p-4">
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
