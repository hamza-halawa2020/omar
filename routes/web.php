<?php

use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\ClientController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\LoginController;
use App\Http\Controllers\Dashboard\PaymentWayController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

Route::prefix('dashboard')->middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'index'])->name('dashboard.profile.index');
    Route::post('/profile', [ProfileController::class, 'update'])->name('dashboard.profile.update');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/list', [CategoryController::class, 'list'])->name('categories.list');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('payment-ways', [PaymentWayController::class, 'index'])->name('payment_ways.index');

    Route::get('sub-categories/{id}', [PaymentWayController::class, 'getSubCategoryOnCategory'])->name('getSubCategoryOnCategory');

    Route::get('payment-ways/list', [PaymentWayController::class, 'list'])->name('payment_ways.list');
    Route::post('payment-ways', [PaymentWayController::class, 'store'])->name('payment_ways.store');
    Route::put('payment-ways/{id}', [PaymentWayController::class, 'update'])->name('payment_ways.update');
    Route::delete('payment-ways/{id}', [PaymentWayController::class, 'destroy'])->name('payment_ways.destroy');

    Route::get('/payment-ways/show/{id}', [PaymentWayController::class, 'show'])->name('payment_ways.show');
    Route::get('/payment-ways/show-list/{id}', [PaymentWayController::class, 'showList'])->name('payment_ways.showList');

    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/list', [TransactionController::class, 'list'])->name('transactions.list');
    Route::post('transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::put('transactions/{id}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('transactions/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    Route::get('clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('clients/list', [ClientController::class, 'list'])->name('clients.list');
    Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
    Route::put('clients/{id}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('clients/{id}', [ClientController::class, 'destroy'])->name('categories.destroy');
    Route::get('/clients/{id}', [ClientController::class, 'showPage'])->name('clients.showPage');
});
