
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\MobileGoogleController;



Route::controller(AuthController::class)->group(function(){
    Route::get('init', 'init');
    Route::post('register', 'register');
    Route::post('login', 'login');
});

// Route::middleware('protect.auth.api')->group(function () {
//     Route::get('/init', [AuthController::class, 'init']);
//     Route::post('/login', [AuthController::class, 'login']);
//     Route::post('/register', [AuthController::class, 'register']);
// });


Route::controller(UserController::class)->group(function(){      
    Route::post('users','index');           
    Route::get('users/{id}','show');           
    Route::delete('users/{id}','destroy');    
    Route::delete('users','destroyMultiple');
    Route::put('users/{id}','update');

});



Route::post('/auth/google/verify', [MobileGoogleController::class, 'verify']);
Route::middleware('auth:sanctum')->get('/user', function () {
    return auth()->user();
});

// Route::middleware('auth:sanctum')->controller(UserController::class)->group(function(){
//     Route::post('users', 'index');    
//     Route::get('users/{$id}', 'index');    
//     Route::delete('users/{$id}', 'destroy');    
// });

Route::middleware('auth:sanctum')->controller(ProductController::class)->group(function(){
    Route::post('products', 'filter');
    Route::get('products/{id}', 'show');
    Route::post('order', 'orderStore');
});


// Route::middleware('auth:sanctum')->group( function () {
//     Route::resource('products', ProductController::class);
// });


 