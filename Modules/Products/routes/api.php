<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Products\Http\Controllers\Api\ProductController;

Route::middleware('auth:sanctum')->controller(ProductController::class)->group(function(){
    Route::post('products', 'filter');
    Route::get('products/{id}', 'show');
    Route::post('order', 'orderStore');
});
