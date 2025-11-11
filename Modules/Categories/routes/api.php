
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Categories\Http\Controllers\Api\CategoryController;

Route::prefix('categories')->group(function () {
    Route::post('/', [CategoryController::class, 'index']);      // Lấy danh sách (lọc, tìm kiếm)
    Route::post('/store', [CategoryController::class, 'store']); // Tạo mới
    Route::post('/{key}', [CategoryController::class, 'show']);   // Xem chi tiết
    Route::post('/update/{id}', [CategoryController::class, 'update']); // Cập nhật
    Route::post('/delete/{id}', [CategoryController::class, 'destroy']); // Xóa
});