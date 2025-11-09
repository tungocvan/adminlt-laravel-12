<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ImportMedicineStocks extends Command
{
    protected $signature = 'import:medicine-stocks';
    protected $description = 'Import dữ liệu từ medicines vào medicine_stocks với số lô và hạn dùng tự động';

    public function handle(): int
    {
        $today = Carbon::now();
        $soLo = $today->format('dmy'); // ddmmyy
        $hanDung = $today->copy()->addYears(2)->format('Y-m-d');

        $medicines = DB::table('medicines')->get();

        foreach ($medicines as $medicine) {
            // Kiểm tra nếu đã tồn tại lô hôm nay cho sản phẩm này
            $exists = DB::table('medicine_stocks')
                ->where('medicine_id', $medicine->id)
                ->where('so_lo', $soLo)
                ->where('han_dung', $hanDung)
                ->exists();

            if ($exists) {
                $this->info("Bỏ qua thuốc ID {$medicine->id}, lô {$soLo} đã tồn tại.");
                continue;
            }

            DB::table('medicine_stocks')->insert([
                'medicine_id' => $medicine->id,
                'so_lo' => $soLo,
                'han_dung' => $hanDung,
                'so_luong' => rand(1000, 2000),
                'gia_von' => $medicine->gia_von ?? rand(1000, 2000),       
                'status' => 'available',
                'location' => 'Kho A',
                'notes' => 'Lô nhập tự động',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info("Thêm thuốc ID {$medicine->id}, lô {$soLo} thành công.");
        }

        $this->info('Hoàn tất import medicine_stocks!');
        return 0;
    }
}
