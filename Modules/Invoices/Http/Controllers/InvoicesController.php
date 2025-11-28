<?php

namespace Modules\Invoices\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
         $this->middleware('permission:invoices-list|invoices-create|invoices-edit|invoices-delete', ['only' => ['index','show']]);
         $this->middleware('permission:invoices-create', ['only' => ['create','store']]);
         $this->middleware('permission:invoices-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:invoices-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        return view('Invoices::invoices');
    }
    public function hoadon()
    {
        return view('Invoices::hoadon');
    }
    public function hoadonList()
    {
        return view('Invoices::hoadon-list');
    }
    public function createToken()
    {
        return view('Invoices::create-token');
    }
    public function download($lookup_code)
    {
        $filePath = storage_path('app/hoadon_temp/' . $lookup_code . '.pdf');

        if(!file_exists($filePath)) {
            abort(404);
        }

        return response()->download($filePath);
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
