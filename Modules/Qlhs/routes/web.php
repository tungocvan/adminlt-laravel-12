<?php

use Illuminate\Support\Facades\Route;
use Modules\Qlhs\Http\Controllers\QlhsController;

Route::middleware(['web','auth'])->prefix('/qlhs')->name('qlhs.')->group(function(){
    Route::get('/', [QlhsController::class,'index'])->name('index');
});
