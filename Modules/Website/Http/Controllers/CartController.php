<?php

namespace Modules\Website\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Hiển thị trang giỏ hàng.
     * 
     * GET /website/cart
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        return view('Website::cart.index');
    }
}