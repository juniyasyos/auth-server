<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'app_key' => $this->faker->unique()->slug(2),
            'name' => $this->faker->unique()->company(),
            'description' => $this->faker->optional()->sentence(),
            'enabled' => true,
        ];
    }
}
