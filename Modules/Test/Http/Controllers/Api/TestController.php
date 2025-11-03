<?php

namespace Modules\Test\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Modules\Order\Models\Order;

class TestController extends Controller
{
   public function index()
    {
        //$data = Order::where('id',3)->get();
        $data = Order::with('user')->find(3);
        return response()->json([
            'status' => 'Api Test success',    
            'data' => $data    
        ]);
    }  

}
