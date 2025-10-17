
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use  Modules\Order\Http\Controllers\Api\OrderController;


Route::middleware(['web'])->prefix('/api')->controller(OrderController::class)->group(function(){
    Route::get('order', 'index');
});

// Route::middleware(['web','auth'])->prefix('/api')->controller(OrderController::class)->group(function(){
//     Route::post('order', 'index');
// });