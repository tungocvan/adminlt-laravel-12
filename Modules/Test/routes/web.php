<?php

use Illuminate\Support\Facades\Route;
use Modules\Test\Http\Controllers\TestController;

Route::middleware(['web','auth'])->prefix('/test')->name('test.')->group(function(){
    Route::get('/', [TestController::class,'index'])->name('index');
});
