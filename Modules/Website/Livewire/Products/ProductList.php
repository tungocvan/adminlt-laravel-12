<?php


namespace Modules\Website\Livewire\Products;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Modules\Website\Models\WpProduct;

class ProductList extends Component
{
    use WithPagination;

    /**
     * Pager Bootstrap 4
     */
    protected $paginationTheme = 'bootstrap';

    /**
     * Từ khóa tìm kiếm
     */
    #[Url(as: 'q', history: true)]
    public string $search = '';

    /**
     * Sắp xếp theo
     */
    #[Url(as: 'sort', history: true)]
    public string $sortBy = 'latest';

    /**
     * Lọc theo giá
     */
    #[Url(as: 'price', history: true)]
    public string $priceRange = '';

    /**
     * Số sản phẩm mỗi trang
     */
    public int $perPage = 12;

    /**
     * Reset pagination khi filter thay đổi
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function updatedPriceRange(): void
    {
        $this->resetPage();
    }

    /**
     * Xóa tất cả filter
     */
    public function clearFilters(): void
    {
        $this->reset(['search', 'sortBy', 'priceRange']);
        $this->resetPage();
    }

    /**
     * Lấy danh sách sản phẩm
     */
    #[Computed]
    public function products()
    {
        $query = WpProduct::query()->available();

        // Tìm kiếm theo tên
        if (!empty($this->search)) {
            $query->search($this->search);
        }

        // Lọc theo khoảng giá
        if (!empty($this->priceRange)) {
            $query = $this->applyPriceFilter($query);
        }

        // Sắp xếp
        $query = $this->applySorting($query);

        return $query->paginate($this->perPage);
    }

    /**
     * Áp dụng filter giá
     */
    protected function applyPriceFilter($query)
    {
        return match ($this->priceRange) {
            'under-5m' => $query->where(function ($q) {
                $q->whereNotNull('sale_price')->where('sale_price', '<', 5000000)
                  ->orWhere(function ($q2) {
                      $q2->whereNull('sale_price')->where('regular_price', '<', 5000000);
                  });
            }),
            '5m-10m' => $query->where(function ($q) {
                $q->whereNotNull('sale_price')->whereBetween('sale_price', [5000000, 10000000])
                  ->orWhere(function ($q2) {
                      $q2->whereNull('sale_price')->whereBetween('regular_price', [5000000, 10000000]);
                  });
            }),
            '10m-20m' => $query->where(function ($q) {
                $q->whereNotNull('sale_price')->whereBetween('sale_price', [10000000, 20000000])
                  ->orWhere(function ($q2) {
                      $q2->whereNull('sale_price')->whereBetween('regular_price', [10000000, 20000000]);
                  });
            }),
            '20m-50m' => $query->where(function ($q) {
                $q->whereNotNull('sale_price')->whereBetween('sale_price', [20000000, 50000000])
                  ->orWhere(function ($q2) {
                      $q2->whereNull('sale_price')->whereBetween('regular_price', [20000000, 50000000]);
                  });
            }),
            'above-50m' => $query->where(function ($q) {
                $q->whereNotNull('sale_price')->where('sale_price', '>', 50000000)
                  ->orWhere(function ($q2) {
                      $q2->whereNull('sale_price')->where('regular_price', '>', 50000000);
                  });
            }),
            default => $query,
        };
    }

    /**
     * Áp dụng sắp xếp
     */
    protected function applySorting($query)
    {
        return match ($this->sortBy) {
            'latest' => $query->latest(),
            'oldest' => $query->oldest(),
            'price-asc' => $query->orderByRaw('COALESCE(sale_price, regular_price) ASC'),
            'price-desc' => $query->orderByRaw('COALESCE(sale_price, regular_price) DESC'),
            'name-asc' => $query->orderBy('title', 'asc'),
            'name-desc' => $query->orderBy('title', 'desc'),
            default => $query->latest(),
        };
    }

    /**
     * Lấy options sắp xếp
     */
    #[Computed]
    public function sortOptions(): array
    {
        return [
            'latest' => 'Mới nhất',
            'oldest' => 'Cũ nhất',
            'price-asc' => 'Giá tăng dần',
            'price-desc' => 'Giá giảm dần',
            'name-asc' => 'Tên A-Z',
            'name-desc' => 'Tên Z-A',
        ];
    }

    /**
     * Lấy options khoảng giá
     */
    #[Computed]
    public function priceOptions(): array
    {
        return [
            '' => 'Tất cả giá',
            'under-5m' => 'Dưới 5 triệu',
            '5m-10m' => '5 - 10 triệu',
            '10m-20m' => '10 - 20 triệu',
            '20m-50m' => '20 - 50 triệu',
            'above-50m' => 'Trên 50 triệu',
        ];
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('Website::livewire.products.product-list');
       
    }
}