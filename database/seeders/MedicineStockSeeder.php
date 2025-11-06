<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class MedicineStockSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy ngày hiện tại
        $today = Carbon::now();
        $soLo = $today->format('dmy'); // ddmmyy
        $hanDung = $today->copy()->addYears(2)->format('Y-m-d'); // cộng 2 năm

        // Lấy tất cả medicines
        $medicines = DB::table('medicines')->get();

        foreach ($medicines as $medicine) {
            DB::table('medicine_stocks')->insert([
                'medicine_id' => $medicine->id,
                'so_lo' => $soLo,
                'han_dung' => $hanDung,
                'so_luong' => rand(50, 200), // số lượng mẫu
                'gia_von' => $medicine->gia_von ?? rand(1000, 2000),
                'don_gia' => $medicine->don_gia ?? rand(1500, 2500),
                'status' => 'available',
                'location' => 'Kho A', // tùy ý
                'notes' => 'Lô nhập tự động',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Import dữ liệu medicine_stocks thành công!');
    }
}
