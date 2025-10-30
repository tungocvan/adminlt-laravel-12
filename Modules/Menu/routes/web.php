<?php

use Illuminate\Support\Facades\Route;
use Modules\Menu\Http\Controllers\MenuController;

Route::middleware(['web','auth'])->prefix('/menu')->name('menu.')->group(function(){
    Route::get('/', [MenuController::class,'index'])->name('index');
});
