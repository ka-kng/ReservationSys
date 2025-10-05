<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';

    protected $fillable = [
        'reservation_number',
        'patient_id',
        'reservation_slot_id',
        'status'
    ];

    // 患者情報
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // 予約に紐づく症状
    public function symptom()
    {
        return $this->hasOne(Symptom::class);
    }

    // 予約スロット
    public function slot()
    {
        return $this->belongsTo(ReservationSlot::class, 'reservation_slot_id');
    }
}
