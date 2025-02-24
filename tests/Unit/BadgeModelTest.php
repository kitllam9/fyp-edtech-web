<?php

use App\Enum\BadgeType;
use App\Models\Badge;
use function PHPUnit\Framework\assertNotNull;



/*
    protected $fillable = [
        'name',
        'description',
        'type',
        'target'
    ];
 */


it('can create badges', function () {
    $badge = Badge::factory()->create([
        'name' => 'Badge A',
        'description' => 'Some description',
        'type' => BadgeType::POINTS->value,
        'target' => 10,
    ]);

    expect($badge->name)->toBe('Badge A');
    expect($badge->description)->toBe('Some description');
    expect($badge->type)->toBe('points');
});


it('can update badges', function () {
    $badge = Badge::factory()->create();

    $badge->update([
        'name' => 'Updated Title',
        'description' => 'Updated description',
        'type' => BadgeType::MIXED->value,
        'target' => 42,
    ]);

    // Reload the user from the database
    $updatedBadge = Badge::find($badge->id);

    expect($updatedBadge->name)->toBe('Updated Title');
    expect($updatedBadge->description)->toBe('Updated description');
    expect($updatedBadge->type)->toBe('mixed');
    expect($updatedBadge->target)->toBe(42);
});

it('can delete badges', function () {
    $badge = Badge::factory()->create();
    $created = $badge;

    $badge->delete();
    $deleted = Badge::find($badge->id);

    assertNotNull($created);
    expect($deleted)->toBeNull();
});
