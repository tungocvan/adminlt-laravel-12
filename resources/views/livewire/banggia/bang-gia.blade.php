<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Danh sách Bảng Báo Giá</h5>

            <div>
                {{-- Nút xóa hàng loạt --}}
                @if (count($selectedRows) > 0)
                    <button class="btn btn-danger btn-sm mr-2"
                            wire:click="deleteSelected"
                            onclick="return confirm('Bạn có chắc muốn xóa các bản ghi đã chọn không?')">
                        🗑️ Xóa đã chọn ({{ count($selectedRows) }})
                    </button>
                @endif

                {{-- Nút thêm mới --}}
                <button class="btn btn-primary btn-sm" wire:click="toggleForm">
                    {{ $formVisible ? 'Ẩn Form' : '➕ Thêm mới' }}
                </button>
            </div>
        </div>

        <div class="card-body">
            {{-- Thông báo --}}
            @if (session()->has('message'))
                @php $msg = session('message'); @endphp
                <div class="alert alert-success mt-2">
                    {!! str_contains($msg, 'storage/')
                        ? '✅ <a href="' . asset($msg) . '" target="_blank">Tải file báo giá</a>'
                        : e($msg) !!}
                </div>
            @endif

            {{-- Ô tìm kiếm --}}
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Tìm kiếm..." wire:model.live.debounce.500ms="search">
            </div>

            {{-- Form thêm mới --}}
            @if ($formVisible)
                <div class="border p-3 mb-3 rounded bg-light">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label>Tên khách hàng</label>
                            <input type="text" class="form-control" wire:model.live="ten_khach_hang">
                            @error('ten_khach_hang') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group col-md-3">
                            <label>Người duyệt Báo giá</label>
                            <input type="text" class="form-control" wire:model.live="nguoi_duyet_bg">
                            @error('nguoi_duyet_bg') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label>Tiêu đề báo giá</label>
                            <input type="text" class="form-control" wire:model.live="tieu_de_bg">
                            @error('tieu_de_bg') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group col-md-2">
                            <label>Ngày lập Báo giá</label>
                            <input type="text" class="form-control" wire:model.live="ngay_lap_bg">
                            @error('ngay_lap_bg') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Ghi chú</label>
                        <textarea class="form-control" wire:model.live="ghi_chu" rows="2"></textarea>
                    </div>

                    {{-- File báo giá --}}
                    @if ($file_path) 
                        <div class="form-group">
                            <label>File báo giá đã tạo</label><br>
                            <a href="{{ asset('storage/' . $file_path) }}" download>
                                📄 Xem / Tải báo giá
                            </a>
                        </div>
                    @endif

                    {{-- Danh sách thuốc --}}
                    <div class="form-group" x-data="{ search: '' }">
                        <label class="font-weight-bold">Danh sách thuốc áp dụng</label>
                        <input
                            type="text"
                            x-model="search"
                            placeholder="🔍 Tìm nhanh tên thuốc..."
                            class="form-control form-control-sm mb-2"
                        >

                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" id="selectAll" class="custom-control-input" wire:model.live="selectAll">
                            <label for="selectAll" class="custom-control-label font-weight-bold">Chọn tất cả</label>
                        </div>

                        <div class="border rounded bg-white p-2" style="max-height: 250px; overflow-y: auto;">
                            @forelse ($medicines as $m)
                                <div 
                                    x-show="search === '' || '{{ strtolower($m->ten_biet_duoc) }}'.includes(search.toLowerCase()) || '{{ strtolower($m->ten_hoat_chat) }}'.includes(search.toLowerCase())"
                                    class="custom-control custom-checkbox mb-1 ml-3"
                                >
                                    <input
                                        type="checkbox"
                                        class="custom-control-input"
                                        id="med{{ $m->id }}"
                                        value="{{ $m->id }}"
                                        wire:model.live="selectedMedicines"
                                    >
                                    <label class="custom-control-label" for="med{{ $m->id }}">
                                        {{ $m->ten_biet_duoc }}
                                        <small class="text-muted">({{ $m->ten_hoat_chat }} - {{ $m->don_vi_tinh }})</small>
                                    </label>
                                </div>
                            @empty
                                <p class="text-muted ml-3">Không có thuốc nào trong danh sách.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-secondary mr-2" wire:click="toggleForm">Hủy</button>
                        <button class="btn btn-success" wire:click="save">Lưu</button>
                    </div>
                </div>
            @endif

            {{-- Bảng danh sách --}}
            <table class="table table-bordered table-hover mt-3">
                <thead class="thead-light">
                    <tr>
                        <th style="width:40px; text-align:center;">
                            <input type="checkbox" wire:model.live="selectAllRows">
                        </th>
                        <th wire:click="sortBy('ma_so')" style="cursor:pointer;">Mã số</th>
                        <th wire:click="sortBy('ten_khach_hang')" style="cursor:pointer;">Khách hàng</th>
                        <th>Thuốc áp dụng</th>
                        <th>File báo giá</th>
                        <th>Ngày tạo</th>
                        <th width="80">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $r)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" wire:model.live="selectedRows" value="{{ $r->id }}">
                            </td>
                            <td>{{ $r->ma_so }}</td>
                            <td>{{ $r->ten_khach_hang }}</td>
                            <td>
                                @php
                                    $ids = is_array($r->product_ids)
                                        ? $r->product_ids
                                        : (is_string($r->product_ids) ? json_decode($r->product_ids, true) : []);
                                    $ids = $ids ?? [];
                                @endphp
                                @if(count($ids))
                                    @foreach(\App\Models\Medicine::whereIn('id', $ids)->get() as $m)
                                        <span class="badge badge-info">{{ $m->ten_biet_duoc }}</span>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @if ($r->file_path)
                                    <a href="{{ route('banggia.download', $r->id) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-download"></i> Excel
                                    </a>
                                @else
                                    <span class="text-muted">Chưa có file</span>
                                @endif
                            </td>
                            <td>{{ $r->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-danger"
                                        wire:click="delete({{ $r->id }})"
                                        onclick="return confirm('Xóa bản ghi này?')">
                                    Xóa
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Không có dữ liệu</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">
                {{ $records->links() }}
            </div>
        </div>
    </div>
</div>
