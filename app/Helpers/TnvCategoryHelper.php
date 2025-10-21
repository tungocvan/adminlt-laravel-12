<?php

namespace App\Helpers;

use App\Models\Category;

class TnvCategoryHelper
{
    /**
     * Lấy danh sách categories với các điều kiện lọc, tìm kiếm, phân trang
     */
    public static function getAll(array $params = [])
    {
        $query = Category::query();

        // 🔍 Tìm kiếm theo từ khóa
        if (!empty($params['search'])) {
            $search = $params['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('slug', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhere('meta_title', 'like', "%$search%")
                  ->orWhere('meta_description', 'like', "%$search%");
            });
        }

        // 📂 Lọc theo loại (menu/category)
        if (!empty($params['type'])) {
            $query->where('type', $params['type']);
        }

        // 📊 Lọc theo trạng thái hoạt động
        if (isset($params['is_active'])) {
            $query->where('is_active', $params['is_active']);
        }

        // 🔗 Lọc theo danh mục cha
        if (!empty($params['parent_id'])) {
            $query->where('parent_id', $params['parent_id']);
        }

        // 🔄 Sắp xếp
        if (!empty($params['sort'])) {
            $direction = $params['direction'] ?? 'asc';
            $query->orderBy($params['sort'], $direction);
        } else {
            $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc');
        }

        // 📄 Phân trang hoặc lấy tất cả
        if (!empty($params['paginate'])) {
            return $query->paginate($params['paginate']);
        }

        return $query->get();
    }

    /**
     * Tạo mới hoặc cập nhật category
     */
    public static function save(array $data, $id = null)
    {
        if ($id) {
            $category = Category::findOrFail($id);
            $category->update($data);
        } else {
            $category = Category::create($data);
        }

        return $category;
    }

    /**
     * Xóa category
     */
    public static function delete($id)
    {
        $category = Category::findOrFail($id);
        return $category->delete();
    }

    /**
     * Lấy chi tiết category theo ID
     */
    public static function getById($id)
    {
        return Category::with('parent', 'children')->findOrFail($id);
    }
    public static function getBySlug($key, $withChildren = true)
    {
        $query = Category::query();

        if ($withChildren) {
            $query->with('children');
        }

        // Nếu là số → tìm theo id
        if (is_numeric($key)) {
            return $query->find($key);
        }

        // Nếu là chữ → tìm theo slug
        return $query->where('slug', $key)->first();
    }
}
