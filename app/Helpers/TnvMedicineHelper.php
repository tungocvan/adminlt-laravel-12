<?php

namespace App\Helpers;

use App\Models\Medicine;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class TnvMedicineHelper
{
    public static function getMedicine(array $params = [])
    {
        $query = Medicine::query()->with('categories');

        // 🎯 Chọn cột cụ thể
        if (!empty($params['fields'])) {
            $query->select($params['fields']);
        }

        // 🔍 Tìm kiếm theo nhiều cột
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

        // 🏷️ Lọc theo danh mục (category_id hoặc slug)
        if (!empty($params['slug'])) {
            // Nếu có slug, tìm danh mục và các con
            $category = Category::where('slug', $params['slug'])->first();
            if ($category) {
                $categoryIds = Category::where('id', $category->id)
                    ->orWhere('parent_id', $category->id)
                    ->pluck('id')
                    ->toArray();

                $query->whereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('categories.id', $categoryIds);
                });
            }
        } elseif (!empty($params['category_id'])) {
            // Nếu không có slug, lọc theo id
            $query->whereHas('categories', function ($q) use ($params) {
                $q->where('categories.id', $params['category_id']);
            });
        }

        // 🧩 Lọc theo phân nhóm TT15
        if (!empty($params['phan_nhom_tt15'])) {
            $query->where('phan_nhom_tt15', $params['phan_nhom_tt15']);
        }

        // 💰 Lọc theo giá
        if (!empty($params['min_price'])) {
            $query->where('don_gia', '>=', $params['min_price']);
        }
        if (!empty($params['max_price'])) {
            $query->where('don_gia', '<=', $params['max_price']);
        }

        // 🏭 Lọc theo nhà sản xuất
        if (!empty($params['manufacturer'])) {
            $query->where('co_so_san_xuat', 'like', "%{$params['manufacturer']}%");
        }

        // 🌍 Lọc theo nước sản xuất
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

        // 📄 Phân trang mặc định 20 dòng
        $fetch = function () use ($query, $params) {
            $perPage = $params['paginate'] ?? 20;
            return $query->paginate($perPage);
        };

        return $cacheMinutes > 0
            ? Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), $fetch)
            : $fetch();
    }
}

