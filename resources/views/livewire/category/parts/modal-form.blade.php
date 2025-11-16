<div wire:ignore.self class="modal fade" id="categoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $editingId ? 'Cập nhật Danh mục' : 'Thêm Danh mục mới' }}</h5>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body row">

                <div class="form-group col-md-6">
                    <label>Tên</label>
                    <input type="text" class="form-control" wire:model.blur="name">
                    @error('name') <span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                <div class="form-group col-md-6">
                    <label>Slug</label>
                    <input type="text" class="form-control" wire:model.blur="slug">
                </div>

                <div class="form-group col-md-6">
                    <label>Loại</label>
                    <select class="form-control" wire:model.live="type">
                        <option value="">-- Chọn --</option>
                        <option value="menu">Menu</option>
                        <option value="category">Category</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label>Danh mục cha</label>
                    <select class="form-control" wire:model.live="parent_id">
                        <option value="">Không có</option>
                        @foreach ($parents as $p)
                            <option value="{{ $p['id'] }}">{{ $p['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-12">
                    <label>Mô tả</label>
                    <textarea class="form-control" wire:model.blur="description"></textarea>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button class="btn btn-primary" wire:click="save">Lưu</button>
            </div>

        </div>
    </div>
</div>
