<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h4>Quản lý Danh mục</h4>
        <button wire:click="openCreate" class="btn btn-primary">
            <i class="fa fa-plus"></i> Thêm mới
        </button>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="row mb-3">
        <div class="col-md-2">
            <select wire:model.live="perPage" class="form-control">
                <option value="10">10 / page</option>
                <option value="25">25 / page</option>
                <option value="50">50 / page</option>
                <option value="100">100 / page</option>
                <option value="all">All</option>
            </select>
        </div>
        <div class="col-md-3">
            <select wire:model.live="filterType" class="form-control">
                <option value="">-- Lọc loại --</option>
                @foreach($typeOptions as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-3">
            <select wire:model.live="selectedParent" class="form-control">
                <option value="">-- Chọn Menu gốc --</option>
                @foreach($parents as $p)
                    <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <button wire:click="$set('filterType','')" 
                    class="btn btn-sm btn-secondary"
                    @if($filterType === '') disabled @endif>
                Clear Loại
            </button>
            <button wire:click="$set('selectedParent', null)" 
                    class="btn btn-sm btn-secondary"
                    @if($selectedParent === null) disabled @endif>
                Clear Menu gốc
            </button>
        </div>
    </div>

    {{-- Chuẩn bị map prefix theo ID --}}
    @php
        $prefixMap = collect($parents)->mapWithKeys(function($item) {
            return [$item['id'] => $item['name']];
        });
    @endphp

    {{-- Table --}}
    <table class="table table-bordered table-striped">
        <thead class="thead-light">
            <tr>
                <th width="50">ID</th>
                <th>Tên</th>
                <th>Slug</th>
                <th>Loại</th>
                <th>Trạng thái</th>
                <th width="160">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $prefixMap[$category->id] ?? $category->name }}</td>
                    <td>{{ $category->slug }}</td>
                    <td><span class="badge badge-info">{{ $category->type }}</span></td>
                    <td>
                        @if($category->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <button wire:click="openEdit({{ $category->id }})"
                                class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i> Sửa
                        </button>
                        <button wire:click="deleteCategory({{ $category->id }})"
                                onclick="return confirm('Xác nhận xóa?')"
                                class="btn btn-sm btn-danger">
                            <i class="fa fa-trash"></i> Xóa
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Không có dữ liệu</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            Hiển thị {{ $categories->firstItem() }} đến {{ $categories->lastItem() }} 
            của {{ $categories->total() }} bản ghi
        </div>
        <div>
            {{ $categories->links('components.pagination') }}
        </div>
    </div>

    {{-- Modal Bootstrap --}}
    <div class="modal fade @if($isModalOpen) show d-block @endif" tabindex="-1" role="dialog"
     style="background: rgba(0,0,0,0.5); @if(!$isModalOpen) display:none; @endif">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $categoryId ? 'Cập nhật Danh mục' : 'Thêm Danh mục' }}</h5>
                <button type="button" class="close" wire:click="closeModal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-row">

                    <div class="form-group col-md-6">
                        <label>Tên</label>
                        <input type="text" wire:model="name" class="form-control">
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label>Slug</label>
                        <input type="text" wire:model="slug" class="form-control">
                        @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label>Loại</label>
                        <select wire:model.live="type" class="form-control">
                            @foreach($typeOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                            <option value="new">➕ Thêm loại mới...</option>
                        </select>
                    
                        @if($isAddingType)
                            <div class="input-group mt-2">
                                <input type="text" wire:model="customType" class="form-control" placeholder="Nhập loại mới...">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-success btn-sm" wire:click="saveNewType">OK</button>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    

                    <div class="form-group col-md-6">
                        <label>Cha</label>
                        <select wire:model="parent_id" class="form-control">
                            <option value="">-- Không chọn --</option>
                            @foreach($parents as $p)
                                <option value="{{ $p['id'] }}">{{ $p['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Trạng thái</label><br>
                        <input type="checkbox" wire:model="is_active"> Active
                    </div>

                    <div class="form-group col-md-6">
                        <label>Thứ tự</label>
                        <input type="number" wire:model="sort_order" class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Icon</label>
                        <input type="text" wire:model="icon" class="form-control">
                        @error('icon') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label>Can</label>
                        <input type="text" wire:model="can" class="form-control">
                        @error('can') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group col-md-12">
                        <label>Mô tả</label>
                        <textarea wire:model="description" class="form-control"></textarea>
                        @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label>Ảnh</label>
                        <input type="file" wire:model="imageFile" class="form-control" style="height:calc(2.25rem + 10px)">
                        @error('imageFile') <small class="text-danger">{{ $message }}</small> @enderror

                        @if($image)
                            <div class="mt-2 position-relative d-inline-block">
                                <img src="{{ $image instanceof \Livewire\TemporaryUploadedFile ? $image->temporaryUrl() : asset('storage/' . $image) }}" 
                                     alt="" width="100" class="img-thumbnail">
                                <button type="button" wire:click="removeImage" 
                                        class="btn btn-sm btn-danger position-absolute" 
                                        style="top:0; right:0; transform: translate(50%,-50%);">
                                    &times;
                                </button>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" wire:click="closeModal" class="btn btn-secondary">Đóng</button>
                <button type="button" wire:click="saveCategory" class="btn btn-primary">Lưu</button>
            </div>
        </div>
    </div>
</div>

</div>
