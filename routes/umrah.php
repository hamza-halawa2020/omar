<?php

use App\Http\Controllers\Dashboard\UmrahController as Umrah;
use Illuminate\Support\Facades\Route;

Route::prefix('dashboard/umrah')->name('umrah.')
    ->middleware(['auth.or_impersonate', 'tenancy', 'umrah.access'])->group(function () {
        Route::get('/', [Umrah::class, 'index'])->name('index');
        Route::get('/trips/{id}', [Umrah::class, 'trip'])->name('trips.show');
        Route::post('/trips', [Umrah::class, 'saveTrip'])->middleware('check.permission:umrah_trips_manage')->name('trips.store');
        Route::put('/trips/{id}', [Umrah::class, 'saveTrip'])->middleware('check.permission:umrah_trips_manage')->name('trips.update');
        Route::post('/trips/{tripId}/programs', [Umrah::class, 'saveProgram'])->middleware('check.permission:umrah_trips_manage')->name('programs.store');
        Route::put('/trips/{tripId}/programs/{id}', [Umrah::class, 'saveProgram'])->middleware('check.permission:umrah_trips_manage')->name('programs.update');
        Route::post('/programs/{programId}/prices', [Umrah::class, 'savePrice'])->middleware('check.permission:umrah_trips_manage')->name('prices.store');
        Route::put('/programs/{programId}/prices/{id}', [Umrah::class, 'savePrice'])->middleware('check.permission:umrah_trips_manage')->name('prices.update');
        Route::get('/travelers', [Umrah::class, 'travelers'])->name('travelers.index');
        Route::get('/travelers/{id}', [Umrah::class, 'traveler'])->name('travelers.show');
        Route::post('/travelers', [Umrah::class, 'saveTraveler'])->middleware('check.permission:umrah_bookings_manage')->name('travelers.store');
        Route::put('/travelers/{id}', [Umrah::class, 'saveTraveler'])->middleware(['check.permission:umrah_bookings_manage', 'check.permission:umrah_passports_view'])->name('travelers.update');
        Route::post('/bookings', [Umrah::class, 'createBooking'])->middleware('check.permission:umrah_bookings_manage')->name('bookings.store');
        Route::get('/bookings/{id}', [Umrah::class, 'booking'])->name('bookings.show');
        Route::post('/bookings/{id}/payments', [Umrah::class, 'payment'])->name('payments.store');
        Route::post('/bookings/{id}/payments/{paymentId}/review', [Umrah::class, 'reviewPayment'])->middleware('check.permission:umrah_payments_confirm')->name('payments.review');
        Route::post('/bookings/{id}/settle', [Umrah::class, 'settle'])->middleware('check.permission:umrah_refunds_approve')->name('bookings.settle');
        Route::post('/bookings/{id}/program', [Umrah::class, 'changeProgram'])->middleware('check.permission:umrah_refunds_approve')->name('bookings.program');
        Route::post('/bookings/{id}/schedule', [Umrah::class, 'schedule'])->middleware('check.permission:umrah_bookings_manage')->name('schedules.store');
        Route::post('/bookings/{id}/procedures', [Umrah::class, 'procedures'])->middleware('check.permission:umrah_procedures_manage')->name('procedures.update');
        Route::post('/bookings/{id}/complete', [Umrah::class, 'complete'])->middleware('check.permission:umrah_bookings_manage')->name('bookings.complete');
        Route::post('/travelers/{id}/movements', [Umrah::class, 'movement'])->middleware(['check.permission:umrah_procedures_manage', 'check.permission:umrah_passports_view'])->name('movements.store');
        Route::post('/travelers/{id}/documents', [Umrah::class, 'upload'])->middleware(['check.permission:umrah_procedures_manage', 'check.permission:umrah_passports_view'])->name('documents.store');
        Route::get('/documents/{id}', [Umrah::class, 'download'])->middleware('check.permission:umrah_passports_view')->name('documents.download');
        Route::get('/receipts/{id}', [Umrah::class, 'receipt'])->name('receipts.show');
        Route::get('/reports', [Umrah::class, 'reports'])->middleware('check.permission:umrah_reports_view')->name('reports');
        Route::post('/notifications/{id}/retry', [Umrah::class, 'retryNotification'])->middleware('check.permission:umrah_procedures_manage')->name('notifications.retry');
    });
