<div class="container my-5">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="font-weight-bold text-success mb-0">
            🌿 Sản phẩm mới về
        </h4>

        <a href="#" class="text-success font-weight-bold" style="text-decoration:none;">
            Xem tất cả →
        </a>
    </div>

    {{-- Line phân cách --}}
    <div class="mb-4" style="height:3px;width:80px;background:#28a745;border-radius:2px;"></div>

    {{-- Danh sách sản phẩm --}}
    <div class="row">
        @foreach ($products as $product)
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-success" style="border-radius:12px;">

                    {{-- Badge giảm giá --}}
                    <span class="badge badge-danger position-absolute" style="top:10px;left:10px;">
                        -{{ $product['discount_percent'] }}%
                    </span>

                    {{-- Ảnh --}}
                    <div class="pt-4 text-center">
                        <img src="{{ $product['image'] }}" class="img-fluid" style="max-height:160px;">
                    </div>

                    {{-- Nhãn --}}
                    <div class="mt-2 px-3">
                        @foreach ($product['labels'] as $label)
                            <span class="badge badge-danger">{{ $label }}</span>
                        @endforeach
                    </div>

                    {{-- Giá --}}
                    <div class="mt-3 px-3">
                        <h5 class="text-success font-weight-bold mb-1">
                            {{ number_format($product['price']) }}đ
                        </h5>
                        <small class="text-muted">
                            <del>{{ number_format($product['old_price']) }}đ</del>
                        </small>
                    </div>

                    {{-- Tên --}}
                    <div class="mt-2 px-3">
                        <strong>{{ $product['name'] }}</strong>
                    </div>

                    {{-- Quy cách --}}
                    <div class="text-muted mt-1 px-3" style="font-size:14px;">
                        {{ $product['specification'] }}
                    </div>

                    {{-- Nhà cung cấp --}}
                    <div class="mt-2 px-3">
                        <i class="fa fa-store text-primary"></i>
                        <strong>{{ $product['supplier'] }}</strong>
                    </div>

                    {{-- Trạng thái --}}
                    <div class="mt-2 px-3">
                        <span class="badge badge-pill badge-danger">
                            {{ $product['status'] }}
                        </span>
                    </div>

                    <div class="text-muted mt-1 px-3" style="font-size:13px;">
                        Đặt tối đa {{ $product['max_order'] }} sản phẩm
                    </div>

                </div>
            </div>
        @endforeach
    </div>
</div>
