<?php

use App\Http\Controllers\Dashboard\LoginController;
use App\Http\Controllers\Dashboard\PermissionController;
use App\Http\Controllers\Dashboard\ProjectController;
use App\Http\Controllers\Dashboard\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\UserRolePermissionController;
use Illuminate\Support\Facades\Auth;

// Login routes (accessible only by guests)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('auth.login');
    Route::post('/login', [LoginController::class, 'login'])->name('login');
});


Route::middleware('auth')->group(function () {
    Route::get('/systems', [ProjectController::class, 'settings'])->name('settings');
});


Route::middleware('CheckProjectAccess')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::resource('roles', RoleController::class);
    Route::resource('user-role-permissions', UserRolePermissionController::class);
});
