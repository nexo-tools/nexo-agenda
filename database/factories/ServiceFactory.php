<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'name' => fake()->randomElement(['Corte', 'Color', 'Consulta', 'Sesión', 'Clase']),
            'duration_minutes' => fake()->randomElement([15, 30, 45, 60]),
            'price' => fake()->numberBetween(50, 500) * 100,
            'mode' => 'in_person',
            'buffer_minutes' => 0,
            'min_notice_hours' => 0,
            'cancellation_hours' => 12,
            'max_advance_days' => 60,
            'is_active' => true,
        ];
    }
}
