<?php

use Illuminate\Support\Facades\Route;
use Modules\Proposal\Http\Controllers\ProposalController;

// Route::middleware(['web','auth'])->prefix('/proposal')->name('proposal.')->group(function(){
//     Route::get('/', [ProposalController::class,'index'])->name('index');
// });

Route::middleware(['web','auth'])
    ->prefix('admin/proposals')
    ->name('admin.proposals.')
    ->group(function () {
        Route::get('/', [ProposalController::class, 'index'])->name('index');

        Route::get('/create', [ProposalController::class, 'create'])->name('create');

        Route::get('/{proposal}', [ProposalController::class, 'show'])->name('show');
    });
