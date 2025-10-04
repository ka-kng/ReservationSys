<?php

use App\Http\Controllers\Management\AdminReservationController;
use App\Http\Controllers\Management\ScheduleController ;
use App\Http\Controllers\Patients\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.app');
});

Route::get('/schedule', [ScheduleController::class, 'index'])->name('calendar.index');
Route::post('/schedule', [ScheduleController::class, 'store'])->name('calendar.store');

Route::get('/reservations/list', [AdminReservationController::class, 'index'])->name('reservations.index');


Route::get('/reservations/calendar', [ReservationController::class, 'selectDate'])->name('reservations.selectDate');
Route::get('/patients/reservations/available-dates', [ReservationController::class, 'availableDates'])
    ->name('patients.reservations.available-dates');
Route::get('/reservations/slots', [ReservationController::class, 'create'])->name('reservations.create');
