<?php

namespace Modules\Medicine\Http\Controllers\Api;

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
            $params = $request->all();

            // 🏷️ Nếu có slug → tìm danh mục tương ứng
            if (!empty($params['slug'])) {
                $category = Category::where('slug', $params['slug'])->first();

                if ($category) {
                    // Lấy luôn danh mục con
                    $categoryIds = Category::where('id', $category->id)
                        ->orWhere('parent_id', $category->id)
                        ->pluck('id')
                        ->toArray();

                    $params['category_ids'] = $categoryIds;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không tìm thấy danh mục có slug: ' . $params['slug'],
                    ], 404);
                }
            }

   

            // 🚀 Gọi helper xử lý
            $data = TnvMedicineHelper::getMedicine($params);

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
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách thuốc',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    
}
