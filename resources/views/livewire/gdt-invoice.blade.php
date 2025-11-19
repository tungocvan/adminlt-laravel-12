<div>
    <div class="card mb-3">
        <div class="card-body">
            <form wire:submit.prevent="searchInvoices" class="form-inline">
                <div class="form-group mr-2">
                    <label for="fromDate" class="mr-1">Từ ngày</label>
                    <input type="date" id="fromDate" wire:model.defer="fromDate" class="form-control">
                </div>

                <div class="form-group mr-2">
                    <label for="toDate" class="mr-1">Đến ngày</label>
                    <input type="date" id="toDate" wire:model.defer="toDate" class="form-control">
                </div>

                <div class="form-group mr-2">
                    <label for="searchKeyword" class="mr-1">Tìm kiếm</label>
                    <input type="text" id="searchKeyword" wire:model.defer="searchKeyword" class="form-control" placeholder="Số hóa đơn, MST...">
                </div>

                <button type="submit" class="btn btn-primary">Tìm hóa đơn</button>
            </form>
        </div>
    </div>

    @if(!empty($invoices))
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>STT</th>
                            <th>Mã tra cứu hóa đơn</th>
                            <th>Số hóa đơn</th>
                            <th>Ngày lập</th>
                            <th>Người mua</th>
                            <th>Người bán</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item['cttkhac'][16]['dlieu'] ?? '' }}</td>
                                <td>{{ $item['shdon'] ?? '' }}</td>
                                <td>{{ $item['tdlap'] ? \Carbon\Carbon::parse($item['tdlap'])->format('d/m/Y') : '' }}</td>
                                <td>{{ $item['nmten'] ?? '' }}</td>
                                <td>{{ $item['nbten'] ?? '' }}</td>
                                <td>{{ number_format($item['tgtcthue'] ?? 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="alert alert-info">Chưa có hóa đơn để hiển thị</div>
    @endif
</div>
