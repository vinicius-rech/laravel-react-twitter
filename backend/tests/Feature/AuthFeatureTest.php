<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_registers_new_user(): void
    {
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
    }

    public function test_validates_registration(): void
    {
        $res = $this->postJson(route('register'), []);
        $res->assertUnprocessable();
        $res->assertJsonStructure(['message', 'errors' => ['name', 'email', 'password']]);
    }

    public function test_logs_in_and_returns_token(): void
    {
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
    }

    public function test_rejects_invalid_login(): void
    {
        $res = $this->postJson(route('login'), [
            'email' => 'nope@example.com',
            'password' => 'Wrong!123',
        ]);

        $res->assertUnauthorized();
    }

    public function test_gets_current_user_when_authenticated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $res = $this->getJson(route('user.current'));
        $res->assertOk();
        $res->assertJsonPath('data.user.email', $user->email);
    }

    public function test_logs_out_and_revokes_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token);
        $this->actingAs($user);

        $res = $this->postJson(route('logout'));
        $res->assertOk();
    }
}
