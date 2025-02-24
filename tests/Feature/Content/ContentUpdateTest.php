<?php

use App\Models\Content;
use App\Models\Tag;
use App\Models\Admin;
use Illuminate\Support\Facades\Storage;

test('update screen can be rendered', function () {
    $user = Admin::factory()->create();
    $content = Content::factory()->create();

    Storage::fake('public');

    $response = $this
        ->actingAs($user)
        ->get(route('content.edit', $content));

    $response->assertOk();
});

test('content can be updated', function () {
    $user = Admin::factory()->create();
    $content = Content::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(
            route(
                'content.update',
                [
                    'content' => $content,
                    'title' => 'Updated Title',
                    'description' => 'Updated description',
                    'tags' => json_encode(["UpdatedtagA", "UpdatedtagB", "UpdatedtagC", "UpdatedtagD"]),
                    'points' => 45,
                ]
            )
        );

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('content'));

    $content->refresh();

    $this->assertSame('Updated Title', $content->title);
    $this->assertSame('Updated description', $content->description);
});
