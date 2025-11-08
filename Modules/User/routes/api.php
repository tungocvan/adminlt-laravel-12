<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\UserController;
// use Modules\User\Models\User;
// use Illuminate\Validation\ValidationException;
// use Modules\User\Http\Controllers\Api\SanctumController;

// Route::get('/api/user', function (Request $request) {
//     return 'api module user';
// });


// Route::post('/api/user/login', [SanctumController::class,'login'])->name('sanctum.login');
// Route::post('/api/user/refresh-token', [SanctumController::class,'refreshToken'])->name('sanctum.refresh');
// Route::middleware('auth:sanctum')->get('/api/user/list-token', [SanctumController::class,'listToken']);

Route::controller(UserController::class)->group(function(){      
    Route::post('users','index');           
    Route::get('users/{id}','show');           
    Route::delete('users/{id}','destroy');    
    Route::delete('users','destroyMultiple');
    Route::put('users/{id}','update');
    Route::put('users/{id}/app','updateApp');

});
