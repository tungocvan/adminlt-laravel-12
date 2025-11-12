<?php

use Illuminate\Support\Facades\Route;
use Modules\Menu\Http\Controllers\MenuController;
use Modules\Menu\Http\Controllers\NotificationsController;

Route::middleware(['web','auth'])->prefix('/menu')->name('menu.')->group(function(){
    Route::get('/', [MenuController::class,'index'])->name('index');
});


Route::middleware(['web','auth'])->group(function(){
    Route::get('user-notify', [NotificationsController::class, 'index']);
    Route::get('notifications/get',[NotificationsController::class, 'getNotificationsData'])->name('notifications.get');
    Route::post('/notifications/read/{id}', [NotificationsController::class, 'markAsRead'])->name('notifications.read');
    Route::get('language/get',[NotificationsController::class, 'getLanguage'])->name('language.get');
    Route::get('lang/{lang}', function ($lang) {
        if (!in_array($lang, ['en', 'vi'])) {
            abort(400);
        }
        session()->put('locale', $lang);
        app()->setLocale($lang);
        return redirect()->back();
    })->name('change.lang');
});

Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});