<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Medicine;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class ImportMedicinesCommand extends Command
{
    protected $signature = 'import:danhmucthuoc 
        {file : Đường dẫn đến file Excel (.xlsx)} 
        {--fresh : Xóa dữ liệu cũ trước khi import}';

    protected $description = 'Import dữ liệu thuốc từ file Excel vào bảng medicines';

    public function handle(): int
    {
        $filePath = $this->argument('file');
        $fresh = $this->option('fresh');

        if (!file_exists($filePath)) {
            $this->error("❌ File không tồn tại: {$filePath}");
            return Command::FAILURE;
        }

        if ($fresh) {
            $confirm = $this->confirm('⚠️ Bạn có chắc chắn muốn xóa toàn bộ dữ liệu bảng medicines không?', true);
            if ($confirm) {
                DB::table('medicines')->truncate();
                $this->warn('🧹 Đã xóa toàn bộ dữ liệu cũ trong bảng medicines.');
            }
        }

        $this->info("🔍 Đang đọc dữ liệu từ: {$filePath}");

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getSheet(0);
            $rows = $sheet->toArray(null, true, true, true);

            // Bỏ qua tiêu đề
            $header = array_shift($rows);
            $this->info("📦 Đang import " . count($rows) . " dòng...");

            $count = 0;

            foreach ($rows as $row) {
                // 🔹 Làm sạch dữ liệu
                $giayPhep = $this->cleanString($row['K'] ?? '');
                $tenBietDuoc = $this->cleanString($row['F'] ?? '');

                // Bỏ qua dòng rỗng
                if (empty($tenBietDuoc) && empty($giayPhep)) {
                    continue;
                }

                // 🔹 Chuẩn hóa giá trị số
                $giaKeKhai = $this->parseNumber($row['O'] ?? null);
                $donGia    = $this->parseNumber($row['P'] ?? null);
                $giaVon    = $this->parseNumber($row['Q'] ?? null);

                $data = [
                    'stt_tt20_2022'      => isset($row['B']) && is_numeric($row['B']) ? (float) $row['B'] : null,
                    'phan_nhom_tt15'     => isset($row['C']) && is_numeric($row['C']) ? (int) $row['C'] : null,
                    'ten_hoat_chat'      => $this->cleanString($row['D'] ?? ''),
                    'nong_do_ham_luong'  => $this->cleanString($row['E'] ?? ''),
                    'ten_biet_duoc'      => $tenBietDuoc,
                    'dang_bao_che'       => $this->cleanString($row['G'] ?? ''),
                    'duong_dung'         => $this->cleanString($row['H'] ?? ''),
                    'don_vi_tinh'        => $this->cleanString($row['I'] ?? ''),
                    'quy_cach_dong_goi'  => $this->cleanString($row['J'] ?? ''),
                    'giay_phep_luu_hanh' => $this->cleanLicense($row['K'] ?? null),
                    'han_dung'           => $this->cleanString($row['L'] ?? ''),
                    'co_so_san_xuat'     => $this->cleanString($row['M'] ?? ''),
                    'nuoc_san_xuat'      => $this->cleanString($row['N'] ?? ''),
                    'gia_ke_khai'        => $giaKeKhai,
                    'don_gia'            => $donGia,
                    'gia_von'            => $giaVon,
                    'nha_phan_phoi'      => $this->cleanString($row['R'] ?? ''),
                    'nhom_thuoc'         => $this->cleanString($row['S'] ?? ''),
                    'link_hinh_anh'      => trim((string)($row['T'] ?? 'images/thuoc.png')),
                ];

                // 🔹 Kiểm tra trùng lặp
                $exists = Medicine::where('ten_biet_duoc', $data['ten_biet_duoc'])
                    ->orWhere('giay_phep_luu_hanh', $data['giay_phep_luu_hanh'])
                    ->exists();

                if (!$exists) {
                    Medicine::create($data);
                    $count++;
                }
            }

            $this->info("✅ Đã import thành công {$count} dòng mới!");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("⚠️ Lỗi khi import: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
 * Làm sạch chuỗi: chỉ loại bỏ xuống dòng, tab và khoảng trắng đầu/cuối
 */
private function cleanString(?string $value): string
{
    if ($value === null) return '';
    $value = trim((string) $value);
    // Chỉ loại bỏ ký tự điều khiển, KHÔNG xoá khoảng trắng trong nội dung
    $value = str_replace(["\n", "\r", "\t"], ' ', $value);
    return preg_replace('/\s+/', ' ', $value); // chuẩn hoá nhiều khoảng trắng thành 1
}

/**
 * Chuẩn hóa chuỗi số: "15.000" → 15000, "98,000" → 98000
 */
private function parseNumber($value): ?float
{
    if (is_null($value)) {
        return null;
    }

    // Nếu là số thật
    if (is_numeric($value)) {
        return (float) $value;
    }

    // Nếu là chuỗi có ký tự . hoặc ,
    $clean = preg_replace('/[^\d.]/', '', str_replace(',', '', (string)$value));
    return is_numeric($clean) ? (float) $clean : null;
}
private function cleanLicense(?string $value): ?string
{
    if (!$value) return null;

    // Loại bỏ tất cả dấu phẩy, chấm, xuống dòng, tab
    $clean = preg_replace('/[,\r\n\t]+/', '', $value);

    // Xóa khoảng trắng đầu và cuối
    $clean = trim($clean);

    // Nếu là chuỗi toàn số (như 560110011523), trả về nguyên
    // Nếu có ký tự chữ (VD: VN-12345-12), vẫn giữ nguyên
    return $clean;
}

}
