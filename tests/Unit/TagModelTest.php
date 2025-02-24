<?php

use App\Models\Tag;
use function PHPUnit\Framework\assertNotNull;


/*
    protected $fillable = [
        'name',
    ];
 */


it('can create tags', function () {
    $tag = Tag::factory()->create([
        'name' => 'Tag A',
    ]);

    expect($tag->name)->toBe('Tag A');
});


it('can update tags', function () {
    $tag = Tag::factory()->create();

    $tag->update([
        'name' => 'Updated Name',
    ]);

    // Reload the user from the database
    $updatedTag = Tag::find($tag->id);

    expect($updatedTag->name)->toBe('Updated Name');
});

it('can delete tags', function () {
    $tag = Tag::factory()->create();
    $created = $tag;

    $tag->delete();
    $deleted = Tag::find($tag->id);

    assertNotNull($created);
    expect($deleted)->toBeNull();
});
