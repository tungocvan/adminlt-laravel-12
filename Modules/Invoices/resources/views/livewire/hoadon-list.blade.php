<div x-data="invoiceTypeData">

    <!-- Dashboard tổng quan -->
    <div class="row mb-4" :class="{ 'd-none': type !== null }">
        <div class="col-md-6">
            <div class="small-box bg-primary" style="cursor:pointer;" @click="type='sold'">
                <div class="inner">
                    <h3>{{ number_format($totalSoldAmount) }} đ</h3>
                    <p>Hóa đơn bán ra</p>
                    <small>Tổng khách hàng: {{ $totalSoldCustomers }}</small>
                </div>
                <div class="icon"><i class="fas fa-shopping-cart"></i></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="small-box bg-success" style="cursor:pointer;" @click="type='purchase'">
                <div class="inner">
                    <h3>{{ number_format($totalPurchaseAmount) }} đ</h3>
                    <p>Hóa đơn mua vào</p>
                    <small>Tổng nhà cung cấp: {{ $totalPurchaseCustomers }}</small>
                </div>
                <div class="icon"><i class="fas fa-truck"></i></div>
            </div>
        </div>
    </div>

    <!-- Filter + Table -->
    <div :class="{ 'd-none': type === null }" class="mt-2">

        <!-- Card Filter -->
        <div class="card mb-3">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Bộ lọc <span x-text="type=='sold'?'bán ra':'mua vào'"></span></h5>
                <button class="btn btn-sm btn-light" @click="type = null; @this.resetFilters()">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </button>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <!-- Người mua / Người bán -->
                    <div class="col-md-4">
                        <label x-text="type == 'sold' ? 'Người mua' : 'Người bán'"></label>
                        <select x-ref="nameSelect" x-init="initTomSelect('nameSelect')" class="form-control" wire:model.live="name">
                            <option value=''>-- Tất cả --</option>
                            @foreach ($nameList as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- MST -->
                    <div class="col-md-2">
                        <label>MST</label>
                        <select x-ref="taxSelect" x-init="initTomSelect('taxSelect')" class="form-control" wire:model.live="tax_code">
                            <option value=''>-- Tất cả --</option>
                            @foreach ($taxCodeList as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label>Thuế</label>
                        <select class="form-control" wire:model.live="taxRateFilter">
                            <option value="all">Tất cả</option>
                            <option value="5%">5%</option>
                            <option value="8%">8%</option>
                            <option value="10%">10%</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label>Số dòng / trang:</label>
                        <select wire:model.live="perPage" class="form-control w-auto d-inline-block">
                            <option value="all">Tất cả</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="200">200</option>
                        </select>
                    </div>
                    
                    <!-- Từ ngày / Đến ngày -->
                    <div class="col-md-4 d-flex">
                        <div class="w-50 mr-2">
                            <label>Từ ngày</label>
                            <input type="date" wire:model.live="from_date" class="form-control">
                        </div>
                        <div class="w-50">
                            <label>Đến ngày</label>
                            <input type="date" wire:model.live="to_date" class="form-control">
                        </div>
                    </div>
                </div>
              
                
                <!-- Nút xóa filter -->
                <div class="row mb-3">
                    <div class="col-md-6 d-flex align-items-end">
                        <button wire:click="resetFilters" class="btn btn-secondary btn-block">Xóa lọc</button>
                        <button wire:click="exportSelected"
                        class="btn btn-primary btn-block mx-1">
                            <i class="fas fa-file-excel"></i> Xuất các hóa đơn đã chọn
                        </button>

                    </div>
                </div>

                <!-- Tổng hợp hóa đơn -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h6 class="mb-0">Tổng hợp hóa đơn</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Tổng hóa đơn:</strong> {{ $this->filteredInvoiceCount }}</p>
                                <p><strong>Tổng VAT:</strong> {{ number_format($this->filteredTotalVat) }} đ</p>
                                <p><strong>Tổng tiền:</strong> {{ number_format($this->filteredTotalAmount) }} đ</p>
                                <p><strong>Tổng theo thuế:</strong></p>
                                <ul class="mb-0">
                                    @foreach ($this->filteredTotalByTaxRate as $rate => $total)
                                        <li>{{ $rate === 'other' ? 'Không thuế' : $rate }}: {{ number_format($total) }} đ</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table hóa đơn -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>
                            <input type="checkbox" 
                                   x-on:change="$wire.set('selected', $event.target.checked 
                                        ? @js($invoices->pluck('id')) 
                                        : [])">
                        </th>                        
                        <th>#</th>
                        <th>Mã tra cứu</th>
                        <th>Ký hiệu</th>
                        <th>Số HĐ</th>
                        <th>Ngày lập</th>
                        <th x-text="type=='sold'?'Người mua':'Người bán'"></th>
                        <th x-text="type=='sold'?'MST Người mua':'MST Người bán'"></th>
                        <th>Thuế suất</th>
                        <th>Thuế VAT</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $key => $invoice)
                        <tr>
                            <td>
                                <input type="checkbox" 
                                       wire:model.live="selected" 
                                       value="{{ $invoice->id }}">
                            </td>
                            <td>{{ $key+1}}</td>
                            <td>{{ $invoice->lookup_code }}</td>
                            <td>{{ $invoice->symbol }}</td>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->issued_date?->format('d/m/Y') }}</td>
                            <td>{{ $invoice->name }}</td>
                            <td>{{ $invoice->tax_code }}</td>
                            <td>{{ $invoice->tax_rate }}</td>
                            <td>{{ number_format($invoice->vat_amount) }}</td>
                            <td>{{ number_format($invoice->total_amount) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Không có dữ liệu</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            @if ($perPage !== 'all')
                {{ $invoices->links() }}
            @endif

        </div>

    </div>

    @push('css')
        <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap4.min.css" rel="stylesheet">
    @endpush

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('invoiceTypeData', () => ({
                    type: @js($type),
                    selects: {},

                    init() {
                        this.$watch('type', value => this.$wire.set('type', value));

                        Livewire.hook('message.processed', () => {
                            for (let ref in this.selects) {
                                let el = this.$refs[ref];
                                if (el) this.selects[ref].setValue(el.value, true);
                            }
                        });
                    },

                    initTomSelect(refName) {
                        let el = this.$refs[refName];
                        if (!el) return;

                        let ts = new TomSelect(el, {});

                        ts.on('focus', () => ts.clear(true));
                        ts.on('change', () => this.$wire.resetTomSelect(refName));
                    }

                }));
            });
        </script>
    @endpush

</div>
