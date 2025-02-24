<?php

use App\Models\User;
use Carbon\Carbon;

/*
    protected $fillable = [
        'username',
        'email',
        'password',
        'points',
        'interests',
        'badges',
        'finished_quests',
        'group_id'
    ];
*/

it('can create user', function () {
    $user = User::factory()->create([
        'username' => 'user123',
        'email' => 'email@example.com',
        'password' => bcrypt('password'),
    ]);

    expect($user->username)->toBe('user123');
    expect($user->email)->toBe('email@example.com');
    expect(password_verify('password', $user->password))->toBeTrue();
    expect($user->points)->toBeNull();
    expect($user->interests)->toBeNull();
    expect($user->badges)->toBeNull();
    expect($user->finished_quests)->toBeNull();
    expect($user->group_id)->toBeNull();
});

it('can update a user', function () {
    $user = User::factory()->create();

    $requestInterestsJson = '["InterestA","InterestB","InterestC","InterestD"]';
    $interestsJson = json_decode($requestInterestsJson);

    $badgesJson = '[1,2,3,4]';

    $finishedQuestsJson = '{"1":"2025-02-10 15:30:45","2":"2025-02-11 15:30:45"}';

    $user->update([
        'username' => 'user456',
        'email' => 'updated@example.com',
        'password' => bcrypt('updated'),
        'points' => 50,
        'interests' => $interestsJson,
        'badges' => [1, 2, 3, 4],
        'finished_quests' => [
            '1' => '2025-02-10 15:30:45',
            '2' => '2025-02-11 15:30:45',
        ],
        'group_id' => 0,
    ]);

    // Reload the user from the database
    $updatedUser = User::find($user->id);

    expect($updatedUser->username)->toBe('user456');
    expect($updatedUser->email)->toBe('updated@example.com');
    expect(password_verify('updated', $updatedUser->password))->toBeTrue();
    expect($updatedUser->points)->toBe(50);

    // Ignore the array cast
    expect($updatedUser->getRawOriginal('interests'))->toBeJson();
    expect($updatedUser->getRawOriginal('interests'))->toBe($requestInterestsJson);
    expect($updatedUser->getRawOriginal('badges'))->toBeJson();
    expect($updatedUser->getRawOriginal('badges'))->toBe($badgesJson);
    expect($updatedUser->getRawOriginal('finished_quests'))->toBeJson();
    expect($updatedUser->getRawOriginal('finished_quests'))->toBe($finishedQuestsJson);

    // Casting Test
    expect($updatedUser->interests)->toBeArray();
    expect($updatedUser->badges)->toBeArray();
    expect($updatedUser->finished_quests)->toBeArray();

    expect($updatedUser->group_id)->toBe(0);
});
