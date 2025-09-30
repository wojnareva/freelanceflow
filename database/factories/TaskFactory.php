<?php

namespace Database\Factories;

use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * The Faker instance for a specific locale.
     *
     * @var \Faker\Generator|null
     */
    protected $faker;

    /**
     * Create a new factory instance for a specific locale.
     *
     * @return $this
     */
    public function withLocale(string $locale)
    {
        $this->faker = Faker::create($locale);

        return $this;
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = $this->faker ?? fake();

        return [
            'project_id' => \App\Models\Project::factory(),
            'title' => $faker->sentence(4),
            'description' => $faker->optional()->paragraph(),
            'status' => $faker->randomElement(['todo', 'in_progress', 'completed', 'blocked']),
            'priority' => $faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'estimated_hours' => $faker->optional()->randomFloat(2, 1, 40),
            'actual_hours' => $faker->randomFloat(2, 0, 20),
            'due_date' => $faker->optional()->dateTimeBetween('now', '+2 months'),
            'sort_order' => $faker->numberBetween(0, 100),
        ];
    }
}
