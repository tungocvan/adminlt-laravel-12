<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WpProduct;
use App\Models\Category;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Illuminate\Support\Str;

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
    public $regular_price, $sale_price, $image, $gallery = [], $tags = '';
    public $categories = []; 
    public $selectedCategories = [];

    public $selectedProducts = [];
    public $selectAll = false;

    public $bulkCategory = null;

    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('wp_products', 'slug')->ignore($this->productId)
            ],
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'regular_price' => 'nullable|numeric',
            'sale_price' => [
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
           
                    if ($value > 0 && $this->regular_price > 0 && $value > $this->regular_price) {
                        session()->flash('status', 'Giá giảm phải nhỏ hơn giá bình thường.');
                        $fail('Giá giảm phải nhỏ hơn giá bình thường.');
                    }
                }
            ],
        ];
    }
    
    protected $messages = [
        'title.required' => 'Vui lòng nhập tên sản phẩm',
    ];

    public function render()
    {
        $this->categories = Category::with('children')
        ->where('id', 4)              // lấy chính nó
        ->orWhere('parent_id', 4)     // hoặc các con của nó
        ->get();

        //dd($this->categories);
        return view('livewire.products.product-manager', [
            'products'   => WpProduct::with('categories')
                ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->dispatch('setHeader', 'Thêm sản phẩm');
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
        $this->tags = (is_array($product->tags) && !empty($product->tags))
        ? implode(';', $product->tags)
        : '';
        $this->selectedCategories = $product->categories->pluck('id')->toArray();
        $this->dispatch('setHeader', 'Chỉnh sửa sản phẩm');
        $this->showForm = true;
        // Gửi dữ liệu xuống để đổ vào Summernote
        $this->dispatch('setDescription', [
            'short_description' => $this->short_description,
            'description' => $this->description,
        ]);
    }

    public function save()
    {
        $this->sale_price = $this->sale_price ?: 0;
        $this->regular_price = $this->regular_price ?: 0;
        $this->slug = $this->slug ?: Str::slug($this->title);
        $data = $this->validate();

        if($this->image == null) {
            $data['image'] = null;
        }
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
        
        if(count($this->gallery) > 0){
            $data['gallery'] = array_values(array_unique(array_merge(
                $this->gallery,            // ảnh mới upload (đã push từ updatedGalleryUpload)
                $data['gallery'] ?? []     // ảnh cũ trong DB
            )));
           
        }else{
            if($this->galleryUpload == null){
                $data['gallery'] = null;
            }
        }
        
        $this->tags = $this->tags 
        ? array_map('trim', explode(';', $this->tags)) 
        : [];
        $product = WpProduct::updateOrCreate(
            ['id' => $this->productId],
            array_merge($data, [
                'tags' => $this->tags,
            ])
        );

        $product->categories()->sync($this->selectedCategories);

        $this->resetForm();
        $this->showForm = false;
        $this->dispatch('setHeader', 'Danh sách các sản phẩm');
        $title= $data['title'] ;
        session()->flash('success', "Lưu  $title thành công.");
        $this->redirect('/products');  
    }


    public function delete($id)
    {
        $item = WpProduct::findOrFail($id);
        $title = $item['title'];
        $item->delete();
        session()->flash('success', "Đã xoá $title thành công.");
    }

    private function resetForm()
    {
        $this->reset([
            'productId','title','slug','short_description','description',
            'regular_price','sale_price','image','gallery','tags','selectedCategories'
        ]);
    }
    public function removeImage()
    {
        // Xóa file cũ trong storage nếu cần
        if($this->image && Storage::exists($this->image)){
            Storage::delete($this->image);
        }
        $this->image = null;
    }
    public function removeGallery($img)
    {
  
        // Nếu file tồn tại trong storage thì xóa
        if (Storage::disk('public')->exists($img)) {
            Storage::disk('public')->delete($img);
            
        }
    
        // Xóa khỏi mảng trong Livewire
        $this->gallery = array_values(array_filter($this->gallery, function ($item) use ($img) {
            return $item !== $img;
        }));
        
        //$this->galleryUpload = $this->gallery;
        //dd($this->galleryUpload);
        // Nếu bạn lưu gallery dưới dạng JSON trong DB thì nhớ update lại
       // $this->product->gallery = json_encode($this->gallery);
       // $this->product->save();
    }
    public function updatedImageUpload()
    {
        $this->image = null;

    }
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function clearSearch()
    {
        $this->reset('search');
        $this->resetPage();
    }

    public function cancel()
    {
        $this->resetForm();       // reset dữ liệu form
        $this->showForm = false;  // ẩn form/modal

        // cập nhật lại header và title về mặc định
        $this->dispatch('setHeader', 'Danh sách các sản phẩm');
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedProducts = WpProduct::with('categories')
                ->when($this->search, fn($q) =>
                    $q->where('title', 'like', "%{$this->search}%")
                )
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage)
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedProducts = [];
        }
    }


    public function deleteSelected()
    {
        if (!empty($this->selectedProducts)) {
            WpProduct::whereIn('id', $this->selectedProducts)->delete();
            $this->selectedProducts = [];
            $this->selectAll = false;
            $this->dispatch('setHeader', 'Danh sách các sản phẩm');            
            session()->flash('message', 'Đã xóa các sản phẩm đã chọn.');
        }
    }

    public function duplicate($id)
    {
        $product = WpProduct::with('categories')->findOrFail($id);

        // clone sản phẩm
        $newProduct = $product->replicate();   // copy toàn bộ attributes trừ id
        $newProduct->title = $product->title . ' (Bản sao)';
        $newProduct->slug  = $product->slug . '-' . time(); // slug phải unique
        $newProduct->push(); // lưu lại

        // copy quan hệ (ví dụ categories)
        $newProduct->categories()->sync($product->categories->pluck('id')->toArray());

        session()->flash('success', "Đã nhân bản sản phẩm '{$product->title}' thành công.");
    }

    
    public function updateCategorySelected()
    {
        if (!empty($this->selectedProducts) && $this->bulkCategory) {
            // lấy danh mục
            $category = Category::find($this->bulkCategory);

            if ($category) {
                foreach ($this->selectedProducts as $productId) {
                    $product = WpProduct::find($productId);
                    if ($product) {
                        // đồng bộ: xóa hết danh mục cũ, chỉ gán danh mục mới
                        $product->categories()->sync([$this->bulkCategory]);
                    }
                }

                $this->selectedProducts = [];
                $this->selectAll = false;
                $this->bulkCategory = null;

                session()->flash('success', 'Đã cập nhật danh mục cho các sản phẩm đã chọn.');
            }
        }
    }
}
 