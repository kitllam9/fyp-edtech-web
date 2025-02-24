<?php

namespace Database\Factories;

use App\Enum\QuestType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quest>
 */
class QuestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomPercentageAmount = fake()->randomElement([null, fake()->randomDigitNotZero()]);
        return [
            'name' => fake()->realText(100),
            'description' => fake()->realText(255),
            'type' => fake()->randomElement(QuestType::values()),
            'target' => fake()->randomNumber(2),
            'multiple_percentage_amount' => function (array $attributes) use ($randomPercentageAmount) {
                return $attributes['type'] === 'percentage' ? $randomPercentageAmount : null;
            },
            'reward' => fake()->randomNumber(3),
        ];
    }
}
