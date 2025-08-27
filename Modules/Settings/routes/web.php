<?php

use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\SettingsController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FullCalenderController;

Route::middleware(['web','auth'])->prefix('/settings')->name('settings.')->group(function(){
    Route::get('/', [SettingsController::class,'index'])->name('index');
    Route::get('/help', [SettingsController::class,'help'])->name('help');
    Route::get('/menu', [SettingsController::class,'menu'])->name('menu');
    Route::get('/artisan', [SettingsController::class,'artisan'])->name('artisan');
    Route::get('/components', [SettingsController::class,'components'])->name('components');
    Route::get('/components/tabs', [SettingsController::class,'tabs'])->name('components.tabs');
    Route::get('/components/form', [SettingsController::class,'form'])->name('components.form');
    Route::get('/components/posts', [SettingsController::class,'posts'])->name('components.posts');
    Route::get('/components/email', [SettingsController::class,'email'])->name('components.email');
    Route::get('/components/file-manager', [SettingsController::class,'fileManager'])->name('components.file-manager');
    Route::get('/components/tables', [SettingsController::class,'tables'])->name('components.tables');
    Route::get('/components/vnAddress', [SettingsController::class,'vnAddress'])->name('components.vn-Address');
    Route::get('/components/users/create', [ FormController::class, 'createUser' ]);
    Route::post('/components/users/create', [ FormController::class, 'storeUser' ])->name('store-user.store'); 
    Route::get('/components/fullcalender', [FullCalenderController::class,'index']);
    Route::post('/components/fullcalenderAjax', [FullCalenderController::class,'ajax']);

});

// Route::controller(FullCalenderController::class)->group(function(){    

    
//     Route::get('fullcalender', [FullCalenderController::class,'index']);
//     Route::post('fullcalenderAjax', 'ajax');
// });