<?php

use Illuminate\Support\Facades\Route;
use Modules\Components\Http\Controllers\Api\ComponentsController;


// Route::middleware('auth:sanctum')->controller(ComponentsController::class)->prefix('components')->group(function(){
//         Route::get('/', 'index');              
// });

Route::prefix('components')->controller(ComponentsController::class)->group(function(){
        Route::get('/', 'index');              
});