<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('registers a new user', function () {
    $payload = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'Aa!23456',
        'password_confirmation' => 'Aa!23456',
    ];

    $res = $this->postJson(route('register'), $payload);
    $res->assertCreated();
    $res->assertJsonPath('data.user.email', 'jane@example.com');
    $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
});

it('validates registration input', function () {
    $res = $this->postJson(route('register'), []);
    $res->assertUnprocessable();
    $res->assertJsonStructure(['message', 'errors' => ['name', 'email', 'password']]);
});

it('logs in and returns token', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('Aa!23456'),
    ]);

    $res = $this->postJson(route('login'), [
        'email' => 'john@example.com',
        'password' => 'Aa!23456',
    ]);

    $res->assertCreated();
    $res->assertJsonStructure(['data' => ['token', 'token_type', 'user' => ['id', 'name', 'email']]]);
});

it('rejects invalid login', function () {
    $res = $this->postJson(route('login'), [
        'email' => 'nope@example.com',
        'password' => 'Wrong!123',
    ]);

    $res->assertUnauthorized();
});

it('returns current user when authenticated', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $res = $this->getJson(route('user.current'));
    $res->assertOk();
    $res->assertJsonPath('data.user.email', $user->email);
});

it('logs out and revokes token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('token')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer '.$token);
    $this->actingAs($user);

    $res = $this->postJson(route('logout'));
    $res->assertOk();
});
