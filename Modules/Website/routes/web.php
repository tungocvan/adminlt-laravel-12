<?php

use Illuminate\Support\Facades\Route;
use Modules\Website\Http\Controllers\WebsiteController;
use Modules\Website\Http\Controllers\ProductController;

Route::middleware(['web'])->prefix('/website')->name('website.')->group(function(){
    Route::get('/', [WebsiteController::class,'index'])->name('index');
    Route::get('/about', [WebsiteController::class,'about'])->name('about');
    Route::get('/help-order', [WebsiteController::class,'helpOrder'])->name('help-order');
    Route::get('/news', [WebsiteController::class,'news'])->name('news');
    Route::get('/register', [WebsiteController::class,'register'])->name('register');
    Route::get('/product', [ProductController::class,'index'])->name('product.index');
    Route::get('/product/{id}', [ProductController::class,'productDetail'])->name('product.detail');
    Route::prefix('products')->name('products.')->group(function () {    
        Route::get('/', [ProductController::class, 'index'])->name('index');    
        Route::get('/{slug}', [ProductController::class, 'show'])->name('show');
    });
});
