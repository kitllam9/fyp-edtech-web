<?php

use App\Models\Content;
use App\Models\Tag;
use App\Models\Admin;
use Illuminate\Support\Facades\Storage;

/*
    protected $fillable = [
        'title',
        'description',
        'type',
        'pdf_url',
        'exercise_details',
        'tags',
        'points',
        'difficulty',
    ];
*/

test('create screen can be rendered', function () {
    $user = Admin::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('content.create'));

    $response->assertOk();
});

test('can create content', function () {
    $admin = Admin::factory()->create();
    $this->actingAs($admin);

    $response = $this->post(
        route(
            'content.store',
            [
                'title' => 'Title A',
                'description' => 'Some Description',
                'type' => 'notes',
                'pdf_content' => '<p>Test PDF Content</p>',
                'tags' => json_encode(['1' => ['value' => 'tag1'], '2' => ['value' => 'tag2']]),
                'points' => 10,
                'test' => true,
            ]
        )
    );

    $response->assertRedirect(route('content'));
});
