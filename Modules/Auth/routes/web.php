<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;

Route::middleware(['web'])->prefix('/auth')->name('auth.')->group(function(){
    Route::get('/login', [AuthController::class,'index'])->name('login');
    Route::get('/register', [AuthController::class,'index'])->name('register');
    Route::get('/forgot', [AuthController::class,'index'])->name('forgot');
});
