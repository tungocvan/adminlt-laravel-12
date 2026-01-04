<div>
    @if($this->isEmpty)
        {{-- Empty Cart --}}
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <span style="font-size: 80px;">🛒</span>
                </div>
                <h4 class="text-muted">Giỏ hàng trống</h4>
                <p class="text-muted">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
                <a href="{{ route('website.products.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag mr-2"></i>Tiếp tục mua sắm
                </a>
            </div>
        </div>
    @else
        <div class="row">
            {{-- Cart Items --}}
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            Sản phẩm trong giỏ 
                            <span class="badge badge-primary ml-1">{{ $this->totalQuantity }}</span>
                        </h5>
                        <button wire:click="clearCart" 
                                wire:confirm="Bạn có chắc muốn xóa toàn bộ giỏ hàng?"
                                class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-trash mr-1"></i>Xóa tất cả
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 50%;">Sản phẩm</th>
                                        <th class="text-center" style="width: 15%;">Đơn giá</th>
                                        <th class="text-center" style="width: 20%;">Số lượng</th>
                                        <th class="text-right" style="width: 15%;">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($this->items as $item)
                                        <tr wire:key="cart-item-{{ $item->id }}">
                                            {{-- Product Info --}}
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    {{-- Product Image --}}
                                                    <div class="mr-3">
                                                        @if($item->product && $item->product->image)
                                                            <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                                 alt="{{ $item->product->title }}"
                                                                 class="img-thumbnail"
                                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-light d-flex align-items-center justify-content-center"
                                                                 style="width: 80px; height: 80px;">
                                                                <span class="text-muted h4 mb-0">📦</span>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    {{-- Product Details --}}
                                                    <div>
                                                        <h6 class="mb-1">
                                                            @if($item->product)
                                                                <a href="{{ route('website.products.show', $item->product->slug) }}" 
                                                                   class="text-dark">
                                                                    {{ $item->product->title }}
                                                                </a>
                                                            @else
                                                                <span class="text-muted">Sản phẩm không tồn tại</span>
                                                            @endif
                                                        </h6>
                                                        <button wire:click="removeItem({{ $item->id }})"
                                                                wire:loading.attr="disabled"
                                                                class="btn btn-link text-danger p-0 btn-sm">
                                                            <i class="fas fa-times mr-1"></i>Xóa
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Unit Price --}}
                                            <td class="text-center align-middle">
                                                {{ $this->formatPrice($item->price) }}
                                            </td>

                                            {{-- Quantity --}}
                                            <td class="text-center align-middle">
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <div class="input-group input-group-sm" style="width: 120px;">
                                                        <div class="input-group-prepend">
                                                            <button class="btn btn-outline-secondary" 
                                                                    type="button"
                                                                    wire:click="decrement({{ $item->id }})"
                                                                    wire:loading.attr="disabled"
                                                                    {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                        </div>
                                                        <input type="number" 
                                                               class="form-control text-center"
                                                               value="{{ $item->quantity }}"
                                                               min="1" 
                                                               max="99"
                                                               wire:change="updateQuantity({{ $item->id }}, $event.target.value)">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-secondary" 
                                                                    type="button"
                                                                    wire:click="increment({{ $item->id }})"
                                                                    wire:loading.attr="disabled"
                                                                    {{ $item->quantity >= 99 ? 'disabled' : '' }}>
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Line Total --}}
                                            <td class="text-right align-middle">
                                                <span class="font-weight-bold text-primary">
                                                    {{ $this->formatPrice($item->total) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Continue Shopping --}}
                <div class="mt-3">
                    <a href="{{ route('website.products.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left mr-2"></i>Tiếp tục mua sắm
                    </a>
                </div>
            </div>

            {{-- Cart Summary --}}
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Tóm tắt đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        {{-- Subtotal --}}
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tạm tính ({{ $this->totalQuantity }} sản phẩm):</span>
                            <span class="font-weight-bold">{{ $this->formatPrice($this->subtotal) }}</span>
                        </div>

                        {{-- Shipping Notice --}}
                        <div class="alert alert-info py-2 mb-3">
                            <small>
                                <i class="fas fa-truck mr-1"></i>
                                @if($this->subtotal >= 500000)
                                    Bạn được <strong>miễn phí vận chuyển</strong>!
                                @else
                                    Mua thêm <strong>{{ $this->formatPrice(500000 - $this->subtotal) }}</strong> để được miễn phí vận chuyển
                                @endif
                            </small>
                        </div>

                        <hr>

                        {{-- Total --}}
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5 mb-0">Tổng cộng:</span>
                            <span class="h5 mb-0 text-danger">{{ $this->formatPrice($this->subtotal) }}</span>
                        </div>

                        {{-- Checkout Button --}}
                        <a href="{{ route('website.checkout.index') }}" class="btn btn-danger btn-lg btn-block">
                            <i class="fas fa-lock mr-2"></i>Tiến hành thanh toán
                        </a>

                        {{-- Payment Methods --}}
                        <div class="text-center mt-3">
                            <small class="text-muted">Chấp nhận thanh toán</small>
                            <div class="mt-2">
                                <span class="badge badge-light p-2 mr-1">💳 COD</span>
                                <span class="badge badge-light p-2 mr-1">🏦 Banking</span>
                                <span class="badge badge-light p-2">💰 Momo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>