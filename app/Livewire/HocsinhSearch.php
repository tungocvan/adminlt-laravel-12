<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Hocsinh;

class HocsinhSearch extends Component
{
    public $keyword;
    public $student;
    public $searched = false;

    
    protected $rules = [
        'keyword' => 'required|digits:12',
    ];

    protected $messages = [
        'keyword.required' => 'Vui lòng nhập mã định danh học sinh',
        'keyword.digits'   => 'Mã định danh phải gồm đúng 12 chữ số',
    ];

    public function search()
    {
        $this->validate();

        $this->student = Hocsinh::where('ma_dinh_danh_hoc_sinh', $this->keyword)->first();

        $this->searched = true;
    }

    public function render()
    {
        return view('livewire.hocsinh-search');
    }
}
