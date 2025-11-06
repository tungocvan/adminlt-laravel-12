<?php

namespace Modules\Components\Http\Controllers\Api;
use App\Http\Controllers\Controller;


class ComponentsController extends Controller
{
   public function index()
    {
        return response()->json([
            'status' => 'Api Components success',            
        ]);
    }  

}
