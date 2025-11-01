<div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Migration Manager - Gộp theo bảng</h3>
        </div>
        <div class="card-body">
            @if(session()->has('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif
            @if(session()->has('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @forelse($groupedMigrations as $table => $migrations)
                @php
               
                    //$excelPath = public_path("excel/database/{$table}.xlsx");
                    $excelPath = storage_path("app/public/excel/database/{$table}.xlsx");
                   
                @endphp

                <div class="card mb-3 border-info">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <span>Bảng: {{ $table }}</span>
                        <div>
                            @if(File::exists($excelPath))
                               
                                <a href="{{ asset("excel/database/{$table}.xlsx") }}" class="btn btn-success btn-sm" target="_blank">
                                    <i class="fas fa-download"></i> Tải file Excel
                                </a>
                                <button wire:click="confirmDelete('{{ $table }}')" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Xóa & Migrate lại + Import
                                </button>
                            @else
                                <span class="badge bg-warning text-dark">Chưa có file Excel để import</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <ul>
                            @foreach($migrations as $migration)
                                <li>{{ $migration->migration }} (batch: {{ $migration->batch }})</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">Không có migration nào để hiển thị.</div>
            @endforelse
        </div>
    </div>

    {{-- Modal xác nhận AdminLTE --}}
    <div class="modal fade @if($modalVisible) show d-block @endif" tabindex="-1" role="dialog" style="@if($modalVisible) display: block; @endif">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Xác nhận xóa bảng</h5>
                    <button type="button" class="close" wire:click="cancelDelete">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa bảng <strong>{{ $selectedTable }}</strong> không?</p>
                    <p>Các bảng sau sẽ bị drop:</p>
                    <ul>
                        @foreach($tablesToDrop as $t)
                            <li>{{ $t }}</li>
                        @endforeach
                    </ul>
                    <p class="text-danger">**Lưu ý:** Dữ liệu trong các bảng sẽ bị mất.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="cancelDelete">Hủy</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteTableMigrations">Xóa & Migrate lại + Import</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="@if(!$modalVisible) display:none; @endif"></div>
</div>
