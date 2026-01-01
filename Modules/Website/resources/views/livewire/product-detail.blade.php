<div>
    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('website.products.index') }}">Sản phẩm</a></li>
                <li class="breadcrumb-item active">{{ $product->title }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Product Images -->
            <div class="col-md-6 mb-4">
                <!-- Main Image -->
                <div class="card mb-3 shadow-sm"> 
                    <img src="{{ $selectedImage ?? $product->image_url }}" class="card-img-top"
                        alt="{{ $product->title }}" style="height: 500px; object-fit: cover;"
                       >
                </div>

                <!-- Gallery Thumbnails -->
                @php
                    $allImages = [];

                    // Thêm ảnh chính vào đầu
                    if ($product->image_url) {
                        $allImages[] = $product->image_url;
                    }

                    // Thêm gallery
                    if ($product->gallery_urls && count($product->gallery_urls) > 0) {
                        $allImages = array_merge($allImages, $product->gallery_urls);
                    }
                @endphp

                @if (count($allImages) > 1)
                    <div class="d-flex flex-wrap">
                        @foreach ($allImages as $image)
                            <div class="mb-2 mr-2">
                                <img src="{{ $image }}"
                                    class="img-thumbnail {{ $selectedImage === $image ? 'border-success border-3' : '' }} cursor-pointer"
                                    style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                    wire:click="selectImage('{{ $image }}')"
                                  >
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>

            <!-- Product Info -->
            <div class="col-md-6">
                <!-- Discount Badge -->
                @if ($product->discount_percent)
                    <span class="badge badge-danger badge-pill mb-3 px-3 py-2">
                        🔥 Giảm {{ $product->discount_percent }}%
                    </span>
                @endif

                <!-- Product Title -->
                <h1 class="h2 font-weight-bold mb-3">{{ $product->title }}</h1>

                <!-- Price -->
                <div class="mb-4">
                    <div class="d-flex align-items-center">
                        <span class="h2 text-danger font-weight-bold mb-0">
                            {{ $this->formatPrice($product->final_price) }}
                        </span>

                        @if ($product->sale_price && $product->regular_price)
                            <span class="h5 text-muted mb-0 ml-3">
                                <del>{{ $this->formatPrice($product->regular_price) }}</del>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Short Description -->
                @if ($product->short_description)
                    <div class="alert alert-light mb-4 border">
                        <strong>📌 Mô tả ngắn:</strong>
                        <div class="mt-2">{!! $product->short_description !!}</div>
                    </div>
                @endif

                <!-- Tags -->
                @if ($product->tags && count($product->tags) > 0)
                    <div class="mb-4">
                        <strong class="d-block mb-2">🏷️ Thẻ:</strong>
                        @foreach ($product->tags as $tag)
                            <span class="badge badge-success badge-pill mb-2 mr-2 px-3 py-2">
                                {{ $tag }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="mb-4">
                    <button class="btn btn-success btn-lg btn-block mb-2">
                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng
                    </button>
                    <button class="btn btn-outline-success btn-lg btn-block">
                        <i class="fas fa-phone"></i> Liên hệ tư vấn
                    </button>
                </div>

                <!-- Additional Info -->
                <div class="card bg-light">
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="fas fa-check-circle text-success"></i> Giao hàng toàn quốc</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success"></i> Đổi trả trong 7 ngày
                            </li>
                            <li class="mb-0"><i class="fas fa-check-circle text-success"></i> Thanh toán khi nhận hàng
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        @if ($product->description)
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h3 class="mb-0"><i class="fas fa-info-circle"></i> Mô tả chi tiết</h3>
                        </div>
                        <div class="card-body">
                            <div class="content">
                                {!! $product->description !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
