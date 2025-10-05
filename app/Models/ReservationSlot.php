<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationSlot extends Model
{
    protected $table = 'reservation_slots';

    // 更新可能なカラム
    protected $fillable = [
        'date',
        'slot_type',
        'start_time',
        'end_time',
        'is_available',
        'capacity',
    ];

    public $timestamps = true;

    // 予約とのリレーション
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
