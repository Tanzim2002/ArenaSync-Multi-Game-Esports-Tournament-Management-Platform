<?php

namespace Database\Factories;

use App\Models\Livestream;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;

class LivestreamFactory extends Factory
{
    protected $model = Livestream::class;

    public function definition(): array
    {
        return [
            'tournament_id' => Tournament::factory(),
            'platform'      => 'youtube',
            'url'           => 'https://www.youtube.com/watch?v=' . $this->faker->lexify('???????????'),
            'label'         => 'Main Stream',
            'is_active'     => true,
        ];
    }
}