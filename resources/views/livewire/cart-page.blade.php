<div class="container py-5">
    <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Giỏ hàng của bạn</h2>

    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if($cartItems->count() > 0)
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">Giá</th>
                                    <th class="text-center" style="width: 150px;">Số lượng</th>
                                    <th class="text-center">Tổng</th>
                                    <th class="text-center">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cartItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img 
                                                    src="{{ $item->product->image_url }}" 
                                                    alt="{{ $item->product->title }}"
                                                    style="width: 60px; height: 60px; object-fit: cover;"
                                                    class="rounded mr-3"
                                                >
                                                <div>
                                                    <a href="{{ route('website.products.show', $item->product->slug) }}" 
                                                       class="font-weight-bold text-dark">
                                                        {{ $item->product->title }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="text-danger font-weight-bold">
                                                {{ $this->formatPrice($item->price) }}
                                            </span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <button 
                                                        class="btn btn-outline-secondary" 
                                                        wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                                    >
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                                <input 
                                                    type="number" 
                                                    class="form-control text-center" 
                                                    value="{{ $item->quantity }}"
                                                    min="1"
                                                    wire:change="updateQuantity({{ $item->id }}, $event.target.value)"
                                                >
                                                <div class="input-group-append">
                                                    <button 
                                                        class="btn btn-outline-secondary"
                                                        wire:click="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                                    >
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <strong class="text-success">
                                                {{ $this->formatPrice($item->subtotal) }}
                                            </strong>
                                        </td>
                                        <td class="text-center align-middle">
                                            <button 
                                                class="btn btn-sm btn-danger"
                                                wire:click="removeItem({{ $item->id }})"
                                                wire:confirm="Bạn có chắc muốn xóa sản phẩm này?"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="text-right">
                            <button 
                                wire:click="clearCart" 
                                class="btn btn-outline-danger"
                                wire:confirm="Bạn có chắc muốn xóa toàn bộ giỏ hàng?"
                            >
                                <i class="fas fa-trash-alt"></i> Xóa toàn bộ
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-calculator"></i> Tổng đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Tạm tính:</span>
                            <strong>{{ $this->formatPrice($cartTotal) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Phí vận chuyển:</span>
                            <span class="text-muted">Miễn phí</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Tổng cộng:</strong>
                            <strong class="h4 text-danger">{{ $this->formatPrice($cartTotal) }}</strong>
                        </div>

                        <button class="btn btn-success btn-block btn-lg mb-2">
                            <i class="fas fa-credit-card"></i> Thanh toán
                        </button>
                        <a href="{{ route('website.products.index') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-arrow-left"></i> Tiếp tục mua hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
            <h4>Giỏ hàng trống</h4>
            <p class="text-muted mb-4">Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm.</p>
            <a href="{{ route('website.products.index') }}" class="btn btn-success btn-lg">
                <i class="fas fa-shopping-bag"></i> Khám phá sản phẩm
            </a>
        </div>
    @endif
</div>
