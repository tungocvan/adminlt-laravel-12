<?php

use Illuminate\Support\Facades\Route;
use Modules\Website\Http\Controllers\WebsiteController;

Route::middleware(['web','auth'])->prefix('/website')->name('website.')->group(function(){
    Route::get('/', [WebsiteController::class,'index'])->name('index');
});
