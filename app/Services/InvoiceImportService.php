<?php

namespace App\Services;

use Modules\Invoices\Models\Invoices;
use Rap2hpoutre\FastExcel\FastExcel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InvoiceImportService
{
    /**
     * Import Excel vào bảng invoices
     */
    public function import(string $filePath, string $type = 'sold', callable $callback = null)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File không tồn tại: $filePath");
        }

        if ($callback) {
            $callback("📂 Đang đọc file Excel: $filePath");
        }

        $rows = (new FastExcel)->import($filePath);

        $count = 0;

        foreach ($rows as $row) {
            try {

                Invoices::create([
                    'lookup_code'        => trim($row['Mã tra cứu'] ?? ''),
                    'symbol'             => trim($row['Ký hiệu'] ?? ''),
                    'invoice_number'     => trim($row['Số hóa đơn'] ?? ''),
                    'type'               => trim($row['Loại hóa đơn'] ?? ''),
                    'issued_date' => !empty($row['Ngày lập'])
                        ? Carbon::createFromFormat('d/m/Y', trim($row['Ngày lập']))
                        : null,

                    'tax_code'      => trim($row['Mã số thuế'] ?? ''),
                    'name'          => trim($row['Đơn vị'] ?? ''),
                    'address'       => trim($row['Địa chỉ'] ?? ''),
                    'email'         => trim($row['Email'] ?? ''),
                    'phone'         => trim($row['Phone'] ?? ''),
                    'tax_rate'          => $this->toDecimal($row['Thuế suất'] ?? 0),
                    'vat_amount'        => $this->toDecimal($row['Tiền VAT'] ?? 0),
                    'amount_before_vat' => $this->toDecimal($row['Trước VAT'] ?? 0),
                    'total_amount'      => $this->toDecimal($row['Thành tiền'] ?? 0),
                    'invoice_type' => $type === 'sold' ? 'sold' : 'purchase',
                ]);

                $count++;

                if ($callback) {
                    $callback("✔ Đã import hóa đơn số: " . ($row['Số hóa đơn'] ?? 'N/A'));
                }

            } catch (\Throwable $e) {

                if ($callback) {
                    $callback("❌ Lỗi import hóa đơn: " . ($row['Số hóa đơn'] ?? 'N/A') . ' - ' . $e->getMessage());
                }
            }
        }

        if ($callback) {
            $callback("🎉 Hoàn tất import! Tổng cộng: $count hóa đơn.");
        }

        return $count;
    }

    private function toDecimal($value)
    {
        if ($value === null || $value === '' || $value === false) {
            return 0;
        }

        // xử lý cả dạng 1,234,567 và 1.234.567,89
        $value = str_replace(['.', ','], ['', '.'], $value);

        return floatval($value);
    }
}
