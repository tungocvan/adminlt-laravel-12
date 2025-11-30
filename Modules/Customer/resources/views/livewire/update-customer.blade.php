<div>

    {{-- Filters --}}
    <div class="row mb-3 my-2">
        <div class="col-md-3">
            <input type="text" class="form-control"
                   placeholder="Tìm tax code hoặc tên..."
                   wire:model.live.debounce.500ms="search">
        </div>

        <div class="col-md-3">
            <select class="form-control" wire:model.live="invoiceType">
                <option value="">Tất cả loại hóa đơn</option>
                <option value="sold">Bán ra</option>
                <option value="purchase">Mua vào</option>
            </select>
        </div>

        <div class="col-md-3">
            <select class="form-control" wire:model.live="perPage">
                <option value="50">50 dòng</option>
                <option value="100">100 dòng</option>
                <option value="all">Tất cả</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <table class="table table-hover table-bordered">
        <thead class="thead-light">
            <tr>
                <th width="40">
                    <input type="checkbox" wire:model.live="selectAll">
                </th>

                <th width="60">STT</th>
                <th>Tax Code</th>

                <th style="cursor:pointer;" wire:click="sortBy('name')">
                    Tên
                    @if ($sortField === 'name')
                        {!! $sortDirection === 'asc' ? '▲' : '▼' !!}
                    @endif
                </th>

                <th>Địa chỉ</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Ngày lập</th>
                <th>Loại</th>
            </tr>
        </thead>

        <tbody>
            @php
                $page = $customers instanceof \Illuminate\Pagination\LengthAwarePaginator
                    ? $customers->currentPage()
                    : 1;

                $perPageNum = $perPage === 'all'
                    ? $customers->count()
                    : $perPage;

                $i = ($page - 1) * $perPageNum + 1;
            @endphp

            @foreach ($customers as $c)
                <tr>
                    <td>
                        <input type="checkbox"
                               wire:model.live="selected"
                               value="{{ $c->id }}">
                    </td>

                    <td>{{ $i++ }}</td>

                    <td>{{ $c->tax_code }}</td>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->address }}</td>
                    <td>{{ $c->email }}</td>
                    <td>{{ $c->phone }}</td>
                    <td>{{ $c->issued_date }}</td>

                    <td>
                        <span class="badge badge-primary">
                            {{ $c->invoice_type }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($perPage !== 'all')
        {{ $customers->links() }}
    @endif

</div>
