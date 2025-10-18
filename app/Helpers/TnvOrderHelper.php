<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use App\Models\Order;

class TnvOrderHelper
{
    /**
     * Lấy danh sách đơn hàng theo điều kiện lọc.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public static function getOrders(array $params = [])
    {
        $query = Order::query()->with('user');

        // 🎯 Chọn cột cụ thể nếu được chỉ định
        if (!empty($params['fields'])) {
            $query->select($params['fields']);
        }

        // 🔍 Tìm kiếm theo email hoặc thông tin đơn hàng
        if (!empty($params['search'])) {
            $search = $params['search'];
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%$search%")
                  ->orWhere('order_detail', 'like', "%$search%");
            });
        }

        // 📧 Lọc theo email người dùng
        if (!empty($params['email'])) {
            $query->where('email', $params['email']);
        }

        // 📦 Lọc theo trạng thái đơn hàng
        if (!empty($params['status'])) {
            $query->where('status', $params['status']);
        }

        // 💰 Lọc theo tổng tiền
        if (!empty($params['min_total'])) {
            $query->where('total', '>=', $params['min_total']);
        }
        if (!empty($params['max_total'])) {
            $query->where('total', '<=', $params['max_total']);
        }

        // 🕓 Lọc theo ngày tạo
        if (!empty($params['date_from'])) {
            $query->whereDate('created_at', '>=', $params['date_from']);
        }
        if (!empty($params['date_to'])) {
            $query->whereDate('created_at', '<=', $params['date_to']);
        }

        // 🔽 Sắp xếp
        $orderBy = $params['order_by'] ?? 'created_at';
        $sort = $params['sort'] ?? 'desc';
        $query->orderBy($orderBy, $sort);

        // ⚡ Cache (nếu cần)
        $cacheMinutes = $params['cache'] ?? 0;
        $cacheKey = 'tnv_orders_' . md5(json_encode($params));

        // 📄 Phân trang (mặc định 20 đơn hàng/trang)
        $fetch = function () use ($query, $params) {
            $perPage = $params['paginate'] ?? 20;
            return $query->paginate($perPage);
        };

        return $cacheMinutes > 0
            ? Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), $fetch)
            : $fetch();
    }
}
