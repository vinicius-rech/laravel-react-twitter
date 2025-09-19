<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Carlos', 'email' => 'carlos@example.com'],
            ['name' => 'Fernanda', 'email' => 'fernanda@example.com'],
            ['name' => 'Isabela', 'email' => 'isabela@example.com'],
            ['name' => 'Rafael', 'email' => 'rafael@example.com'],
            ['name' => 'Beatriz', 'email' => 'beatriz@example.com'],
            ['name' => 'Rodrigo', 'email' => 'rodrigo@example.com'],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'uuid' => Str::uuid(),
                'password' => Hash::make('Password@123'),
                'remember_token' => Str::random(10),
                'email_verified_at' => now(),
            ]);
        }
    }
}
