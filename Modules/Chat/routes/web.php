<?php

use Illuminate\Support\Facades\Route;
use Modules\Chat\Http\Controllers\ChatController;

Route::middleware(['web','auth'])->prefix('/chat')->name('chat.')->group(function(){
    Route::get('/', [ChatController::class,'index'])->name('index');
    Route::get('/history/{userId}', [ChatController::class, 'history']);
    Route::post('/send', [ChatController::class, 'store']);
});
