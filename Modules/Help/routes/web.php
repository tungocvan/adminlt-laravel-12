<?php

use Illuminate\Support\Facades\Route;
use Modules\Help\Http\Controllers\HelpController;

Route::middleware(['web','auth'])->prefix('/help')->name('help.')->group(function(){
    Route::get('/', [HelpController::class,'index'])->name('index');
});
