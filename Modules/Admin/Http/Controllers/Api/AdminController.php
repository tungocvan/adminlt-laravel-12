<?php

namespace Modules\Admin\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    
    public function index()
    {
        return response()->json([
            'status' => 'success',            
        ]);
    }    
}
