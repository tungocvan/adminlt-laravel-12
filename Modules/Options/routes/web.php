<?php

use Illuminate\Support\Facades\Route;
use Modules\Options\Http\Controllers\OptionsController;

Route::middleware(['web','auth'])->group(function(){
    Route::any('bulk-action', [OptionsController::class, 'bulkAction'])->name('options.bulk');
    Route::resource('options', OptionsController::class);
    
});

