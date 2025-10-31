<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminController;
use Modules\Admin\Http\Controllers\ProfileController;

Route::middleware(['web','auth'])->prefix('/admin')->name('admin.')->group(function(){
    Route::get('/', [AdminController::class,'index'])->name('index');
    Route::get('/component', [AdminController::class,'component'])->name('component');
    Route::get('/datatables', [AdminController::class,'datatables'])->name('datatables');
});

// Route::middleware(['web','auth'])->prefix('/user')->name('user.')->group(function(){
//     Route::get('/', [AdminController::class,'user'])->name('index');
// });

Route::middleware(['auth'])->prefix('/admin')->name('admin.')->group(function(){
    Route::get('/profile', [ProfileController::class,'index'])->name('profile');
    Route::patch('/profile', [ProfileController::class,'update'])->name('profile-update');
    Route::put('/profile', [ProfileController::class,'updatePassword'])->name('profile-password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile-destroy');
    
});