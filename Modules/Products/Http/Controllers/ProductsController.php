<?php

namespace Modules\Products\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

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
        // $product = Product::whereJsonContains('details->tags', 'money')->get();       
        // return view('Products::index');
        $products = Product::all();
        return view('Products::products', compact('products'));

    }
    public function addMore()
    {
        // $product = Product::whereJsonContains('details->tags', 'money')->get();       
        // return view('Products::index');
        $products = Product::paginate(10);
        return view('Products::addMore', compact('products'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $input = [
            'name' => 'Gold',
            'price' => 1000.00,
            'details' => [
                'brand' => 'Jewellery', 
                'tags' => ['gold', 'jewellery', 'money']
            ]
        ];

        $product = Product::create($input);
        return view('Products::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $rules = [ "name" => "required", "stocks.*" => "required" ];

        foreach($request->stocks as $key => $value) {
            $rules["stocks.{$key}.quantity"] = 'required';
            $rules["stocks.{$key}.price"] = 'required';
            
        }

        $request->validate($rules);
        $price = doubleval($request->stocks[0]['price']);
        
        $product = Product::create(["name" => $request->name,"price" => $price]);
        foreach($request->stocks as $key => $value) {
            $product->stocks()->create($value);
        }

        return redirect()->back()->with(['success' => 'Product created successfully.']);
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function thankyou()
    {
        
        return view('Products::thankyou');
    }    
    public function cart()
    {
        
        return view('Products::cart');
    }    
    public function addToCart($id)
    {
        //dd($id);
        $product = Product::findOrFail($id);
          
        $cart = session()->get('cart', []);
  
        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->image
            ];
        }
          
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function update(Request $request)
    {
        if($request->id & $request->quantity){
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
            session()->flash('success', 'Cart updated successfully');
        }
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            session()->flash('success', 'Product removed successfully');
        }
    }
    public function checkout(Request $request)
    {
        $cartItems = $request->input('cart');
    
        if (!$cartItems || !is_array($cartItems)) {
            return response()->json(['error' => 'Cart data invalid'], 400);
        }
    
        // Debug thử
        \Log::info('Checkout items:', $cartItems);
    
        return response()->json(['message' => 'Checkout thành công!']);
    }
    

}
