 {{-- ========================= FORM TẠO/SỬA ĐƠN ========================= --}}
 @if ($formVisible)
 <div class="card shadow-sm">
     <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
         <h3 class="card-title mb-0">{{ $orderId ? 'Sửa đơn hàng' : 'Tạo đơn hàng' }}</h3>
         <button class="btn btn-outline-light btn-sm" wire:click="hideForm">Hủy</button>
     </div>
     <div class="card-body">
         <div class="row">
             {{-- LEFT: Thông tin khách --}}
             <div class="col-md-3">
                 <div class="mb-2">
                     <label>Email khách</label>
                     <input type="text" class="form-control" wire:model="email" placeholder="Nhập email khách">
                     @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                 </div>

                 <div class="mb-2">
                     <label>Khách giới thiệu</label>
                     <select class="form-control" wire:model.live="customer_id">
                         <option value="">-- Chọn khách giới thiệu --</option>
                         @foreach ($customers as $c)
                             <option value="{{ $c->id }}">{{ $c->username }}</option>
                         @endforeach
                     </select>
                 </div>

                 <div class="mb-2">
                     <label>Status</label>
                     <select class="form-control" wire:model="status">
                         <option value="pending">Pending</option>
                         <option value="confirmed">Confirmed</option>
                         <option value="cancelled">Cancelled</option>
                         <option value="finished">Finished</option>
                     </select>
                 </div>

                 <div class="mb-2">
                     <label>Ghi chú khách</label>
                     <textarea class="form-control" wire:model="order_note" rows="3"></textarea>
                 </div>

                 <div class="mb-2">
                     <label>Ghi chú admin</label>
                     <textarea class="form-control" wire:model="admin_note" rows="3"></textarea>
                 </div>
             </div>

             {{-- RIGHT: Sản phẩm --}}
             <div class="col-md-9">
                 {{-- Tìm kiếm --}}
                 <div class="input-group mb-2" style="max-width: 100%;">
                     <input type="text" class="form-control" placeholder="Tìm thuốc..."
                         wire:model.live.debounce.500ms="productSearch" x-data
                         @keydown.escape="$wire.clearProductSearch()" />
                     @if ($productSearch)
                         <button class="btn btn-outline-danger" wire:click="clearProductSearch"
                             title="Xóa tìm kiếm"><i class="fas fa-times"></i></button>
                     @endif
                 </div>

                 {{-- Thông báo --}}
                 @if (session()->has('message'))
                     <div class="alert alert-success">{{ session('message') }}</div>
                 @endif

                 {{-- Danh sách sản phẩm --}}
                 <div class="row g-2" style="max-height: 400px; overflow-y:auto;">
                     @forelse($filteredProducts as $product)
                         <div class="col-12">
                             <div class="card p-2">
                                 <div class="d-flex justify-content-between align-items-start">
                                     <div>
                                         <input type="checkbox" wire:change="toggleProduct({{ $product->id }})"
                                             @checked(isset($selectedProducts[$product->id]))>
                                         <strong class="ms-2">{{ $product->ten_hoat_chat }}</strong>

                                         {{-- Hiển thị số lô / hạn dùng --}}
                                         @if(isset($selectedProducts[$product->id]))
                                             <div class="small text-muted mt-1">
                                                 @if($selectedProducts[$product->id]['so_lo'])
                                                     <div>Số lô: {{ $selectedProducts[$product->id]['so_lo'] }}</div>
                                                 @endif
                                                 @if($selectedProducts[$product->id]['han_dung'])
                                                     <div>Hạn dùng: {{ $selectedProducts[$product->id]['han_dung'] }}</div>
                                                 @endif
                                             </div>
                                         @endif
                                     </div>
                                     <div><small>{{ $product->don_vi_tinh }}</small></div>
                                 </div>

                                 <div class="mt-1">
                                     <div><strong>Đơn giá:</strong> {{ number_format($product->don_gia ?? 0) }}</div>

                                     @if(isset($selectedProducts[$product->id]))
                                         {{-- Số lượng --}}
                                         <div class="input-group input-group-sm mt-1" style="max-width:140px;">
                                             <button class="btn btn-outline-secondary" type="button"
                                                 wire:click="decrementQuantity({{ $product->id }})">-</button>
                                             <input type="number" class="form-control text-center"
                                                 wire:model.live="selectedProducts.{{ $product->id }}.quantity" min="1">
                                             <button class="btn btn-outline-secondary" type="button"
                                                 wire:click="incrementQuantity({{ $product->id }})">+</button>
                                         </div>

                                         {{-- Chọn lô / hạn dùng --}}
                                         <button type="button" class="btn btn-sm btn-info mt-1"
                                             wire:click="toggleLotInput({{ $product->id }})">
                                             Chọn lô / Hạn dùng
                                         </button>

                                         @if($selectedProducts[$product->id]['show_lot_input'] ?? false)
                                         <div class="mt-1">
                                             <select class="form-control mb-1"
                                                 wire:model.live="selectedProducts.{{ $product->id }}.so_lo">
                                                 <option value="">-- Chọn số lô --</option>
                                                 @foreach($selectedProducts[$product->id]['available_stocks'] as $stock)
                                                     <option value="{{ $stock->so_lo }}">
                                                         Lô: {{ $stock->so_lo }} | Hạn: {{ $stock->han_dung }} | Tồn: {{ $stock->so_luong }}
                                                     </option>
                                                 @endforeach
                                             </select>
                                     
                                             <input type="text" class="form-control" readonly
                                                 wire:model="selectedProducts.{{ $product->id }}.han_dung"
                                                 placeholder="Hạn dùng"
                                                 value="{{ $selectedProducts[$product->id]['han_dung'] }}">
                                         </div>
                                     @endif
                                     

                                         {{-- Thành tiền --}}
                                         <div class="mt-1 text-end">
                                             <strong>Thành tiền:</strong>
                                             {{ number_format(($selectedProducts[$product->id]['don_gia'] ?? 0) * ($selectedProducts[$product->id]['quantity'] ?? 1)) }}
                                         </div>
                                     @endif
                                 </div>
                             </div>
                         </div>
                     @empty
                         <div class="col-12 text-center">Không có sản phẩm</div>
                     @endforelse
                 </div>

                 {{-- Tổng tiền & nút --}}
                 <div class="mt-3 text-end">
                     <h5>Tổng tiền: {{ number_format($total) }}</h5>
                     <button class="btn btn-success" wire:click="saveOrder">Lưu đơn hàng</button>
                     <button class="btn btn-secondary" wire:click="hideForm">Hủy</button>
                 </div>
             </div>
         </div>
     </div>
 </div>
@endif