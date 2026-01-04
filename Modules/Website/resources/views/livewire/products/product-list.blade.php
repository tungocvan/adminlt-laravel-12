<div>
    {{-- Filter & Sort Bar --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row align-items-center">
                {{-- Search --}}
                <div class="col-lg-4 mb-lg-0 mb-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-left-0"
                            placeholder="Tìm kiếm sản phẩm...">
                        @if ($search)
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button"
                                    wire:click="$set('search', '')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Price Filter --}}
                <div class="col-lg-3 col-md-6 mb-lg-0 mb-3">
                    <select wire:model.live="priceRange" class="form-control">
                        @foreach ($this->priceOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Sort --}}
                <div class="col-lg-3 col-md-6 mb-lg-0 mb-3">
                    <select wire:model.live="sortBy" class="form-control">
                        @foreach ($this->sortOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Clear Filters --}}
                <div class="col-lg-2">
                    @if ($search || $priceRange || $sortBy !== 'latest')
                        <button wire:click="clearFilters" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-redo mr-1"></i> Xóa lọc
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Results Info --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">
            Hiển thị <strong>{{ $this->products->count() }}</strong> /
            <strong>{{ $this->products->total() }}</strong> sản phẩm
        </p>

        {{-- Loading Indicator --}}
        <div wire:loading class="text-primary">
            <span class="spinner-border spinner-border-sm mr-1"></span>
            Đang tải...
        </div>
    </div>

    {{-- Product Grid --}}
    <div wire:loading.class="opacity-50">
        @if ($this->products->count() > 0)
            <div class="row">
                @foreach ($this->products as $product)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100 product-card shadow-sm">
                            {{-- Product Image --}}
                            <div class="position-relative overflow-hidden">
                                <a href="{{ route('website.products.show', $product->slug) }}">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top"
                                            alt="{{ $product->title }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center"
                                            style="height: 200px;">
                                            <span class="text-muted h1">📦</span>
                                        </div>
                                    @endif
                                </a>

                                {{-- Discount Badge --}}
                                @if ($product->discount_percent > 0)
                                    <span class="badge badge-danger position-absolute" style="top: 10px; right: 10px;">
                                        -{{ $product->discount_percent }}%
                                    </span>
                                @endif
                            </div>

                            {{-- Product Info --}}
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-2">
                                    <a href="{{ route('website.products.show', $product->slug) }}"
                                        class="text-dark text-decoration-none product-title"
                                        title="{{ $product->title }}">
                                        {{ Str::limit($product->title, 50) }}
                                    </a>
                                </h5>

                                {{-- Price --}}
                                <div class="mt-auto">
                                    @if ($product->sale_price && $product->sale_price < $product->regular_price)
                                        <div class="d-flex align-items-center">
                                            <span class="h5 text-danger mb-0 mr-2">
                                                {{ number_format($product->sale_price, 0, ',', '.') }}đ
                                            </span>
                                            <small class="text-muted text-decoration-line-through">
                                                {{ number_format($product->regular_price, 0, ',', '.') }}đ
                                            </small>
                                        </div>
                                    @else
                                        <span class="h5 text-primary mb-0">
                                            {{ number_format($product->regular_price, 0, ',', '.') }}đ
                                        </span>
                                    @endif
                                </div>

                                {{-- Add to Cart Button --}}
                                <div class="mt-3">
                                    @livewire(
                                        'website.cart.add-to-cart',
                                        [
                                            'productId' => $product->id,
                                            'size' => 'sm',
                                            'showText' => true,
                                        ],
                                        key('add-to-cart-' . $product->id)
                                    )
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $this->products->links() }}
            </div>
        @else
            {{-- No Products Found --}}
            <div class="py-5 text-center">
                <div class="mb-4">
                    <span style="font-size: 80px;">🔍</span>
                </div>
                <h4 class="text-muted">Không tìm thấy sản phẩm</h4>
                <p class="text-muted">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
                <button wire:click="clearFilters" class="btn btn-primary">
                    Xóa bộ lọc
                </button>
            </div>
        @endif
    </div>

</div>
@push('styles')
    <style>
        .product-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .product-title:hover {
            color: #007bff !important;
        }

        .opacity-50 {
            opacity: 0.5;
        }

        .text-decoration-line-through {
            text-decoration: line-through;
        }
    </style>
@endpush
