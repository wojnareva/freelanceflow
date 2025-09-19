<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeEntry>
 */
class TimeEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'project_id' => \App\Models\Project::factory(),
            'task_id' => \App\Models\Task::factory(),
            'description' => fake()->sentence(),
            'duration' => fake()->numberBetween(15, 480), // 15 minutes to 8 hours
            'hourly_rate' => fake()->randomFloat(2, 50, 200),
            'billable' => fake()->boolean(80), // 80% chance of being billable
            'billed' => fake()->boolean(30), // 30% chance of being already billed
            'date' => fake()->dateTimeBetween('-3 months', 'now'),
            'started_at' => fake()->time(),
            'ended_at' => fake()->time(),
        ];
    }
}
