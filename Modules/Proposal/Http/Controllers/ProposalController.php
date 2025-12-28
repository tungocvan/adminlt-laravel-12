<?php

namespace Modules\Proposal\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Proposal\Models\Proposal;

class ProposalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        //  $this->middleware('permission:proposal-list|proposal-create|proposal-edit|proposal-delete', ['only' => ['index','show']]);
        //  $this->middleware('permission:proposal-create', ['only' => ['create','store']]);
        //  $this->middleware('permission:proposal-edit', ['only' => ['edit','update']]);
        //  $this->middleware('permission:proposal-delete', ['only' => ['destroy']]);
    } 
    public function index()
    {
       
        return view('Proposal::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Proposal::create');
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
     public function show(Proposal $proposal)
    {
        return view('proposal::show', compact('proposal'));
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
