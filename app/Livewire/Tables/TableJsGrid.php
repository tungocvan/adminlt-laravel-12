<?php

namespace App\Livewire\Tables;

use Livewire\Component;
use App\Models\Client;
use App\Models\Countries;

class TableJsGrid extends Component
{
    public $db;
    public $country;
    public $search = '';

    public function mount()
    {
        $this->db = Client::all();        
        $this->country = Countries::all()->toArray();
        //dd($this->country);
        $item = [
            'id' => 0,
            'name' => 'All'
        ];
        array_unshift($this->country,$item);
    }

    public function addData($item){        
        dd($item);
        
    }
    public function deleteData($item){        
        dd($item);
        
    }
    public function updateData($item){        
        dd($item);
        
    }
    public function getCountry(){

        return response()->json(Countries::all());
    }
    public function loadData($filter = null)
    {
        dd($this->search);
        // dd($filter);
       // $query = Client::query();
       // $this->db = $query->get();
      // $this->db = Client::all();
       //return $this->db->json();
        
//         if($filter['name'] !==""  || $filter['address']!=="" || $filter['country_id']!==0) {
//             if(isset($filter['name'])) {
//                 $query->where('name', 'like', '%' . $filter['name'] . '%');
//             }
//             if(isset($filter['age'])) {
//                 $query->where('age', '=', $filter['age'] );
//             }
//             if(isset($filter['address'])) {
//                 $query->where('address', 'like', '%' . $filter['address'] . '%');
//             }
//             if($filter['country_id']!==0) {
//                 $query->where('country_id', '=', $filter['country_id'] );
//             }
//             // Thêm điều kiện lọc cho các trường khác nếu cần
//             $this->db = $query->get();
//             //dd($this->db);
//         }else{
//             $this->db = Client::all();  
// //            dd($this->db);
//         }
        //dd($this->db);

        // $query = Client::query();
        // if($this->search!=='') {
        //     $query->where('name', 'like', '%' . $this->search . '%');
        //     $this->db = $query->get();
        // }else{
        //     $this->db = Client::all(); 
        // }

        return $this->db;
    }


    public function importExcel($data)
    {
        // Process the imported data
        dd($data);
        foreach ($data as $row) {
            // $row is an array representing a single row from the Excel sheet
        }
        // You might want to store it in a database or perform other actions
    }

    public function render()
    {
        return view('livewire.tables.table-js-grid');
    }
}
