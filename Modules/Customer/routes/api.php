<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\Api\CustomerController;


// Route::middleware('auth:sanctum')->controller(CustomerController::class)->prefix('customer')->group(function(){
//         Route::get('/', 'index');              
// });

Route::prefix('customer')->controller(CustomerController::class)->group(function(){
        Route::get('/', 'index');              
});