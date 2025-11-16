- import số lượng tồn
php artisan import:medicine-stocks
php artisan orderstock:create --stock='[{"medicine_id":21,"so_lo":"091125","han_dung":"2027-11-09","so_luong":2000}]'

- tạo đơn hàng:
php artisan order:create {user_id} {customer_id}  {--products=}'[...]'; // dạng JSON

php artisan order:create 1 2 --products=='[{ "product_id": 21, "quantity": 10 },{ "product_id": 15, "quantity": 10 }]'

<x-components::tnv-categories wire:model.live="selectedCategory"  />

<livewire:categories.categories 
    slug="nhom-thuoc"
    :selectedCategories="$selectedCategories"
    render="tree" 
/>
render="tree hoặc dropdown" 

<livewire:category-dropdown :categories="$categories"  width="100%"
    wire:model.live="selectedCategories" 
    :selected="$selectedCategories" 
/>