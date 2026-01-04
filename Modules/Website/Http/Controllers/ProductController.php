<?php

namespace Modules\Website\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WpProduct;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        //  $this->middleware('permission:website-list|website-create|website-edit|website-delete', ['only' => ['index','show']]);
        //  $this->middleware('permission:website-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:website-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:website-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        //dd(1);
        //return view('Website::hamada');
        return view('Website::products.index');
    }
    public function show(string $slug)
    {
        
        // Kiểm tra sản phẩm tồn tại
        //$product = WpProduct::where('slug', $slug)->firstOrFail();        
        $product = WpProduct::where('slug', $slug)->firstOrFail();

        return view('Website::products.show', [
            'product' => $product,
            'slug' => $slug
        ]);
    }
    
    public function productDetail($id)
    {
        dd('productDetail:' . $id);
        //return view('Website::hamada');
    }
}
