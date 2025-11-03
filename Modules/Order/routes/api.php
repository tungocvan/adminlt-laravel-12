
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use  Modules\Order\Http\Controllers\Api\OrderController;


// Route::middleware(['web'])->prefix('/api/orders')->controller(OrderController::class)->group(function(){
//     Route::post('list','list'); 
//     Route::get('{id}','show');
//     Route::post('/','store');
//     Route::put('{id}','update');
//     Route::delete('{id}','destroy');
// });

// Route::middleware(['web','auth'])->prefix('/api')->controller(OrderController::class)->group(function(){
//     Route::post('order', 'index');
// });
Route::prefix('/orders')->group(function () {
    Route::post('/', [OrderController::class, 'list']); // 🔹 Dùng POST để lấy danh sách
    Route::get('{id}', [OrderController::class, 'show']);
    Route::post('/update', [OrderController::class, 'store']);
    Route::put('{id}', [OrderController::class, 'update']);
    Route::delete('{id}', [OrderController::class, 'destroy']);
    Route::post('/update-item', [OrderController::class, 'updateItem']);
    Route::post('/remove-item', [OrderController::class, 'removeItem']);
});