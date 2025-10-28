<?php

namespace App\Helpers;

use App\Models\Medicine;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Traits\HasExcelExportTemplate;

class TnvMedicineHelper
{
    use HasExcelExportTemplate;
    public static function getMedicine(array $params = [])
    {
        $query = Medicine::query();

            // 🆕 Lọc theo id hoặc danh sách id
            if (!empty($params['id'])) {
                if (is_array($params['id'])) {
                    $query->whereIn('id', $params['id']);
                } else {
                    $query->where('id', $params['id']);
                }
            }
        
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
            $allColumns = Schema::getColumnListing('medicines');
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
                    ->orWhere('ten_hoat_chat', 'like', "%$search%");
                    // ->orWhere('nha_phan_phoi', 'like', "%$search%")
                    // ->orWhere('phan_nhom_tt15', 'like', "%$search%")
                    // ->orWhere('co_so_san_xuat', 'like', "%$search%");
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
            $query->where('don_gia', '>=', $params['don_gia']);
        }
        if (!empty($params['max_price'])) {
            $query->where('don_gia', '<=', $params['gia_ke_khai']);
        }
        // if (!empty($params['co_so_san_xuat'])) {
        //     $query->where('co_so_san_xuat', 'like', "%{$params['co_so_san_xuat']}%");
        // }
        // if (!empty($params['nuoc_san_xuat'])) {
        //     $query->where('nuoc_san_xuat', 'like', "%{$params['nuoc_san_xuat']}%");
        // }
        // if (!empty($params['nha_phan_phoi'])) {
        //     $query->where('nha_phan_phoi', 'like', "%{$params['nha_phan_phoi']}%");
        // }

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
            ? Cache::remember($cacheKey, now()->addMinutes($cacheMinutes), $fetch)
            : $fetch();
    }

    public static function exportWithTemplate(array $options = [])
    {
        // ----- 1️⃣ Mặc định các tham số -----
        $defaultColumns = [
            ['field' => 'stt_tt20_2022', 'title' => 'STT TT20/2022'],
            ['field' => 'phan_nhom_tt15', 'title' => 'Phân nhóm TT15'],
            ['field' => 'ten_hoat_chat', 'title' => 'Tên hoạt chất', 'align' => 'left'],
            ['field' => 'nong_do_ham_luong', 'title' => 'Nồng độ / Hàm lượng'],
            ['field' => 'ten_biet_duoc', 'title' => 'Tên biệt dược', 'align' => 'left'],
            ['field' => 'dang_bao_che', 'title' => 'Dạng bào chế'],
            ['field' => 'don_vi_tinh', 'title' => 'Đơn vị tính'],
            ['field' => 'quy_cach_dong_goi', 'title' => 'Quy cách đóng gói'],
            ['field' => 'giay_phep_luu_hanh', 'title' => 'Số GPLH'],
            ['field' => 'han_dung', 'title' => 'Hạn dùng'],
            ['field' => 'co_so_san_xuat', 'title' => 'Cơ sở sản xuất', 'align' => 'left'],
            ['field' => 'don_gia', 'title' => 'Đơn giá', 'type' => 'numeric'],
            ['field' => 'gia_ke_khai', 'title' => 'Giá kê khai', 'type' => 'numeric'],
        ];

        $defaults = [
            'templatePath' => database_path('exports/MAU-BANG-BAO-GIA.xlsx'),
            'sheetName'    => 'Sheet1',
            'startRow'     => 10,
            'columns'      => $defaultColumns,
            'auto_width'   => false,
            'auto_height'  => true,
            'fit_to_page'  => true,
            'row_font'     => ['name' => 'Times New Roman', 'size' => 12],
            'titles'       => [
                ['cell' => 'A6', 'text' => 'BẢNG BÁO GIÁ', 'style' => ['bold' => true, 'size' => 28, 'align' => 'center'], 'merge' => 'A6:N6'],
                ['cell' => 'L12', 'text' => 'TP.HCM, ngày ' . now()->day . ' tháng ' . now()->month . ' năm ' . now()->year, 'style' => ['align' => 'right']],
                ['cell' => 'J13', 'text' => 'PHÒNG KINH DOANH', 'style' => ['bold' => true, 'align' => 'center']]
            ],
            'images' => [
                ['path' => storage_path('app/logo.png'), 'cell' => 'B1', 'width_in' => 1.86, 'height_in' => 1.18, 'offsetX' => 0, 'offsetY' => 0],
                ['path' => storage_path('app/ck.png'), 'cell' => 'L14', 'width_in' => 2, 'height_in' => 1.33, 'offsetX' => 0, 'offsetY' => 0],
            ],
            'selectedId' => [], // ← thêm ở đây
        ];

        $options = array_merge($defaults, $options);

        // ----- 2️⃣ Kiểm tra danh sách sản phẩm -----
        if (empty($options['selectedId'])) {
            throw new \Exception('Vui lòng chọn ít nhất một sản phẩm để xuất Excel.');
        }

        // ----- 3️⃣ Lấy dữ liệu từ DB -----
        $fields = array_column($options['columns'], 'field');
        $data = Medicine::whereIn('id', $options['selectedId'])->get($fields);

        $options['data'] = $data;

        // ----- 4️⃣ Gọi hàm exportTemplate (bạn đã có sẵn trong class khác) -----
        if (!method_exists(static::class, 'exportTemplate') && !function_exists('exportTemplate')) {
            throw new \Exception('Hàm exportTemplate chưa được định nghĩa hoặc include.');
        }

        return (new static)->exportTemplate($options);

    }

}

