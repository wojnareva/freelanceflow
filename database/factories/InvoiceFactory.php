<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 500, 5000);
        $taxRate = fake()->randomFloat(2, 0, 25);
        $taxAmount = $subtotal * ($taxRate / 100);
        $total = $subtotal + $taxAmount;

        return [
            'client_id' => \App\Models\Client::factory(),
            'project_id' => \App\Models\Project::factory(),
            'status' => fake()->randomElement(['draft', 'sent', 'paid', 'overdue', 'cancelled']),
            'issue_date' => fake()->dateTimeBetween('-3 months', 'now'),
            'due_date' => fake()->dateTimeBetween('now', '+2 months'),
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'currency' => fake()->randomElement(['USD', 'EUR', 'GBP']),
            'notes' => fake()->optional()->paragraph(),
            'client_details' => [
                'name' => fake()->name(),
                'company' => fake()->company(),
                'address' => fake()->address(),
            ],
            'paid_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
