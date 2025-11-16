<div class="card mb-3">
    <div class="card-body row">

        <div class="form-group col-md-3">
            <label>Loại</label>
            <select wire:model.live="filterType" class="form-control">
                <option value="">-- Lọc loại --</option>
                @foreach ($typeOptions as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            
        </div>

        <div class="form-group col-md-3">
            <label>Chỉ danh mục cha</label>
            <select wire:model.live="filterParentOnly" class="form-control">
                <option value="">Tất cả</option>
                <option value="1">Chỉ danh mục cha</option> 
            </select>
        </div>

        <div class="form-group col-md-3">
            <label>Sắp xếp</label>
            <select wire:model.live="sortField" class="form-control">
                <option value="id">ID</option>
                <option value="name">Tên</option>
                <option value="sort_order">Thứ tự</option>
            </select>
        </div>

        <div class="form-group col-md-3">
            <label>Chiều</label>
            <select wire:model.live="sortDirection" class="form-control">
                <option value="asc">Tăng dần</option>
                <option value="desc">Giảm dần</option>
            </select>
        </div>

    </div>
</div>
