<?php

use Illuminate\Support\Facades\Route;
use Modules\Medicine\Http\Controllers\MedicineController;

Route::middleware(['web','auth'])->prefix('/medicine')->name('medicine.')->group(function(){
    Route::get('/', [MedicineController::class,'index'])->name('index');
    Route::get('/medicine-stock', [MedicineController::class,'medicineStock'])->name('stock');
});
