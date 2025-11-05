<div>
    {{-- FORM TẠO/SỬA ĐƠN --}}
    @if ($formVisible)
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                <h3 class="card-title mb-0">{{ $orderId ? 'Sửa đơn hàng' : 'Tạo đơn hàng' }}</h3>
                <button class="btn btn-outline-light btn-sm" wire:click="hideForm">Hủy</button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label>Email</label>
                            <input type="text" class="form-control" wire:model="email"
                                placeholder="Nhập email khách hàng">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-2">
                            <label>Status</label>
                            <select class="form-control" wire:model="status">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="finished">Finished</option>
                            </select>
                        </div>

                        <div class="form-group mb-2">
                            <label>Ghi chú khách hàng</label>
                            <textarea class="form-control" wire:model="order_note"></textarea>
                        </div>

                        <div class="form-group mb-2">
                            <label>Ghi chú admin</label>
                            <textarea class="form-control" wire:model="admin_note"></textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="input-group mb-2" style="max-width: 400px;">
                            <input type="text" class="form-control"
                                placeholder="Tìm kiếm hoạt chất hoặc tên thuốc..."
                                wire:model.live.debounce.500ms="productSearch" x-data
                                @keydown.escape="$wire.clearProductSearch()" />

                            @if ($productSearch)
                                <button type="button" class="btn btn-outline-danger" wire:click="clearProductSearch"
                                    title="Xóa tìm kiếm">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>

                        @if (session()->has('message'))
                            <div class="alert alert-success">{{ session('message') }}</div>
                        @endif

                        <div class="table-responsive" style="max-height: 350px; overflow-y:auto;">
                            <table class="table-bordered table-sm table">
                                <thead class="thead-light sticky-top">
                                    <tr>
                                        <th><input type="checkbox" wire:model.live="selectAllProducts"></th>
                                        <th>Tên thuốc</th>
                                        <th>Đơn vị</th>
                                        <th>Đơn giá</th>
                                        <th>Số lượng</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($filteredProducts as $product)
                                        <tr wire:key="product-{{ $product->id }}">
                                            <td><input type="checkbox" wire:change="toggleProduct({{ $product->id }})"
                                                    @checked(isset($selectedProducts[$product->id]))>
                                            </td>
                                            <td>{{ $product->ten_hoat_chat }}</td>
                                            <td>{{ $product->don_vi_tinh }}</td>
                                            <td>{{ number_format($product->don_gia ?? 0) }}</td>
                                            <td>
                                                @if (isset($selectedProducts[$product->id]))
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <button type="button" class="btn btn-outline-secondary"
                                                                wire:click="decrementQuantity({{ $product->id }})">-</button>
                                                        </div>
                                                        <input type="text" class="form-control text-center"
                                                            wire:model="selectedProducts.{{ $product->id }}.quantity"
                                                            readonly>
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-outline-secondary"
                                                                wire:click="incrementQuantity({{ $product->id }})">+</button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($selectedProducts[$product->id]))
                                                    {{ number_format(($selectedProducts[$product->id]['don_gia'] ?? 0) * ($selectedProducts[$product->id]['quantity'] ?? 1)) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Không có sản phẩm</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-2 text-right">
                            <strong>Tổng tiền: {{ number_format($total) }}</strong>
                        </div>

                        <div class="mt-2 text-right">
                            <button class="btn btn-success" wire:click="saveOrder">Lưu đơn hàng</button>
                            <button class="btn btn-secondary" wire:click="hideForm">Hủy</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- DANH SÁCH ĐƠN HÀNG --}}
    @if (!$formVisible)
        <div class="card mb-3 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-info text-white">
                <h3 class="card-title mb-0">Danh sách đơn hàng</h3>
                <button class="btn btn-primary btn-sm" wire:click="showForm" wire:loading.attr="disabled">Tạo đơn
                    hàng</button>

            </div>
            <div class="card-body table-responsive">
                <table class="table-bordered table-hover table-sm table">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Ngày tạo</th>
                            <th>Link Download</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->email }}</td>
                                <td>{{ number_format($order->total, 0) }}</td>
                                <td>{{ ucfirst($order->status) }}</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if ($order->link_download)
                                        <a href="{{ asset("storage/{$order->link_download}") }}" target="_blank">Tải
                                            xuống</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info"
                                        wire:click="showForm({{ $order->id }})">Sửa</button>
                                    <button class="btn btn-sm btn-danger" x-data
                                        @click="
                                            if (confirm('Bạn có chắc muốn xóa đơn #{{ $order->id }}?')) {
                                                $wire.deleteOrder({{ $order->id }});
                                            }
                                        ">
                                        Xóa
                                    </button>

                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Không có đơn hàng</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $orders->links() }}
            </div>
        </div>
    @endif
</div>
