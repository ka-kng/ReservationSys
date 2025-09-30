<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'reservation_slots';

    protected $fillable = [
        'date', 'start_time', 'end_time', 'is_available', 'capacity'
    ];
}
