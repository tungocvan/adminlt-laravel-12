<?php

namespace Modules\Invoices\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Invoices\Models\Invoices;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Invoices\Exports\InvoicesSelectedExport;

class HoadonList extends Component
{
    use WithPagination;

    public $type = null;      // sold | purchase
    public $name = '';        // buyer hoặc seller, tùy type
    public $tax_code = '';
    public $from_date = '';
    public $to_date = '';
    public $taxRateFilter = 'all'; // all | 5% | 8% | 10% | other

    public $nameList = [];
    public $taxCodeList = [];
    public array $selected = [];
    public $perPage = 50; // mặc định


    protected $updatesQueryString = [
        'type', 'name', 'tax_code', 'from_date', 'to_date', 'taxRateFilter', 'page', 'perPage'
    ];

    public function mount()
    {
        $this->updateNameList();
        $this->updateTaxCodeList();
    }

    public function updatedType($value)
    {
        $this->resetFilters();
        $this->updateNameList();
        $this->updateTaxCodeList();
    }

    public function updating($key)
    {
        if ($key !== 'page') $this->resetPage();
    }

    public function resetTomSelect($refName)
    {
        if($refName === 'nameSelect') {
            $this->tax_code = '';
        }
        if($refName === 'taxSelect') {
            $this->name = '';
        }
    }

    public function resetFilters()
    {
        $this->name = '';
        $this->tax_code = '';
        $this->from_date = '';
        $this->to_date = '';
        $this->taxRateFilter = 'all';

        $this->updateNameList();
        $this->updateTaxCodeList();
    }

    private function updateNameList()
    {
        if (!$this->type) {
            $this->nameList = [];
            return;
        }

        $query = Invoices::query()
            ->where('invoice_type', $this->type)
            ->when($this->from_date, fn($q) => $q->whereDate('issued_date', '>=', Carbon::parse($this->from_date)))
            ->when($this->to_date, fn($q) => $q->whereDate('issued_date', '<=', Carbon::parse($this->to_date)));

        $this->nameList = $query->select('name')
            ->groupBy('name')
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
    }

    private function updateTaxCodeList()
    {
        if (!$this->type) {
            $this->taxCodeList = [];
            return;
        }

        $query = Invoices::query()
            ->where('invoice_type', $this->type)
            ->when($this->name, fn($q) => $q->where('name', $this->name))
            ->when($this->from_date, fn($q) => $q->whereDate('issued_date', '>=', Carbon::parse($this->from_date)))
            ->when($this->to_date, fn($q) => $q->whereDate('issued_date', '<=', Carbon::parse($this->to_date)));

        $this->taxCodeList = $query->select('tax_code')
            ->groupBy('tax_code')
            ->orderBy('tax_code')
            ->pluck('tax_code')
            ->toArray();
    }

    public function getInvoicesProperty()
    {
        $query = Invoices::query()
            ->when($this->type, fn($q) => $q->where('invoice_type', $this->type))
            ->when($this->name, fn($q) => $q->where('name', $this->name))
            ->when($this->tax_code, fn($q) => $q->where('tax_code', $this->tax_code))
            ->when($this->from_date, fn($q) => $q->whereDate('issued_date', '>=', Carbon::parse($this->from_date)))
            ->when($this->to_date, fn($q) => $q->whereDate('issued_date', '<=', Carbon::parse($this->to_date)))
            ->when($this->taxRateFilter && $this->taxRateFilter !== 'all', function ($q) {
                if ($this->taxRateFilter === 'other') {
                    $q->whereNotNull('tax_rate')->whereNotIn('tax_rate', ['5%', '8%', '10%']);
                } else {
                    $q->where('tax_rate', $this->taxRateFilter);
                }
            })
            ->orderBy('issued_date', 'desc');

        if ($this->perPage === 'all') {
            return $query->get();
        }

        return $query->paginate($this->perPage);
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }


    public function getFilteredTotalAmountProperty()
    {
        return Invoices::query()
            ->when($this->type, fn($q) => $q->where('invoice_type', $this->type))
            ->when($this->name, fn($q) => $q->where('name', $this->name))
            ->when($this->tax_code, fn($q) => $q->where('tax_code', $this->tax_code))
            ->when($this->from_date, fn($q) => $q->whereDate('issued_date', '>=', Carbon::parse($this->from_date)))
            ->when($this->to_date, fn($q) => $q->whereDate('issued_date', '<=', Carbon::parse($this->to_date)))
            ->when($this->taxRateFilter && $this->taxRateFilter !== 'all', function ($q) {
                if ($this->taxRateFilter === 'other') {
                    $q->whereNotNull('tax_rate')->whereNotIn('tax_rate', ['5%', '8%', '10%']);
                } else {
                    $q->where('tax_rate', $this->taxRateFilter);
                }
            })
            ->sum('total_amount');
    }

