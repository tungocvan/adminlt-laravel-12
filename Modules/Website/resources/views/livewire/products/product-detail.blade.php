<div>
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item">
                <a href="{{ route('website.index') }}">Trang chủ</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('website.products.index') }}">Sản phẩm</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ Str::limit($this->product->title, 30) }}
            </li>
        </ol>
    </nav>

    {{-- Product Detail --}}
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <div class="row">
                {{-- Product Gallery --}}
                <div class="col-lg-5 mb-4 mb-lg-0">
                    {{-- Main Image --}}
                    <div class="mb-3 text-center position-relative">
                        @php
                            $currentImage = $this->galleryImages[$selectedImageIndex] ?? null;
                        @endphp
                        
                        @if($currentImage)
                            <img src="{{ asset('storage/' . $currentImage) }}" 
                                 class="img-fluid rounded shadow-sm" 
                                 alt="{{ $this->product->title }}"
                                 style="max-height: 400px; object-fit: contain;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                 style="height: 400px;">
                                <span class="text-muted" style="font-size: 100px;">📦</span>
                            </div>
                        @endif

                        {{-- Discount Badge --}}
                        @if($this->product->discount_percent > 0)
                            <span class="badge badge-danger position-absolute" 
                                  style="top: 15px; right: 15px; font-size: 1rem; padding: 8px 12px;">
                                -{{ $this->product->discount_percent }}%
                            </span>
                        @endif
                    </div>

                    {{-- Thumbnail Gallery --}}
                    @if(count($this->galleryImages) > 1)
                        <div class="d-flex justify-content-center flex-wrap">
                            @foreach($this->galleryImages as $index => $image)
                                <div class="p-1">
                                    <img src="{{ asset('storage/' . $image) }}" 
                                         class="img-thumbnail cursor-pointer {{ $selectedImageIndex === $index ? 'border-primary' : '' }}"
                                         style="width: 70px; height: 70px; object-fit: cover; cursor: pointer;"
                                         wire:click="selectImage({{ $index }})"
                                         alt="Gallery {{ $index + 1 }}">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Product Info --}}
                <div class="col-lg-7">
                    <h1 class="h3 mb-3">{{ $this->product->title }}</h1>

                    {{-- Short Description --}}
                    @if($this->product->short_description)
                        <p class="text-muted mb-4">{{ $this->product->short_description }}</p>
                    @endif

                    {{-- Price --}}
                    <div class="mb-4 p-3 bg-light rounded">
                        @if($this->product->sale_price && $this->product->sale_price < $this->product->regular_price)
                            <div class="d-flex align-items-center flex-wrap">
                                <span class="h3 text-danger mb-0 mr-3">
                                    {{ number_format($this->product->sale_price, 0, ',', '.') }}đ
                                </span>
                                <span class="text-muted text-decoration-line-through mr-3">
                                    {{ number_format($this->product->regular_price, 0, ',', '.') }}đ
                                </span>
                                <span class="badge badge-success">
                                    Tiết kiệm {{ number_format($this->product->regular_price - $this->product->sale_price, 0, ',', '.') }}đ
                                </span>
                            </div>
                        @else
                            <span class="h3 text-primary mb-0">
                                {{ number_format($this->product->regular_price, 0, ',', '.') }}đ
                            </span>
                        @endif
                    </div>

                    {{-- Tags --}}
                    @if($this->product->tags && count($this->product->tags) > 0)
                        <div class="mb-4">
                            @foreach($this->product->tags as $tag)
                                <span class="badge badge-secondary mr-1">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Quantity & Add to Cart --}}
                    <div class="mb-4">
                        <label class="font-weight-bold mb-2">Số lượng:</label>
                        <div class="d-flex align-items-center">
                            <div class="input-group" style="width: 150px;">
                                <div class="input-group-prepend">
                                    <button class="btn btn-outline-secondary" 
                                            type="button"
                                            wire:click="decrementQuantity"
                                            {{ $quantity <= 1 ? 'disabled' : '' }}>
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                                <input type="number" 
                                       class="form-control text-center" 
                                       wire:model.live="quantity"
                                       min="1" 
                                       max="99">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" 
                                            type="button"
                                            wire:click="incrementQuantity"
                                            {{ $quantity >= 99 ? 'disabled' : '' }}>
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @livewire('website.cart.add-to-cart', [
                            'productId' => $this->product->id,
                            'quantity' => $quantity,
                            'size' => 'lg'
                        ], key('add-to-cart-detail-' . $this->product->id))
                        
                        <a href="{{ route('website.cart.index') }}" class="btn btn-outline-primary btn-lg ml-2">
                            <i class="fas fa-shopping-cart mr-2"></i>Xem giỏ hàng
                        </a>
                    </div>

                    {{-- Features --}}
                    <div class="border-top pt-4">
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="text-success mr-2">✓</span>
                                    <span>Sản phẩm chính hãng 100%</span>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="text-success mr-2">✓</span>
                                    <span>Giao hàng miễn phí</span>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="text-success mr-2">✓</span>
                                    <span>Bảo hành 12 tháng</span>
                                </div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="text-success mr-2">✓</span>
                                    <span>Đổi trả trong 30 ngày</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Product Description --}}
    @if($this->product->description)
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-white">
                <h5 class="mb-0">Mô tả sản phẩm</h5>
            </div>
            <div class="card-body">
                <div class="product-description">
                    {!! $this->product->description !!}
                </div>
            </div>
        </div>
    @endif

    {{-- Related Products --}}
    @if($this->relatedProducts->count() > 0)
        <div class="mb-5">
            <h4 class="mb-4">Sản phẩm liên quan</h4>
            <div class="row">
                @foreach($this->relatedProducts as $related)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100 shadow-sm">
                            <a href="{{ route('website.products.show', $related->slug) }}">
                                @if($related->image)
                                    <img src="{{ asset('storage/' . $related->image) }}" 
                                         class="card-img-top" 
                                         alt="{{ $related->title }}"
                                         style="height: 150px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 150px;">
                                        <span class="text-muted h3">📦</span>
                                    </div>
                                @endif
                            </a>
                            <div class="card-body">
                                <h6 class="card-title">
                                    <a href="{{ route('website.products.show', $related->slug) }}" 
                                       class="text-dark">
                                        {{ Str::limit($related->title, 40) }}
                                    </a>
                                </h6>
                                <p class="text-danger font-weight-bold mb-0">
                                    {{ number_format($related->final_price, 0, ',', '.') }}đ
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .text-decoration-line-through {
        text-decoration: line-through;
    }
    .product-description img {
        max-width: 100%;
        height: auto;
    }
    .product-description ul {
        padding-left: 20px;
    }
    .product-description li {
        margin-bottom: 8px;
    }
    .gap-2 {
        gap: 0.5rem;
    }
</style>
@endpush