<?php

namespace Modules\Banggia\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BangBaoGia;
use Illuminate\Support\Facades\Storage;
use App\Helpers\TnvHelper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class BanggiaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
         $this->middleware('permission:banggia-list|banggia-create|banggia-edit|banggia-delete', ['only' => ['index','show']]);
         $this->middleware('permission:banggia-create', ['only' => ['create','store']]);
         $this->middleware('permission:banggia-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:banggia-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        return view('Banggia::banggia');
    }

    public function download($id)
    {
        $record = \App\Models\BangBaoGia::findOrFail($id);

        if (!$record->file_path) {
            return back()->with('error', '❌ File chưa được tạo.');
        } 
        return TnvHelper::downloadFile($record->file_path, 'public');
    }

    public function downloadPdf($id)
    {
        $record = \App\Models\BangBaoGia::findOrFail($id);

        if (!$record->pdf_path) {
            return back()->with('error', '❌ File chưa được tạo.');
        } 
        return TnvHelper::downloadFile($record->pdf_path, 'public');
    }

    public function downloadPdf1($id)
    {
        $record = \App\Models\BangBaoGia::findOrFail($id);
    
        $file = storage_path('app/public/' . $record->file_path);
        if (!$record->file_path || !file_exists($file)) {
            abort(404, 'File không tồn tại');
        }
    
        $spreadsheet = IOFactory::load($file);
        $rows = $spreadsheet->getActiveSheet()->toArray();
    
        $html = '<!DOCTYPE html>
    <html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid black; padding: 5px; word-wrap: break-word; }
    </style>
    </head>
    <body>
    <table>';
    
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . htmlspecialchars($cell ?? '') . '</td>';
            }
            $html .= '</tr>';
        }
    
        $html .= '</table></body></html>';
    
        $pdf = Pdf::loadHTML($html)
                  ->setPaper('a4', 'landscape');
    
        return $pdf->download("bang-gia-{$record->ma_so}.pdf");
    }
    


    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
