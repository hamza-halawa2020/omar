<?php

use App\Http\Controllers\Dashboard\LoginController;
use App\Http\Controllers\Dashboard\PermissionController;
use App\Http\Controllers\Dashboard\ProjectController;
use App\Http\Controllers\Dashboard\RoleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\UserRolePermissionController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Dashboard\DepartmentController;
use App\Http\Controllers\Dashboard\TabController;

// Login routes (accessible only by guests)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('auth.login');
    Route::post('/login', [LoginController::class, 'login'])->name('login');
});


Route::middleware('auth')->group(function () {
    Route::get('/systems', [ProjectController::class, 'settings'])->name('settings');
    Route::get('/', [ProjectController::class, 'index'])->name('dashboard');
    Route::post('fcm-token', [LoginController::class, 'fcmToken'])->name('fcm.token');
});


Route::middleware('CheckProjectAccess')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::resource('roles', RoleController::class);
    Route::resource('user-role-permissions', UserRolePermissionController::class);
    
    Route::resource('departments', DepartmentController::class);
    Route::resource('tabs', TabController::class);

});
