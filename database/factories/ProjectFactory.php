<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => \App\Models\Client::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(['draft', 'active', 'on_hold', 'completed', 'archived']),
            'budget' => fake()->optional()->randomFloat(2, 1000, 50000),
            'hourly_rate' => fake()->randomFloat(2, 50, 200),
            'deadline' => fake()->optional()->dateTimeBetween('now', '+6 months'),
            'started_at' => fake()->optional()->dateTimeBetween('-3 months', 'now'),
            'completed_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'color' => fake()->randomElement(['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6']),
        ];
    }
}
