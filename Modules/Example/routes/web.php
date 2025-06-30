<?php

use Illuminate\Support\Facades\Route;
use Modules\Example\Http\Controllers\ExampleController;

Route::middleware(['web','auth'])->prefix('/example')->name('example.')->group(function(){
    Route::get('/', [ExampleController::class,'index'])->name('index');
    Route::get('/form', [ExampleController::class,'form'])->name('form');
});
