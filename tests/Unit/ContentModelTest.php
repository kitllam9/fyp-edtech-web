<?php

use App\Models\Content;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\assertNotNull;

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


it('can create notes', function () {
    $requestTagsJson = '["tagA","tagB","tagC","tagD"]';
    $tagsJson = json_decode($requestTagsJson);

    $notes = Content::factory()->create([
        'title' => 'Title A',
        'description' => 'Some description',
        'type' => 'notes',
        'tags' => json_encode($tagsJson),
        'points' => 10,
    ]);

    expect($notes->title)->toBe('Title A');
    expect($notes->description)->toBe('Some description');
    expect($notes->type)->toBe('notes');
    expect($notes->pdf_url)->toBeUrl();
    expect($notes->exerciseDetailsJson)->toBeNull();
    expect($notes->tags)->toBeJson();
    expect($notes->tags)->toBe($requestTagsJson);
    expect($notes->points)->toBe(10);
});

it('can create exercise', function () {

    $requestExerciseJson = '[{"question":"Question 1?","mc":["Answer 1","Answer 2","Answer 3","Answer 4"],"answer":"B"},{"question":"Question 2?","mc":["Answer 1","Answer 2","Answer 3","Answer 4"],"answer":"C"}]';
    $exerciseJson = json_decode($requestExerciseJson);

    $requestTagsJson = '["tagA","tagB","tagC","tagD"]';
    $tagsJson = json_decode($requestTagsJson);

    $notes = Content::factory()->create([
        'title' => 'Title A',
        'description' => 'Some description',
        'type' => 'exercise',
        'tags' => json_encode($tagsJson),
        'points' => 10,
        'exercise_details' => json_encode($exerciseJson),
    ]);

    expect($notes->title)->toBe('Title A');
    expect($notes->description)->toBe('Some description');
    expect($notes->type)->toBe('exercise');
    expect($notes->pdf_url)->toBeNull();
    expect($notes->exercise_details)->toBeJson();
    expect($notes->exercise_details)->toBe($requestExerciseJson);
    expect($notes->tags)->toBeJson();
    expect($notes->tags)->toBe($requestTagsJson);
    expect($notes->points)->toBe(10);
});

it('can update notes', function () {
    $notes = Content::factory()->create([
        'type' => 'notes',
    ]);

    $requestTagsJson = '["UpdatedtagA","UpdatedtagB","UpdatedtagC","UpdatedtagD"]';
    $tagsJson = json_decode($requestTagsJson);

    $notes->update([
        'title' => 'Updated Title',
        'description' => 'Updated description',
        'tags' => json_encode($tagsJson),
        'points' => 45,
        'pdf_url' => 'http://example.com/storage/pdf/' . $notes->id . '/Updated_Title.pdf'
    ]);

    // Reload the user from the database
    $updatedNotes = Content::find($notes->id);

    expect($updatedNotes->title)->toBe('Updated Title');
    expect($updatedNotes->description)->toBe('Updated description');
    expect($updatedNotes->type)->toBe('notes');
    expect($updatedNotes->pdf_url)->toBeUrl();
    expect($updatedNotes->pdf_url)->toBe('http://example.com/storage/pdf/' . $notes->id . '/Updated_Title.pdf');
    expect($updatedNotes->exerciseDetailsJson)->toBeNull();
    expect($updatedNotes->tags)->toBeJson();
    expect($updatedNotes->tags)->toBe($requestTagsJson);
    expect($updatedNotes->points)->toBe(45);
});

it('can update exercise', function () {
    $exercise = Content::factory()->create([
        'type' => 'exercise',
    ]);

    $requestExerciseJson = '[{"question":"Updated 1?","mc":["Answer 4","Answer 5","Answer 6","Answer 7"],"answer":"A"},{"question":"Updated 2?","mc":["Answer 10","Answer 11","Answer 12","Answer 13"],"answer":"D"}]';
    $exerciseJson = json_decode($requestExerciseJson);

    $requestTagsJson = '["UpdatedtagA","UpdatedtagB","UpdatedtagC","UpdatedtagD"]';
    $tagsJson = json_decode($requestTagsJson);

    $exercise->update([
        'title' => 'Updated Title',
        'description' => 'Updated description',
        'tags' => json_encode($tagsJson),
        'points' => 45,
        'exercise_details' => json_encode($exerciseJson),
    ]);

    // Reload the user from the database
    $updatedExercise = Content::find($exercise->id);

    expect($updatedExercise->title)->toBe('Updated Title');
    expect($updatedExercise->description)->toBe('Updated description');
    expect($updatedExercise->type)->toBe('exercise');
    expect($updatedExercise->pdf_url)->toBeNull();
    expect($updatedExercise->exercise_details)->toBeJson();
    expect($updatedExercise->exercise_details)->toBe($requestExerciseJson);
    expect($updatedExercise->tags)->toBeJson();
    expect($updatedExercise->tags)->toBe($requestTagsJson);
    expect($updatedExercise->points)->toBe(45);
});

it('can delete content', function () {
    $content = Content::factory()->create();
    $created = $content;

    $content->delete();
    $deleted = Content::find($content->id);

    assertNotNull($created);
    expect($deleted)->toBeNull();
});
