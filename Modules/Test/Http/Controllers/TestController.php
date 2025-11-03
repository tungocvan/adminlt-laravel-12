<?php

namespace Modules\Test\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
         $this->middleware('permission:test-list|test-create|test-edit|test-delete', ['only' => ['index','show']]);
         $this->middleware('permission:test-create', ['only' => ['create','store']]);
         $this->middleware('permission:test-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:test-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        return view('Test::test');
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
