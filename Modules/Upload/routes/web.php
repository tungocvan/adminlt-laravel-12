<?php

use Illuminate\Support\Facades\Route;
use Modules\Upload\Http\Controllers\UploadController;
use App\Livewire\Upload\UploadImage;
use App\Livewire\Upload\UploadImages;

Route::middleware(['web','auth'])->prefix('/upload')->name('upload.')->group(function(){
    Route::get('/', [UploadController::class,'index'])->name('index');
    Route::get('image-upload', UploadImage::class);
    Route::post('image-upload', [UploadImage::class,'store'])->name('image.store');
    Route::get('images-upload', UploadImages::class);
    Route::post('images-upload', [UploadImages::class,'store'])->name('images.store');
});
