<?php

use Illuminate\Support\Facades\Route;
use Modules\Products\Http\Controllers\ProductsController;

Route::middleware(['web','auth'])->prefix('/products')->name('products.')->group(function(){
    Route::get('/', [ProductsController::class,'index'])->name('index');
    Route::get('/add', [ProductsController::class,'create'])->name('create');
    Route::get('/add-more', [ProductsController::class, 'addMore']);
    Route::post('/add-more', [ProductsController::class, 'store'])->name('add-more.store');
    
    Route::get('cart', [ProductsController::class, 'cart'])->name('cart');
    Route::post('cart', [ProductsController::class, 'checkout'])->name('checkout.cart');
    Route::get('add-to-cart/{id}', [ProductsController::class, 'addToCart'])->name('add.to.cart');
    Route::patch('update-cart', [ProductsController::class, 'update'])->name('update.cart');
    Route::delete('remove-from-cart', [ProductsController::class, 'remove'])->name('remove.from.cart');
    Route::get('/thank-you', [ProductsController::class,'thankyou'])->name('thankyou');    
});
