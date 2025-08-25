<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;
use App\Models\Hocsinh;

class HocsinhImport implements OnEachRow, WithHeadingRow
{
    public function onRow(Row $row)
    {
        $r = $row->toArray();

        $ngaySinh = null;
        if (!empty($r['ngay_sinh'])) {
            if (is_numeric($r['ngay_sinh'])) {
                $ngaySinh = ExcelDate::excelToDateTimeObject($r['ngay_sinh']);
            } else {
                try {
                    $ngaySinh = Carbon::createFromFormat('d/m/Y', $r['ngay_sinh']);
                } catch (\Exception $e) {
                    $ngaySinh = Carbon::parse($r['ngay_sinh'], null);
                }
            }
        }

        Hocsinh::updateOrCreate(
            ['ma_dinh_danh_hoc_sinh' => $r['ma_dinh_danh_hoc_sinh']],
            [
                'stt'   => $r['stt'] ?? null,
                'lop'   => $r['lop'] ?? null,
                'ho_va_ten' => $r['ho_va_ten'] ?? null,
                'ngay_sinh' => $ngaySinh,
                'gioi_tinh' => $r['gioi_tinh'] ?? null,
                'gvcn'  => $r['gvcn'] ?? null,
                'bao_mau' => $r['bao_mau'] ?? null,
            ]
        );
    }
}

