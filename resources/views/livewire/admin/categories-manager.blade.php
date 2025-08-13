<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Product Categories</h3>

        <div class="d-flex">
            <input wire:model.live.debounce.500ms="search" type="text" class="form-control mr-2" placeholder="Tìm kiếm danh mục...">

            <select wire:model="bulkAction" class="form-control mr-2">
                <option value="">Hành động hàng loạt</option>
                <option value="delete">Xóa</option>
            </select>

            <button wire:click="applyBulkAction" class="btn btn-secondary" @if(count($selected) === 0) disabled @endif>Áp dụng</button>
        </div>
    </div>

    <div class="card-body row">
        <div class="col-md-4">
            @if(session()->has('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <form wire:submit.prevent="save">
                <div class="form-group">
                    <label>Tên danh mục</label>
                    <input wire:model.defer="name" type="text" class="form-control" placeholder="Category name">
                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Slug</label>
                    <input wire:model.defer="slug" type="text" class="form-control" placeholder="Slug">
                    @error('slug') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Danh mục cha</label>
                    <select wire:model.defer="parent_id" class="form-control">
                        <option value="">Không có</option>
                        @foreach ($allCategories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->term->name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea wire:model.defer="description" class="form-control" rows="3"></textarea>
                </div>

                
                @if ($editingId)
                    <button type="button" wire:click="saveUpdate" class="btn btn-primary">Cập nhật</button>
                @else
                    <button type="button" wire:click="save" class="btn btn-primary">Lưu</button>
                @endif

                <button type="button" wire:click="resetFields" class="btn btn-secondary ml-2">Làm mới</button>
            </form>
        </div>

        <div class="col-md-8">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th><input type="checkbox" wire:click="toggleSelectAll" @if(count($selected) === count($currentPageIds) && count($currentPageIds) > 0) checked @endif></th>
                        <th>STT</th>
                        <th>Tên danh mục</th>
                        <th>Slug</th>
                        <th>Danh mục cha</th>
                        <th>Mô tả</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($categories as $cat)
                <tr>
                    <td><input type="checkbox" value="{{ $cat->id }}" wire:model="selected"></td>
                    <td>{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                    <td>{{ $cat->term->name }}</td>
                    <td>{{ $cat->term->slug }}</td>
                    <td>{{ $cat->parent ? $cat->parent->term->name : '-' }}</td>
                    <td>{{ $cat->description }}</td>
                    <td>
                        <button wire:click="edit({{ $cat->id }})" class="btn btn-sm btn-info">Sửa</button>
                        <button wire:click="delete({{ $cat->id }})" class="btn btn-sm btn-danger" onclick="confirm('Bạn có chắc muốn xóa?') || event.stopImmediatePropagation()">Xóa</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Không có danh mục nào.</td>
                </tr>
                @endforelse
                
                
            </table>

            <div>
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>
