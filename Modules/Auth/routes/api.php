
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\Api\AuthController;

Route::controller(AuthController::class)->group(function(){
    Route::get('init', 'init');
    Route::post('register', 'register'); 
    Route::post('login', 'login');
});
