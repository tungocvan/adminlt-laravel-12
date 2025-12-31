<?php

namespace Modules\Website\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        return view('Website::index');
    }
    public function show(string $slug)
    {
        
        return view('Website::show', ['slug' => $slug]);
    }
    public function productDetail($id)
    {
        dd('productDetail:' . $id);
        //return view('Website::hamada');
    }
}
