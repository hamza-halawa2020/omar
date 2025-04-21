<?php

use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\DealController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\LeadStatusController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;


Route::prefix('/v1')
    ->name('api.v1.')
    ->group(function () {
    Route::get('/leads/list', [LeadController::class, 'list'])->name('leads.list');
    Route::patch('/leads/{lead}/update', [LeadController::class, 'update'])->name('leads.update');
    Route::get('/contacts/list', [ContactController::class, 'list'])->name('contacts.list');
    Route::get('/deals/list', [DealController::class, 'list'])->name('deals.list');
    Route::get('/lead-statuses/list', [LeadStatusController::class, 'list'])->name('lead-statuses.list');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
});
