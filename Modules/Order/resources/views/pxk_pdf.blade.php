<div class="container my-4">
    <div class="invoice p-4 bg-white shadow-sm border">
        {{-- Tiêu đề --}}
        <h4 class="text-center font-weight-bold mb-4" style="font-size: 26px;">
            PHIẾU XUẤT KHO #{{ $order->id }}
        </h4>

        {{-- Thông tin --}}
        <div class="row mb-4">
            <div class="col-sm-6">
                <h6 class="font-weight-bold">Người gửi (From):</h6>
                <address>
                    <strong>Admin Shop</strong><br>
                    123 Đường ABC, Quận 1, TP. Hồ Chí Minh<br>
                    SĐT: 0909 999 999<br>
                    Email: admin@shopdemo.vn
                </address>
            </div>
            <div class="col-sm-6 text-right">
                <h6 class="font-weight-bold">Người nhận (To):</h6>
                <address>
                    <strong>{{ $order->email }}</strong><br>
                    @if(isset($order->user) && $order->user->name)
                        {{ $order->user->name }}<br>
                    @endif
                    Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}<br>
                    Trạng thái: 
                    @if($order->status === 'confirmed')
                        <span class="badge badge-success">Đã xác nhận</span>
                    @elseif($order->status === 'pending')
                        <span class="badge badge-warning">Chờ xử lý</span>
                    @else
                        <span class="badge badge-danger">Đã hủy</span>
                    @endif
                </address>
            </div>
        </div>

        {{-- Bảng sản phẩm --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-light">
                    <tr class="text-center">
                        <th style="width: 50px;">#</th>
                        <th>Tên sản phẩm</th>
                        <th style="width: 100px;">SL</th>
                        <th style="width: 150px;">Đơn giá</th>
                        <th style="width: 150px;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 1; $total = 0; @endphp
                    @foreach($order->order_detail as $item)
                        @php
                            $qty = $item['quantity'] ?? 1;
                            $price = $item['price'] ?? 0;
                            $subtotal = $qty * $price;
                            $total += $subtotal;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i++ }}</td>
                            <td>{{ $item['title'] ?? 'Sản phẩm không xác định' }}</td>
                            <td class="text-center">{{ $qty }}</td>
                            <td class="text-right">{{ number_format($price, 0, ',', '.') }} đ</td>
                            <td class="text-right">{{ number_format($subtotal, 0, ',', '.') }} đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

       {{-- Tổng tiền --}}
<div class="row mt-4">
    <div class="col-8">
        <p class="lead mb-2">Phương thức thanh toán:</p>
        <p>Thanh toán khi nhận hàng (COD)</p>

        {{-- Ký xác nhận --}}
        <div class="row mt-2 text-center">
            <div class="col-6">
                <p><span class="font-weight-bold mb-5">Người giao hàng</span><br />(Ký, ghi rõ họ tên)</p>                
            </div>
            <div class="col-6">
                <p><span class="font-weight-bold mb-5">Người nhận hàng</span><br />(Ký, ghi rõ họ tên)</p>                
            </div>
        </div>
    </div>

    <div class="col-4">
        <table class="table">
            <tr>
                <th>Tạm tính:</th>
                <td class="text-right">{{ number_format($total, 0, ',', '.') }} đ</td>
            </tr>
            <tr>
                <th>Phí vận chuyển:</th>
                <td class="text-right">0 đ</td>
            </tr>
            <tr>
                <th>Tổng cộng:</th>
                <td class="text-right font-weight-bold text-danger" style="font-size: 18px;">
                    {{ number_format($total, 0, ',', '.') }} đ
                </td>
            </tr>
        </table>
    </div>
</div>

        {{-- Nút in --}}
        <div class="row no-print mt-3">
            <div class="col-12 text-center">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fa fa-print"></i> In đơn hàng
                </button>
            </div>
        </div>
    </div>
</div>

{{-- CSS in A4 --}}
<style>
    @media print {
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        .no-print {
            display: none !important;
        }
        .invoice {
            box-shadow: none !important;
            border: none !important;
        }
    }
</style>