<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Create reusable local demo accounts.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'ArenaSync Admin',
                'email' => 'admin@arenasync.test',
                'role' => User::ROLE_ADMIN,
            ],
            [
                'name' => 'Demo Organizer',
                'email' => 'organizer@arenasync.test',
                'role' => User::ROLE_ORGANIZER,
            ],
            [
                'name' => 'Demo Player',
                'email' => 'player@arenasync.test',
                'role' => User::ROLE_PLAYER,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'role' => $userData['role'],
                    'password' => Hash::make('Password123!'),
                ]
            );
        }
    }
}