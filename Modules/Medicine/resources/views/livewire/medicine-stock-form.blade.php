<form wire:submit.prevent="saveStock">
<x-components::tnv-modal id="modalMedicine" title="{{ $editingStockId ? 'Cập nhật lô thuốc' : 'Tạo mới lô thuốc' }}">
    
        <div class="form-group">
            <label>Thuốc:</label>
            <select class="form-control" wire:model="medicine_id">
                <option value="">-- Chọn thuốc--</option>
                @foreach($medicines as $med)
                    <option value="{{ $med->id }}">{{ $med->ten_biet_duoc }}</option>
                @endforeach
            </select>
            @error('medicine_id') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Số lô:</label>
            <input type="text" class="form-control" wire:model="so_lo">
            @error('so_lo') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Hạn dùng:</label>
            <input type="date" class="form-control" wire:model="han_dung">
            @error('han_dung') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Số lượng:</label>
            <input type="number" class="form-control" wire:model="so_luong">
            @error('so_luong') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Giá vốn:</label>
            <input type="number" class="form-control" wire:model="gia_von">
            @error('gia_von') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

     
        <div class="form-group">
            <label>Trạng thái:</label>
            <select class="form-control" wire:model="status">
                @foreach($statusOptions as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            @error('status') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Vị trí:</label>
            <input type="text" class="form-control" wire:model="location">
            @error('location') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Ghi chú:</label>
            <textarea class="form-control" wire:model="notes"></textarea>
            @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="text-right mt-2">
            <button type="submit" class="btn btn-primary">{{ $editingStockId ? 'Cập nhật' : 'Tạo mới' }}</button>
            <button type="button" class="btn btn-secondary" wire:click="closeModal">Đóng</button>
        </div>

</x-components::tnv-modal>
</form>