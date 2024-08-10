<?php

namespace Database\Factories;

use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Player>
 */
class PlayerFactory extends Factory
{
    protected $model = Player::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'level' => $this->faker->numberBetween(1, 5),
            'is_goalkeeper' => $this->faker->boolean,
            'confirmed' => $this->faker->boolean,
        ];
    }
}
