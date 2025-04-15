<?php

use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DealController;
use App\Http\Controllers\Api\LeadController;
use Illuminate\Support\Facades\Route;


Route::prefix('/v1')
    ->name('api.v1.')
    ->group(function () {
    Route::get('/leads/list', [LeadController::class, 'list'])->name('leads.list');
    Route::get('/contacts/list', [ContactController::class, 'list'])->name('contacts.list');
    Route::get('/deals/list', [DealController::class, 'list'])->name('deals.list');
});
