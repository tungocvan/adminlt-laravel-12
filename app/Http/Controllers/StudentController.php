<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function index()
    {
        // Khi vào trang lần đầu -> chưa tìm kiếm
        return view('students.search', [
            'title'    => 'Tìm kiếm học sinh',
            'student'  => null,
            'keyword'  => '',
            'searched' => false,
        ]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'ma_dinh_danh' => 'required|digits:12',
        ], [
            'ma_dinh_danh.required' => 'Vui lòng nhập mã định danh học sinh',
            'ma_dinh_danh.digits'   => 'Mã định danh phải gồm đúng 12 chữ số',
        ]);

        // Link export CSV từ Google Sheets
        $sheetUrl = 'https://docs.google.com/spreadsheets/d/1K_bljel-0mQYUA4_4qVh8YcXLRddwzhC/export?format=csv';

        $response = Http::get($sheetUrl);

        if ($response->failed()) {
            return back()->withErrors(['msg' => 'Không tải được dữ liệu từ Google Sheet']);
        }

        $lines = array_filter(explode("\n", trim($response->body())));
        $students = collect();

        foreach ($lines as $i => $line) {
            $row = str_getcsv($line);
            if ($i === 0) continue; // bỏ header

            $students->push([
                'lop'          => $row[1] ?? '',
                'ho_ten'       => $row[2] ?? '',
                'ngay_sinh'    => $this->formatDate($row[3] ?? ''),
                'gioi_tinh'    => $row[4] ?? '',
                'ma_dinh_danh' => trim($row[5] ?? ''),
                'gvcn'         => $row[6] ?? '',
                'bao_mau'      => $row[7] ?? '',
            ]);
        }

        $keyword = trim($request->ma_dinh_danh);
        $student = $students->firstWhere('ma_dinh_danh', $keyword);

        return view('students.search', [
            'student'  => $student,
            'keyword'  => $keyword,
            'searched' => true, // ✅ để blade biết là đã bấm tìm
        ]);
    }

    private function formatDate($value): string
{
    $value = trim($value);
    if (empty($value)) return '';

    try {
        // Nếu yyyy-mm-dd
        if (preg_match('~^\d{4}-\d{2}-\d{2}$~', $value)) {
            return Carbon::parse($value)->format('d/m/Y');
        }

        // Nếu dd/mm/yyyy
        if (preg_match('~^\d{1,2}/\d{1,2}/\d{4}$~', $value)) {
            return Carbon::createFromFormat('d/m/Y', $value)->format('d/m/Y');
        }

        // Nếu dd-mm-yyyy
        if (preg_match('~^\d{1,2}-\d{1,2}-\d{4}$~', $value)) {
            return Carbon::createFromFormat('d-m-Y', $value)->format('d/m/Y');
        }

    } catch (\Exception $e) {
        // Nếu parse lỗi -> trả về nguyên bản
        return $value;
    }

    return $value;
}

}
