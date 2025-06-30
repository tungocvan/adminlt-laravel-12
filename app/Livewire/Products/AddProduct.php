<?php

namespace App\Livewire\Products;

use Livewire\Component;
use App\Models\WpPost;
use App\Models\WpPostMeta;
use App\Models\WpTerm;
use App\Models\WpTermTaxonomy;
use App\Models\WpTermRelationship; 
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AddProduct extends Component
{
    use WithFileUploads;

    public $name;   
    public $description;
    public $image;
    public $gallery = [];  
    public $categoriesTree = [];
    public $selectedCategories = [];
    public $regularPrice;
    public $salePrice;
    public $shortDescription;
    public $tags = '';


    protected $rules = [
        'name' => 'required|string',
        'description' => 'required|string',
        'image' => 'required|image',
        'regularPrice' => 'required|numeric|min:0',
        'salePrice' => 'nullable|numeric|lt:regularPrice',
        'shortDescription' => 'required|string',
        'selectedCategories' => 'required|array|min:1' 
    ];

    protected $messages = [
        'salePrice.lt' => 'The sale price must be less than regular price.',
    ];


    public function render()
    {
        $allCategories = $this->getCategories();
        //dd($allCategories->toArray());
        $this->categoriesTree = $this->buildTree($allCategories->toArray());       
        return view('livewire.products.add-product', [
            'allCategories' => $allCategories,
        ]);
    }

    public function submit()
    {
        
        $imagePath = null;
        // Upload main image
        if(isset($this->image)){
            $imagePath = $this->image->store('products', 'public');
            
        }
        
        //dd($imagePath);
        
        $product = [
            'post_author'      => 1,
            'post_date'        => Carbon::now(),
            'post_date_gmt'    => Carbon::now('UTC'),
            'post_content'     => $this->description ?? '',
            'post_title'       => $this->name,
            'post_excerpt'     => $this->shortDescription,
            'post_status'      => 'publish',
            'comment_status'   => 'open',
            'ping_status'      => 'open',
            'to_ping' => '',  // Thêm giá trị mặc định
            'pinged' => '',   // Thêm giá trị mặc định
            'post_content_filtered' => '', // Thêm giá trị mặc định
            'post_name'        => Str::slug($this->name),
            'post_modified'    => Carbon::now(),
            'post_modified_gmt' => Carbon::now('UTC'),
            'guid'             => $imagePath?$imagePath:'',
            'post_type'        => 'product',
        ];
        $this->validate($this->rules);
        // dd($product);
        //dd($this->gallery);
        // dd($this->categoriesTree);        
        //dd($this->selectedCategories);
        // Insert to wp_posts
        $postId = DB::table('wp_posts')->insertGetId($product);
        
        // Insert meta data (price, sale price, image)
        DB::table('wp_postmeta')->insert([
            ['post_id' => $postId, 'meta_key' => '_regular_price', 'meta_value' => $this->regularPrice],
            ['post_id' => $postId, 'meta_key' => '_price', 'meta_value' => $this->salePrice ?? $this->regularPrice],
            ['post_id' => $postId, 'meta_key' => '_categories', 'meta_value' => serialize($this->selectedCategories)],
            ['post_id' => $postId, 'meta_key' => '_tags', 'meta_value' => $this->tags],
        ]);

        // (Optional) Upload gallery images
        $this->processGalleryImages($postId);

        // Xử lý danh mục
        $this->saveTerms($postId, $this->selectedCategories);
        // Xử lý thẻ
        if (!empty($this->tags)) {
            //$this->saveTerms($postId, $this->tags, 'product_tag');
            $this->saveProductTags($postId, $this->tags);
        }
        // Thông báo thành công
        session()->flash('success', 'Product added successfully!');
        $this->reset();
    }

    protected function processGalleryImages($productId)
    {
        if (empty($this->gallery)) {
            return '';
        }
        // 'guid' => asset('storage/' . $imagePath),
        $galleryIds = [];$imageGallery = [];
        foreach ($this->gallery as $image) {
            $imagePath = $image->store('products/gallery', 'public');
            $imageGallery[]=$imagePath;
            $attachment = WpPost::create([
                'post_author' => auth()->id() ?? 1,
                'post_title' => $this->name . ' Gallery Image',
                'post_status' => 'inherit',
                'post_type' => 'attachment',
                'guid' => $imagePath,
                'post_mime_type' => $image->getMimeType(),
                'post_name' => 'product-' . $productId . '-gallery-' . count($galleryIds),
                'post_content' => '',
                'post_excerpt'     => '',
                'to_ping' => '',  // Thêm giá trị mặc định
                'pinged' => '',   // Thêm giá trị mặc định
                'post_content_filtered' => '', // Thêm giá trị mặc định
                'post_parent' => $productId, 
                'post_date' => now(),
                'post_date_gmt' => now(),
            ]);
            
            $galleryIds[] = $attachment->id;
            
        }

        DB::table('wp_postmeta')->insert([
            ['post_id' => $productId, 'meta_key' => '_thumbnail_id', 'meta_value' => serialize($imageGallery)],
        ]);

        return implode(',', $galleryIds);
    }

    protected function saveTerms($productId, $terms)
    {
        
        foreach ($terms as $term) {       
                // Gán terms cho sản phẩm
                $term_id = explode(":",$term);
                WpTermRelationship::create([
                    'object_id' => $productId,
                    'term_taxonomy_id' => $term_id[0],
                    'term_order' => 0,
                ]);
        }

    }


    protected function saveProductTags($productId, $tags)
    {
        $tagIds = [];
        $tagsArray = array_filter(array_map('trim', explode(',', $tags)));

        foreach ($tagsArray as $tag) {
            $tagSlug = Str::slug($tag);

            // Kiểm tra xem tag đã tồn tại chưa
            $existingTag = WpTerm::where('slug', $tagSlug)->first();

            if ($existingTag) {
                $tagId = $existingTag->term_id; // Sử dụng term_id
            } else {
                // Tạo tag mới
                $newTag = WpTerm::create([
                    'name' => $tag,
                    'slug' => $tagSlug,
                    'term_group' => 0,
                ]);

                // Kiểm tra nếu tag mới được tạo thành công
                if ($newTag) {
                    // Tạo term taxonomy cho tag
                   
                    $taxonomyCreated = WpTermTaxonomy::create([
                        'term_id' => $newTag->term_id, // Sử dụng term_id
                        'taxonomy' => 'product_tag',
                        'description' => '',
                        'parent' => 0,
                        'count' => 0,
                    ]);

                    if ($taxonomyCreated) {
                        $tagId = $newTag->term_id; // Sử dụng term_id
                    } else {
                        // Xử lý khi tạo taxonomy thất bại
                        session()->flash('error', 'Không thể tạo taxonomy cho tag: ' . $tag);
                        continue; // Bỏ qua tag này
                    }
                } else {
                    // Xử lý khi tạo tag thất bại
                    session()->flash('error', 'Không thể tạo tag: ' . $tag);
                    continue; // Bỏ qua tag này
                }
            }

            if (isset($tagId)) {
                $tagIds[] = $tagId;
            }
        }

        // Gán tags cho sản phẩm
        foreach ($tagIds as $tagId) {
            WpTermRelationship::create([
                'object_id' => $productId,
                'term_taxonomy_id' => $tagId,
                'term_order' => 0,
            ]);
        }
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

    public function hanlderEditor($id,$data){
        //dd($id,$data);
        if($id === "description"){
            $this->description = $data;
        }
        if($id === "shortDescription"){
            $this->shortDescription = $data;
        }
    }

}