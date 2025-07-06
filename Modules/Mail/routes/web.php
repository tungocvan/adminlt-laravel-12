<?php

use Illuminate\Support\Facades\Route;
use Modules\Mail\Http\Controllers\MailController;

Route::middleware(['web','auth'])->prefix('/mail')->name('mail.')->group(function(){
    Route::get('/', [MailController::class,'index'])->name('index');
    Route::get('/user-notify', [MailController::class, 'notification']);
});
