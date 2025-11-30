<?php

namespace Modules\Customer\Http\Controllers\Api;
use App\Http\Controllers\Controller;


class CustomerController extends Controller
{
   public function index()
    {
        return response()->json([
            'status' => 'Api Customer success',            
        ]);
    }  

}
