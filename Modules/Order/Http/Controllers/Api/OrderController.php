<?php

namespace Modules\Order\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\TnvOrderHelper;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;

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
            'paginate', 'cache','is_admin'
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
       
        // ✅ 1. Xác thực dữ liệu
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'email' => 'required|email',
            'orderDetail' => 'required|array|min:1',
            'orderDetail.*.product_id' => 'required|integer',           
            'orderDetail.*.price' => 'required|numeric|min:0',
            'orderDetail.*.quantity' => 'required|integer|min:1',
            'orderDetail.*.total' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
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
                'user_id' => $request->user_id,
                'email' => $request->email,
                'order_detail' => $request->orderDetail,
                'order_note' => $request->order_note,
                'admin_note' => $request->admin_note,
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
            'user_id' => 'nullable|numeric|min:0', 
            'total' => 'nullable|numeric|min:0',             
            'admin_note' => 'nullable|string',
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
        // kiểm tra status có phải là pending thì mới cho thực hiện, không phải thì báo đơn hàng đã được xác thực
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'email' => 'required|email',
            'status' => 'required|string',
            'order_detail' => 'nullable|array', // <-- cho phép rỗng
            'order_note' => 'nullable|string', // <-- cho phép rỗng
            'admin_note' => 'nullable|string', // <-- cho phép rỗng
            'total' => 'required|numeric',
        ]);

        $order = Order::find($request->order_id);
            // Chỉ cho phép update nếu pending
        if ($order->status !== 'pending') {
            return response()->json([
                'message' => 'Đơn hàng đã được xác thực, không thể cập nhật.'
            ], 403); // 403 Forbidden
        }
        $details = $request->order_detail;
        // Nếu mảng rỗng → xóa đơn hàng
        if (empty($details)) {
            $order->delete();
            return response()->json([
                'message' => 'Đơn hàng đã bị xóa vì không còn sản phẩm nào.'
            ]);
        }

        // Cập nhật order
        $order->order_detail = $details;
        $order->total = array_sum(array_column($details, 'total'));
        $order->email = $request->email;
        $order->status = $request->status;
        $order->order_note = $request->order_note;
        $order->admin_note = $request->admin_note;
        $order->save();

      
        return response()->json([
            'success' => true,
            'message' => 'Cập nhật đơn hàng thành công',
            'order' => $order,
        ], 200);
        
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
