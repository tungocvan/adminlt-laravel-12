<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\CustomerController;

Route::middleware(['web','auth'])->prefix('/customer')->name('customer.')->group(function(){
    Route::get('/', [CustomerController::class,'index'])->name('index');
    Route::get('/update-customer', [CustomerController::class,'updateCustomer'])->name('update-customer');
});
