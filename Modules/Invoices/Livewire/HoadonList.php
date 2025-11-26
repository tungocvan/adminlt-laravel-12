<?php

namespace Modules\Invoices\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Invoices\Models\Invoices;
use Carbon\Carbon;

class HoadonList extends Component
{
    use WithPagination;

    public $type = null;      // sold | purchase
    public $name = '';        // buyer hoặc seller, tùy type
    public $tax_code = '';
    public $from_date = '';
    public $to_date = '';

    public $nameList = [];
    public $taxCodeList = [];

    protected $updatesQueryString = [
        'type', 'name', 'tax_code', 'from_date', 'to_date', 'page'
    ];

    public function mount()
    {
        $this->updateNameList();
        $this->updateTaxCodeList();
        
    }

    public function updatedType($value)
    {
        // Reset các filter khi đổi tab
        $this->resetFilters();
        $this->updateNameList();
        $this->updateTaxCodeList();
    }

    public function updating($key)
    {
        if ($key !== 'page') $this->resetPage();
    }

    public function resetFilters()
    {
        $this->name = '';
        $this->tax_code = '';
        $this->from_date = '';
        $this->to_date = '';

        // Cập nhật lại dropdown theo filter trống
        $this->updateNameList();
        $this->updateTaxCodeList();
    }

    /**
     * Cập nhật dropdown nameList dựa trên type và date filter
     */
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

    /**
     * Cập nhật dropdown taxCodeList dựa trên type, name và date filter
     */
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

    /**
     * Lấy hóa đơn theo filter
     */
    public function getInvoicesProperty()
    {
        return Invoices::query()
            ->when($this->type, fn($q) => $q->where('invoice_type', $this->type))
            ->when($this->name, fn($q) => $q->where('name', $this->name))
            ->when($this->tax_code, fn($q) => $q->where('tax_code', $this->tax_code))
            ->when($this->from_date, fn($q) => $q->whereDate('issued_date', '>=', Carbon::parse($this->from_date)))
            ->when($this->to_date, fn($q) => $q->whereDate('issued_date', '<=', Carbon::parse($this->to_date)))
            ->orderBy('issued_date', 'desc')
            ->paginate(20);
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
