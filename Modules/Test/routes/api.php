<?php

use Illuminate\Support\Facades\Route;
use Modules\Test\Http\Controllers\Api\TestController;


// Route::middleware('auth:sanctum')->controller(TestController::class)->prefix('test')->group(function(){
//         Route::get('/', 'index');              
// });

Route::prefix('test')->controller(TestController::class)->group(function(){
        Route::get('/', 'index');              
});