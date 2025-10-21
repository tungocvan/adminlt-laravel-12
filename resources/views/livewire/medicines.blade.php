<div>
    @if(!$showForm)
        <div class="d-flex justify-content-between mb-2">
            <div class="d-flex">
                <button class="btn btn-primary mr-2" wire:click="create">+ Thêm thuốc</button>

                @if(count($selectedProducts) == 0)
                    <button class="btn btn-info mr-2" wire:click="exportJson">
                        <i class="fa fa-file-code"></i> Export All / Filtered
                    </button>
                @endif

                @if(count($selectedProducts) > 0)
                    <button class="btn btn-danger mr-2" wire:click="deleteSelected" onclick="return confirm('Bạn có chắc muốn xóa các thuốc đã chọn?')">
                        <i class="fa fa-trash"></i> Xóa đã chọn ({{ count($selectedProducts) }})
                    </button>
                    <button class="btn btn-info mr-2" wire:click="exportJson">
                        <i class="fa fa-file-code"></i> Xuất JSON
                    </button>
                @endif
            </div>

            <div class="input-group" style="width:50%">
                <input type="text" class="form-control" placeholder="Tìm thuốc..." wire:model.live.debounce.300ms="search">
                @if($search)
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" wire:click="clearSearch">✕</button>
                    </div>
                @endif
            </div>
        </div>

        <div class="d-flex mb-2">
            @if(count($selectedProducts) > 0)
                <select class="form-control mr-2 w-50" wire:model.live="bulkCategory">
                    <option value="">-- Chọn danh mục --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>

                <button class="btn btn-success" wire:click="updateCategorySelected" @disabled(!$bulkCategory)>Cập nhật danh mục</button>
            @endif
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('message'))
            <div class="alert alert-info">{{ session('message') }}</div>
        @endif

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

        {{ $medicines->links() }}
    @else
        <div class="container-fluid">
            <div class="card mt-3">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                      <li class="nav-item" role="presentation">
                        <button class="nav-link {{ @when(!request()->tab, 'active') }}" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">{{ $medicineId ? 'Chỉnh sửa thuốc' : 'Thêm thuốc' }}</button>
                      </li>
                      <li class="nav-item" role="presentation">
                        <button class="nav-link {{ @when(request()->tab == 'profile', 'active') }}" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Mở rộng</button>
                      </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                      <div class="tab-pane fade  {{ @when(!request()->tab, 'show active') }}" id="home" role="tabpanel" aria-labelledby="home-tab"><br/>
                        <form wire:submit.prevent="save" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tên biệt dược</label>
                                        <input type="text" class="form-control" wire:model="ten_biet_duoc">
                                        @error('ten_biet_duoc') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Hoạt chất</label>
                                        <input type="text" class="form-control" wire:model="ten_hoat_chat">
                                    </div>

                                    <div class="form-group">
                                        <label>Dạng bào chế</label>
                                        <input type="text" class="form-control" wire:model="dang_bao_che">
                                    </div>

                                    <div class="form-group">
                                        <label>Đơn giá</label>
                                        <input type="number" class="form-control" wire:model="don_gia">
                                    </div>

                                    <div class="form-group">
                                        <label>Nhà phân phối</label>
                                        <input type="text" class="form-control" wire:model="nha_phan_phoi">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Giấy phép lưu hành</label>
                                        <input type="text" class="form-control" wire:model="giay_phep_luu_hanh">
                                    </div>

                                    <div class="form-group">
                                        <label>Hạn dùng</label>
                                        <input type="text" class="form-control" wire:model="han_dung">
                                    </div>

                                    <div class="form-group">
                                        <label>Nhóm thuốc</label>
                                        <input type="text" class="form-control" wire:model="nhom_thuoc">
                                    </div>

                                    <div class="form-group">
                                        <label>Link hình ảnh (hoặc upload bên dưới)</label>
                                        <input type="text" class="form-control" wire:model="link_hinh_anh">
                                    </div>

                                    <x-image-upload label="Ảnh chính" model="imageUpload" :current="$link_hinh_anh" removeMethod="removeImage" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Danh mục</label>
                                <div class="card" style="max-height: 250px; overflow-y: auto;">
                                    <div class="card-body p-2">
                                        {!! renderCategoryTree($categories, $selectedCategories) !!}
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex">
                                <button type="submit" class="btn btn-success">Lưu</button>
                                <button type="button" class="btn btn-secondary ml-2" wire:click="cancel">Huỷ</button>
                            </div>
                        </form>
                      </div>

                      <div class="tab-pane fade {{ @when(request()->tab == 'profile', 'show active') }}" id="profile" role="tabpanel" aria-labelledby="profile-tab"><br/>
                        {{-- Thêm trường mở rộng nếu cần --}}
                        <div class="form-group">
                            <label>Ghi chú / Thông tin bổ sung</label>
                            <textarea class="form-control" rows="4"></textarea>
                        </div>
                      </div>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>

@script
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
@endscript
