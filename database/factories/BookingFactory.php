<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Business;
use App\Models\Professional;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        $starts = fake()->dateTimeBetween('+1 day', '+10 days');

        return [
            'business_id' => Business::factory(),
            'professional_id' => fn (array $attributes) => Professional::factory()->for(Business::find($attributes['business_id'])),
            'service_id' => fn (array $attributes) => Service::factory()->for(Business::find($attributes['business_id'])),
            'client_name' => fake()->name(),
            'client_email' => fake()->safeEmail(),
            'client_phone' => fake()->phoneNumber(),
            'starts_at' => $starts,
            'ends_at' => (clone $starts)->modify('+30 minutes'),
            'status' => 'confirmed',
            'management_token' => hash('sha256', fake()->unique()->uuid()),
        ];
    }
}
