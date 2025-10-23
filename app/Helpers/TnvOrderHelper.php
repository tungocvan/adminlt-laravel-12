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
    $query = Order::query()->with('user'); // Liên kết với bảng users
    // \Log::info('Params:', $params); 
    // Lọc theo is_admin nếu có trong tham số
    if (isset($params['is_admin'])) {
        
        $query->whereHas('user', function ($q) use ($params) {
            $q->where('is_admin', (int) $params['is_admin']);
        });
    }

    // Các điều kiện lọc khác
    if (!empty($params['fields'])) {
        $query->select($params['fields']);
    }

    if (!empty($params['search'])) {
        $search = $params['search'];
        $query->where(function ($q) use ($search) {
            $q->where('email', 'like', "%$search%")
              ->orWhere('order_detail', 'like', "%$search%");
        });
    }

    // Các điều kiện lọc khác (email, status, total, date, ...)
    if (!empty($params['email'])) {
        $query->where('email', $params['email']);
    }

    if (!empty($params['status'])) {
        $query->where('status', $params['status']);
    }

    if (!empty($params['min_total'])) {
        $query->where('total', '>=', $params['min_total']);
    }

    if (!empty($params['max_total'])) {
        $query->where('total', '<=', $params['max_total']);
    }

    if (!empty($params['date_from'])) {
        $query->whereDate('created_at', '>=', $params['date_from']);
    }

    if (!empty($params['date_to'])) {
        $query->whereDate('created_at', '<=', $params['date_to']);
    }

    // Sắp xếp
    $orderBy = $params['order_by'] ?? 'created_at';
    $sort = $params['sort'] ?? 'desc';
    $query->orderBy($orderBy, $sort);

    // Phân trang
    $perPage = $params['paginate'] ?? 20;
    return $query->paginate($perPage);
}

}
