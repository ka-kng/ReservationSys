<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Management\AdminReservationController;
use App\Http\Controllers\Management\ScheduleController;
use App\Http\Controllers\Patients\ReservationController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {
    Route::get('/reservations/list', [AdminReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/show/{id}', [AdminReservationController::class, 'show'])->name('reservations.show');
    Route::patch('/reservations/show/{id}/status', [AdminReservationController::class, 'updateStatus'])->name('reservations.updateStatus');
    Route::get('/reservations/{id}/pdf', [AdminReservationController::class, 'downloadPdf'])->name('reservations.downloadPdf');
    Route::get('/schedule', [ScheduleController::class, 'index'])->name('calendar.index');
    Route::post('/schedule', [ScheduleController::class, 'store'])->name('calendar.store');
});



Route::get('/', [ReservationController::class, 'selectDate'])->name('reservations.selectDate');
Route::get('/patients/reservations/available-dates', [ReservationController::class, 'availableDates'])
    ->name('patients.reservations.available-dates');

Route::get('/reservations/slots', [ReservationController::class, 'create'])->name('reservations.create');
Route::post('/reservations/complete', [ReservationController::class, 'store'])->name('reservations.store');
Route::get('/reservations/complete', [ReservationController::class, 'complete'])->name('reservations.complete');

require __DIR__ . '/auth.php';
