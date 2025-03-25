<?php

use App\Models\Badge;
use App\Models\User;
use function Pest\Laravel\actingAs;

it('can get badges based on badge IDs', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;

    Badge::factory(3)->create();

    $badgeIds = [1, 2, 3]; // Sample badge IDs
    $response = $this
        ->actingAs($user)
        ->get(
            '/api/badge?badge_ids=' . json_encode($badgeIds),
            ['Authorization' => 'Bearer ' . $token]
        );
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['badges']
        ]);
});

it('can check and update user points and badges', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;
    $pointsToAdd = 50; // Sample points to add

    $response = $this->actingAs($user)->get(
        '/api/badge/check',
        [
            'points' => $pointsToAdd,
            'Authorization' => 'Bearer ' . $token
        ]
    );
    $response->assertStatus(200)
        ->assertJsonStructure(
            [
                'data' => [
                    'targets',
                    'current_points',
                    'target_points',
                    'earned_badges'
                ]
            ]
        );
});
