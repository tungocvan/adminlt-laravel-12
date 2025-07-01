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

    public $name, $slug, $description, $parent_id, $editingId;
    public $search = '';
    public $bulkAction = '';
    public $selected = [];
    public $selectAll = false;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'refreshComponent' => '$refresh',
    ];

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = TermTaxonomy::where('taxonomy', 'category')->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected()
    {
        // Nếu người dùng bỏ chọn 1 checkbox thì bỏ luôn selectAll nếu đang được chọn
        if (count($this->selected) !== TermTaxonomy::where('taxonomy', 'category')->count()) {
            $this->selectAll = false;
        }
    }

    public function applyBulkAction()
    {
        if ($this->bulkAction === 'delete' && count($this->selected) > 0) {
            TermTaxonomy::whereIn('id', $this->selected)->delete();
            // Có thể bạn muốn xóa Term tương ứng nếu không còn liên kết
            // Term::whereIn('id', $termsIds)->delete();

            $this->selected = [];
            $this->selectAll = false;
            $this->bulkAction = '';

            session()->flash('message', 'Đã xóa danh mục được chọn.');
        }
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
            ->select('term_taxonomy.*'); // quan trọng để tránh lỗi khi join

            

        $categories = $query->paginate(10);

        return view('livewire.admin.categories-manager', [
            'categories' => $categories,
            'allCategories' => TermTaxonomy::with('term')->where('taxonomy', 'category')->get(),
        ]);
    }


    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:term_taxonomy,id',
        ]);

        $slug = $this->slug ?: Str::slug($this->name);
        $originalSlug = $slug;
        $counter = 1;

        $termIdToExclude = 0;
        if ($this->editingId) {
            $taxonomy = TermTaxonomy::find($this->editingId);
            $termIdToExclude = $taxonomy->term_id ?? 0;
        }

        while (Term::where('slug', $slug)
            ->where('id', '!=', $termIdToExclude)
            ->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        if ($this->editingId) {
            $taxonomy = TermTaxonomy::findOrFail($this->editingId);
            $term = $taxonomy->term;
        } else {
            $term = new Term();
        }

        $term->name = $this->name;
        $term->slug = $slug;
        $term->description = $this->description;
        $term->save();

        if (!$this->editingId) {
            $taxonomy = new TermTaxonomy();
            $taxonomy->term_id = $term->id;
            $taxonomy->taxonomy = 'category';
        }

        $taxonomy->parent_id = $this->parent_id;
        $taxonomy->description = $this->description;
        $taxonomy->save();

        session()->flash('message', 'Lưu danh mục thành công.');

        $this->resetFields();
        $this->resetPage();
    }

    public function edit($id)
    {
        $taxonomy = TermTaxonomy::findOrFail($id);
        $this->editingId = $taxonomy->id;
        $this->name = $taxonomy->term->name;
        $this->slug = $taxonomy->term->slug;
        $this->description = $taxonomy->description;
        $this->parent_id = $taxonomy->parent_id;
    }

    public function delete($id)
    {
        TermTaxonomy::destroy($id);
        session()->flash('message', 'Đã xóa danh mục.');
        $this->resetPage();
    }

    public function resetFields()
    {
        $this->editingId = null;
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->parent_id = null;
    }
}
