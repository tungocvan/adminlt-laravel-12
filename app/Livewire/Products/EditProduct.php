<?php

namespace App\Livewire\Products;
use Modules\Products\Models\Products;
use App\Models\WpPostMeta; 
use App\Models\WpTerm;
use App\Models\WpTermTaxonomy;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class EditProduct extends Component
{
    public $product;
    public $selectedCategories = [];
    public $categoriesTree = [];
    public $tags = '';
    public function mount($id){        
         $this->product = Products::find($id);
         $postMeta = WpPostMeta::where('post_id', $id)->get();
         foreach ($postMeta as  $meta) {
             if($meta['meta_key'] === '_regular_price'){
                $this->product['_regular_price'] = $meta['meta_value'];
             }       
             if($meta['meta_key'] === '_price'){
                $this->product['_price'] = $meta['meta_value'];
             }       
             if($meta['meta_key'] === '_tags'){
                $this->tags = $meta['meta_value'];
             }       
             if($meta['meta_key'] === '_categories'){
                $this->selectedCategories = unserialize($meta['meta_value']);                
             }       
             if($meta['meta_key'] === '_thumbnail_id'){
                $this->product['_thumbnail_id'] = unserialize($meta['meta_value']);
             }       
         }   
         $allCategories = $this->getCategories();
         $this->categoriesTree = $this->buildTree($allCategories->toArray());  

      
    }
    
    public function render()
    {
        return view('livewire.products.edit-product');
    }

    public function getCategories()
    {
        return  DB::table('wp_terms')
        ->join('wp_term_taxonomy', 'wp_terms.term_id', '=', 'wp_term_taxonomy.term_id')
        ->where('wp_term_taxonomy.taxonomy', 'product_cat')
        ->select('wp_terms.term_id', 'wp_terms.name','wp_term_taxonomy.parent')
        ->get();
    }
    protected function buildTree(array $elements, $parentId = 0)
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element->parent == $parentId) {
                $children = $this->buildTree($elements, $element->term_id);
                if ($children) {
                    $element->children = $children; // Thêm danh mục con vào đối tượng
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    public function submit(){
        dd($this->product);
    }
}
