<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tournament>
 */
class TournamentFactory extends Factory
{
    protected $model = Tournament::class;

    /**
     * Define a valid tournament record for testing.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $registrationDeadline = now()->addDays(3);
        $startAt = now()->addDays(5);
        $endAt = now()->addDays(6);

        return [
            'organizer_id' => User::factory()->state([
                'role' => User::ROLE_ORGANIZER,
            ]),

            'game_id' => Game::factory(),

            'title' => fake()->unique()->words(4, true),

            'description' => fake()->paragraph(3),

            'registration_deadline' => $registrationDeadline,

            'start_at' => $startAt,

            'end_at' => $endAt,

            'rules' => fake()->sentence(15),

            'prize_pool' => fake()->randomFloat(
                2,
                0,
                100000
            ),

            'team_limit' => fake()->numberBetween(
                2,
                64
            ),

            'match_format' => fake()->randomElement([
                'Single Elimination',
                'Double Elimination',
                'Round Robin',
                'Group Stage',
            ]),

            'status' => Tournament::STATUS_DRAFT,
        ];
    }
}