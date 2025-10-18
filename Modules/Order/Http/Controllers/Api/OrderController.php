<?php

namespace Modules\Order\Http\Controllers\Api;

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
    
        $params = $request->only([
            'fields', 'search', 'email', 'status',
            'min_total', 'max_total',
            'date_from', 'date_to',
            'order_by', 'sort',
            'paginate', 'cache'
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

    public function updateItem(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'index' => 'required|integer|min:0', // vị trí sản phẩm trong mảng
            'quantity' => 'required|integer|min:1',
        ]);

        $order = Order::find($request->order_id);
        $details = $order->order_detail;

        if (!isset($details[$request->index])) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

        // Cập nhật số lượng và total
        $details[$request->index]['quantity'] = $request->quantity;
        $details[$request->index]['total'] = $details[$request->index]['price'] * $request->quantity;

        $order->order_detail = $details;
        $order->total = array_sum(array_column($details, 'total'));
        $order->save();

        return response()->json([
            'message' => 'Cập nhật thành công',
            'order' => $order,
        ]);
    }
    
    public function removeItem(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'index' => 'required|integer|min:0',
        ]);

        $order = Order::find($request->order_id);
        $details = $order->order_detail;

        if (!isset($details[$request->index])) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

        // Xóa sản phẩm
        array_splice($details, $request->index, 1);

        $order->order_detail = $details;
        $order->total = array_sum(array_column($details, 'total'));
        $order->save();

        return response()->json([
            'message' => 'Xóa sản phẩm thành công',
            'order' => $order,
        ]);
    }

}
