<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Game>
 */
class GameFactory extends Factory
{
    protected $model = Game::class;

    /**
     * Define a valid game record for testing.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'genre' => fake()->randomElement([
                'Tactical Shooter',
                'Battle Royale',
                'MOBA',
                'Sports',
                'Fighting',
            ]),
            'platform' => fake()->randomElement([
                'PC',
                'Mobile',
                'Console',
            ]),
            'rules' => fake()->sentence(15),
            'team_size' => fake()->numberBetween(1, 10),
        ];
    }
}