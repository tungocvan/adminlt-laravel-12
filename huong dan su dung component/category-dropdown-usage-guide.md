# 📘 Hướng dẫn sử dụng CategoryDropdown trong component cha

Giả sử bạn có component cha (ví dụ: `ProductForm`, `PostEditor`, `MedicineManager`...)  
và muốn sử dụng component con:

```blade
<livewire:category-dropdown 
    :categories="$categories" 
    wire:model="selectedCategories"
    applyMethod="applySelectedCategory"
/>
```

---

## 🧱 Bước 1. Khai báo biến trong component cha

Trong class Livewire cha (ví dụ `app/Livewire/ProductForm.php`):

```php
use App\Models\Category;

class ProductForm extends Component
{
    public $categories = [];
    public $selectedCategories = [];

    public function mount()
    {
        // Lấy danh sách danh mục (bao gồm cả con nhiều cấp)
        $this->categories = Category::with('childrenRecursive')
            ->whereNull('parent_id')
            ->get();
    }

    public function applySelectedCategory($selected)
    {
        // $selected là mảng ID các danh mục được chọn
        $this->selectedCategories = $selected;

        // Ở đây bạn có thể xử lý tùy theo logic của mình
        // ví dụ: cập nhật danh mục cho product đang chỉnh sửa
        // Product::find($this->productId)->categories()->sync($selected);

        session()->flash('message', 'Đã áp dụng ' . count($selected) . ' danh mục!');
    }

    public function render()
    {
        return view('livewire.product-form');
    }
}
```

---

## 🧩 Bước 2. Gọi trong Blade của component cha

```blade
<!-- resources/views/livewire/product-form.blade.php -->
<div>
    <h4>Chọn danh mục cho sản phẩm</h4>

    <livewire:category-dropdown 
        :categories="$categories" 
        wire:model="selectedCategories"
        applyMethod="applySelectedCategory"
    />

    <p class="mt-3">
        Danh mục đã chọn: 
        <strong>{{ implode(', ', $selectedCategories) }}</strong>
    </p>
</div>
```

---

## 🧠 Giải thích

| Thành phần | Vai trò |
|-------------|----------|
| `$categories` | Danh sách danh mục (có childrenRecursive) truyền vào component con |
| `$selectedCategories` | Biến ràng buộc `wire:model` để đồng bộ danh mục được chọn |
| `applyMethod="applySelectedCategory"` | Hàm trong component cha được gọi khi người dùng nhấn nút **Áp dụng danh mục** |

---

## ✅ Kết quả

- Khi người dùng chọn các danh mục và nhấn **Áp dụng**,  
  Livewire sẽ gọi hàm `applySelectedCategory($selected)` trong component cha.
- Biến `$selectedCategories` được cập nhật tự động.  
- Bạn có thể tiếp tục xử lý (gán vào sản phẩm, bài viết, v.v...).

---

📄 *Tóm tắt:*  
Chỉ cần:
1. Khai báo `$categories` và `$selectedCategories` trong component cha.  
2. Có hàm `applySelectedCategory($selected)` để nhận dữ liệu từ dropdown.  
3. Gọi `<livewire:category-dropdown ... />` trong view.
