<div class="container mt-4">
    <h3 class="mb-3">Tra cứu thuốc trúng thầu</h3>

    <div class="form-inline mb-3">
        <input type="text" class="form-control mr-2 flex-fill" placeholder="Nhập tên thuốc / hoạt chất" wire:model.defer="keyword">
        <button class="btn btn-primary" wire:click="search" @if($loading) disabled @endif>
            @if($loading) Đang tìm... @else Tìm kiếm @endif
        </button>
    </div>

    @if($error)
        <div class="alert alert-danger">{{ $error }}</div>
    @endif
    
    @if(!empty($results['data']))       
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-light">
                    <tr>
                        <th>STT</th>
                        <th>Tên thuốc</th>
                        <th>Hoạt chất</th>                        
                        <th>Nồng độ hàm lượng</th>                        
                        <th>Đơn vị tính</th>                        
                        <th>Giá trúng thầu</th>
                        <th>Số lượng</th>
                        <th>Số Quyết định</th>
                        <th>Ngày ban hành</th>
                        <th>Mã TBMT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results['data']['page']['content'] as $key => $item)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $item['tenThuoc'] ?? '-' }}</td>
                            <td>{{ $item['tenHoatChat'] ?? '-' }}</td>
                            <td>{{ $item['nongDo'] ?? '-' }}</td>
                            <td>{{ $item['donViTinh'] ?? '-' }}</td>
                            <td>{{ $item['donGia'] ?? '-' }}</td>                          
                            <td>{{ $item['soLuong'] ?? '-' }}</td>
                            <td>{{ $item['soQuyetDinh'] ?? '-' }}</td>
                            <td>{{ $item['ngayBanHanhQuyetDinh'] ?? '-' }}</td>
                            <td>{{ $item['maTbmt'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info">Không có dữ liệu</div>
    @endif
</div>
