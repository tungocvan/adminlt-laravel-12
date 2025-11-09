<?php

namespace App\Services;

use App\Models\Medicine;
use App\Models\MedicineStock;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderStockService
{
    /**
     * Thêm mới hoặc cập nhật tồn kho
     *
     * @param array $data ['medicine_id'=>int, 'so_lo'=>string, 'han_dung'=>string, 'so_luong'=>int, 'don_gia'=>float|null]
     * @return MedicineStock
     * @throws Exception
     */
    public function addOrUpdateStock(array $data): MedicineStock
    {
        // Kiểm tra medicine tồn tại
        $medicine = Medicine::find($data['medicine_id']);
        if (!$medicine) {
            throw new Exception("Sản phẩm không tồn tại: ID {$data['medicine_id']}");
        }

        // Xử lý default so_lo và han_dung nếu không có
        $today = Carbon::now();
        $soLo = $data['so_lo'] ?? $today->format('dmy'); // ddmmyy
        $hanDung = isset($data['han_dung']) 
            ? Carbon::parse($data['han_dung'])->format('Y-m-d')
            : $today->copy()->addYears(2)->format('Y-m-d');

        $soLuong = $data['so_luong'] ?? 0;
        if ($soLuong <= 0) {
            throw new Exception("Số lượng phải lớn hơn 0");
        }

        $donGia = $data['gia_von'] ?? null;

        return DB::transaction(function () use ($medicine, $soLo, $hanDung, $soLuong, $donGia) {
            // Kiểm tra tồn kho trùng 3 trường
            $stock = MedicineStock::where('medicine_id', $medicine->id)
                ->where('so_lo', $soLo)
                ->where('han_dung', $hanDung)
                ->first();

            if ($stock) {
                // Cập nhật số lượng
                $newQuantity = $stock->so_luong + $soLuong;

                // Cập nhật giá vốn trung bình nếu có don_gia
                if ($donGia !== null && $stock->gia_von > 0) {
                    $stock->gia_von = (($stock->gia_von * $stock->so_luong) + ($donGia * $soLuong)) / $newQuantity;
                } elseif ($donGia !== null) {
                    $stock->gia_von = $donGia;
                }

                $stock->so_luong = $newQuantity;
                $stock->save();
            } else {
                // Thêm mới
                $stock = MedicineStock::create([
                    'medicine_id' => $medicine->id,
                    'so_lo' => $soLo,
                    'han_dung' => $hanDung,
                    'so_luong' => $soLuong,
                    'gia_von' => $donGia ?? 0,
                    'status' => 'available',
                ]);
            }

            return $stock;
        });
    }
}
