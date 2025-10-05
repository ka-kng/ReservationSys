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
    ];

    // 患者の予約一覧
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
