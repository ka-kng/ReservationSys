<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients';

    protected $fillable = [
        'name',
        'name_kana',
        'birth_date',
        'gender',
        'phone',
        'email',
        'reservation_id',
        'symptoms_start',
        'symptoms_type',
        'symptoms_other',
        'past_disease_flag',
        'past_disease_detail',
        'allergy_flag',
        'allergy_detail',
        'notes'
    ];

    // 患者の予約一覧
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
