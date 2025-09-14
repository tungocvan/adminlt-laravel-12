<?php

use Illuminate\Support\Facades\Route;
use Modules\Categories\Http\Controllers\CategoriesController;
//use App\Livewire\Category\Index;

Route::middleware(['web','auth'])->prefix('/categories')->name('categories.')->group(function(){
    Route::get('/', [CategoriesController::class,'index'])->name('index');
   // Route::get('/', Index::class)->name('categories.index');
    // Route::get('/add', [CategoriesController::class,'create'])->name('create');
});
