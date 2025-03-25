<?php

use App\Models\Content;
use App\Models\Tag;
use App\Models\User;

it('can search for content based on keyword', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;

    Tag::factory(10)->create();

    // Title
    Content::factory()->create([
        'title' => 'test title',
    ]);
    // Description
    Content::factory()->create([
        'description' => 'test description',
    ]);
    // Tags
    Content::factory()->create([
        'tags' => '["test1","test2"]',
    ]);
    // Random false search
    Content::factory()->create();

    $keyword = 'test';

    $response = $this->actingAs($user)->get(
        '/api/content?keyword=' . $keyword,
        ['Authorization' => 'Bearer ' . $token]
    );

    $response->assertOk()
        ->assertJsonFragment(
            ['title' => 'test title'],
            ['tags' => '["test1","test2"]'],
            ['description' => 'test description'],
            ['total' => 3]
        );
});

it('can mark content as completed and update user interests', function () {
    Tag::factory(10)->create();

    $user = User::factory()->create();
    $content = Content::factory()->create();

    $tags = json_decode($content->tags);

    $response = $this->actingAs($user)->get('/api/content/complete/' . $content->id);
    $response->assertOk()
        ->assertJson([
            'message' => 'Completed'
        ]);

    $interests = array_unique(array_merge($user->interests, $tags));
    sort($interests);
    expect($user->interests)->toBe($interests);
});
