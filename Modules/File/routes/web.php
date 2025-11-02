<?php

use Illuminate\Support\Facades\Route;
use Modules\File\Http\Controllers\FileController;

Route::middleware(['web','auth'])->prefix('/file')->name('file.')->group(function(){
     Route::get('/', [FileController::class,'index'])->name('index');
     Route::get('/json-excel', [FileController::class,'jsonExcel'])->name('json-excel');
     Route::get('/db-excel', [FileController::class,'dbExcel'])->name('db-excel');
     Route::get('/migrations', [FileController::class,'migrations'])->name('migrations');
     Route::get('/artisan', [FileController::class,'artisan'])->name('artisan');
     Route::get('/env', [FileController::class,'env'])->name('env');
});
