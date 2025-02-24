<?php

use App\Enum\QuestType;
use App\Models\Quest;
use function PHPUnit\Framework\assertNotNull;


/*
    protected $fillable = [
        'name',
        'description',
        'type',
        'target',
        'multiple_percentage_amount',
        'reward',
    ];
 */


it('can create quests', function () {
    $quest = Quest::factory()->create([
        'name' => 'Quest A',
        'description' => 'Some description',
        'type' => QuestType::NOTES->value,
        'target' => 10,
        'reward' => 25,
    ]);

    expect($quest->name)->toBe('Quest A');
    expect($quest->description)->toBe('Some description');
    expect($quest->type)->toBe('notes');
    expect($quest->multiple_percentage_amount)->toBeNull();
    expect($quest->target)->toBe(10);
    expect($quest->reward)->toBe(25);
});


it('can update quests', function () {
    $quest = Quest::factory()->create();

    $quest->update([
        'name' => 'Updated Name',
        'description' => 'Updated description',
        'type' => QuestType::PERCENTAGE->value,
        'multiple_percentage_amount' => 2,
        'target' => 50,
        'reward' => 45,
    ]);

    // Reload the user from the database
    $updatedQuest = Quest::find($quest->id);

    expect($updatedQuest->name)->toBe('Updated Name');
    expect($updatedQuest->description)->toBe('Updated description');
    expect($updatedQuest->type)->toBe('percentage');
    expect($updatedQuest->multiple_percentage_amount)->toBe(2);
    expect($updatedQuest->target)->toBe(50);
    expect($updatedQuest->reward)->toBe(45);
});

it('can delete quests', function () {
    $quest = Quest::factory()->create();
    $created = $quest;

    $quest->delete();
    $deleted = Quest::find($quest->id);

    assertNotNull($created);
    expect($deleted)->toBeNull();
});
