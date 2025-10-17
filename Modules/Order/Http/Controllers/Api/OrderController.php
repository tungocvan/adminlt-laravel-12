<?php

namespace Modules\Order\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\Models\Order;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $orders = Order::latest()->paginate(10);
        return $orders;
    }
    
}
