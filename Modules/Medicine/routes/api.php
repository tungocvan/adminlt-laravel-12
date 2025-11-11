<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Modules\Medicine\Http\Controllers\Api\MedicineController;

Route::post('/medicines', [MedicineController::class, 'getList']);