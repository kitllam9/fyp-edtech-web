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

    $newInterests = json_decode('["InterestA","InterestB","InterestC","InterestD"]');

    $user->update([
        'username' => 'user456',
        'email' => 'updated@example.com',
        'password' => bcrypt('updated'),
        'points' => 50,
        'interests' => $newInterests,
        'badges' => [1, 2, 3, 4],
        'finished_quests' => [
            '1' => '2025-02-10 15:30:45',
        ],
        'group_id' => 0,
    ]);

    // Reload the user from the database
    $updatedUser = User::find($user->id);

    expect($updatedUser->username)->toBe('user456');
    expect($updatedUser->email)->toBe('updated@example.com');
    expect(password_verify('updated', $updatedUser->password))->toBeTrue();
    expect($updatedUser->points)->toBe(50);
    expect($updatedUser->interests)->toBe($newInterests);
    expect($updatedUser->badges)->toBe([1, 2, 3, 4]);
    expect($updatedUser->finished_quests)->toBe([
        '1' => '2025-02-10 15:30:45',
    ]);
    expect($updatedUser->group_id)->toBe(0);
});
