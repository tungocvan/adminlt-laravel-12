<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Term;
use App\Models\TermTaxonomy;
use Illuminate\Support\Str;

class CategoriesManager extends Component
{
    use WithPagination;

    public $name, $slug, $description, $parent_id, $editingId, $termId;
    public $search = '';
    public $bulkAction = '';
    public $selected = [];
    public $selectAll = false;
    public $currentPageIds = [];

    protected $paginationTheme = 'bootstrap';

    public function toggleSelectAll()
    {
        if (count(array_intersect($this->selected, $this->currentPageIds)) < count($this->currentPageIds)) {
            $this->selected = array_unique(array_merge($this->selected, $this->currentPageIds));
        } else {
            $this->selected = array_diff($this->selected, $this->currentPageIds);
        }
    }

    public function applyBulkAction()
    {
        if ($this->bulkAction === 'delete' && count($this->selected) > 0) {
            $taxonomies = TermTaxonomy::whereIn('id', $this->selected)->get();

            foreach ($taxonomies as $taxonomy) {
                $termId = $taxonomy->term_id;
                $taxonomy->delete();

                $remaining = TermTaxonomy::where('term_id', $termId)->count();
                if ($remaining === 0) {
                    Term::destroy($termId);
                }
            }

            $this->selected = [];
            $this->bulkAction = '';
            session()->flash('message', 'Đã xóa danh mục được chọn.');
            $this->resetPage();
        }
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->currentPageIds : [];
    }

    public function updatedSearch($value)
    {
        //dd($value);
        $this->search = $value;
    }

    public function updatedSelected()
    {
        $this->selectAll = count(array_intersect($this->selected, $this->currentPageIds)) === count($this->currentPageIds);
    }

    public function render()
    {
        $query = TermTaxonomy::with(['term', 'parent.term'])
            ->where('taxonomy', 'category')
            ->when($this->search, fn($q) =>
                $q->whereHas('term', fn($q2) =>
                    $q2->where('name', 'like', '%' . $this->search . '%')
                )
            )
            ->join('terms', 'term_taxonomy.term_id', '=', 'terms.id')
            ->orderBy('terms.name')
            ->select('term_taxonomy.*');

        $categories = $query->paginate(10);
        $this->currentPageIds = $categories->pluck('id')->toArray();

        return view('livewire.admin.categories-manager', [
            'categories' => $categories,
            'allCategories' => TermTaxonomy::with('term')->where('taxonomy', 'category')->get(),
            'currentPageIds' => $this->currentPageIds,
        ]);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:term_taxonomy,id',
        ]);

        $exists = Term::where('name', $this->name)->exists();

        if ($exists) {
            $this->addError('name', 'Tên danh mục đã tồn tại.');
            return;
        }

        $slug = $this->slug ?: Str::slug($this->name);
        $originalSlug = $slug;
        $counter = 1;

        while (Term::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $term = new Term();
        $term->name = $this->name;
        $term->slug = $slug;
        $term->description = $this->description;
        $term->save();

        $taxonomy = new TermTaxonomy();
        $taxonomy->term_id = $term->id;
        $taxonomy->taxonomy = 'category';
        $taxonomy->parent_id = $this->parent_id;
        $taxonomy->description = $this->description;
        $taxonomy->save();

        session()->flash('message', 'Thêm danh mục thành công.');
        $this->resetValidation();
        $this->resetFields();
        $this->resetPage();
    }

    public function saveUpdate()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:term_taxonomy,id',
        ]);

        $term = Term::findOrFail($this->termId);

        if ($term->name !== $this->name) {
            $exists = Term::where('name', $this->name)->where('id', '!=', $term->id)->exists();
            if ($exists) {
                $this->addError('name', 'Tên danh mục đã tồn tại.');
                return;
            }
        }

        $slug = $this->slug ?: Str::slug($this->name);
        $originalSlug = $slug;
        $counter = 1;

        while (Term::where('slug', $slug)->where('id', '!=', $term->id)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $term->name = $this->name;
        $term->slug = $slug;
        $term->description = $this->description;
        $term->save();

        $taxonomy = TermTaxonomy::findOrFail($this->editingId);
        $taxonomy->parent_id = $this->parent_id;
        $taxonomy->description = $this->description;
        $taxonomy->save();

        session()->flash('message', 'Cập nhật danh mục thành công.');
        $this->resetValidation();
        $this->resetFields();
        $this->resetPage();
    }

    public function edit($id)
    {
        $taxonomy = TermTaxonomy::with('term')->findOrFail($id);

        $this->editingId = $taxonomy->id;
        $this->termId = $taxonomy->term_id;
        $this->name = $taxonomy->term->name;
        $this->slug = $taxonomy->term->slug;
        $this->description = $taxonomy->description;
        $this->parent_id = $taxonomy->parent_id;
    }

    public function delete($id)
    {
        $taxonomy = TermTaxonomy::findOrFail($id);
        $termId = $taxonomy->term_id;
        $taxonomy->delete();

        $remaining = TermTaxonomy::where('term_id', $termId)->count();
        if ($remaining === 0) {
            Term::destroy($termId);
        }

        session()->flash('message', 'Đã xóa danh mục.');
        $this->resetPage();
    }

    public function resetFields()
    {
        $this->editingId = null;
        $this->termId = null;
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->parent_id = null;
    }
}
