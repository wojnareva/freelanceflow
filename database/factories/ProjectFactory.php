<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
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
     * @param string $locale
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
            'client_id' => \App\Models\Client::factory(),
            'name' => $faker->words(3, true),
            'description' => $faker->optional()->paragraph(),
            'status' => $faker->randomElement(['draft', 'active', 'on_hold', 'completed', 'archived']),
            'budget' => $faker->optional()->randomFloat(2, 1000, 50000),
            'hourly_rate' => $faker->randomFloat(2, 50, 200),
            'deadline' => $faker->optional()->dateTimeBetween('now', '+6 months'),
            'started_at' => $faker->optional()->dateTimeBetween('-3 months', 'now'),
            'completed_at' => $faker->optional()->dateTimeBetween('-1 month', 'now'),
            'color' => $faker->randomElement(['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6']),
        ];
    }
}
