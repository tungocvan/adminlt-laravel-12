<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\TnvProductHelper;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;

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
    
    public function orderStore(Request $request)
    {
        dd($request->all());
        // ✅ 1. Xác thực dữ liệu
        $request->validate([
            'email' => 'required|email',
            'orderDetail' => 'required|array',
            'orderDetail.*.product_id' => 'required|numeric',
            // 'orderDetail.*.title' => 'nullable|string', // đổi từ required -> nullable
            'orderDetail.*.price' => 'required|numeric',
            'orderDetail.*.quantity' => 'required|numeric',
            'orderDetail.*.total' => 'required|numeric',
            'total' => 'required|numeric',
        ]);
        

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // ✅ 2. Tạo đơn hàng
            $order = Order::create([
                'email' => $request->email,
                'order_detail' => $request->orderDetail,
                'total' => $request->total,
                'status' => 'pending',
            ]);

            // ✅ 3. Trả về JSON phản hồi
            return response()->json([
                'status' => true,
                'message' => 'Đơn hàng đã được tạo thành công!',
                'order_id' => $order->id,
                'order' => $order,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi lưu đơn hàng: ' . $e->getMessage(),
            ], 500);
        }
    }

}
