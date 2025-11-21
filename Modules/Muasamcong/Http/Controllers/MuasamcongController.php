<?php

namespace Modules\Muasamcong\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MuasamcongController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
         $this->middleware('permission:muasamcong-list|muasamcong-create|muasamcong-edit|muasamcong-delete', ['only' => ['index','show']]);
         $this->middleware('permission:muasamcong-create', ['only' => ['create','store']]);
         $this->middleware('permission:muasamcong-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:muasamcong-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        return view('Muasamcong::muasamcong');
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
