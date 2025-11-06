<?php

use Illuminate\Support\Facades\Route;
use Modules\Components\Http\Controllers\ComponentsController;

Route::middleware(['web','auth'])->prefix('/components')->name('components.')->group(function(){
    Route::get('/', [ComponentsController::class,'index'])->name('index');
});
