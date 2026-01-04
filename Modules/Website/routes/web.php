<?php

use Illuminate\Support\Facades\Route;
use Modules\Website\Http\Controllers\WebsiteController;
use Modules\Website\Http\Controllers\ProductController;
use Modules\Website\Http\Controllers\CartController;
use Modules\Website\Http\Controllers\CheckoutController;

Route::middleware(['web'])->prefix('/website')->name('website.')->group(function(){
    Route::get('/', [WebsiteController::class,'index'])->name('index');
    Route::get('/about', [WebsiteController::class,'about'])->name('about');
    Route::get('/help-order', [WebsiteController::class,'helpOrder'])->name('help-order');
    Route::get('/news', [WebsiteController::class,'news'])->name('news');
    Route::get('/register', [WebsiteController::class,'register'])->name('register');
    Route::get('/product', [ProductController::class,'index'])->name('product.index');
    Route::get('/product/{id}', [ProductController::class,'productDetail'])->name('product.detail');

    Route::controller(ProductController::class)
            ->prefix('products')
            ->name('products.')
            ->group(function () {
                // GET /website/products - Danh sách sản phẩm
                Route::get('/', 'index')->name('index');
                
                // GET /website/products/{slug} - Chi tiết sản phẩm
                Route::get('/{slug}', 'show')->name('show');
    });

    Route::controller(CartController::class)
            ->prefix('cart')
            ->name('cart.')
            ->group(function () {
                // GET /website/cart - Trang giỏ hàng
                Route::get('/', 'index')->name('index');
    });

    Route::controller(CheckoutController::class)
            ->prefix('checkout')
            ->name('checkout.')
            ->group(function () {
                // GET /website/checkout - Trang thanh toán
                Route::get('/', 'index')->name('index');
                
                // POST /website/checkout - Xử lý thanh toán
                Route::post('/', 'process')->name('process');
    });

    Route::get('/order-success/{code}', [CheckoutController::class, 'success'])
    ->name('order.success');

    
    
});
