<?php

use Illuminate\Support\Facades\Route;
use Modules\Invoices\Http\Controllers\InvoicesController;

Route::middleware(['web','auth'])->prefix('/invoices')->name('invoices.')->group(function(){
    Route::get('/', [InvoicesController::class,'index'])->name('index');
    Route::get('/create-token', [InvoicesController::class,'createToken'])->name('create-token');
    Route::get('/hoadon', [InvoicesController::class,'hoadon'])->name('hoadon');
    Route::get('/hoadon-list', [InvoicesController::class,'hoadonList'])->name('hoadon-list');
    Route::get('download/{lookup_code}', [InvoicesController::class, 'download'])->name('download');
});
