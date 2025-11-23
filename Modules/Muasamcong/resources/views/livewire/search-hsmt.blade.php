<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Tra cứu HSMT</h5>
    </div>

    <div class="card-body">

        <div class="form-row">

            <div class="form-group col-md-3">
                <label>Từ ngày</label>
                <input type="date" class="form-control" wire:model.defer="from_date">
            </div>

            <div class="form-group col-md-3">
                <label>Đến ngày</label>
                <input type="date" class="form-control" wire:model.defer="to_date">
            </div>

            <div class="form-group col-md-3">
                <label>Từ khóa</label>
                <input type="text" class="form-control" wire:model.defer="keyword" placeholder="Tên gói thầu...">
            </div>

            <div class="form-group col-md-3 d-flex align-items-end">
                <button wire:click="search" wire:loading.attr="disabled" class="btn btn-primary btn-block">
                    <span wire:loading.remove>🔍 Tìm kiếm</span>
                    <span wire:loading>⏳ Đang tìm...</span>
                </button>
            </div>

        </div>

        @if ($error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endif

        @if (!$loading && $total > 0)
            <div class="alert alert-info">
                Tìm thấy <b>{{ $total }}</b> kết quả.
            </div>
        @endif

        {{-- Nút export --}}
        @if (count($results) > 0)
        <div class="mb-3">
            <button class="btn btn-success"
                    wire:click="exportExcel"
                    @if(count($selected) == 0) disabled @endif>
                📤 Xuất Excel ({{ count($selected) }} mục)
            </button>
        </div>
        @endif

        <div wire:loading class="text-center py-4">
            <div class="spinner-border text-primary"></div>
            <p>Đang tải dữ liệu...</p>
        </div>

        @if (!$loading && count($results) > 0)

            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                <tr>
                    <th width="40">
                        <input type="checkbox" wire:model.live="selectAll">
                    </th>
                    <th>Tên gói thầu</th>
                    <th>Mã TBMT</th>
                    <th>Ngày đăng tải</th>
                    <th>Đóng thầu</th>
                    <th>Bên mời thầu</th>
                    <th>Địa điểm</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($results as $item)
                    <tr>
                        <td>
                            <input type="checkbox"
                                   wire:model.live="selected"
                                   value="{{ $item['notifyNo'] }}">
                        </td>
                        <td>{{ $item['bidName'][0] ?? '' }}</td>
                        <td>{{ $item['notifyNo'] }}</td>
                        <td>{{ $item['publicDate'] }}</td>
                        <td>{{ $item['bidOpenDate'] }}</td>
                        <td>{{ $item['investorName'] }}</td>
                        <td>
                            {{ $item['locations'][0]['districtName'] ?? '' }} -
                            {{ $item['locations'][0]['provName'] ?? '' }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        @endif
    </div>
</div>
