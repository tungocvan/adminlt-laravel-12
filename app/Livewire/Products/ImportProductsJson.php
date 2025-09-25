<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Category;
use App\Models\WpProduct;
use Illuminate\Support\Str;

class ImportProductsJson extends Component
{
    use WithFileUploads;

    public $file;
    public $skippedCategories = [];
    public $skippedProducts = [];

    public function importJson()
    {
        $this->validate([
            'file' => 'required|mimes:json',
        ]);

        $path = $this->file->getRealPath();
        $content = file_get_contents($path);
        $data = json_decode($content, true);

        if (!$data || !isset($data['categories']) || !isset($data['products'])) {
            session()->flash('error', 'File JSON không hợp lệ!');
            return;
        }

        $this->skippedCategories = [];
        $this->skippedProducts = [];

        // 1. Import Categories
        foreach ($data['categories'] as $cat) {
            $exists = Category::where('slug', $cat['slug'])->exists();

            if ($exists) {
                $this->skippedCategories[] = $cat['name'];
            } else {
                Category::create([
                    'name' => $cat['name'],
                    'slug' => $cat['slug'],
                    'type' => $cat['type'] ?? 'san-pham',
                    'is_active' => $cat['is_active'] ?? 1,
                ]);
            }
        }

        // 2. Import Products
        foreach ($data['products'] as $prod) {
            $exists = WpProduct::where('slug', $prod['slug'])
                ->orWhere('title', $prod['title'])
                ->exists();

            if ($exists) {
                $this->skippedProducts[] = $prod['title'];
            } else {
                $product = WpProduct::create([
                    'title' => $prod['title'],
                    'slug' => Str::slug($prod['slug']),
                    'short_description' => $prod['short_description'] ?? null,
                    'description' => $prod['description'] ?? null,
                    'regular_price' => $prod['regular_price'] ?? 0,
                    'sale_price' => $prod['sale_price'] ?? 0,
                    'image' => $prod['image'] ?? null,
                    'gallery' => $prod['gallery'] ?? [],
                    'tags' => $prod['tags'] ?? [],
                ]);

                // Gắn categories
                if (!empty($prod['categories'])) {
                    $product->categories()->syncWithoutDetaching($prod['categories']);
                }
            }
        }

        session()->flash('message', 'Import JSON hoàn tất!');
    }

    public function render()
    {
        return view('livewire.products.import-products-json');
    }
}
