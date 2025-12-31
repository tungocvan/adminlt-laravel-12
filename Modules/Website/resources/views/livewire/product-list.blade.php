<div>
    <div class="container py-5">
        <!-- Header Section -->
        <div class="text-center mb-5">
            <h2 class="display-4 font-weight-bold">
                <span class="text-success">🌿</span> Sản phẩm mới về
            </h2>
            <p class="lead text-muted">Khám phá bộ sưu tập sản phẩm chất lượng cao</p>
        </div>

        <!-- Product Grid -->
        <div class="row">
            @forelse($products as $product)
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 shadow-sm hover-shadow transition">
                        <!-- Discount Badge -->
                        @if($product->discount_percent)
                            <div class="position-absolute" style="top: 10px; right: 10px; z-index: 10;">
                                <span class="badge badge-danger badge-pill px-3 py-2">
                                    -{{ $product->discount_percent }}%
                                </span>
                            </div>
                        @endif

                      <!-- Product Image -->
                    <a href="{{ route('website.products.show', $product->slug) }}">
                        <img 
                            src="{{ $product->image_url }}" 
                            class="card-img-top" 
                            alt="{{ $product->title }}"
                            style="height: 250px; object-fit: cover;"                           
                        >
                    </a>


                        <div class="card-body d-flex flex-column">
                            <!-- Product Title -->
                            <h5 class="card-title font-weight-bold">
                                <a href="{{ route('website.products.show', $product->slug) }}" class="text-dark text-decoration-none">
                                    {{ $product->title }}
                                </a>
                            </h5>

                            <!-- Short Description -->
@if($product->short_description)
<p class="card-text text-muted small flex-grow-1">
    {!! Str::limit(strip_tags($product->short_description, '<b><strong><i><em><u>'), 100) !!}
</p>
@endif


                            <!-- Price Section -->
                            <div class="mb-3">
                                <span class="h4 text-danger font-weight-bold">
                                    {{ $this->formatPrice($product->final_price) }}
                                </span>
                                
                                @if($product->sale_price && $product->regular_price)
                                    <span class="text-muted ml-2">
                                        <del>{{ $this->formatPrice($product->regular_price) }}</del>
                                    </span>
                                @endif
                            </div>

                            <!-- Tags -->
                            @if($product->tags && count($product->tags) > 0)
                                <div class="mb-3">
                                    @foreach($product->tags as $tag)
                                        <span class="badge badge-secondary badge-pill mr-1 mb-1">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Action Button -->
                            <a 
                                href="{{ route('website.products.show', $product->slug) }}" 
                                class="btn btn-success btn-block mt-auto"
                            >
                                <i class="fas fa-eye"></i> Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> Chưa có sản phẩm nào.
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
    </div>

    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }
        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
        }
    </style>
</div>
