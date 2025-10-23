<?php

namespace App\Helpers;

use App\Models\Medicine;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class TnvMedicineHelper
{
    public static function getMedicine(array $params = [])
{
    $query = Medicine::query();

    // ✅ Nếu yêu cầu load categories
    if (!empty($params['categories']) && $params['categories'] === true) {
        if (!empty($params['fields_categories']) && is_array($params['fields_categories'])) {
            $query->with(['categories' => function ($q) use ($params) {
                $fields = $params['fields_categories'];
    
                // 🔧 Thêm prefix tên bảng cho mỗi field để tránh lỗi trùng
                $fields = array_map(function ($field) {
                    return str_contains($field, '.') ? $field : "categories.$field";
                }, $fields);
    
                // Đảm bảo luôn có id
                if (!in_array('categories.id', $fields)) {
                    $fields[] = 'categories.id';
                }
    
                $q->select($fields);
            }]);
        } else {
            $query->with('categories');
        }
    }
    

    // 🎯 Lọc theo slug danh mục
    if (!empty($params['slug'])) {
        $slug = $params['slug'];

        $categoryIds = \App\Models\Category::where('slug', $slug)
            ->orWhere('parent_id', function ($q) use ($slug) {
                $q->select('id')->from('categories')->where('slug', $slug);
            })
            ->pluck('id');

        if ($categoryIds->isNotEmpty()) {
            $query->whereHas('categories', fn($q) => $q->whereIn('categories.id', $categoryIds));
        } else {
            return collect([]); // không có slug hợp lệ
        }
    }

    // 🎯 Fields cần lấy
    if (!empty($params['fields'])) {
        $fields = is_string($params['fields'])
            ? array_map('trim', explode(',', $params['fields']))
            : (array) $params['fields'];

        // đảm bảo luôn có id để join pivot
        if (!in_array('id', $fields)) {
            $fields[] = 'id';
        }

        $query->select($fields);
    }

    // 🚫 Bỏ qua các field nếu có "except_fields"
    if (!empty($params['except_fields'])) {
        $allColumns = \Schema::getColumnListing('medicines');
        $except = is_array($params['except_fields'])
            ? $params['except_fields']
            : array_map('trim', explode(',', $params['except_fields']));
        $fields = array_diff($allColumns, $except);
        $query->select($fields);
    }

    // 🔍 Tìm kiếm
    if (!empty($params['search'])) {
        $search = $params['search'];
        $query->where(function ($q) use ($search) {
            $q->where('ten_biet_duoc', 'like', "%$search%")
                ->orWhere('ten_hoat_chat', 'like', "%$search%")
                ->orWhere('nha_phan_phoi', 'like', "%$search%")
                ->orWhere('phan_nhom_tt15', 'like', "%$search%")
                ->orWhere('co_so_san_xuat', 'like', "%$search%");
        });
    }

    // 🧩 Bộ lọc khác
    if (!empty($params['category_id'])) {
        $query->whereHas('categories', fn($q) => $q->where('categories.id', $params['category_id']));
    }
    if (!empty($params['phan_nhom_tt15'])) {
        $query->where('phan_nhom_tt15', $params['phan_nhom_tt15']);
    }
    if (!empty($params['min_price'])) {
        $query->where('don_gia', '>=', $params['min_price']);
    }
    if (!empty($params['max_price'])) {
        $query->where('don_gia', '<=', $params['max_price']);
    }
    if (!empty($params['manufacturer'])) {
        $query->where('co_so_san_xuat', 'like', "%{$params['manufacturer']}%");
    }
    if (!empty($params['country'])) {
        $query->where('nuoc_san_xuat', 'like', "%{$params['country']}%");
    }

    // 🔽 Sắp xếp
    $orderBy = $params['order_by'] ?? 'created_at';
    $sort = $params['sort'] ?? 'desc';
    $query->orderBy($orderBy, $sort);

    // ⚡ Cache (tùy chọn)
    $cacheMinutes = $params['cache'] ?? 0;
    $cacheKey = 'tnv_medicines_' . md5(json_encode($params));

    $fetch = function () use ($query, $params) {
        $perPage = $params['paginate'] ?? 20;
        return $query->paginate($perPage);
    };

    return $cacheMinutes > 0
        ? \Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), $fetch)
        : $fetch();
}


}

