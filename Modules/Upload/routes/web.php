<?php

use Illuminate\Support\Facades\Route;
use Modules\Upload\Http\Controllers\DropzoneController;
use Modules\Upload\Http\Controllers\UploadController;
use Modules\Upload\Http\Controllers\ImageGalleryController;
use App\Livewire\Upload\UploadImage;
use App\Livewire\Upload\UploadImages;

Route::middleware(['web','auth'])->prefix('/upload')->name('upload.')->group(function(){
    Route::get('/', [UploadController::class,'index'])->name('index');
    Route::get('image-upload', UploadImage::class);
    Route::post('image-upload', [UploadImage::class,'store'])->name('image.store');
    Route::get('images-upload', UploadImages::class);
    Route::post('images-upload', [UploadImages::class,'store'])->name('images.store');

    Route::get('image-upload-resize', [UploadController::class, 'imageResize']);
    Route::post('image-upload-resize', [UploadController::class, 'storeImageResize'])->name('image-resize.store');

    Route::get('image-gallery', [ImageGalleryController::class,'index']);
    Route::post('image-gallery', [ImageGalleryController::class,'upload']);
    Route::delete('image-gallery/{id}', [ImageGalleryController::class,'destroy']);

});

Route::middleware(['web','auth'])->prefix('/dropzone')->name('dropzone.')->group(function(){
    Route::get('/', [DropzoneController::class, 'index']);
    Route::post('store', [DropzoneController::class, 'store'])->name('store');
});
