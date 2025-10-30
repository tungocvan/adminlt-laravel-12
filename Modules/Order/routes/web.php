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

Route::get('/orders/{folder}/{filename}', function ($folder, $filename) {
    $path = storage_path("app/public/orders/{$folder}/{$filename}");

    if (!file_exists($path)) {
        abort(404, 'Không tìm thấy file PDF');
    }

    return response()->file($path, [
        'Content-Type' => 'application/pdf',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0',
    ]);
});