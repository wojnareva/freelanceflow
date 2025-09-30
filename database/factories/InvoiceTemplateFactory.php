<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceTemplate>
 */
class InvoiceTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 500, 5000);
        $taxRate = fake()->randomElement([0, 8.25, 10, 15, 20]);
        $frequency = fake()->randomElement(['weekly', 'monthly', 'quarterly', 'yearly']);
        $startDate = fake()->dateTimeBetween('-6 months', '+1 month');

        return [
            'user_id' => User::factory(),
            'client_id' => Client::factory(),
            'project_id' => fake()->boolean(70) ? Project::factory() : null,
            'name' => fake()->randomElement([
                'Monthly Retainer',
                'Weekly Maintenance',
                'Quarterly Support',
                'Annual License',
                'Development Retainer',
                'Hosting & Maintenance',
                'Consulting Services',
                'Support Package',
            ]),
            'description' => fake()->optional()->sentence(),
            'frequency' => $frequency,
            'start_date' => $startDate,
            'end_date' => fake()->optional(30)->dateTimeBetween($startDate, '+2 years'),
            'next_generation_date' => $this->calculateNextDate($startDate, $frequency),
            'days_until_due' => fake()->randomElement([7, 14, 30, 45, 60]),
            'amount' => $amount,
            'currency' => fake()->randomElement(['USD', 'EUR', 'GBP', 'CAD']),
            'tax_rate' => $taxRate,
            'line_items' => $this->generateLineItems($amount),
            'notes' => fake()->optional()->paragraph(),
            'is_active' => fake()->boolean(85),
            'invoices_generated' => fake()->numberBetween(0, 12),
            'last_generated_at' => fake()->optional(60)->dateTimeBetween('-3 months', 'now'),
        ];
    }

    private function calculateNextDate($startDate, $frequency): \DateTime
    {
        $date = clone $startDate;

        return match ($frequency) {
            'weekly' => $date->modify('+1 week'),
            'monthly' => $date->modify('+1 month'),
            'quarterly' => $date->modify('+3 months'),
            'yearly' => $date->modify('+1 year'),
        };
    }

    private function generateLineItems($totalAmount): array
    {
        $itemCount = fake()->numberBetween(1, 3);
        $items = [];
        $remainingAmount = $totalAmount;

        for ($i = 0; $i < $itemCount; $i++) {
            if ($i === $itemCount - 1) {
                // Last item gets the remaining amount
                $rate = $remainingAmount;
            } else {
                $rate = fake()->randomFloat(2, 100, $remainingAmount * 0.6);
                $remainingAmount -= $rate;
            }

            $items[] = [
                'description' => fake()->randomElement([
                    'Development Services',
                    'Maintenance & Support',
                    'Consulting Hours',
                    'Project Management',
                    'Design Services',
                    'Testing & QA',
                    'Documentation',
                    'Training Services',
                ]),
                'quantity' => 1,
                'rate' => $rate,
                'type' => 'fixed',
            ];
        }

        return $items;
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function dueForGeneration(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'next_generation_date' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }
}
