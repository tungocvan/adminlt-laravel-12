<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Danh sách Bảng Báo Giá</h5>
            <button class="btn btn-primary btn-sm" wire:click="toggleForm">
                {{ $formVisible ? 'Ẩn Form' : '➕ Thêm mới' }}
            </button>
        </div>

        <div class="card-body">
            @if (session()->has('message'))
                @php $msg = session('message'); @endphp
                <div class="alert alert-success mt-2">
                    {!! str_contains($msg, 'storage/') 
                        ? '✅ <a href="' . asset($msg) . '" target="_blank">Tải file báo giá</a>' 
                        : e($msg) !!}
                </div>
            @endif


            <div class="form-group">
                <input type="text" class="form-control" placeholder="Tìm kiếm..." wire:model.debounce.500ms="search">
            </div>

            {{-- Form thêm/sửa --}}
            @if ($formVisible)
                <div class="border p-3 mb-3 rounded bg-light">
                    <div class="form-row">
                        {{-- <div class="form-group col-md-4">
                            <label>Mã số</label>
                            <input type="text" class="form-control" wire:model="ma_so">
                            @error('ma_so') <small class="text-danger">{{ $message }}</small> @enderror
                        </div> --}}

                        <div class="form-group col-md-8">
                            <label>Tên khách hàng</label>
                            <input type="text" class="form-control" wire:model="ten_khach_hang">
                            @error('ten_khach_hang') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Ghi chú</label>
                        <textarea class="form-control" wire:model="ghi_chu" rows="2"></textarea>
                    </div>

                    {{-- File path (auto-generated) --}}
                    @if ($file_path)
                        <div class="form-group">
                            <label>File báo giá đã tạo</label><br>                            
                            <a href="{{ asset('storage/' . $record->file_path) }}" download>
                                📄 Xem / Tải báo giá
                            </a>
                        </div>
                    @endif

                   {{-- Danh sách thuốc áp dụng --}}
                    <div class="form-group" x-data="{ search: '' }">
                        <label class="font-weight-bold">Danh sách thuốc áp dụng</label>

                        {{-- Ô tìm kiếm --}}
                        <input
                            type="text"
                            x-model="search"
                            placeholder="🔍 Tìm nhanh tên thuốc..."
                            class="form-control form-control-sm mb-2"
                        >

                        {{-- Checkbox chọn tất cả --}}
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" id="selectAll" class="custom-control-input" wire:model="selectAll">
                            <label for="selectAll" class="custom-control-label font-weight-bold">Chọn tất cả</label>
                        </div>

                        {{-- Danh sách thuốc --}}
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
                                        wire:model="selectedMedicines"
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
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th wire:click="sortBy('ma_so')" style="cursor:pointer;">Mã số</th>
                        <th wire:click="sortBy('ten_khach_hang')" style="cursor:pointer;">Khách hàng</th>
                        <th>Thuốc áp dụng</th>
                        <th>File báo giá</th>
                        <th>Ngày tạo</th>
                        <th width="120">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $r)
                        <tr>
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
                            
                                    <a href="{{ route('banggia.downloadPdf', $r->id) }}" class="btn btn-sm btn-primary" target="_blank">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                @else
                                    <span class="text-muted">Chưa có file</span>
                                @endif
                            </td>
                            
                            <td>{{ $r->created_at->format('d/m/Y') }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning" wire:click="toggleForm({{ $r->id }})">Sửa</button>
                                <button class="btn btn-sm btn-danger" wire:click="delete({{ $r->id }})"
                                    onclick="return confirm('Xóa bản ghi này?')">Xóa</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">Không có dữ liệu</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">
                {{ $records->links() }}
            </div>
        </div>
    </div>
</div>
