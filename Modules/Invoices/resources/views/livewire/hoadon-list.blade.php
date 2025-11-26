<div x-data="invoiceTypeData">
    <!-- Dashboard -->
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

        <div class="card mb-3">
            <div class="card-header bg-dark d-flex justify-content-between text-white">
                <span>Bộ lọc <span x-text="type=='sold'?'bán ra':'mua vào'"></span></span>
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
                    <div class="col-md-4">
                        <label>MST</label>

                        <select x-ref="taxSelect" x-init="initTomSelect('taxSelect')" class="form-control" wire:model.live="tax_code">
                            <option value=''>-- Tất cả --</option>
                            @foreach ($taxCodeList as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
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

                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-end">
                        <button wire:click="resetFilters" class="btn btn-secondary btn-block">Xóa lọc</button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table-bordered table-hover table">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Mã tra cứu</th>
                        <th>Ký hiệu</th>
                        <th>Số HĐ</th>
                        <th>Ngày lập</th>
                        <th x-text="type=='sold'?'Người mua':'Người bán'"></th>
                        <th x-text="type=='sold'?'MST Người mua':'MST Người bán'"></th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->id }}</td>
                            <td>{{ $invoice->lookup_code }}</td>
                            <td>{{ $invoice->symbol }}</td>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->issued_date?->format('d/m/Y') }}</td>
                            <td>{{ $invoice->name }}</td>
                            <td>{{ $invoice->tax_code }}</td>
                            <td>{{ number_format($invoice->total_amount) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-muted text-center">Không có dữ liệu</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $invoices->links() }}</div>

    </div>

    @push('css')
        <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap4.min.css" rel="stylesheet">
    @endpush

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
        
                // Registry lưu tất cả TomSelect trong trang
                window.TS_REGISTRY = window.TS_REGISTRY || [];
        
                Alpine.data('invoiceTypeData', () => ({
                    type: @js($type),
                    selects: {},
        
                    init() {
        
                        // Đồng bộ type với Livewire
                        this.$watch('type', value => {
                            this.$wire.set('type', value);
                        });
        
                        // Re-init giá trị TomSelect nếu Livewire render lại
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
        
                        // Nếu đã init rồi thì bỏ qua
                        if (el.tomselect) {
                            this.selects[refName] = el.tomselect;
                            return;
                        }
        
                        // Khởi tạo TomSelect
                        let ts = new TomSelect(el, {});
        
                        // Lưu vào registry toàn cục
                        window.TS_REGISTRY.push(ts);
        
                        // Focus → clear tất cả TomSelect khác
                        ts.on('focus', () => {
                            window.TS_REGISTRY.forEach(t => {
                                if (t !== ts) {
                                    t.clear(true);
                                }
                            });
                        });
        
                        // Gửi event change về Livewire
                        ts.on('change', () => {
                            el.dispatchEvent(
                                new Event('input', { bubbles: true })
                            );
                        });
                        
                        // Lưu instance vào Alpine để sync lại sau render
                        this.selects[refName] = ts;
                    }
        
                }));
            });
        </script>
        
    @endpush
</div>
