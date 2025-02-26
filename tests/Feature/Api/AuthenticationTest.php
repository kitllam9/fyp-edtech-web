<?php

use App\Models\User;

test('users can register', function () {
    $response = $this->post('/api/user/register', [
        'username' => 'test',
        'password' => 'password',
        'email' => 'test@test.com',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['user', 'access_token', 'token_type'],
            'message'
        ])
        ->assertJsonFragment([
            'success' => true,
        ]);
});

test('users can login', function () {
    $user = User::factory()->create();

    $response = $this->post('/api/user/login', [
        'username' => $user->username,
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['user', 'access_token', 'token_type'],
            'message'
        ])
        ->assertJsonFragment([
            'success' => true,
        ]);
});

test('users cannot register with duplicate username or email', function () {
    $user = User::factory()->create();

    $response = $this->post('/api/user/register', [
        'username' => $user->username,
        'password' => 'password',
        'email' => 'test@test.com',
    ]);

    $response->assertStatus(400)
        ->assertJsonStructure([
            'errors'
        ])
        ->assertJsonFragment([
            'success' => false,
        ]);

    $response = $this->post('/api/user/register', [
        'username' => 'test',
        'password' => 'password',
        'email' => $user->email,
    ]);

    $response->assertStatus(400)
        ->assertJsonStructure([
            'errors'
        ])
        ->assertJsonFragment([
            'success' => false,
        ]);
});

test('users cannot login with invalid credentials', function () {
    $user = User::factory()->create();

    $response = $this->post('/api/user/login', [
        'username' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(401)
        ->assertJsonStructure([
            'errors'
        ])
        ->assertJsonFragment([
            'success' => false,
        ]);

    $response = $this->post('/api/user/login', [
        'username' => 'wrong-email',
        'password' => 'password',
    ]);

    $response->assertStatus(401)
        ->assertJsonStructure([
            'errors'
        ])
        ->assertJsonFragment([
            'success' => false,
        ]);
});

test('users can logout', function () {
    $user = User::factory()->create();

    $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this->actingAs($user)->get('/api/user/logout', [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message'
        ])
        ->assertJsonFragment([
            'success' => true,
        ]);

    // Check if the api token is deleted
    $this->assertEquals(0, $user->tokens()->count());
});
