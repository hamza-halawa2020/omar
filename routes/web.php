<?php

use App\Http\Controllers\Dashboard\AssociationController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\ClientController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\InstallmentContractController;
use App\Http\Controllers\Dashboard\LoginController;
use App\Http\Controllers\Dashboard\PaymentWayController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\TransactionController;
use App\Http\Controllers\Dashboard\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

Route::prefix('dashboard')->middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'index'])->name('dashboard.profile.index');
    Route::post('/profile', [ProfileController::class, 'update'])->name('dashboard.profile.update');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/list', [CategoryController::class, 'list'])->name('categories.list');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/list', [UserController::class, 'list'])->name('users.list');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::put('users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/list', [ProductController::class, 'list'])->name('products.list');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::put('products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/products/{id}/details', [ProductController::class, 'details'])->name('products.details');

    Route::get('installment_contracts', [InstallmentContractController::class, 'index'])->name('installment_contracts.index');
    Route::get('installment_contracts/list', [InstallmentContractController::class, 'list'])->name('installment_contracts.list');
    Route::post('installment_contracts', [InstallmentContractController::class, 'store'])->name('installment_contracts.store');
    Route::put('installment_contracts/{id}', [InstallmentContractController::class, 'update'])->name('installment_contracts.update');
    Route::delete('installment_contracts/{id}', [InstallmentContractController::class, 'destroy'])->name('installment_contracts.destroy');
    Route::post('installments/pay', [InstallmentContractController::class, 'pay'])->name('installments.pay');

    Route::get('installment_contracts/{id}', [InstallmentContractController::class, 'showPage'])->name('installment_contracts.show');

    Route::get('payment-ways', [PaymentWayController::class, 'index'])->name('payment_ways.index');

    Route::get('sub-categories/{id}', [PaymentWayController::class, 'getSubCategoryOnCategory'])->name('getSubCategoryOnCategory');

    Route::post('payment-ways/reorder', [PaymentWayController::class, 'reorder'])->name('payment_ways.reorder');

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
    Route::get('debts', [ClientController::class, 'debts'])->name('debts.index');
    Route::get('merchants', [ClientController::class, 'merchants'])->name('merchants.index');
    Route::get('client_installments', [ClientController::class, 'client_installments'])->name('client_installments');
    Route::get('clients/list', [ClientController::class, 'list'])->name('clients.list');
    Route::get('clients/listDebts', [ClientController::class, 'listDebts'])->name('clients.listDebts');
    Route::get('clients/listMerchants', [ClientController::class, 'listMerchants'])->name('clients.listMerchants');
    Route::get('clients/listClientInstallments', [ClientController::class, 'listClientInstallments'])->name('listClientInstallments');
    Route::get('clients/listCreditor', [ClientController::class, 'listCreditor'])->name('listCreditor');
    Route::get('clients/client_creditor', [ClientController::class, 'client_creditor'])->name('client_creditor');
    Route::post('clients', [ClientController::class, 'store'])->name('clients.store');
    Route::put('clients/{id}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('clients/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::get('/clients/{id}', [ClientController::class, 'showPage'])->name('clients.showPage');

    Route::resource('roles', RoleController::class);

    Route::get('associations', [AssociationController::class, 'index'])->name('associations.index');
    Route::get('associations/list', [AssociationController::class, 'list'])->name('associations.list');
    Route::post('associations', [AssociationController::class, 'store'])->name('associations.store');
    Route::get('associations/{id}', [AssociationController::class, 'show'])->name('associations.show');
    Route::put('associations/{id}', [AssociationController::class, 'update'])->name('associations.update');
    Route::delete('associations/{id}', [AssociationController::class, 'destroy'])->name('associations.destroy');

    Route::get('associations/{id}/details', [AssociationController::class, 'details'])->name('associations.details');
    Route::post('associations/{id}/add-member', [AssociationController::class, 'addMember'])->name('associations.addMember');

    Route::delete('associations/{associationId}/members/{memberId}', [AssociationController::class, 'deleteMember'])->name('associations.deleteMember');
    Route::post('/associations/{id}/add-payment', [AssociationController::class, 'addPayment'])->name('associations.addPayment');
    Route::post('associations/{id}/pay-member', [AssociationController::class, 'payMember'])->name('associations.payMember');

});
