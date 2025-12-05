<?php

namespace Modules\File\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
         $this->middleware('permission:file-list|file-create|file-edit|file-delete', ['only' => ['index','show']]);
         $this->middleware('permission:file-create', ['only' => ['create','store']]);
         $this->middleware('permission:file-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:file-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        return view('File::file');
    }
    public function jsonExcel()
    {
        return view('File::json-excel');
    }
    public function dbExcel()
    {
        return view('File::db-excel');
    }
    public function migrations()
    {
        return view('File::migrations');
    }
    public function artisan()
    {
        return view('File::artisan');
    }
    public function env()
    {
        return view('File::env');
    }
    public function lichvannien()
    {
        return view('File::lichvannien');
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
