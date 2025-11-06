<?php

namespace Modules\Components\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComponentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
         $this->middleware('permission:components-list|components-create|components-edit|components-delete', ['only' => ['index','show']]);
         $this->middleware('permission:components-create', ['only' => ['create','store']]);
         $this->middleware('permission:components-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:components-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        return view('Components::components');
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
