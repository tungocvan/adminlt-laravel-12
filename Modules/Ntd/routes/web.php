<?php

use Illuminate\Support\Facades\Route;
use Modules\Ntd\Http\Controllers\NtdController;


Route::middleware(['web'])->get('/ntd/tra-cuu-ho-so', [NtdController::class,'tracuu'])->name('ntd.tracuu');

Route::middleware(['web','auth'])->prefix('/ntd')->name('ntd.')->group(function(){
    Route::get('/', [NtdController::class,'index'])->name('index');
});

