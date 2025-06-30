<?php

namespace Modules\Products\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Products\Models\Products;
use App\Models\WpPostMeta;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
         $this->middleware('permission:products-list|products-create|products-edit|products-delete', ['only' => ['index','show']]);
         $this->middleware('permission:products-create', ['only' => ['create','store']]);
         $this->middleware('permission:products-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:products-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        return view('Products::products');
    }
    public function addProduct()
    {
        return view('Products::add');
    }
    public function categories()
    {
        return view('Products::categories');
    }
    public function edit($id)
    {
        //dd($id);    
        //$product = Products::find($id);
        // dd($product);
        return view('Products::edit',compact('id'));
    }
    public function delete($id)
    {
        
       // $product = Products::find($id);
        $productMeta = WpPostMeta::where('post_id', $id)->delete();
        $productImages = Products::where('post_parent',$id)->delete();
        $product = Products::where('ID', $id)->delete();        
        //dd($product);
        //$product->delete();
        return redirect('/admin/products');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
  

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
