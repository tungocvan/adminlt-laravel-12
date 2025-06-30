<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class ProductCategory extends Component
{
    public $name;
    public $parentId = null;
    public $editingId = null;
    public $showForm = false;
    public $expanded = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'parentId' => 'nullable|integer|exists:wp_terms,term_id',
    ];

    public function mount()
    {
        $this->expanded = [];
    }

    public function showCreateForm()
    {
        $this->resetForm();
        $this->showForm = !$this->showForm;
    }
    // Thêm vào class ProductCategory
    public function toggleExpand($categoryId)
    {
        $this->expanded[$categoryId] = !($this->expanded[$categoryId] ?? false);
    }
    public function createCategory()
    {
        $this->validate();

        DB::transaction(function () {
            if ($this->editingId) {
                // Update existing category
                DB::table('wp_terms')
                    ->where('term_id', $this->editingId)
                    ->update([
                        'name' => $this->name,
                        'slug' => Str::slug($this->name),
                    ]);

                DB::table('wp_term_taxonomy')
                    ->where('term_id', $this->editingId)
                    ->update([
                        'parent' => $this->parentId ?: 0,
                    ]);
            } else {
                // Create new category
                $termId = DB::table('wp_terms')->insertGetId([
                    'name' => $this->name,
                    'slug' => Str::slug($this->name),
                ]);

                DB::table('wp_term_taxonomy')->insert([
                    'term_id' => $termId,
                    'taxonomy' => 'product_cat',
                    'description' => '',
                    'parent' => $this->parentId ?: 0,
                    'count' => 0,
                ]);
            }
        });

        $this->resetForm();
        session()->flash('message', $this->editingId ? 'Category updated successfully!' : 'Category created successfully!');
        $this->dispatch('category-updated');
    }

    public function editCategory($id)
    {
        $category = DB::table('wp_terms')
            ->join('wp_term_taxonomy', 'wp_terms.term_id', '=', 'wp_term_taxonomy.term_id')
            ->where('wp_terms.term_id', $id)
            ->select('wp_terms.term_id', 'wp_terms.name', 'wp_term_taxonomy.parent')
            ->first();

        $this->editingId = $category->term_id;
        $this->name = $category->name;
        $this->parentId = $category->parent;
        $this->showForm = true;
    }

    public function deleteCategory($id)
    {
        DB::transaction(function () use ($id) {
            DB::table('wp_term_taxonomy')->where('term_id', $id)->delete();
            DB::table('wp_terms')->where('term_id', $id)->delete();
        });

        session()->flash('message', 'Category deleted successfully!');
        $this->dispatch('category-updated');
    }

    public function resetForm()
    {
        $this->reset(['name', 'parentId', 'editingId']);
        $this->resetErrorBag();
    }

    public function getParentOptions()
    {
        $categories = $this->getFlatCategories();
        $options = collect();
        
        $this->buildOptions($categories, $options);
        
        return $options;
    }

    protected function getFlatCategories()
    {
        return DB::table('wp_terms')
            ->join('wp_term_taxonomy', 'wp_terms.term_id', '=', 'wp_term_taxonomy.term_id')
            ->where('wp_term_taxonomy.taxonomy', 'product_cat')
            ->select('wp_terms.term_id', 'wp_terms.name', 'wp_term_taxonomy.parent')
            ->get()
            ->map(function ($item) {
                $item->level = $this->calculateLevel($item->term_id);
                return $item;
            });
    }

    protected function calculateLevel($termId, $level = 0)
    {
        $parent = DB::table('wp_term_taxonomy')
            ->where('term_id', $termId)
            ->value('parent');

        return $parent ? $this->calculateLevel($parent, $level + 1) : $level;
    }

    protected function buildOptions($categories, &$options, $parent = 0, $level = 0)
    {
        foreach ($categories as $category) {
            if ($category->parent == $parent && $category->term_id != $this->editingId) {
                $category->level = $level;
                $options->push($category);
                $this->buildOptions($categories, $options, $category->term_id, $level + 1);
            }
        }
    }

    public function getHierarchicalCategories()
    {
        $categories = $this->getFlatCategories();
        return $this->buildTree($categories);
    }

    protected function buildTree($categories, $parent = 0)
    {
        $tree = collect();

        foreach ($categories as $category) {
            if ($category->parent == $parent) {
                $children = $this->buildTree($categories, $category->term_id);
                $category->children = $children;
                $tree->push($category);
            }
        }

        return $tree;
    }

    public function render()
    {
        return view('livewire.products.product-category', [
            'categories' => $this->getHierarchicalCategories()
        ]);
    }
}