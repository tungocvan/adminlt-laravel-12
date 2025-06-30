<?php

namespace App\Livewire\Products;

use Livewire\Component;
use App\Models\WpPost; 
use App\Models\WpPostMeta; 
use Illuminate\Support\Facades\DB;

class ListProduct extends Component
{
    public $posts;

    public function getProducts(){
        
        //$posts = DB::table('wp_posts')->where('post_parent', 0)->get();
        //dd($posts);     
        
        $posts = WpPost::where('post_parent',0)->get();    
        //dd($posts);
        $postsId = [];
        $images = [];$garlley = []; $postMeta = [];
        foreach ($posts as $key => $value) {
            $id = $value['ID'];
            array_push($postsId,$id);
            $images = WpPost::select('guid')->where('post_parent',$id)->get();
            
            foreach ($images as $x => $image) {
                $garlley[] = $image['guid'];                
            }       
            $posts[$key]['garlley'] = serialize($garlley);

            $postMeta = WpPostMeta::where('post_id', $id)->get();
            foreach ($postMeta as  $meta) {
                if($meta['meta_key'] === '_regular_price'){
                    $posts[$key]['_regular_price'] = $meta['meta_value'];
                }       
                if($meta['meta_key'] === '_price'){
                    $posts[$key]['_price'] = $meta['meta_value'];
                }       
                if($meta['meta_key'] === '_categories'){
                    $posts[$key]['_categories'] = $meta['meta_value'];
                }       
            }              
        }
        return $posts;
    }

    public function mount()
    {
        $this->posts = $this->getProducts();    
        //dd($this->posts);
    }
    public function render()
    {
        return view('livewire.products.list-product');
    }
}
