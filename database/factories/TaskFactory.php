<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => \App\Models\Project::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(['todo', 'in_progress', 'completed', 'blocked']),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'estimated_hours' => fake()->optional()->randomFloat(2, 1, 40),
            'actual_hours' => fake()->randomFloat(2, 0, 20),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+2 months'),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
