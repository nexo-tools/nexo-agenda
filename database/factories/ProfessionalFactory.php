<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Professional;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Professional>
 */
class ProfessionalFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'name' => fake()->firstName(),
            'is_active' => true,
        ];
    }

    public function withWeekdaySchedule(string $start = '09:00', string $end = '18:00'): static
    {
        return $this->afterCreating(function (Professional $professional) use ($start, $end) {
            foreach (range(1, 5) as $weekday) {
                $professional->scheduleBlocks()->create([
                    'weekday' => $weekday,
                    'start_time' => $start,
                    'end_time' => $end,
                ]);
            }
        });
    }
}
