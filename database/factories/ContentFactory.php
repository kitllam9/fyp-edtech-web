<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content>
 */
class ContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $exerciseDetails = [];
        $amount = rand(1, 10);
        for ($x = 0; $x < $amount; $x++) {
            $exerciseDetails[] = [
                'question' => fake()->realText(),
                'mc' => fake()->words(4),
                'answer' => fake()->randomElement(['A', 'B', 'C', 'D']),
            ];
        }

        $tags = [];
        $amount = rand(1, 10);
        $tags = fake()->words($amount);

        $pdfId = DB::select("SHOW TABLE STATUS LIKE 'content'")[0]->Auto_increment;

        return [
            'title' => fake()->realText(100),
            'description' => fake()->realText(255),
            'type' => fake()->randomElement(['notes', 'exercise']),
            'pdf_url' => function (array $attributes) use ($pdfId) {
                return $attributes['type'] === 'notes' ?
                    'http://' . fake()->domainName() . 'storage/pdf/' .  $pdfId . '/' . snakeTitle($attributes['title']) . '.pdf' :
                    null;
            },
            'exercise_details' => function (array $attributes) use ($exerciseDetails) {
                return $attributes['type'] === 'exercise' ? json_encode($exerciseDetails) : null;
            },
            'tags' => json_encode($tags),
            'points' => fake()->randomNumber(3),
        ];
    }
}
