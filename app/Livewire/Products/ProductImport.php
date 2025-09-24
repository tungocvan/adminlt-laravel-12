<?php

namespace App\Livewire\Products;


use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\WpProductsImport;

class ProductImport extends Component
{
    use WithFileUploads;

    public $file;

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // tối đa 10MB
        ]);

        try {
            Excel::import(new WpProductsImport, $this->file->getRealPath());

            session()->flash('success', 'Import sản phẩm thành công!');
        } catch (\Exception $e) {
            session()->flash('error', 'Lỗi import: ' . $e->getMessage());
        }

        $this->reset('file');
    }

    public function render()
    {
        return view('livewire.products.product-import');
    }
}
