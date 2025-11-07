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
            throw new \Exception("User không tồn tại");
        }

        DB::beginTransaction();

        try {
            $orderDetail = [];
            $total = 0;

            foreach ($payload['order_detail'] as $item) {
                $productId = $item['product_id'];
                $qty        = (int)$item['quantity'];

                $product = Medicine::find($productId);
                if (!$product) {
                    throw new \Exception("Sản phẩm {$productId} không tồn tại");
                }

                // Lấy lô gần hết hạn nhất
                $stockInfo = MedicineStock::getNearestExpiry($productId);

                if (!$stockInfo) {
                    throw new \Exception("Thuốc {$product->ten_hoat_chat} không còn tồn kho");
                }

                if ($stockInfo['so_luong'] < $qty) {
                    throw new \Exception("Không đủ tồn kho cho thuốc {$product->ten_hoat_chat}. Tồn: {$stockInfo['so_luong']}");
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

                // Build chi tiết đơn
                $orderDetail[] = [
                    'product_id' => $productId,
                    'title'      => $product->ten_hoat_chat,
                    'dvt'        => $product->don_vi_tinh,
                    'quy_cach'   => $product->quy_cach_dong_goi,
                    'quantity'   => $qty,
                    'don_gia'    => $product->don_gia,
                    'total'      => $qty * $product->don_gia,
                    'so_lo'      => $stockInfo['so_lo'],
                    'han_dung'   => $stockInfo['han_dung'],
                ];

                $total += $qty * $product->don_gia;
            }

            // Lưu order
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

            DB::commit();

            return $order;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function updateOrder(int $orderId, array $payload)
    {
        $order = Order::find($orderId);

        if (!$order) {
            throw new \Exception("Đơn hàng không tồn tại");
        }

        \DB::beginTransaction();

        try {
            // ================================
            // 1) Trả tồn kho của order cũ
            // ================================
            foreach ($order->order_detail as $old) {
                if (!empty($old['so_lo']) && !empty($old['han_dung'])) {
                    $stock = MedicineStock::where('medicine_id', $old['product_id'])
                        ->where('so_lo', $old['so_lo'])
                        ->where('han_dung', $old['han_dung'])
                        ->first();

                    if ($stock) {
                        $stock->so_luong += $old['quantity'];

                        if ($stock->status === 'empty' && $stock->so_luong > 0) {
                            $stock->status = 'in_stock';
                        }

                        $stock->save();
                    }
                }
            }

            // ================================
            // 2) Xử lý order_detail mới
            // ================================
            $newOrderDetail = [];
            $total = 0;

            foreach ($payload['order_detail'] as $item) {
                $productId = $item['product_id'];
                $qty = (int)$item['quantity'];

                $medicine = Medicine::find($productId);
                if (!$medicine) {
                    throw new \Exception("Thuốc ID {$productId} không tồn tại");
                }

                // Lấy lô gần hết hạn nhất
                $stockInfo = MedicineStock::getNearestExpiry($productId);
                if (!$stockInfo || $stockInfo['so_luong'] < $qty) {
                    throw new \Exception(
                        "Không đủ tồn kho cho thuốc {$medicine->ten_hoat_chat}"
                    );
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

                $newOrderDetail[] = [
                    'product_id' => $productId,
                    'title'      => $medicine->ten_hoat_chat,
                    'dvt'        => $medicine->don_vi_tinh,
                    'quy_cach'   => $medicine->quy_cach_dong_goi,
                    'quantity'   => $qty,
                    'don_gia'    => $medicine->don_gia,
                    'total'      => $qty * $medicine->don_gia,
                    'so_lo'      => $stockInfo['so_lo'],
                    'han_dung'   => $stockInfo['han_dung'],
                ];

                $total += $qty * $medicine->don_gia;
            }

            // ================================
            // 3) Cập nhật order
            // ================================
            $order->update([
                'email'        => $payload['email'] ?? $order->email,
                'customer_id'  => $payload['customer_id'] ?? $order->customer_id,
                'status'       => $payload['status'] ?? $order->status,
                'order_note'   => $payload['order_note'] ?? $order->order_note,
                'admin_note'   => $payload['admin_note'] ?? $order->admin_note,
                'order_detail' => $newOrderDetail,
                'total'        => $total,
            ]);

            \DB::commit();

            return $order;

        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $orderId)
    {
        return DB::transaction(function () use ($orderId) {

            $order = Order::find($orderId);

            if (!$order) {
                throw new \Exception("Order #{$orderId} không tồn tại.");
            }

            // ✅ Hoàn trả tồn kho nếu có order_detail
            $details = is_array($order->order_detail)
                ? $order->order_detail
                : json_decode($order->order_detail, true);

            foreach ($details as $item) {
                if (empty($item['product_id']) || empty($item['quantity'])) {
                    continue;
                }

                $productId = $item['product_id'];
                $qty = (int) $item['quantity'];
                $soLo = $item['so_lo'] ?? null;
                $hanDung = $item['han_dung'] ?? null;

                if ($soLo && $hanDung) {
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

            // ✅ Xóa order
            $order->delete();

            return [
                'success' => true,
                'message' => "Order #{$orderId} đã được xóa thành công."
            ];
        });
    }

}
