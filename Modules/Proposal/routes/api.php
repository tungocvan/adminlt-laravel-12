<?php

use Illuminate\Support\Facades\Route;
use Modules\Proposal\Http\Controllers\Api\ProposalController;


// Route::middleware('auth:sanctum')->controller(ProposalController::class)->prefix('proposal')->group(function(){
//         Route::get('/', 'index');              
// });

Route::prefix('proposal')->controller(ProposalController::class)->group(function(){
        Route::get('/', 'index');              
});