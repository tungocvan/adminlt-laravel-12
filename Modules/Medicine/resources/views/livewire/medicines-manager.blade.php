<div>
    @if (!$showForm)
        {{-- ================= Toolbar ================= --}}
        <div class="d-flex justify-content-between flex-wrap mb-3">
            {{-- Nhóm thao tác chung --}}
            <div class="btn-group mb-1">
                <button class="btn btn-primary" wire:click="create">+ Thêm thuốc</button>
                <button class="btn btn-info" wire:click="exportJson">
                    <i class="fa fa-file-code"></i> Export All / Filtered
                </button>
                <a href="{{ route('medicine.stock') }}" class="btn btn-primary">+ Quản lý tồn kho</a>
            </div>

            {{-- Nhóm filter / search --}}
            <div class="d-flex mb-1">
                {{-- <select wire:model.live="selectedCategory" class="form-control mr-2">
                    <option value="">-- Chọn danh mục --</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @if ($cat->children)
                            @foreach ($cat->children as $child)
                                <option value="{{ $child->id }}">— {{ $child->name }}</option>
                            @endforeach
                        @endif
                    @endforeach
                </select> --}}
                <x-components::tnv-categories wire:model.live="selectedCategory"  />
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Tìm thuốc..." wire:model.live.debounce.300ms="search">
                    @if ($search)
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" wire:click="clearSearch">✕</button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Nhóm thao tác khi chọn thuốc --}}
            @if(count($selectedProducts) > 0)
            <div class="row mb-2 w-100">
                <div class="col-md-5">
                    {{-- Nhóm filter category --}}
                    <livewire:category-dropdown :categories="$categories"  width="100%"
                    wire:model.live="selectedCategories" 
                    applyMethod="applySelectedCategory" />
                </div>
                <div class="col-md-2">
                      {{-- Nhóm Xóa đã chọn --}}
                      <button class="btn btn-danger" 
                      wire:click="deleteSelected" 
                      onclick="return confirm('Bạn có chắc muốn xóa các thuốc đã chọn?')">
                      <i class="fa fa-trash"></i> Xóa đã chọn ({{ count($selectedProducts) }})
                  </button>
                </div>
                <div class="col-md-5">
                     {{-- Nhóm xuất dữ liệu --}}
                    <div class="btn-group mb-1 d-flex justify-content-end">
                        <button class="btn btn-info" wire:click="exportJson">
                            <i class="fa fa-file-code"></i> Xuất JSON
                        </button>
                        <button class="btn btn-success" wire:click="exportSelectedToExcel">
                            <i class="fa fa-file-excel"></i> Xuất Excel
                        </button>
                        <button class="btn btn-success" wire:click="exportWithTemplate">
                            <i class="fa fa-file-excel"></i> Xuất bảng giá
                        </button>
                    </div>
                </div>
            </div>
            
            @endif
        </div>

        {{-- ================= Thông báo ================= --}}
        @if(session('success'))
            <div class="alert alert-success mb-2">{{ session('success') }}</div>
        @endif
        @if(session('message'))
            <div class="alert alert-info mb-2">{{ session('message') }}</div>
        @endif

        {{-- ================= Hiển thị số bản ghi ================= --}}
        <div class="d-flex mb-3">
            <label>
                Hiển thị
                <select wire:model.live="perPage" class="form-control d-inline-block w-auto ml-2">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">Tất cả</option>
                </select>
                bản ghi
            </label>
        </div>

        {{-- ================= Bảng danh sách thuốc ================= --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
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
                                    <span class="badge badge-info mr-1">
                                        {{ $cat->name }}
                                        <a href="#" wire:click.prevent="removeCategory({{ $m->id }}, {{ $cat->id }}, @js($cat->name))"
                                           class="text-white ml-1">&times;</a>
                                    </span>
                                @endforeach
                            </td>
                            <td>{{ $m->created_at?->format('d/m/Y') }}</td>
                            <td>
                                <button class="btn btn-sm btn-warning" title="Sửa" wire:click="edit({{ $m->id }})">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" title="Xoá" wire:click="delete({{ $m->id }})" onclick="return confirm('Xoá thuốc này?')">
                                    <i class="fa fa-trash"></i>
                                </button>
                                <button class="btn btn-sm btn-info" title="Nhân bản" wire:click="duplicate({{ $m->id }})">
                                    <i class="fa fa-clone"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Không có dữ liệu</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ================= Pagination ================= --}}
        <div class="d-flex justify-content-end mt-2">
            {{ $medicines instanceof \Illuminate\Pagination\LengthAwarePaginator ? $medicines->links() : '' }}
        </div>

    @else
        {{-- ================= Form thêm / sửa thuốc ================= --}}
        @include('Medicine::livewire.medicines-form') 
    @endif
</div>

{{-- ================= Script ================= --}}
<script>
    window.addEventListener('setHeader', function(e) {
        const title = e.detail ? e.detail[0] : null;
        if (title) {
            const el = document.getElementById('page-header');
            if (el) el.innerText = title;
            document.title = title;
        }
    });

    document.addEventListener("DOMContentLoaded", () => {
        $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function(e) {
            const tabId = $(e.target).attr('href').replace('#', '');
            Livewire.first().set('activeTab', tabId);
            localStorage.setItem('activeTab', tabId);
        });

        Livewire.hook('message.processed', (message, component) => {
            let tab = localStorage.getItem('activeTab') || 'general';
            $('a[href="#' + tab + '"]').tab('show');
        });

        Livewire.on('image-removed', (data) => {
            const tab = data.tab || localStorage.getItem('activeTab') || 'extend';
            $('a[href="#' + tab + '"]').tab('show');
            localStorage.setItem('activeTab', tab);
        });

        $('.dropdown-menu').on('click', function(e) {
            e.stopPropagation();
        });
    });
</script>
