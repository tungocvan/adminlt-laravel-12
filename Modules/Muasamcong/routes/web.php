<?php

use Illuminate\Support\Facades\Route;
use Modules\Muasamcong\Http\Controllers\MuasamcongController;

Route::middleware(['web','auth'])->prefix('/muasamcong')->name('muasamcong.')->group(function(){
    Route::get('/', [MuasamcongController::class,'index'])->name('index');
    Route::get('/hsmt', [MuasamcongController::class,'hsmt'])->name('hsmt');
});
