<?php

namespace Modules\Categories\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
         $this->middleware('permission:categories-list|categories-create|categories-edit|categories-delete', ['only' => ['index','show']]);
         $this->middleware('permission:categories-create', ['only' => ['create','store']]);
         $this->middleware('permission:categories-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:categories-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        return view('Categories::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Categories::create');
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
