<?php

use Illuminate\Support\Facades\Route;
use Modules\Banggia\Http\Controllers\BanggiaController;
use App\Models\BangBaoGia;
use Illuminate\Support\Facades\Storage;

Route::middleware(['web','auth'])->prefix('/banggia')->name('banggia.')->group(function(){
    Route::get('/', [BanggiaController::class,'index'])->name('index');
});
Route::get('/banggia/{id}/download', [BangGiaController::class, 'download'])->withoutMiddleware(['web', 'throttle', 'VerifyCsrfToken'])->name('banggia.download');

Route::get('/banggia/{id}/pdf', [BangGiaController::class, 'downloadPdf'])->name('banggia.downloadPdf');

Route::get('/baogia/{filename}', function ($filename) {
    $path = storage_path('app/public/baogia/' . $filename);

    if (!file_exists($path)) {
        abort(404, 'File not found.');
    }

    return response()->download($path, $filename);
});
