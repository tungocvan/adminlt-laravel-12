<div>
    <div class="d-flex justify-content-between mb-3">
        <div class="d-flex gap-2">
            <input type="text" class="form-control" placeholder="Tìm kiếm..." wire:model.debounce.300ms="search">
            
            <select class="form-control" wire:model.live="perPage">
                @foreach($perPageOptions as $option)
                    <option value="{{ $option }}">{{ $option }} / trang</option>
                @endforeach
            </select>
        </div>
    
        <button class="btn btn-success" wire:click="openCreateForm">+ Thêm lô thuốc</button>
    </div>
    

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th width="40">
                    <input type="checkbox" wire:model.live="selectAll">
                </th>
                <th>ID</th> 
                <th>Thuốc</th> 
                <th>Số lô</th>
                <th>Hạn dùng</th>
                <th>Số lượng</th>
                <th>Giá vốn</th>

                <th>Trạng thái</th>
                <th style="width:100px">Vị trí</th>
                <th style="width:150px">Ghi chú</th>
                <th width="120">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $stock)
            <tr>
                <td><input type="checkbox" wire:model="selectedStocks" value="{{ $stock->id }}"></td>
                <td>{{ $stock->medicine->id }}</td>
                <td>{{ $stock->medicine->ten_biet_duoc ?? '-' }}</td>
                <td>{{ $stock->so_lo }}</td>
                <td>{{ \Carbon\Carbon::parse($stock->han_dung)->format('d/m/Y') }}</td>
                <td>{{ $stock->so_luong }}</td>
                <td>{{ number_format($stock->gia_von, 0) }}</td>
 
                <td>{{ $statusOptions[$stock->status] ?? $stock->status }}</td>
                <td>{{ $stock->location }}</td>
                <td>{{ $stock->notes }}</td>
                <td>
                    <button class="btn btn-sm btn-primary" wire:click="openEditForm({{ $stock->id }})">Sửa</button>
                    <button class="btn btn-sm btn-danger" wire:click="deleteStock({{ $stock->id }})">Xóa</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center">Không có lô thuốc nào.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-2 d-flex justify-content-end">
        {{ $stocks->links('pagination::bootstrap-4') }}
    </div>

    @if(count($selectedStocks))
        <button class="btn btn-danger mt-2" wire:click="deleteSelected">
            Xóa các lô đã chọn ({{ count($selectedStocks) }})
        </button>
    @endif

    {{-- Modal Form --}}
    @include('Medicine::livewire.medicine-stock-form')
</div>

@push('js')
<script>
document.addEventListener('livewire:init', () => {
    // Show modal
    document.addEventListener('show-modal-medicine', () => {
        $('#modalMedicine').modal({ backdrop: 'static', keyboard: false }).modal('show');
    });

    // Close modal
    document.addEventListener('close-modal-medicine', () => {
        $('#modalMedicine').modal('hide');
    });

    // Reset form khi đóng modal bằng nút [x]
    $('[data-dismiss="modal"]').on('click', function() {
                $(this).closest('.modal').modal('hide');          
                Livewire.dispatch('reset-form');    
            });
});
</script>
@endpush
