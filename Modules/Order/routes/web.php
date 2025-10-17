<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\OrderController;

Route::middleware(['web','auth'])->prefix('/order')->resource('order', OrderController::class);
