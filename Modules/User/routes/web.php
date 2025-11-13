<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;
use App\Models\User;
// use Modules\User\Livewire\UserManager;

Route::middleware(['web','auth'])->prefix('/user')->name('user.')->group(function(){
    Route::get('/', [UserController::class,'index'])->name('index');  
    // Route::get('/user-manager', UserManager::class);  
   
});

