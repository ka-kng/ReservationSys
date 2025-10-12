<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Schedule;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition()
    {
        return [
            'reservation_number' => $this->faker->unique()->regexify('[A-Z0-9]{5}'),
            'status' => 'reserved',
            'patient_id' => Patient::factory(),
            'reservation_slot_id' => Schedule::factory(),
        ];
    }

    // 状態指定用（オプション）
    public function canceled()
    {
        return $this->state(fn () => ['status' => 'canceled']);
    }
}
