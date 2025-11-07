<div>
   {{-- ========================= DANH SÁCH ĐƠN HÀNG ========================= --}}
    @if(!$formVisible)
        <div class="card mb-3 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-info text-white">
                <h3 class="card-title mb-0">Danh sách đơn hàng</h3>
                <button class="btn btn-primary btn-sm" wire:click="showForm" wire:loading.attr="disabled">Tạo đơn hàng</button>
                @if($selectedOrder)                 

                    <button
                            onclick="if(confirm('Bạn có chắc muốn xóa các đơn hàng đã chọn?')) { @this.deleteSelectedOrders() }"
                            class="btn btn-danger btn-sm">
                            <i class="fa fa-trash mr-1"></i>  Xóa {{ count($selectedOrder) }} đơn hàng đã chọn
                    </button>
                @endif
            </div>
            <div class="card-body table-responsive">
                <table class="table-bordered table-hover table-sm table">
                    <thead class="thead-light text-center">
                        <tr>
                            <th width="40"><input type="checkbox" wire:model.live="selectAll"
                                wire:click="toggleSelectAll"></th>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Khách hàng</th>
                            <th>Ngày tạo</th>
                            <th>Link Download</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr class="text-center">
                                <td><input type="checkbox" wire:model.live="selectedOrder" value="{{ $order->id }}">
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->email }}</td>
                                <td style="text-align:right">{{ number_format($order->total, 0) }}</td>
                                <td>{{ ucfirst($order->status) }}</td>
                                <td>{{ $order->customer_id ?? $order->id }}</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($order->link_download)
                                        <a href="{{ asset("storage/{$order->link_download}") }}" target="_blank">Tải xuống</a>
                                    @else - @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" wire:click="showForm({{ $order->id }})">Sửa</button>
                                    <button class="btn btn-sm btn-danger" x-data
                                        @click="if(confirm('Bạn có chắc muốn xóa đơn #{{ $order->id }}?')) { $wire.deleteOrder({{ $order->id }}); }">
                                        Xóa
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Không có đơn hàng</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $orders->links() }}
            </div>
        </div>
    @endif
    {{-- ========================= FORM TẠO/SỬA ĐƠN ========================= --}}
    @include('livewire.order.order-form')    
</div>
