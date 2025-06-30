<?php

use Illuminate\Support\Facades\Route;
use Modules\Products\Http\Controllers\ProductsController;

Route::middleware(['web','auth'])->prefix('/admin/products')->name('products.')->group(function(){
    Route::get('/', [ProductsController::class,'index'])->name('index');
    Route::get('/add-product', [ProductsController::class,'addProduct'])->name('add');
    Route::get('/categories', [ProductsController::class,'categories'])->name('categories');
    Route::get('/edit/{id}', [ProductsController::class,'edit'])->name('update');
    Route::get('/delete/{id}', [ProductsController::class,'delete'])->name('delete');
});