    public function getFilteredInvoiceCountProperty()
    {
        return Invoices::query()
            ->when($this->type, fn($q) => $q->where('invoice_type', $this->type))
            ->when($this->name, fn($q) => $q->where('name', $this->name))
            ->when($this->tax_code, fn($q) => $q->where('tax_code', $this->tax_code))
            ->when($this->from_date, fn($q) => $q->whereDate('issued_date', '>=', Carbon::parse($this->from_date)))
            ->when($this->to_date, fn($q) => $q->whereDate('issued_date', '<=', Carbon::parse($this->to_date)))
            ->when($this->taxRateFilter && $this->taxRateFilter !== 'all', function ($q) {
                if ($this->taxRateFilter === 'other') {
                    $q->whereNotNull('tax_rate')->whereNotIn('tax_rate', ['5%', '8%', '10%']);
                } else {
                    $q->where('tax_rate', $this->taxRateFilter);
                }
            })
            ->count();
    }

    public function getFilteredTotalByTaxRateProperty()
    {
        $query = Invoices::query()
            ->when($this->type, fn($q) => $q->where('invoice_type', $this->type))
            ->when($this->name, fn($q) => $q->where('name', $this->name))
            ->when($this->tax_code, fn($q) => $q->where('tax_code', $this->tax_code))
            ->when($this->from_date, fn($q) => $q->whereDate('issued_date', '>=', Carbon::parse($this->from_date)))
            ->when($this->to_date, fn($q) => $q->whereDate('issued_date', '<=', Carbon::parse($this->to_date)));

        $totals = [];

        foreach (['5%', '8%', '10%'] as $rate) {
            $totals[$rate] = (clone $query)->where('tax_rate', $rate)->sum('total_amount');
        }

        $totals['other'] = (clone $query)
            ->whereNotNull('tax_rate')
            ->whereNotIn('tax_rate', ['5%', '8%', '10%'])
            ->sum('total_amount');

        // $totals['empty'] = (clone $query)
        //     ->whereNull('tax_rate')
        //     ->sum('total_amount');

        return $totals;
    }

    public function getFilteredTotalVatProperty()
    {
        return Invoices::query()
            ->when($this->type, fn($q) => $q->where('invoice_type', $this->type))
            ->when($this->name, fn($q) => $q->where('name', $this->name))
            ->when($this->tax_code, fn($q) => $q->where('tax_code', $this->tax_code))
            ->when($this->from_date, fn($q) => $q->whereDate('issued_date', '>=', Carbon::parse($this->from_date)))
            ->when($this->to_date, fn($q) => $q->whereDate('issued_date', '<=', Carbon::parse($this->to_date)))
            ->when($this->taxRateFilter && $this->taxRateFilter !== 'all', function ($q) {
                if ($this->taxRateFilter === 'other') {
                    $q->whereNotNull('tax_rate')->whereNotIn('tax_rate', ['5%', '8%', '10%']);
                } else {
                    $q->where('tax_rate', $this->taxRateFilter);
                }
            })
            ->sum('vat_amount');
    }

    public function exportSelected()
    {
        if (count($this->selected) === 0) {
            $this->dispatch('alert', [
                'type' => 'warning',
                'message' => 'Vui lòng chọn hóa đơn trước khi xuất!'
            ]);
            return;
        }

        return Excel::download(
            new InvoicesSelectedExport($this->selected),
            'hoadon_chon_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function render()
    {
        $totalSoldAmount = Invoices::where('invoice_type', 'sold')->sum('total_amount');
        $totalPurchaseAmount = Invoices::where('invoice_type', 'purchase')->sum('total_amount');
        $totalSoldCustomers = Invoices::where('invoice_type', 'sold')->distinct('name')->count('name');
        $totalPurchaseCustomers = Invoices::where('invoice_type', 'purchase')->distinct('name')->count('name');

        return view('Invoices::livewire.hoadon-list', [
            'invoices' => $this->invoices,
            'totalSoldAmount' => $totalSoldAmount,
            'totalPurchaseAmount' => $totalPurchaseAmount,
            'totalSoldCustomers' => $totalSoldCustomers,
            'totalPurchaseCustomers' => $totalPurchaseCustomers
        ]);
    }
}
