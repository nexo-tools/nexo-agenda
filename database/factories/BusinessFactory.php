<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Business>
 */
class BusinessFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'category' => fake()->randomElement(config('nexo.categories')),
            'city' => fake()->city(),
            'timezone' => 'America/Argentina/Buenos_Aires',
        ];
    }
}
