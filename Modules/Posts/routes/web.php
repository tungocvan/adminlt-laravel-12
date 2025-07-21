<?php

use Illuminate\Support\Facades\Route;
use Modules\Posts\Http\Controllers\PostsController;

Route::middleware(['web','auth'])->group(function(){
    Route::get('/posts', [PostsController::class,'index'])->name('posts.index');
    Route::post('/posts', [PostsController::class, 'store'])->name('posts.store');
    Route::get('/posts/{id}/approve', [PostsController::class, 'approve'])->name('posts.approve');
    Route::get('/notifications/{id}/mark-as-read', [PostsController::class, 'markAsRead'])->name('notifications.mark.as.read');

    Route::get('posts/create',[PostsController::class,'create']);
    Route::post('posts/store',[PostsController::class,'storePost'])->name('posts.store-post');
});

