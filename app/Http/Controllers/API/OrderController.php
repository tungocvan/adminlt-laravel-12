<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\TnvOrderHelper;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * POST /api/orders/list
     * Lấy danh sách đơn hàng (lọc, tìm kiếm, phân trang)
     */
    public function list(Request $request)
    {
        \Log::info('Request all:', $request->all());
        $params = $request->only([
            'fields', 'search', 'email', 'status',
            'min_total', 'max_total',
            'date_from', 'date_to',
            'order_by', 'sort',
            'paginate', 'cache',
            'is_admin' 
        ]);
        

        $orders = TnvOrderHelper::getOrders($params);

        return response()->json($orders);
    }

    /**
     * GET /api/orders/{id}
     * Xem chi tiết đơn hàng
     */
    public function show($id)
    {
        $order = Order::with('user')->find($id);

        if (!$order) {
            return response()->json(['message' => 'Đơn hàng không tồn tại.'], 404);
        }

        return response()->json($order);
    }

    /**
     * POST /api/orders
     * Tạo đơn hàng mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'order_detail' => 'required|array',
            'total' => 'required|numeric|min:0',
            'status' => 'nullable|string|max:50',
        ]);

        $order = Order::create($validated);

        return response()->json([
            'message' => 'Tạo đơn hàng thành công.',
            'order' => $order,
        ], 201);
    }

    /**
     * PUT /api/orders/{id}
     * Cập nhật đơn hàng
     */
    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Đơn hàng không tồn tại.'], 404);
        }

        $validated = $request->validate([
            'status' => 'nullable|string|max:50',
            'total' => 'nullable|numeric|min:0',
            'order_detail' => 'nullable|array',
        ]);

        $order->update($validated);

        return response()->json([
            'message' => 'Cập nhật đơn hàng thành công.',
            'order' => $order,
        ]);
    }

    /**
     * DELETE /api/orders/{id}
     */
    public function destroy($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Đơn hàng không tồn tại.'], 404);
        }

        $order->delete();

        return response()->json(['message' => 'Đã xóa đơn hàng thành công.']);
    }
}
