<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\File;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $json = File::get(database_path('seeders/data/categories.json'));
        $items = json_decode($json, true);

        $this->importCategories($items);
    }

    protected function importCategories(array $items, $parentId = null): void
    {
        foreach ($items as $item) {
            $children = $item['children'] ?? [];
            unset($item['children']);

            $category = Category::create(array_merge($item, [
                'parent_id' => $parentId,
            ]));

            if (!empty($children)) {
                $this->importCategories($children, $category->id);
            }
        }
    }
}
