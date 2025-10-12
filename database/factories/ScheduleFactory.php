<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Schedule;

class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition(): array
    {
        return [
            'date' => $this->faker->date(),
            'slot_type' => $this->faker->randomElement(['morning', 'afternoon']),
            'start_time' => $this->faker->time('H:i'),
            'end_time' => $this->faker->time('H:i'),
            'is_available' => true,
            'capacity' => $this->faker->numberBetween(1, 10),
        ];
    }
}
