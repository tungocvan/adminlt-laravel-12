<?php

namespace Modules\User\Livewire;


use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class UserManager extends Component
{
    use WithPagination;
    public $search = '';
     #[Url]
    public $perPage = 5;
     // Selection
    public $selected = [];
    public $selectAll = false;

    public $sortField = 'id';
    public $sortDirection = 'asc';
    
    public function render()
    {
        return view('User::livewire.user-manager',[
            'users' => $this->users,
        ]);
    }

    public function getUsersProperty()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('username', 'like', "%{$this->search}%");
            });
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return $query
            ->paginate($this->perPage)
            ->appends(['search' => $this->search]);
     
    }

    public function toggleSelectAll()
    {       
        $this->selected = $this->selectAll ? $this->users->pluck('id')->toArray() : [];
    }
    public function updatedSelected()
    {
        // xử lý riêng biến Selected
    }
    public function updated()
    {
        // xử lý chung các biến 
    }
    public function edit($id)
    {
        // xử lý edit $id 
        dd($id);
    }
    public function delete($id)
    {
        // xử lý delete $id
        dd($id);
    }
    public function approveUser($id)
    {
        // xử lý delete $id
        dd($id);
    }
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        // $this->setPage(1);
        // $this->message !== null && ($this->message = null);
    }
    public function openModal()
    {
        dd('openModal new/edit');
        // bắn sự kiện về để mở modal
    }
    public function deleteSelected()
    {
        dd($this->selected);
        // bắn sự kiện về để mở modal
    }
} 