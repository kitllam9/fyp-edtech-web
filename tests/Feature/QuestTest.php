<?php

use App\Enum\QuestType;
use App\Models\Quest;
use App\Models\Admin;

test('can display the index page for quests', function () {
    $user = Admin::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('quests'));

    $response->assertOk();
});

test('can display the create page for quests', function () {
    $user = Admin::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('quest.create'));

    $response->assertOk();
});

test('can store a new quest', function () {
    $user = Admin::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(
            route(
                'quest.store',
                [
                    'name' => 'Test Quest',
                    'description' => 'Test Description',
                    'type' => QuestType::PERCENTAGE->value,
                    'target' => 10,
                    'multiple_percentage_amount' => 50,
                    'reward' => 50,
                ]
            )
        );

    $response->assertRedirect(route('quests'));
});

test('can display the edit page for quests', function () {
    $quest = Quest::factory()->create();

    $user = Admin::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('quest.edit', $quest));

    $response->assertOk();
});

test('can update a quest', function () {
    $quest = Quest::factory()->create();

    $user = Admin::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(
            route(
                'quest.update',
                [
                    'quest' => $quest,
                    'name' => 'Updated Name',
                    'description' => 'Updated Description',
                    'type' => QuestType::MIXED,
                    'target' => 20,
                    'reward' => 100,
                ]
            )
        );
    $response->assertRedirect(route('quests'));
});

test('can delete a quest', function () {
    $quest = Quest::factory()->create();

    $user = Admin::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete(route('quest.delete', $quest));

    $response->assertRedirect(route('quests'));
});
