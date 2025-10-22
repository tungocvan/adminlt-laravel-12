<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\TnvMedicineHelper;
use App\Models\Category;

class MedicineController extends Controller
{
    /**
     * Lấy danh sách thuốc (POST)
     *
     * Body JSON ví dụ:
     * {
     *   "search": "Paracetamol",
     *   "category_id": 1,
     *   "min_price": 10000,
     *   "max_price": 50000,
     *   "order_by": "don_gia",
     *   "sort": "asc",
     *   "paginate": 20,
     *   "cache": 10
     * }
     */
    public function getList(Request $request)
    {
        try {
            // 🧾 Lấy tất cả tham số từ body
            $params = $request->all();

            // 🏷️ Nếu request có slug → tìm category tương ứng
            if (!empty($params['slug'])) {
                $category = Category::where('slug', $params['slug'])->first();

                if ($category) {
                    // Lấy luôn các danh mục con (nếu có)
                    $categoryIds = Category::where('id', $category->id)
                        ->orWhere('parent_id', $category->id)
                        ->pluck('id')
                        ->toArray();

                    // Truyền vào helper thay cho slug
                    $params['category_ids'] = $categoryIds;
                } else {
                    // Nếu không tìm thấy slug → trả lỗi sớm
                    return response()->json([
                        'success' => false,
                        'message' => 'Danh mục không tồn tại với slug: ' . $params['slug'],
                    ], 404);
                }
            }

            // 🚀 Gọi helper xử lý danh sách
            $data = TnvMedicineHelper::getMedicine($params);

            // ✅ Trả về JSON chuẩn REST
            return response()->json([
                'success' => true,
                'message' => 'Lấy danh sách thuốc thành công',
                'data' => $data->items(),
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'last_page' => $data->lastPage(),
                ]
            ]);
        } catch (\Throwable $e) {
            // ⚠️ Bắt lỗi và trả về phản hồi an toàn
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách thuốc',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
}
