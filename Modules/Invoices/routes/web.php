<?php

use Illuminate\Support\Facades\Route;
use Modules\Invoices\Http\Controllers\InvoicesController;

Route::middleware(['web','auth'])->prefix('/invoices')->name('invoices.')->group(function(){
    Route::get('/', [InvoicesController::class,'index'])->name('index');
});
