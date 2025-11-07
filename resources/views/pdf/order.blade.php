<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Phiếu xuất kho #{{ $order->id }}</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #333;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .header-table td {
            vertical-align: top;
            border: none;
        }
        .header-table img {
            height: 60px;
        }
        h2.title {
            text-align: center;
            margin: 15px 0;
            font-size: 20px;
            text-transform: uppercase;
        }
        .info {
            margin-bottom: 15px;
            font-size: 13px;
        }
        .info p {
            margin: 4px 0;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.items th, table.items td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
        }
        table.items th {
            background-color: #f3f3f3;
            text-align: center;
        }
        table.items td:nth-child(1),
        table.items td:nth-child(3),
        table.items td:nth-child(4),
        table.items td:nth-child(5) {
            text-align: center;
        }
        .total {
            text-align: right;
            margin-top: 10px;
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #555;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td style="width: 20%; padding-top:10px">
                @php
                    $logoPath = storage_path('app/logo.png');
                @endphp
                @if(file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Logo công ty">
                @endif                    
               
            </td>
            <td style="text-align: left;">
              <strong>Công Ty TNHH Inafo Việt Nam</strong><br/>            
              <span>Địa chỉ: 240/127/26 Nguyễn Văn Luông,P.Bình Phú, TP.HCM</span><br/>            
              <span>Mã số thuế: 0314492345</span>           
              <span>Phone: 036 579 2786 - Email: inafopharma@gmail.com</span><br/>           
               
            </td>
            <td style="text-align: right;width:150px">
                <p><strong>Ngày:</strong> {{ now()->format('d/m/Y') }}</p>
                <p><strong>Số phiếu:</strong> #{{ $order->id }}</p>
            </td>
        </tr>
    </table>

    <h2 class="title">PHIẾU XUẤT KHO</h2>
    @php
       $shipping = optional($customer)->getOption('shipping_info', []);

    @endphp
    {{-- THÔNG TIN KHÁCH HÀNG --}}
    <div class="info">
        <table class="header-table">
            <tr>
                <td style="width:150px;text-align: left;">
                    <strong>Đơn vị mua hàng</strong> 
                </td>
                <td style="text-align: left;">
                    <strong>:</strong> 
                </td>
                <td style="text-align: left;">
                    {{  $shipping['company_name']  ?? $customer->username ?? 'Khách lẻ' }}
                </td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: left;">
                    <strong>Email</strong> 
                </td>
                <td style="text-align: left;">
                    <strong>:</strong> 
                </td>
                <td style="text-align: left;">
                    {{ $shipping['email'] ?? $customer->email ?? '' }}
                </td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: left;">
                    <strong>Địa chỉ</strong> 
                </td>
                <td style="text-align: left;">
                    <strong>:</strong> 
                </td>
                <td style="text-align: left;">
                    {{ $shipping['address'] ?? '0123456789' }}

                </td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: left;">
                    <strong>Mã số thuế</strong> 
                </td>
                <td style="text-align: left;">
                    <strong>:</strong> 
                </td>
                <td style="text-align: left;">
                    {{ $shipping['tax_code'] ?? '0123456789' }}
                </td>
            </tr>
            <tr>
                <td style="width: 30%;text-align: left;">
                    <strong>Số điện thoại</strong> 
                </td>
                <td style="text-align: left;">
                    <strong>:</strong> 
                </td>
                <td style="text-align: left;">
                    {{ $shipping['phone'] ?? '0903.971.949' }}
                </td>
            </tr>
        </table>

  
    </div>

    {{-- DANH SÁCH SẢN PHẨM --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width: 20px;">STT</th>
                <th>Tên sản phẩm</th>
                <th style="width: 70px;">Số lô</th>
                <th style="width: 80px;">Hạn dùng</th>
                <th style="width: 30px;">ĐVT</th>
                <th style="width: 30px;">Số lượng</th>
                <th style="width: 90px;">Đơn giá (₫)</th>
                <th style="width: 110px;">Thành tiền (₫)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: left;">{{ $item['title'] ?? 'Chưa có tên' }}</td>
                    <td style="text-align: center;">{{ $item['so_lo'] ?? '01012025' }}</td>
                    <td style="text-align: center;">{{ $item['han_dung'] ?? '01/01/2027' }}</td>
                    <td style="text-align: center;">{{ $item['dvt'] ?? '-' }}</td>
                    <td style="text-align: center;">{{ $item['quantity'] ?? 0 }}</td>
                    <td style="text-align: right;">{{ number_format($item['don_gia'] ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($item['total'] ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="6" style="text-align: right;">
                    <strong>Tổng tiền:</strong>
                </td>
                <td colspan="2" style="text-align: right;">
                    <strong>{{ number_format($order->total, 0, ',', '.') }} ₫</strong>
                </td>
            </tr>
            <tr>
                <td colspan="8" style="text-align: center;">{{ vn_number_to_words($order->total) }}</td>
            </tr>
        </tbody>
    </table>

    

    {{-- FOOTER --}}
    <div class="footer">
         {{-- HEADER --}}
        <table class="header-table">
            <tr>
                <td style="width: 25%;text-align: left;">
                    <strong>Người lập phiếu</strong>
                </td>
                <td style="width: 25%;">
                    <strong>Thủ kho</strong>
                </td>
                <td style="width: 25%;">
                    <strong>Người nhận hàng</strong>
                </td>
                <td style="width: 25%;text-align: center;">
                    <strong>Giám đốc</strong>
                </td>
               
            </tr>
        </table>
    </div>

</body>
</html>
