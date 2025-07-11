<?php

namespace Modules\Products\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        return view('Products::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Products::create');
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
    public function edit(string $id)
    {
        //
    }

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
