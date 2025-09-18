<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WpProduct;
use App\Models\Category;
use Livewire\WithFileUploads;


class ProductManager extends Component
{
    use WithPagination;
    use WithFileUploads;


    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'desc';
    
    public $imageUpload;   // file upload chính
    public $galleryUpload = []; // nhiều file upload

    // form
    public $showForm = false;
    public $productId;
    public $title, $slug, $short_description, $description;
    public $regular_price, $sale_price, $image, $gallery = [], $tags = [];
    public $categories = [];
    public $selectedCategories = [];

    protected $rules = [
        'title'             => 'required|string|max:255',
        'slug'              => 'nullable|string|max:255|unique:wp_products,slug',
        'short_description' => 'nullable|string|max:500',
        'description'       => 'nullable|string',
        'regular_price'     => 'nullable|numeric',
        'sale_price'        => 'nullable|numeric',
    ];

    public function render()
    {
        return view('livewire.products.product-manager', [
            'products'   => WpProduct::with('categories')
                ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage),
            'allCategories' => Category::where('type', 'category')->pluck('name', 'id'),
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $product = WpProduct::with('categories')->findOrFail($id);

        $this->productId = $product->id;
        $this->title = $product->title;
        $this->slug = $product->slug;
        $this->short_description = $product->short_description;
        $this->description = $product->description;
        $this->regular_price = $product->regular_price;
        $this->sale_price = $product->sale_price;
        $this->image = $product->image;
        $this->gallery = $product->gallery ?? [];
        $this->tags = $product->tags ?? [];
        $this->selectedCategories = $product->categories->pluck('id')->toArray();

        $this->showForm = true;
    }

    public function save()
    {
        $data = $this->validate();

        // Xử lý ảnh chính
        if ($this->imageUpload) {
            $data['image'] = $this->imageUpload->store('products', 'public');
        }

        // Xử lý gallery nhiều ảnh
        if (!empty($this->galleryUpload)) {
            $paths = [];
            foreach ($this->galleryUpload as $file) {
                $paths[] = $file->store('products/gallery', 'public');
            }
            $data['gallery'] = $paths;
        }

        $product = WpProduct::updateOrCreate(
            ['id' => $this->productId],
            array_merge($data, [
                'tags' => $this->tags,
            ])
        );

        $product->categories()->sync($this->selectedCategories);

        $this->resetForm();
        $this->showForm = false;
        session()->flash('success', 'Lưu sản phẩm thành công.');
    }


    public function delete($id)
    {
        WpProduct::findOrFail($id)->delete();
        session()->flash('success', 'Đã xoá sản phẩm.');
    }

    private function resetForm()
    {
        $this->reset([
            'productId','title','slug','short_description','description',
            'regular_price','sale_price','image','gallery','tags','selectedCategories'
        ]);
    }
}
