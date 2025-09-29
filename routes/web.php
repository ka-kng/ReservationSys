<?php

use App\Http\Controllers\Management\ScheduleController ;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.app');
});

Route::get('/schedule', [ScheduleController ::class, 'index'])->name('calender.index');
