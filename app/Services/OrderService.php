<?php
namespace App\Services;

use Modules\Order\Models\Order;
use App\Models\Medicine;
use App\Models\User;
use App\Models\MedicineStock;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    public function createOrder(array $payload)
    {
        $user = User::find((int)$payload['user_id']);
        if (!$user) {
            throw new Exception("User không tồn tại");
        }

        // Không trừ tồn kho khi tạo order
        $orderDetail = [];
        $total = 0;

        foreach ($payload['order_detail'] as $item) {
            $productId = $item['product_id'];
            $qty = (int)$item['quantity'];

            $product = Medicine::find($productId);
            if (!$product) {
                throw new Exception("Sản phẩm {$productId} không tồn tại");
            }

            $orderDetail[] = [
                'product_id' => $productId,
                'title'      => $product->ten_hoat_chat,
                'dvt'        => $product->don_vi_tinh,
                'quy_cach'   => $product->quy_cach_dong_goi,
                'quantity'   => $qty,
                'don_gia'    => $product->don_gia,
                'total'      => $qty * $product->don_gia,
                // lưu so_lo và han_dung trống, sẽ chọn khi confirm
                'so_lo'      => null,
                'han_dung'   => null,
            ];

            $total += $qty * $product->don_gia;
        }

        $order = Order::create([
            'email'       => $user->email,
            'customer_id' => $payload['customer_id'],
            'user_id'     => $user->id,
            'status'      => $payload['status'] ?? 'pending',
            'order_note'  => $payload['order_note'] ?? null,
            'admin_note'  => $payload['admin_note'] ?? null,
            'order_detail'=> $orderDetail,
            'total'       => $total,
        ]);

        return $order;
    }

    public function updateOrder(int $orderId, array $payload)
    {
      
        $order = Order::find($orderId);
        if (!$order) {
            throw new Exception("Đơn hàng không tồn tại");
        }

        DB::beginTransaction();

        try {
            $oldStatus = $order->status;
            $newStatus = $payload['status'] ?? $oldStatus;
            $isConfirming = $oldStatus !== 'confirmed' && $newStatus === 'confirmed';
            $orderDetail = $order->order_detail;
            $newOrderDetail = [];
            $total = 0;

            foreach ($payload['order_detail'] as $item) {
                $productId = $item['product_id'];
                $qty = (int)$item['quantity'];
                $medicine = Medicine::find($productId);

                if (!$medicine) {
                    throw new Exception("Thuốc ID {$productId} không tồn tại");
                }

                $stockInfo = null;
                if ($isConfirming) {
                    // Lấy lô gần hết hạn nhất khi xác nhận
                    $stockInfo = MedicineStock::getNearestExpiry($productId);
                    if (!$stockInfo || $stockInfo['so_luong'] < $qty) {
                        throw new Exception("Không đủ tồn kho cho thuốc {$medicine->ten_hoat_chat}");
                    }

                    // Trừ tồn kho
                    $stock = MedicineStock::where('medicine_id', $productId)
                        ->where('so_lo', $stockInfo['so_lo'])
                        ->where('han_dung', $stockInfo['han_dung'])
                        ->first();

                    $stock->so_luong -= $qty;
                    if ($stock->so_luong <= 0) {
                        $stock->so_luong = 0;
                        $stock->status = 'empty';
                    }
                    $stock->save();
                }

                $newOrderDetail[] = [
                    'product_id' => $productId,
                    'title'      => $medicine->ten_hoat_chat,
                    'dvt'        => $medicine->don_vi_tinh,
                    'quy_cach'   => $medicine->quy_cach_dong_goi,
                    'quantity'   => $qty,
                    'don_gia'    => $medicine->don_gia,
                    'total'      => $qty * $medicine->don_gia,
                    'so_lo'      => $stockInfo['so_lo'] ?? null,
                    'han_dung'   => $stockInfo['han_dung'] ?? null,
                ];

                $total += $qty * $medicine->don_gia;
            }

            $order->update([
                'email'        => $payload['email'] ?? $order->email,
                'customer_id'  => $payload['customer_id'] ?? $order->customer_id,
                'status'       => $newStatus,
                'order_note'   => $payload['order_note'] ?? $order->order_note,
                'admin_note'   => $payload['admin_note'] ?? $order->admin_note,
                'order_detail' => $newOrderDetail,
                'total'        => $total,
            ]);

            DB::commit();

            return $order;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $orderId)
    {
        return DB::transaction(function () use ($orderId) {
            $order = Order::find($orderId);
            if (!$order) {
                throw new Exception("Order #{$orderId} không tồn tại.");
            }

            // Chỉ hoàn trả tồn kho nếu order đã xác nhận
            if ($order->status === 'confirmed') {
                $details = is_array($order->order_detail)
                    ? $order->order_detail
                    : json_decode($order->order_detail, true);

                foreach ($details as $item) {
                    $productId = $item['product_id'] ?? null;
                    $qty = (int)($item['quantity'] ?? 0);
                    $soLo = $item['so_lo'] ?? null;
                    $hanDung = $item['han_dung'] ?? null;

                    if ($productId && $qty && $soLo && $hanDung) {
                        $stock = MedicineStock::where('medicine_id', $productId)
                            ->where('so_lo', $soLo)
                            ->where('han_dung', $hanDung)
                            ->first();
                        if ($stock) {
                            $stock->so_luong += $qty;
                            $stock->status = 'in_stock';
                            $stock->save();
                        }
                    }
                }
            }

            $order->delete();

            return [
                'success' => true,
                'message' => "Order #{$orderId} đã được xóa thành công."
            ];
        });
    }
}
