<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Symptom extends Model
{
    use HasFactory;

    protected $table = 'symptoms';

    // 複数代入可能なカラムを指定
    protected $fillable = [
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

    // 予約とのリレーション
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
