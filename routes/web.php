<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
// use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\SearchController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [HomeController::class, 'index']);

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('roles/permission',[RoleController::class,'permission'])->name('roles.permission');
Route::post('roles/permission',[RoleController::class,'storePermission'])->name('roles.store-permission');
Route::group(['middleware' => ['auth']], function() {
    Route::resource('admin/roles', RoleController::class);
    Route::resource('admin/users', UserController::class);
});


Route::middleware(['auth'])->prefix('/admin')->name('admin.')->group(function(){
    Route::get('/profile', [ProfileController::class,'index'])->name('profile');
    Route::patch('/profile', [ProfileController::class,'update'])->name('profile-update');
    Route::put('/profile', [ProfileController::class,'updatePassword'])->name('profile-password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile-destroy');
});

Route::get('notifications/get',[NotificationsController::class, 'getNotificationsData'])->name('notifications.get');
Route::get('navbar/search',[SearchController::class,'showNavbarSearchResults']);
Route::post('navbar/search',[SearchController::class,'showNavbarSearchResults']);

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});
