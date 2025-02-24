<?php

namespace Database\Factories;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recommendation>
 */
class RecommendationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tagCount = Tag::all()->count();
        $userCount = User::all()->count();

        $tagUserPair = [];
        for ($i = 1; $i <= $tagCount; $i++) {
            for ($j = 1; $j <= $userCount; $j++) {
                array_push($tagUserPair, $i . "-" . $j);
            }
        }

        $tagUserId = fake()->unique()->randomElement($tagUserPair);

        $tagUserId = explode('-', $tagUserId);
        $tagId = $tagUserId[0];
        $userId = $tagUserId[1];

        return [
            'product_id' => $tagId,
            'score' => fake()->randomElement([0, 1]),
            'user_id' => $userId,
        ];
    }
}
