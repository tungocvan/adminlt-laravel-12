<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'In hóa đơn')</title>

    <style>
        /* === Thiết lập khổ giấy A4 === */
        @page {
            size: A4 portrait;
            margin: 15mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h1, h2, h3, h4, h5 {
            font-family: 'DejaVu Sans', sans-serif;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-danger { color: #c00; }
        .font-weight-bold { font-weight: bold; }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-top: 40px;
        }

        /* === Khu vực ký tên === */
        .sign-area {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }

        .sign-box {
            width: 45%;
            text-align: center;
        }

        .sign-box p {
            margin: 5px 0;
        }

        /* === Khi in === */
        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        @yield('content')

        {{-- Chữ ký mặc định (tự động có nếu không bị override) --}}
        <div style="width:100%; margin-top:40px;">
            <div style="width:45%; float:left; text-align:center;">
                <p><strong>Người giao hàng</strong></p>
                <br><br><br>
                <p>(Ký tên)</p>
            </div>
            <div style="width:45%; float:right; text-align:center;">
                <p><strong>Người nhận hàng</strong></p>
                <br><br><br>
                <p>(Ký tên)</p>
            </div>
        </div>
        <div style="clear:both;"></div>
        

        <div class="footer">
            Cảm ơn quý khách đã tin tưởng và sử dụng dịch vụ của chúng tôi!
        </div>
    </div>

</body>
</html>
