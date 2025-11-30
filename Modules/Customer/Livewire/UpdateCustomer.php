<?php

namespace Modules\Customer\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Invoices\Models\Invoices;
use Illuminate\Support\Facades\DB;

class UpdateCustomer extends Component
{
    use WithPagination;

    public $search = '';
    public $invoiceType = '';
    public $perPage = 50;

    public $sortField = 'name';
    public $sortDirection = 'asc';

    public $selected = [];
    public $selectAll = false;

    protected $updatesQueryString = [
        'search', 'invoiceType', 'perPage',
        'sortField', 'sortDirection'
    ];

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->customers->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Query chính
     */
    protected function customersQuery()
    {
        // Lấy id mới nhất theo tax_code
        $latestIds = Invoices::select(DB::raw('MAX(id) AS id'))
            ->whereNotNull('tax_code')
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->groupBy('tax_code')
            ->pluck('id')
            ->toArray();

        $query = Invoices::whereIn('id', $latestIds)
            ->select('id', 'tax_code', 'name', 'address', 'email', 'phone', 'issued_date', 'invoice_type');

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('tax_code', 'like', "%{$this->search}%")
                  ->orWhere('name', 'like', "%{$this->search}%");
            });
        }

        // Filter theo invoice type
        if ($this->invoiceType) {
            $query->where('invoice_type', $this->invoiceType);
        }

        // Sort
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query;
    }

    public function getCustomersProperty()
    {
        if ($this->perPage === 'all') {
            return $this->customersQuery()->get();
        }

        return $this->customersQuery()->paginate($this->perPage);
    }

    public function render()
    {
        return view('Customer::livewire.update-customer', [
            'customers' => $this->customers,
        ]);
    }
}
