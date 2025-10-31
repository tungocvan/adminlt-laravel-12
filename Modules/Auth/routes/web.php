<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\LoginController;
use Modules\Auth\Http\Controllers\RegisterController;
use Modules\Auth\Http\Controllers\ForgotPasswordController;
use Modules\Auth\Http\Controllers\ResetPasswordController;
use Modules\Auth\Http\Controllers\GoogleController;

// Route::middleware(['web'])->prefix('/auth')->name('auth.')->group(function(){
//     Route::get('/login', [AuthController::class,'index'])->name('login');
//     Route::get('/register', [AuthController::class,'index'])->name('register');
//     Route::get('/forgot', [AuthController::class,'index'])->name('forgot');
// });

Route::middleware(['web'])->group(function(){
    Route::get('/login', [LoginController::class,'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class,'login']);
    Route::post('/logout', [LoginController::class,'logout'])->name('logout');
    
    Route::get('/register', [RegisterController::class,'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class,'create']);

    Route::get('/password/reset', [ForgotPasswordController::class,'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class,'sendResetLinkEmail'])->name('password.email');

    Route::get('/password/reset/{token}', [ResetPasswordController::class,'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class,'reset'])->name('password.update');
    
});

Route::middleware(['web'])->controller(GoogleController::class)->group(function(){
    Route::get('/auth/google', 'redirectToGoogle')->name('auth.google');
    Route::get('/auth/google/callback', 'handleGoogleCallback');
});

