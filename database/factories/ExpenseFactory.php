<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
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
            'title' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'amount' => fake()->randomFloat(2, 10, 1000),
            'currency' => fake()->randomElement(['USD', 'EUR', 'GBP']),
            'category' => fake()->randomElement(['Travel', 'Supplies', 'Software', 'Hardware', 'Marketing', 'Other']),
            'billable' => fake()->boolean(60), // 60% chance of being billable
            'billed' => fake()->boolean(20), // 20% chance of being already billed
            'receipt_path' => fake()->optional()->filePath(),
            'expense_date' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
