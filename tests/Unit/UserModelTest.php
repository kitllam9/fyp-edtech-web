<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

/*
    protected $fillable = [
        'username',
        'email',
        'password',
        'interests',
        'badges',
        'finished_quests',
        'points',
        'group_id'
    ];
*/

test('has a name attribute', function () {
    $user = User::factory()->create([
        'username' => 'user123',
        'email' => 'email@example.com',
        'password' => bcrypt('password'),
    ]);

    expect($user->username)->toBe('user123');
    expect($user->email)->toBe('email@example.com');
    expect(password_verify('password', $user->password))->toBeTrue();
});
