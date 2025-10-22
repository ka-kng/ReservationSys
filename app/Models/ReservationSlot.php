<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationSlot extends Model
{
    use HasFactory;

    protected $table = 'reservation_slots';

    protected $fillable = [
        'date',
        'slot_type',
        'start_time',
        'end_time',
        'is_available',
        'capacity'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'reservation_slot_id');
    }
}
