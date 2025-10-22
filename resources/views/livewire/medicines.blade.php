<div>
    @if(!$showForm)
        {{-- Toolbar trên cùng --}}
        <div class="d-flex justify-content-between mb-2">
            <div class="d-flex">
                <button class="btn btn-primary mr-2" wire:click="create">+ Thêm thuốc</button>

                @if(count($selectedProducts) == 0)
                    <button class="btn btn-info mr-2" wire:click="exportJson">
                        <i class="fa fa-file-code"></i> Export All / Filtered
                    </button>
                @endif

                @if(count($selectedProducts) > 0)
                    <button class="btn btn-danger mr-2"
                            wire:click="deleteSelected"
                            onclick="return confirm('Bạn có chắc muốn xóa các thuốc đã chọn?')">
                        <i class="fa fa-trash"></i> Xóa đã chọn ({{ count($selectedProducts) }})
                    </button>
                    <button class="btn btn-info mr-2" wire:click="exportJson">
                        <i class="fa fa-file-code"></i> Xuất JSON
                    </button>
                @endif
            </div>

            <div class="input-group" style="width:50%">
                <input type="text"
                       class="form-control"
                       placeholder="Tìm thuốc..."
                       wire:model.live.debounce.300ms="search">
                @if($search)
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" wire:click="clearSearch">✕</button>
                    </div>
                @endif
            </div>
        </div>

        {{-- Combobox danh mục (lọc hoặc áp dụng) --}}
        <div class="d-flex mb-2 align-items-center">
            <select class="form-control mr-2 w-50" wire:model.live="selectedCategory">
                <option value="">-- Tất cả danh mục --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            @if(count($selectedProducts) > 0)
                <button class="btn btn-success"
                        wire:click="applySelectedCategory"
                        @disabled(!$selectedCategory)">
                    <i class="fa fa-check"></i> Áp dụng danh mục cho {{ count($selectedProducts) }} thuốc
                </button>
            @else
                <button class="btn btn-info"
                        wire:click="filterByCategory"
                        @disabled(!$selectedCategory)">
                    <i class="fa fa-filter"></i> Lọc danh mục
                </button>
            @endif
        </div>

        {{-- Thông báo --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('message'))
            <div class="alert alert-info">{{ session('message') }}</div>
        @endif

        {{-- Hiển thị số bản ghi --}}
        <div class="d-flex mb-2">
            <label>
                Hiển thị
                <select wire:model.live="perPage" class="form-select d-inline-block w-auto">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">Tất cả</option>
                </select>
                bản ghi
            </label>
        </div>

        {{-- Bảng danh sách thuốc --}}
        <table class="table table-bordered">
            <thead>
            <tr>
                <th style="width:32px;"><input type="checkbox" wire:model.live="selectAll"></th>
                <x-sortable-column field="id" label="ID" :sortField="$sortField" :sortDirection="$sortDirection" />
                <x-sortable-column field="ten_biet_duoc" label="Tên biệt dược" :sortField="$sortField" :sortDirection="$sortDirection" />
                <th>Hoạt chất</th>
                <th>Dạng</th>
                <th>Đơn giá</th>
                <th>Danh mục</th>
                <x-sortable-column field="created_at" label="Ngày tạo" :sortField="$sortField" :sortDirection="$sortDirection" />
                <th style="width:140px;">Thao tác</th>
            </tr>
            </thead>
            <tbody>
            @forelse($medicines as $m)
                <tr>
                    <td><input type="checkbox" value="{{ $m->id }}" wire:model.live="selectedProducts"></td>
                    <td>{{ $m->id }}</td>
                    <td>{{ $m->ten_biet_duoc }}</td>
                    <td>{{ $m->ten_hoat_chat }}</td>
                    <td>{{ $m->dang_bao_che }}</td>
                    <td>{{ number_format($m->don_gia ?? 0) }}</td>
                    <td>
                        @foreach($m->categories as $cat)
                            <span class="badge badge-info">{{ $cat->name }}</span>
                        @endforeach
                    </td>
                    <td>{{ $m->created_at ? $m->created_at->format('d/m/Y') : '' }}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" title="Sửa" wire:click="edit({{ $m->id }})">
                            <i class="fa fa-edit fa-sm"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" title="Xoá" wire:click="delete({{ $m->id }})" onclick="return confirm('Xoá thuốc này?')">
                            <i class="fa fa-trash fa-sm"></i>
                        </button>
                        <button class="btn btn-sm btn-info" title="Nhân bản" wire:click="duplicate({{ $m->id }})">
                            <i class="fa fa-clone fa-sm"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9">Không có dữ liệu</td></tr>
            @endforelse
            </tbody>
        </table>

        {{ $medicines instanceof \Illuminate\Pagination\LengthAwarePaginator ? $medicines->links() : '' }}
    @else
        {{-- Form thêm / sửa thuốc --}}
        @include('livewire.medicines-form')
    @endif
</div>


<script>
    window.addEventListener('setHeader', function(e) {
        const title = e.detail ? e.detail[0] : null;
        if (title) {
            const el = document.getElementById('page-header');
            if (el) el.innerText = title;
            document.title = title;
        }
    });
</script>

