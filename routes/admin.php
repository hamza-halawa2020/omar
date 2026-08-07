<?php

use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminTenantController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\AdminUserController;
use Illuminate\Support\Facades\Route;

// Public admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('login.attempt');

    // Protected admin routes
    Route::middleware('auth.admin')->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Tenant management
        Route::get('/tenants/create', [AdminTenantController::class, 'create'])->name('tenants.create');
        Route::post('/tenants', [AdminTenantController::class, 'store'])->name('tenants.store');
        Route::delete('/tenants/{tenant}', [AdminTenantController::class, 'destroy'])->name('tenants.destroy');

        // Role management per tenant
        Route::get('/tenants/{tenant}/roles', [AdminRoleController::class, 'index'])->name('tenants.roles.index');
        Route::post('/tenants/{tenant}/roles', [AdminRoleController::class, 'store'])->name('tenants.roles.store');
        Route::delete('/tenants/{tenant}/roles/{role}', [AdminRoleController::class, 'destroy'])->name('tenants.roles.destroy');
        Route::post('/tenants/{tenant}/roles/{role}/permissions', [AdminRoleController::class, 'syncPermissions'])->name('tenants.roles.permissions');

        // User management per tenant
        Route::get('/tenants/{tenant}/users', [AdminUserController::class, 'index'])->name('tenants.users.index');
        Route::get('/tenants/{tenant}/users/create', [AdminUserController::class, 'create'])->name('tenants.users.create');
        Route::post('/tenants/{tenant}/users', [AdminUserController::class, 'store'])->name('tenants.users.store');
        Route::patch('/tenants/{tenant}/users/{user}/status', [AdminUserController::class, 'updateStatus'])->name('tenants.users.status');
        Route::delete('/tenants/{tenant}/users/{user}', [AdminUserController::class, 'destroy'])->name('tenants.users.destroy');

        // Impersonation — removed
    });
});
