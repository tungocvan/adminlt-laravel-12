<div>
    <div class="card shadow-sm sticky-top" style="top: 20px;">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-receipt mr-2"></i>Đơn hàng của bạn
            </h5>
        </div>
        <div class="card-body">
            {{-- Items List --}}
            <div class="order-items mb-3" style="max-height: 300px; overflow-y: auto;">
                @foreach($this->items as $item)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        {{-- Product Image --}}
                        <div class="position-relative mr-3">
                            @if($item->product && $item->product->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}" 
                                     alt="{{ $item->product->title }}"
                                     class="rounded"
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded"
                                     style="width: 60px; height: 60px;">
                                    <span class="text-muted">📦</span>
                                </div>
                            @endif
                            <span class="badge badge-secondary position-absolute" 
                                  style="top: -5px; right: -5px;">
                                {{ $item->quantity }}
                            </span>
                        </div>

                        {{-- Product Info --}}
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-truncate" style="max-width: 180px;">
                                {{ $item->product->title ?? 'Sản phẩm' }}
                            </h6>
                            <small class="text-muted">
                                {{ $this->formatPrice($item->price) }} × {{ $item->quantity }}
                            </small>
                        </div>

                        {{-- Item Total --}}
                        <div class="text-right">
                            <span class="font-weight-bold">
                                {{ $this->formatPrice($item->total) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Edit Cart Link --}}
            <div class="text-center mb-3">
                <a href="{{ route('website.cart.index') }}" class="text-primary">
                    <i class="fas fa-edit mr-1"></i>Chỉnh sửa giỏ hàng
                </a>
            </div>

            <hr>

            {{-- Subtotal --}}
            <div class="d-flex justify-content-between mb-2">
                <span>Tạm tính:</span>
                <span>{{ $this->formatPrice($this->subtotal) }}</span>
            </div>

            {{-- Shipping --}}
            <div class="d-flex justify-content-between mb-2">
                <span>Phí vận chuyển:</span>
                @if($this->shippingFee > 0)
                    <span>{{ $this->formatPrice($this->shippingFee) }}</span>
                @else
                    <span class="text-success">Miễn phí</span>
                @endif
            </div>

            {{-- Free Shipping Notice --}}
            @if($this->amountForFreeShipping > 0)
                <div class="alert alert-warning py-2 mb-3">
                    <small>
                        <i class="fas fa-info-circle mr-1"></i>
                        Mua thêm <strong>{{ $this->formatPrice($this->amountForFreeShipping) }}</strong> để được miễn phí vận chuyển
                    </small>
                </div>
            @endif

            <hr>

            {{-- Total --}}
            <div class="d-flex justify-content-between align-items-center">
                <span class="h5 mb-0">Tổng cộng:</span>
                <span class="h4 mb-0 text-danger font-weight-bold">
                    {{ $this->formatPrice($this->total) }}
                </span>
            </div>

            {{-- VAT Notice --}}
            <p class="text-muted mb-0 mt-2">
                <small><i class="fas fa-info-circle mr-1"></i>Đã bao gồm VAT (nếu có)</small>
            </p>
        </div>

        {{-- Security Badge --}}
        <div class="card-footer bg-light text-center">
            <small class="text-muted">
                <i class="fas fa-lock text-success mr-1"></i>
                Giao dịch được bảo mật an toàn
            </small>
        </div>
    </div>
</div>