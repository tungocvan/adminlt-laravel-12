<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\OrderController;

Route::middleware(['web','auth'])->prefix('/order')->resource('order', OrderController::class);

Route::middleware(['web', 'auth'])
    ->get('order/print/{order}/{type?}', [OrderController::class, 'print'])
    ->name('order.print');

Route::middleware(['web','auth'])
->get('order/pdf/{order}/{type?}', [OrderController::class, 'exportPdf'])
->name('order.pdf');
