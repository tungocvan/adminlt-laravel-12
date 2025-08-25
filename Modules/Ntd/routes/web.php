<?php

use Illuminate\Support\Facades\Route;
use Modules\Ntd\Http\Controllers\NtdController;


Route::middleware(['web','auth'])->prefix('/ntd')->name('ntd.')->group(function(){
    Route::get('/', [NtdController::class,'index'])->name('index');
    Route::get('/tra-cuu-ho-so', [NtdController::class,'tracuu'])->name('tracuu');
});

