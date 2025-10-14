<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\TnvProductHelper;

class ProductController extends Controller
{
    /**
     * Lấy danh sách sản phẩm (có thể lọc, tìm kiếm, sắp xếp)
     */

     public function filter(Request $request)
    {
        $products = TnvProductHelper::getProducts([
            'fields'      => $request->input('fields'),
            'search'      => $request->input('search'),
            'category_id' => $request->input('category_id'),
            'min_price'   => $request->input('min_price'),
            'max_price'   => $request->input('max_price'),
            'order_by'    => $request->input('order_by'),
            'sort'        => $request->input('sort'),
            'paginate'    => $request->input('paginate', 20),
            'cache'       => $request->input('cache', 0),
        ]);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
    /**
     * Lấy chi tiết 1 sản phẩm theo id hoặc slug
     */
    public function show($id)
    {
        $product = \App\Models\WpProduct::with('categories')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }
}
