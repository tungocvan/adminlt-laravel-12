<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\TnvMedicineHelper;

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
            // Lấy tất cả tham số từ body
            $params = $request->all();

            // Gọi helper xử lý
            $data = TnvMedicineHelper::getMedicine($params);

            // Trả về JSON chuẩn REST
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
