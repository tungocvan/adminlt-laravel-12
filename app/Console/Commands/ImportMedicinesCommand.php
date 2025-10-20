<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Medicine;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use Illuminate\Support\Facades\DB;


class ImportMedicinesCommand extends Command
{
    protected $signature = 'import:danhmucthuoc 
        {file? : Đường dẫn đến file Excel (.xlsx)} 
        {--fresh : Xóa dữ liệu cũ trước khi import}';

    protected $description = 'Import danh mục thuốc từ file Excel (đọc theo chunk, tối ưu RAM & tốc độ)';

    public function handle(): int
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', '0');

        // ✅ File mặc định nếu không truyền tham số
        $filePath = $this->argument('file') ?? database_path('imports/bang-gia-thuoc.xlsx');
        $fresh = $this->option('fresh') ?? true;

        if (!file_exists($filePath)) {
            $this->error("❌ File không tồn tại: {$filePath}");
            return Command::FAILURE;
        }

    
        // ✅ Luôn fresh nếu không truyền tham số
        if ($fresh) {
            $confirm = $this->confirm('⚠️ Xóa toàn bộ dữ liệu bảng medicines trước khi import?', true);
            if ($confirm) {
                DB::table('medicines')->truncate();
                $this->warn('🧹 Đã xóa toàn bộ dữ liệu cũ.');
            }
        }

        $this->info("🔍 Đang đọc dữ liệu từ: {$filePath}");

        try {
            $reader = new Xlsx();
            $reader->setReadDataOnly(true);

            $chunkSize = 500;
            $chunkFilter = new class($chunkSize) implements IReadFilter {
                private $startRow = 0;
                private $chunkSize = 0;
                public function __construct($chunkSize) { $this->chunkSize = $chunkSize; }
                public function setRows($startRow) { $this->startRow = $startRow; }
                public function readCell($column, $row, $worksheetName = '') {
                    return $row >= $this->startRow && $row < $this->startRow + $this->chunkSize;
                }
            };

            $reader->setReadFilter($chunkFilter);

            $rowStart = 10; // bắt đầu đọc từ dòng 10
            $totalImported = 0;
            $insertBuffer = [];

            while (true) {
                $chunkFilter->setRows($rowStart);
                $spreadsheet = $reader->load($filePath);
                $sheet = $spreadsheet->getSheet(0);
                $rows = $sheet->toArray(null, true, true, true);

                if (empty($rows) || $this->isEmptyChunk($rows)) {
                    $spreadsheet->disconnectWorksheets();
                    unset($spreadsheet);
                    break;
                }

                foreach ($rows as $row) {
                    if (empty(array_filter($row))) continue;

                    $giayPhep = $this->cleanString($row['K'] ?? '');
                    $tenBietDuoc = $this->cleanString($row['F'] ?? '');

                    if (empty($tenBietDuoc) && empty($giayPhep) || $tenBietDuoc=='' ) continue;

                    $data = [
                        'stt_tt20_2022'      => $this->parseInt($row['B'] ?? null),
                        'phan_nhom_tt15'     => $this->cleanString($row['C'] ?? ''),
                        'ten_hoat_chat'      => $this->cleanString($row['D'] ?? ''),
                        'nong_do_ham_luong'  => $this->cleanString($row['E'] ?? ''),
                        'ten_biet_duoc'      => $tenBietDuoc,
                        'dang_bao_che'       => $this->cleanString($row['G'] ?? ''),
                        'duong_dung'         => $this->cleanString($row['H'] ?? ''),
                        'don_vi_tinh'        => $this->cleanString($row['I'] ?? ''),
                        'quy_cach_dong_goi'  => $this->cleanString($row['J'] ?? ''),
                        'giay_phep_luu_hanh' => $this->cleanLicense($row['K'] ?? ''),
                        'han_dung'           => $this->cleanString($row['L'] ?? ''),
                        'co_so_san_xuat'     => $this->cleanString($row['M'] ?? ''),
                        'nuoc_san_xuat'      => $this->cleanString($row['N'] ?? ''),
                        'gia_ke_khai'        => $this->parseInt($row['O'] ?? null),
                        'don_gia'            => $this->parseInt($row['P'] ?? null),
                        'gia_von'            => $this->parseInt($row['Q'] ?? null),
                        'nha_phan_phoi'      => $this->cleanString($row['R'] ?? ''),
                        'nhom_thuoc'         => $this->cleanString($row['S'] ?? ''),
                        'link_hinh_anh'      => trim((string)($row['T'] ?? 'images/thuoc.png')),
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ];

                    $insertBuffer[] = $data;

                    if (count($insertBuffer) >= 100) {
                        $this->bulkInsert($insertBuffer);
                        $totalImported += count($insertBuffer);
                        $insertBuffer = [];
                        $this->info("📥 Đã import tạm: {$totalImported} dòng...");
                    }
                }

                $spreadsheet->disconnectWorksheets();
                unset($spreadsheet, $rows);
                gc_collect_cycles();

                $rowStart += $chunkSize;
            }

            if (!empty($insertBuffer)) {
                $this->bulkInsert($insertBuffer);
                $totalImported += count($insertBuffer);
            }

            $this->info("✅ Import hoàn tất! Tổng cộng {$totalImported} dòng mới được thêm.");
            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $this->error("⚠️ Lỗi import: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    // ------------------------
    // 🔹 Bulk insert
    // ------------------------
    private function bulkInsert(array $data): void
    {
        $existsNames = Medicine::whereIn('ten_biet_duoc', array_column($data, 'ten_biet_duoc'))
            ->pluck('ten_biet_duoc')
            ->toArray();

        $newData = array_filter($data, fn($item) =>
            !in_array($item['ten_biet_duoc'], $existsNames)
        );

        if (!empty($newData)) {
            Medicine::insert($newData);
        }
    }

    // ------------------------
    // 🔹 Helpers
    // ------------------------
    private function isEmptyChunk(array $rows): bool
    {
        foreach ($rows as $row) {
            if (!empty(array_filter($row))) return false;
        }
        return true;
    }

    private function cleanString(?string $value): string
    {
        if ($value === null) return '';
        $value = trim((string)$value);
        $value = str_replace(["\n", "\r", "\t"], ' ', $value);
        return preg_replace('/\s+/', ' ', $value);
    }

    private function parseInt($value): ?int
    {
        if (is_null($value)) return null;
        if (is_numeric($value)) return (int)$value;

        $clean = preg_replace('/[^\d]/', '', (string)$value);
        return is_numeric($clean) ? (int)$clean : null;
    }

    private function cleanLicense(?string $value): ?string
    {
        if (!$value) return null;
        $clean = preg_replace('/[,\r\n\t]+/', '', $value);
        return trim($clean);
    }
}
