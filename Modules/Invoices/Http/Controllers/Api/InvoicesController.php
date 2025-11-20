<?php

namespace Modules\Invoices\Http\Controllers\Api;
use App\Http\Controllers\Controller;


class InvoicesController extends Controller
{
   public function index()
    {
        return response()->json([
            'status' => 'Api Invoices success',            
        ]);
    }  

}
