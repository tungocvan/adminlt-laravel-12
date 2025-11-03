<?php

namespace Modules\Qlhs\Http\Controllers\Api;
use App\Http\Controllers\Controller;


class QlhsController extends Controller
{
   public function index()
    {
        return response()->json([
            'status' => 'Api Qlhs success',            
        ]);
    }  

}
