<?php

namespace Modules\Website\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebsiteController extends Controller
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
        return view('Website::hamada');
    }

    
}
