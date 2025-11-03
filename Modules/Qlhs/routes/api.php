<?php

use Illuminate\Support\Facades\Route;
use Modules\Qlhs\Http\Controllers\Api\QlhsController;


// Route::middleware('auth:sanctum')->controller(QlhsController::class)->prefix('qlhs')->group(function(){
//         Route::get('/', 'index');              
// });

Route::prefix('qlhs')->controller(QlhsController::class)->group(function(){
        Route::get('/', 'index');              
});