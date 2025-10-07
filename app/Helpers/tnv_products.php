<?php

use App\Models\WpProduct;
use Illuminate\Support\Facades\Cache;

if (!function_exists('tnv_getProducts')) {
    /**
     * Lấy danh sách sản phẩm (có thể dùng trong API hoặc web)
     *
     * @param array $params
     *  [
     *    'search' => string|null,
     *    'category_id' => int|null,
     *    'min_price' => float|null,
     *    'max_price' => float|null,
     *    'order_by' => string ('created_at'|'title'|'regular_price'|...),
     *    'sort' => string ('asc'|'desc'),
     *    'paginate' => int|null (số item mỗi trang),
     *    'cache' => int (phút, mặc định 0 = không cache)
     *  ]
     */
    function tnv_getProducts(array $params = [])
    {
        $query = WpProduct::query()->with('categories');

        // 🔍 Tìm kiếm
        if (!empty($params['search'])) {
            $search = $params['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('short_description', 'like', "%$search%");
            });
        }

        // 🏷️ Lọc theo danh mục
        if (!empty($params['category_id'])) {
            $query->whereHas('categories', function ($q) use ($params) {
                $q->where('categories.id', $params['category_id']);
            });
        }

        // 💰 Lọc theo giá
        if (!empty($params['min_price'])) {
            $query->where('regular_price', '>=', $params['min_price']);
        }
        if (!empty($params['max_price'])) {
            $query->where('regular_price', '<=', $params['max_price']);
        }

        // 🔽 Sắp xếp
        $orderBy = $params['order_by'] ?? 'created_at';
        $sort = $params['sort'] ?? 'desc';
        $query->orderBy($orderBy, $sort);

        // ⚡ Cache (nếu cần)
        $cacheMinutes = $params['cache'] ?? 0;
        $cacheKey = 'tnv_products_' . md5(json_encode($params));

        // 📄 Luôn phân trang mặc định 20 sản phẩm/trang
        $fetch = function () use ($query, $params) {
            $perPage = $params['paginate'] ?? 20;
            return $query->paginate($perPage);
        };

        return $cacheMinutes > 0
            ? Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), $fetch)
            : $fetch();
    }
}

