<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'BÁO GIÁ' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; margin: 20px; }
        table.content {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto; /* bắt buộc dompdf tuân theo width cột */
            font-size: 10px;
        }
        th, td {
            border: 1px solid #333;
            padding: 4px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal; /* wrap text */
            text-align: center;
        }
        th { background-color: #f0f0f0; }
        tr { page-break-inside: avoid; page-break-after: auto; }

        /* Width cố định cho các cột */
        th.stt, td.stt { width: 30px; }
        th.stt-tt20, td.stt-tt20 { width: 60px; }
        th.phan-nhom, td.phan-nhom { width: 30px; }
        th.ten-hoat-chat, td.ten-hoat-chat { width: 100px; }
        th.nong-do, td.nong-do { width: 60px; }
        th.ten-biet-duoc, td.ten-biet-duoc { width: 120px; }
        th.dang-bao-che, td.dang-bao-che { width: 60px; }
        th.don-vi, td.don-vi { width: 50px; }
        th.quy-cach, td.quy-cach { width: 60px; }
        th.so-gplh, td.so-gplh { width: 80px; }
        th.han-dung, td.han-dung { width: 30px; }
        th.co-so, td.co-so { width: 80px; }
        th.don-gia, td.don-gia { width: 60px; text-align: right; }
        th.don-gia { text-align: center; }
        th.gia-ke-khai, td.gia-ke-khai { width: 60px; text-align: right; }        
        th.gia-ke-khai { text-align: center; }

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
    </style>
</head>
<body>
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 144px; padding-top:10px">
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
                    <p><strong>Mã số:</strong> {{ $ma_so }}</p>                    
                </td>
            </tr>
        </table>
        <h2 class="title">{{ $company['title'] ?? 'BÁO GIÁ' }}</h2>
    </header>



    <main>
        <p><strong>Kính gửi: </strong> {{ $customer_name ?? '' }}</p>
        <p>Công ty <strong>INAFO Việt Nam</strong> xin trân trọng gửi đến Quý Khách hàng báo giá một số sản phẩm chúng tôi đang phân phối trên thị trường hiện nay như sau:</p>
        <table class="content">
            <thead>
                <tr>
                    <th class="stt">STT</th>
                    <th class="stt-tt20">STT TT20/2022</th>
                    <th class="phan-nhom">Phân nhóm TT15</th>
                    <th class="ten-hoat-chat">Tên hoạt chất</th>
                    <th class="nong-do">Nồng độ / Hàm lượng</th>
                    <th class="ten-biet-duoc">Tên biệt dược</th>
                    <th class="dang-bao-che">Dạng bào chế</th>
                    <th class="don-vi">Đơn vị tính</th>
                    <th class="quy-cach">Quy cách đóng gói</th>
                    <th class="so-gplh">Số GPLH</th>
                    <th class="han-dung">Hạn dùng</th>
                    <th class="co-so">Cơ sở sản xuất</th>
                    <th class="don-gia">Đơn giá</th>
                    <th class="gia-ke-khai">Giá kê khai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products ?? [] as $index => $product)
                    @php $m = $product['item']; @endphp
                    <tr>
                        <td class="stt">{{ $index + 1 }}</td>
                        <td class="stt-tt20">{{ $m->stt_tt20_2022 ?? '' }}</td>
                        <td class="phan-nhom">{{ $m->phan_nhom_tt15 ?? '' }}</td>
                        <td class="ten-hoat-chat">{{ $m->ten_hoat_chat ?? '' }}</td>
                        <td class="nong-do">{{ $m->nong_do_ham_luong ?? '' }}</td>
                        <td class="ten-biet-duoc">{{ $m->ten_biet_duoc ?? '' }}</td>
                        <td class="dang-bao-che">{{ $m->dang_bao_che ?? '' }}</td>
                        <td class="don-vi">{{ $m->don_vi_tinh ?? '' }}</td>
                        <td class="quy-cach">{{ $m->quy_cach_dong_goi ?? '' }}</td>
                        <td class="so-gplh">{{ $m->giay_phep_luu_hanh ?? '' }}</td>
                        <td class="han-dung">{{ $m->han_dung ?? '' }}</td>
                        <td class="co-so">{{ $m->co_so_san_xuat ?? '' }}</td>
                        <td class="don-gia">{{ number_format($m->don_gia ?? 0) }}</td>
                        <td class="gia-ke-khai">{{ number_format($m->gia_ke_khai ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- FOOTER --}}
        <div class="footer">
            {{-- HEADER --}}
            <table class="header-table">
                <tr>
                    <td style="width: 75%;">                    
                    </td>            
                    <td style="width: 25%;">    
                        <p style="text-align: right;font-size: 9px;">{{$company['date'] ?? '' }}</p>                    
                        <p style="text-align: center;"><strong>{{$company['departments'] ?? 'Giám đốc' }}</strong></p>
                    </td>
                </tr>
            </table>
       </div>
    </main>
</body>
</html>
