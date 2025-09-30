<?php

use Illuminate\Support\Facades\Route;
use Modules\Website\Http\Controllers\WebsiteController;

Route::middleware(['web','auth'])->prefix('/website')->name('website.')->group(function(){
    Route::get('/', [WebsiteController::class,'index'])->name('index');
    Route::get('/about', [WebsiteController::class,'about'])->name('about');
    Route::get('/help-order', [WebsiteController::class,'helpOrder'])->name('help-order');
    Route::get('/news', [WebsiteController::class,'news'])->name('news');
    Route::get('/register', [WebsiteController::class,'register'])->name('register');
});
