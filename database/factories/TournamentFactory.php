<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TournamentFactory extends Factory
{
    protected $model = Tournament::class;

    public function definition(): array
    {
        return [

            'organizer_id' => User::factory(),

            'game_id' => Game::factory(),

            'category' => 'Solo',

            'region' => 'Global',

            'prize_type' => 'Cash',

            'title' => fake()->sentence(),

            'description' => fake()->paragraph(),

            'registration_deadline' => now()->addDays(5),

            'start_at' => now()->addDays(10),

            'end_at' => now()->addDays(12),

            'rules' => 'Tournament rules',

            'prize_pool' => 5000,

            'team_limit' => 16,

            'match_format' => 'Round Robin',

            'status' => Tournament::STATUS_DRAFT,

        ];
    }
}