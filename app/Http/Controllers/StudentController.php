<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index()
    {
        // render form tìm kiếm
        return view('students.search');
    }

    public function search(Request $request)
    {
        //dd($request->all());
        $request->validate([
            'ma_dinh_danh' => 'required|string',
        ]);

        $import = new StudentsImport;
        Excel::import($import, storage_path('app/public/dsk1.xlsx'));
       // dd($import->students); 
        $student = $import->students
            ->firstWhere('ma_dinh_danh', $request->ma_dinh_danh);

        //dd($student);    

        return view('students.search', [
            'student' => $student,
            'keyword' => $request->ma_dinh_danh
        ]);
    }
}
