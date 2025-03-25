<?php

use App\Enum\BadgeType;
use App\Models\Badge;
use App\Models\Admin;

test('can display the index page', function () {
    $user = Admin::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('badges'));

    $response->assertOk();
});

test('can display the create page', function () {
    $user = Admin::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('badge.create'));

    $response->assertOk();
});

test('can store a new badge', function () {
    $user = Admin::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(
            route(
                'badge.store',
                [
                    'name' => 'Test Badge',
                    'description' => 'Test Description',
                    'type' => BadgeType::POINTS->value,
                    'target' => 10,
                ]
            )
        );

    $response->assertRedirect(route('badges'));
});

test('can display the edit page', function () {
    $badge = Badge::factory()->create();

    $user = Admin::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('badge.edit', $badge));

    $response->assertStatus(200);
});

test('can update a badge', function () {
    $badge = Badge::factory()->create();

    $user = Admin::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(
            route(
                'badge.update',
                [
                    'badge' => $badge,
                    'name' => 'Updated Name',
                    'description' => 'Updated Description',
                    'type' => BadgeType::MIXED->value,
                    'target' => 25,
                ]
            )
        );

    $response->assertRedirect(route('badges'));
});

test('can delete a badge', function () {
    $badge = Badge::factory()->create();

    $user = Admin::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete(route('badge.delete', $badge));

    $response->assertRedirect(route('badges'));
});
