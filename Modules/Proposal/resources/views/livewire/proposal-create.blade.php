<div>
    <h4 class="mb-3">Tạo đề xuất mới</h4>

    <form wire:submit.prevent="submit" class="card card-body">

        <div class="form-group">
            <label>Tiêu đề</label>
            <input type="text" class="form-control" wire:model.defer="title">
            @error('title')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label>Mô tả</label>
            <textarea class="form-control" rows="4" wire:model.defer="description"></textarea>
            @error('description')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Thời gian dự kiến</label>
                <input type="date" class="form-control" wire:model.defer="expected_time">
            </div>

            <div class="form-group col-md-6">
                <label>Ưu tiên</label>
                <select class="form-control" wire:model.defer="priority">
                    <option value="LOW">Thấp</option>
                    <option value="MEDIUM">Trung bình</option>
                    <option value="HIGH">Cao</option>
                </select>
            </div>
        </div>

        <button class="btn btn-success">Gửi đề xuất</button>
    </form>
</div>