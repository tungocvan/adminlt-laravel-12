<?php

namespace Modules\Menu\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
         $this->middleware('permission:menu-list|menu-create|menu-edit|menu-delete', ['only' => ['index','show']]);
         $this->middleware('permission:menu-create', ['only' => ['create','store']]);
         $this->middleware('permission:menu-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:menu-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        return view('Menu::menu');
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
