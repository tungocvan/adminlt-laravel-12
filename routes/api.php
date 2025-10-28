
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\MobileGoogleController;
use App\Http\Controllers\API\UserOptionController;
use App\Http\Controllers\API\MedicineController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\Api\GoogleAuthController;

Route::post('/google/callback', [GoogleAuthController::class, 'callback']);

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


Route::prefix('user-info')->group(function () {    
    Route::post('/', [UserOptionController::class, 'getFilterUserInfo']);
    Route::post('/update', [UserOptionController::class, 'updateUserInfo']);
    Route::get('/{user_id}', [UserOptionController::class, 'getUserInfo']);
    Route::delete('/{user_id}', [UserOptionController::class, 'deleteUserInfo']);
    Route::post('/upload', [UserOptionController::class, 'store']);
    
});

Route::post('/medicines', [MedicineController::class, 'getList']);


Route::prefix('categories')->group(function () {
    Route::post('/', [CategoryController::class, 'index']);      // Lấy danh sách (lọc, tìm kiếm)
    Route::post('/store', [CategoryController::class, 'store']); // Tạo mới
    Route::post('/{key}', [CategoryController::class, 'show']);   // Xem chi tiết
    Route::post('/update/{id}', [CategoryController::class, 'update']); // Cập nhật
    Route::post('/delete/{id}', [CategoryController::class, 'destroy']); // Xóa
});