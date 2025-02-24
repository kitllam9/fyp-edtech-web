<?php

namespace Database\Factories;

use App\Enum\BadgeType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Badge>
 */
class BadgeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->realText(100),
            'description' => fake()->realText(255),
            'type' => fake()->randomElement(BadgeType::cases()),
            'target' => fake()->randomNumber(4),
        ];
    }
}
