<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public $students;

    public function __construct()
    {
        $this->students = collect();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Maatwebsite sẽ slug header về: 'ma_dinh_danh_hoc_sinh', 'ho_va_ten', 'ngay_sinh', 'lop', 'gioi_tinh'
            $this->students->push([
                'ma_dinh_danh' => $this->toString($row['ma_dinh_danh_hoc_sinh'] ?? null),
                'ho_ten'       => trim((string)($row['ho_va_ten'] ?? '')),
                'ngay_sinh'    => $this->formatDate($row['ngay_sinh'] ?? null),
                'lop'          => trim((string)($row['lop'] ?? '')),
                'gioi_tinh'    => trim((string)($row['gioi_tinh'] ?? '')),
                'gvcn'    => trim((string)($row['gvcn'] ?? '')),
                'bao_mau'    => trim((string)($row['bao_mau'] ?? '')),
            ]);
        }
    }

    private function toString($val): string
    {
        // Ép về string an toàn (kể cả đang là số)
        if (is_null($val)) return '';
        return trim((string)$val);
    }

    private function formatDate($val): ?string
    {
        // Trả về yyyy-mm-dd nếu parse được, còn không thì trả về chuỗi gốc (đã trim)
        if (empty($val)) return null;

        // Nếu là instance \DateTimeInterface (Excel parse sẵn)
        if ($val instanceof \DateTimeInterface) {
            return $val->format('Y-m-d');
        }

        // Nếu là chuỗi dd/mm/yyyy hoặc dd-mm-yyyy
        $s = trim((string)$val);
        if (preg_match('~^(\d{1,2})[/-](\d{1,2})[/-](\d{2,4})$~', $s, $m)) {
            $d = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $mth = str_pad($m[2], 2, '0', STR_PAD_LEFT);
            $y = (int)$m[3]; if ($y < 100) $y += 2000;
            return "$y-$mth-$d";
        }

        return $s; // để nguyên nếu không nhận dạng được
    }
}
